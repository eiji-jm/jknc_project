<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSow;
use App\Models\ProjectSowReport;
use App\Models\ProjectStart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = collect();

        if (Schema::hasTable('projects')) {
            $projects = Project::query()
                ->with(['deal:id,deal_code', 'company:id,company_name'])
                ->latest()
                ->get();
        }

        $stats = [
            'all' => $projects->count(),
            'start' => $projects->where('current_phase', 'Start')->count(),
            'planning' => $projects->where('current_phase', 'Planning')->count(),
            'active' => $projects->whereIn('status', ['Start', 'Planning', 'For NTP Approval', 'Execution', 'Reporting', 'Delivery'])->count(),
            'completed' => $projects->where('status', 'Completed')->count(),
        ];

        return view('project.index', [
            'projects' => $projects,
            'stats' => $stats,
        ]);
    }

    public function show(Request $request, Project $project): View
    {
        $project->load([
            'deal:id,deal_code,engagement_type',
            'contact:id,first_name,last_name,email,phone,company_name',
            'company:id,company_name',
            'starts' => fn ($query) => $query->latest(),
            'sows' => fn ($query) => $query->latest(),
            'sowReports' => fn ($query) => $query->latest(),
        ]);

        $start = $project->starts->first();
        $sow = $project->sows->first();
        $report = $project->sowReports->first();
        $tab = in_array((string) $request->query('tab', 'start'), ['start', 'sow', 'report'], true)
            ? (string) $request->query('tab', 'start')
            : 'start';

        return view('project.show', compact('project', 'start', 'sow', 'report', 'tab'));
    }

    public function updateStart(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'date_started' => ['nullable', 'date'],
            'date_completed' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'checklist_label' => ['nullable', 'array'],
            'checklist_label.*' => ['nullable', 'string', 'max:255'],
            'checklist_status' => ['nullable', 'array'],
            'checklist_status.*' => ['nullable', 'in:provided,pending'],
            'engagement_requirement' => ['nullable', 'array'],
            'engagement_requirement.*' => ['nullable', 'string', 'max:255'],
            'engagement_purpose' => ['nullable', 'array'],
            'engagement_purpose.*' => ['nullable', 'string', 'max:255'],
            'engagement_assigned_to' => ['nullable', 'array'],
            'engagement_assigned_to.*' => ['nullable', 'string', 'max:255'],
            'engagement_timeline' => ['nullable', 'array'],
            'engagement_timeline.*' => ['nullable', 'string', 'max:255'],
            'routing_role' => ['nullable', 'array'],
            'routing_role.*' => ['nullable', 'string', 'max:255'],
            'routing_status' => ['nullable', 'array'],
            'routing_status.*' => ['nullable', 'in:pending,approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $start = $project->starts()->latest()->first() ?: new ProjectStart(['project_id' => $project->id]);
        $start->fill([
            'date_started' => $validated['date_started'] ?? null,
            'date_completed' => $validated['date_completed'] ?? null,
            'status' => $validated['status'],
            'checklist' => $this->buildChecklistPayload($request),
            'engagement_requirements' => $this->buildRequirementRows($request, 'engagement'),
            'routing' => $this->buildRoutingRows($request),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);
        $start->project_id = $project->id;
        $start->save();

        return redirect()
            ->route('project.show', ['project' => $project->id, 'tab' => 'start'])
            ->with('success', 'START form updated successfully.');
    }

    public function updateSow(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'version_number' => ['nullable', 'string', 'max:50'],
            'date_prepared' => ['nullable', 'date'],
            'approval_status' => ['required', 'string', 'max:50'],
            'ntp_status' => ['required', 'string', 'max:50'],
            'client_confirmation_name' => ['nullable', 'string', 'max:255'],
            'client_signed_attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'reviewed_by' => ['nullable', 'string', 'max:255'],
            'referred_by_closed_by' => ['nullable', 'string', 'max:255'],
            'sales_marketing' => ['nullable', 'string', 'max:255'],
            'lead_consultant' => ['nullable', 'string', 'max:255'],
            'lead_associate_assigned' => ['nullable', 'string', 'max:255'],
            'finance' => ['nullable', 'string', 'max:255'],
            'president' => ['nullable', 'string', 'max:255'],
            'record_custodian' => ['nullable', 'string', 'max:255'],
            'date_recorded' => ['nullable', 'date'],
            'date_signed' => ['nullable', 'date'],
        ]);

        $sow = $project->sows()->latest()->first() ?: new ProjectSow(['project_id' => $project->id]);
        $sow->fill([
            'version_number' => $validated['version_number'] ?? null,
            'date_prepared' => $validated['date_prepared'] ?? null,
            'within_scope_items' => $this->buildScopeRows($request, 'within'),
            'out_of_scope_items' => $this->buildScopeRows($request, 'out'),
            'client_confirmation_name' => $validated['client_confirmation_name'] ?? null,
            'internal_approval' => $this->buildInternalApprovalPayload($validated),
            'approval_status' => $validated['approval_status'],
            'ntp_status' => $validated['ntp_status'],
        ]);
        $sow->project_id = $project->id;

        if ($request->hasFile('client_signed_attachment')) {
            if ($sow->client_signed_attachment_path && Storage::disk('public')->exists($sow->client_signed_attachment_path)) {
                Storage::disk('public')->delete($sow->client_signed_attachment_path);
            }
            $sow->client_signed_attachment_path = $request->file('client_signed_attachment')->store("projects/{$project->id}/sow", 'public');
        }

        $sow->save();

        return redirect()
            ->route('project.show', ['project' => $project->id, 'tab' => 'sow'])
            ->with('success', 'Scope of Work updated successfully.');
    }

    public function updateReport(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'version_number' => ['nullable', 'string', 'max:50'],
            'date_prepared' => ['nullable', 'date'],
            'project_completion_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'client_confirmation_name' => ['nullable', 'string', 'max:255'],
            'total_main_tasks' => ['nullable', 'numeric', 'min:0'],
            'open' => ['nullable', 'numeric', 'min:0'],
            'in_progress' => ['nullable', 'numeric', 'min:0'],
            'delayed' => ['nullable', 'numeric', 'min:0'],
            'completed' => ['nullable', 'numeric', 'min:0'],
            'on_hold' => ['nullable', 'numeric', 'min:0'],
            'key_issues' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'way_forward' => ['nullable', 'string'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'reviewed_by' => ['nullable', 'string', 'max:255'],
            'referred_by_closed_by' => ['nullable', 'string', 'max:255'],
            'sales_marketing' => ['nullable', 'string', 'max:255'],
            'lead_consultant' => ['nullable', 'string', 'max:255'],
            'lead_associate_assigned' => ['nullable', 'string', 'max:255'],
            'finance' => ['nullable', 'string', 'max:255'],
            'president' => ['nullable', 'string', 'max:255'],
            'record_custodian' => ['nullable', 'string', 'max:255'],
            'date_recorded' => ['nullable', 'date'],
            'date_signed' => ['nullable', 'date'],
        ]);

        $report = $project->sowReports()->latest()->first() ?: new ProjectSowReport(['project_id' => $project->id]);
        $report->fill([
            'version_number' => $validated['version_number'] ?? null,
            'date_prepared' => $validated['date_prepared'] ?? null,
            'within_scope_items' => $this->buildScopeRows($request, 'within'),
            'out_of_scope_items' => $this->buildScopeRows($request, 'out'),
            'status_summary' => [
                'total_main_tasks' => (int) ($validated['total_main_tasks'] ?? 0),
                'open' => (int) ($validated['open'] ?? 0),
                'in_progress' => (int) ($validated['in_progress'] ?? 0),
                'delayed' => (int) ($validated['delayed'] ?? 0),
                'completed' => (int) ($validated['completed'] ?? 0),
                'on_hold' => (int) ($validated['on_hold'] ?? 0),
            ],
            'project_completion_percentage' => $validated['project_completion_percentage'] ?? null,
            'key_issues' => $validated['key_issues'] ?? null,
            'recommendations' => $validated['recommendations'] ?? null,
            'way_forward' => $validated['way_forward'] ?? null,
            'client_confirmation_name' => $validated['client_confirmation_name'] ?? null,
            'internal_approval' => $this->buildInternalApprovalPayload($validated),
        ]);
        $report->project_id = $project->id;
        $report->save();

        return redirect()
            ->route('project.show', ['project' => $project->id, 'tab' => 'report'])
            ->with('success', 'SOW report updated successfully.');
    }

    private function buildChecklistPayload(Request $request): array
    {
        $labels = (array) $request->input('checklist_label', []);
        $statuses = (array) $request->input('checklist_status', []);

        return collect($labels)->map(function ($label, $index) use ($statuses) {
            $clean = trim((string) $label);
            if ($clean === '') {
                return null;
            }

            return [
                'label' => $clean,
                'status' => in_array($statuses[$index] ?? 'pending', ['provided', 'pending'], true) ? $statuses[$index] : 'pending',
            ];
        })->filter()->values()->all();
    }

    private function buildRequirementRows(Request $request, string $prefix): array
    {
        $requirements = (array) $request->input("{$prefix}_requirement", []);
        $purposes = (array) $request->input("{$prefix}_purpose", []);
        $assigned = (array) $request->input("{$prefix}_assigned_to", []);
        $timelines = (array) $request->input("{$prefix}_timeline", []);

        return collect($requirements)->map(function ($value, $index) use ($purposes, $assigned, $timelines) {
            $requirement = trim((string) $value);
            if ($requirement === '') {
                return null;
            }

            return [
                'requirement' => $requirement,
                'purpose' => trim((string) ($purposes[$index] ?? '')),
                'assigned_to' => trim((string) ($assigned[$index] ?? '')),
                'timeline' => trim((string) ($timelines[$index] ?? '')),
            ];
        })->filter()->values()->all();
    }

    private function buildRoutingRows(Request $request): array
    {
        $roles = (array) $request->input('routing_role', []);
        $statuses = (array) $request->input('routing_status', []);

        return collect($roles)->map(function ($value, $index) use ($statuses) {
            $role = trim((string) $value);
            if ($role === '') {
                return null;
            }

            $status = (string) ($statuses[$index] ?? 'pending');

            return [
                'role' => $role,
                'status' => in_array($status, ['pending', 'approved', 'rejected'], true) ? $status : 'pending',
            ];
        })->filter()->values()->all();
    }

    private function buildScopeRows(Request $request, string $prefix): array
    {
        $mainTasks = (array) $request->input("{$prefix}_main_task_description", []);
        $subTasks = (array) $request->input("{$prefix}_sub_task_description", []);
        $responsibles = (array) $request->input("{$prefix}_responsible", []);
        $durations = (array) $request->input("{$prefix}_duration", []);
        $startDates = (array) $request->input("{$prefix}_start_date", []);
        $endDates = (array) $request->input("{$prefix}_end_date", []);
        $statuses = (array) $request->input("{$prefix}_status", []);
        $remarks = (array) $request->input("{$prefix}_remarks", []);

        return collect($mainTasks)->map(function ($value, $index) use ($subTasks, $responsibles, $durations, $startDates, $endDates, $statuses, $remarks) {
            $mainTask = trim((string) $value);
            if ($mainTask === '') {
                return null;
            }

            return [
                'main_task_description' => $mainTask,
                'sub_task_description' => trim((string) ($subTasks[$index] ?? '')),
                'responsible' => trim((string) ($responsibles[$index] ?? '')),
                'duration' => trim((string) ($durations[$index] ?? '')),
                'start_date' => $startDates[$index] ?? null,
                'end_date' => $endDates[$index] ?? null,
                'status' => trim((string) ($statuses[$index] ?? '')),
                'remarks' => trim((string) ($remarks[$index] ?? '')),
            ];
        })->filter()->values()->all();
    }

    private function buildInternalApprovalPayload(array $validated): array
    {
        return [
            'prepared_by' => $validated['prepared_by'] ?? null,
            'reviewed_by' => $validated['reviewed_by'] ?? null,
            'referred_by_closed_by' => $validated['referred_by_closed_by'] ?? null,
            'sales_marketing' => $validated['sales_marketing'] ?? null,
            'lead_consultant' => $validated['lead_consultant'] ?? null,
            'lead_associate_assigned' => $validated['lead_associate_assigned'] ?? null,
            'finance' => $validated['finance'] ?? null,
            'president' => $validated['president'] ?? null,
            'record_custodian' => $validated['record_custodian'] ?? null,
            'date_recorded' => $validated['date_recorded'] ?? null,
            'date_signed' => $validated['date_signed'] ?? null,
        ];
    }
}
