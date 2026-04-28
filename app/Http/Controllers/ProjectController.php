<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesPdfPreview;
use App\Models\Project;
use App\Models\ProjectSow;
use App\Models\ProjectSowReport;
use App\Models\ProjectStart;
use Illuminate\Support\Arr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    use GeneratesPdfPreview;

    public function index(Request $request): View
    {
        $projects = collect();

        if (Schema::hasTable('projects')) {
            $projects = Project::query()
                ->with(['deal:id,deal_code', 'company:id,company_name'])
                ->latest()
                ->get()
                ->filter(fn (Project $project): bool => ! Str::contains(Str::lower(trim((string) $project->engagement_type)), 'regular'))
                ->values();
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

    public function storeManual(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'service_area' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'string', 'max:1000'],
            'products' => ['nullable', 'string', 'max:1000'],
            'planned_start_date' => ['nullable', 'date'],
            'target_completion_date' => ['nullable', 'date'],
            'assigned_project_manager' => ['nullable', 'string', 'max:255'],
            'assigned_consultant' => ['nullable', 'string', 'max:255'],
            'assigned_associate' => ['nullable', 'string', 'max:255'],
            'client_confirmation_name' => ['nullable', 'string', 'max:255'],
            'scope_summary' => ['nullable', 'string', 'max:2000'],
            'engagement_requirements_text' => ['nullable', 'string', 'max:4000'],
        ]);

        $project = Project::query()->create([
            'name' => $validated['name'],
            'engagement_type' => 'Project',
            'status' => 'Start',
            'current_phase' => 'Start',
            'current_step' => 'START Checklist',
            'planned_start_date' => $validated['planned_start_date'] ?? null,
            'target_completion_date' => $validated['target_completion_date'] ?? null,
            'assigned_project_manager' => $validated['assigned_project_manager'] ?? null,
            'assigned_consultant' => $validated['assigned_consultant'] ?? null,
            'assigned_associate' => $validated['assigned_associate'] ?? null,
            'client_name' => $validated['client_name'] ?? null,
            'business_name' => $validated['business_name'] ?? null,
            'service_area' => $validated['service_area'] ?? null,
            'services' => $validated['services'] ?? null,
            'products' => $validated['products'] ?? null,
            'scope_summary' => $validated['scope_summary'] ?? null,
            'client_confirmation_name' => $validated['client_confirmation_name'] ?? ($validated['client_name'] ?? null),
            'metadata' => [
                'created_from' => 'project_dashboard_manual',
            ],
            'opened_at' => now(),
        ]);

        ProjectStart::query()->create([
            'project_id' => $project->id,
            'form_date' => now()->toDateString(),
            'date_started' => $validated['planned_start_date'] ?? now()->toDateString(),
            'status' => 'pending',
            'checklist' => $this->manualStartChecklist(),
            'kyc_requirements' => [
                'organization_type' => 'unknown',
                'sole' => [],
                'juridical' => [],
            ],
            'engagement_requirements' => $this->buildManualRequirementRows(
                $validated['engagement_requirements_text'] ?? '',
                $validated['assigned_associate'] ?? ($validated['assigned_consultant'] ?? ($validated['assigned_project_manager'] ?? 'Assigned team')),
                'START intake'
            ),
            'approval_steps' => $this->manualStartApprovalSteps(
                $validated['assigned_consultant'] ?? null,
                $validated['assigned_associate'] ?? null
            ),
            'routing' => $this->manualRoutingRows(),
            'clearance' => $this->manualClearancePayload(
                $validated['assigned_consultant'] ?? null,
                $validated['assigned_associate'] ?? null
            ),
        ]);

        $scopeRows = $this->manualScopeRows($validated);
        $internalApproval = $this->manualInternalApprovalPayload(
            $validated['assigned_project_manager'] ?? null,
            $validated['assigned_consultant'] ?? null,
            $validated['assigned_associate'] ?? null
        );

        ProjectSow::query()->create([
            'project_id' => $project->id,
            'version_number' => '1.0',
            'date_prepared' => now()->toDateString(),
            'within_scope_items' => $scopeRows,
            'out_of_scope_items' => [],
            'client_confirmation_name' => $project->client_confirmation_name,
            'internal_approval' => $internalApproval,
            'approval_status' => 'draft',
            'ntp_status' => 'pending',
        ]);

        ProjectSowReport::query()->create([
            'project_id' => $project->id,
            'version_number' => '1.0',
            'date_prepared' => now()->toDateString(),
            'within_scope_items' => $scopeRows,
            'out_of_scope_items' => [],
            'status_summary' => [
                'total_main_tasks' => count($scopeRows),
                'open' => count($scopeRows),
                'in_progress' => 0,
                'delayed' => 0,
                'completed' => 0,
                'on_hold' => 0,
            ],
            'project_completion_percentage' => 0,
            'client_confirmation_name' => $project->client_confirmation_name,
            'internal_approval' => $internalApproval,
        ]);

        return redirect()
            ->route('project.show', ['project' => $project, 'tab' => 'sow'])
            ->with('success', 'Project created and opened in Scope of Work.');
    }

    public function show(Request $request, Project $project): View
    {
        $payload = $this->buildProjectDocumentPayload($project);
        extract($payload);
        $tab = in_array((string) $request->query('tab', 'sow'), ['sow', 'report'], true)
            ? (string) $request->query('tab', 'sow')
            : 'sow';

        return view('project.show', compact('project', 'start', 'sow', 'report', 'tab'));
    }

    public function downloadStartPdf(Project $project)
    {
        $payload = $this->buildProjectDocumentPayload($project);
        extract($payload);
        $contactName = trim(collect([$project->contact?->first_name, $project->contact?->last_name])->filter()->implode(' '))
            ?: ($project->client_name ?: '-');
        $startKyc = (array) ($start?->kyc_requirements ?? []);
        $startKycOrganization = (string) ($startKyc['organization_type'] ?? 'unknown');
        $startKycSole = collect($startKyc['sole'] ?? []);
        $startKycJuridical = collect($startKyc['juridical'] ?? []);
        $startReqs = collect($start?->engagement_requirements ?? [])->whenEmpty(fn () => collect([['number' => 1, 'requirement' => '', 'notes' => '', 'purpose' => '', 'provided_by' => '', 'submitted_to' => '', 'assigned_to' => '', 'timeline' => '']]));
        $startApprovalSteps = collect($start?->approval_steps ?? [])->whenEmpty(fn () => collect([
            ['requirement' => 'Client Contact Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Deal Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Business Information Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Client Information Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Service Task Activation & Routing Tracker (Start)', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Engagement-Specific Requirement', 'responsible_person' => 'Sales & Marketing/Consultant/Associate', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Proposal/Contract', 'responsible_person' => 'Sales & Marketing/Lead Consultant/Lead Associate', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Final Quote', 'responsible_person' => 'Lead Consultant/Lead Associate', 'name_and_signature' => '', 'date_time_done' => ''],
        ]));
        $startClearance = (array) ($start?->clearance ?? []);

        $targetPath = 'generated-previews/project/start/' . ($project->project_code ?: $project->id) . '-start-form.pdf';
        $pdfPath = $this->generatePdfPreview('project.pdf.start', compact(
            'project',
            'start',
            'contactName',
            'startKycOrganization',
            'startKycSole',
            'startKycJuridical',
            'startReqs',
            'startApprovalSteps',
            'startClearance'
        ), $targetPath);

        abort_unless($pdfPath && Storage::disk('public')->exists($pdfPath), 500, 'Unable to generate START PDF preview.');

        return redirect()->route('uploads.show', ['path' => $pdfPath, 'download' => 1]);
    }

    public function updateStart(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'form_date' => ['nullable', 'date'],
            'date_started' => ['nullable', 'date'],
            'date_completed' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'checklist_label' => ['nullable', 'array'],
            'checklist_label.*' => ['nullable', 'string', 'max:255'],
            'checklist_status' => ['nullable', 'array'],
            'checklist_status.*' => ['nullable', 'in:provided,pending'],
            'kyc_sole_label' => ['nullable', 'array'],
            'kyc_sole_label.*' => ['nullable', 'string', 'max:255'],
            'kyc_sole_status' => ['nullable', 'array'],
            'kyc_sole_status.*' => ['nullable', 'in:provided,pending'],
            'kyc_juridical_label' => ['nullable', 'array'],
            'kyc_juridical_label.*' => ['nullable', 'string', 'max:255'],
            'kyc_juridical_status' => ['nullable', 'array'],
            'kyc_juridical_status.*' => ['nullable', 'in:provided,pending'],
            'engagement_requirement' => ['nullable', 'array'],
            'engagement_requirement.*' => ['nullable', 'string', 'max:255'],
            'engagement_notes' => ['nullable', 'array'],
            'engagement_notes.*' => ['nullable', 'string', 'max:255'],
            'engagement_purpose' => ['nullable', 'array'],
            'engagement_purpose.*' => ['nullable', 'string', 'max:255'],
            'engagement_provided_by' => ['nullable', 'array'],
            'engagement_provided_by.*' => ['nullable', 'string', 'max:255'],
            'engagement_submitted_to' => ['nullable', 'array'],
            'engagement_submitted_to.*' => ['nullable', 'string', 'max:255'],
            'engagement_assigned_to' => ['nullable', 'array'],
            'engagement_assigned_to.*' => ['nullable', 'string', 'max:255'],
            'engagement_timeline' => ['nullable', 'array'],
            'engagement_timeline.*' => ['nullable', 'string', 'max:255'],
            'approval_requirement' => ['nullable', 'array'],
            'approval_requirement.*' => ['nullable', 'string', 'max:255'],
            'approval_responsible_person' => ['nullable', 'array'],
            'approval_responsible_person.*' => ['nullable', 'string', 'max:255'],
            'approval_name_and_signature' => ['nullable', 'array'],
            'approval_name_and_signature.*' => ['nullable', 'string', 'max:255'],
            'approval_date_time_done' => ['nullable', 'array'],
            'approval_date_time_done.*' => ['nullable', 'string', 'max:255'],
            'clearance_assigned_team_lead' => ['nullable', 'string', 'max:255'],
            'clearance_assigned_team_lead_signature' => ['nullable', 'string', 'max:255'],
            'clearance_lead_consultant_confirmed' => ['nullable', 'string', 'max:255'],
            'clearance_lead_consultant_signature' => ['nullable', 'string', 'max:255'],
            'clearance_lead_associate_assigned' => ['nullable', 'string', 'max:255'],
            'clearance_lead_associate_signature' => ['nullable', 'string', 'max:255'],
            'clearance_sales_marketing' => ['nullable', 'string', 'max:255'],
            'clearance_sales_marketing_signature' => ['nullable', 'string', 'max:255'],
            'clearance_record_custodian_name' => ['nullable', 'string', 'max:255'],
            'clearance_record_custodian_signature' => ['nullable', 'string', 'max:255'],
            'clearance_date_recorded' => ['nullable', 'date'],
            'clearance_date_signed' => ['nullable', 'date'],
            'routing_role' => ['nullable', 'array'],
            'routing_role.*' => ['nullable', 'string', 'max:255'],
            'routing_status' => ['nullable', 'array'],
            'routing_status.*' => ['nullable', 'in:pending,approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $start = $project->starts()->latest()->first() ?: new ProjectStart(['project_id' => $project->id]);
        $resolvedFormDate = $validated['form_date']
            ?? optional($start->form_date)->toDateString()
            ?? optional($start->created_at)->toDateString()
            ?? now()->toDateString();
        $start->fill([
            'form_date' => $resolvedFormDate,
            'date_started' => $validated['date_started'] ?? null,
            'date_completed' => $validated['date_completed'] ?? null,
            'status' => $validated['status'],
            'checklist' => $this->buildChecklistPayload($request),
            'kyc_requirements' => $this->buildKycRequirementsPayload($request, $start->kyc_requirements ?? []),
            'engagement_requirements' => $this->buildRequirementRows($request, 'engagement'),
            'approval_steps' => $this->buildApprovalRows($request),
            'routing' => $this->buildRoutingRows($request),
            'clearance' => $this->buildClearancePayload($validated),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);
        $start->project_id = $project->id;
        $start->save();

        $redirectUrl = $request->input('redirect_url');
        if (is_string($redirectUrl) && $redirectUrl !== '' && URL::isValidUrl($redirectUrl)) {
            $allowedBases = collect([
                rtrim((string) config('app.url'), '/'),
                rtrim($request->getSchemeAndHttpHost(), '/'),
            ])->filter()->unique();

            if ($allowedBases->contains(fn (string $base): bool => str_starts_with($redirectUrl, $base))) {
                return redirect()
                    ->to($redirectUrl)
                    ->with('success', 'START form updated successfully.');
            }
        }

        return redirect()
            ->route('project.show', ['project' => $project->id, 'tab' => 'sow'])
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
            'project_completion_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
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
        $this->updateProjectSowReportSummary($project, $sow, $validated);

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
            'status_summary' => $this->buildReportStatusSummary($validated),
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

    private function updateProjectSowReportSummary(Project $project, ProjectSow $sow, array $validated): void
    {
        $report = $project->sowReports()->latest()->first();

        if (! $report) {
            $report = new ProjectSowReport(['project_id' => $project->id]);
            $report->version_number = $sow->version_number;
            $report->date_prepared = $sow->date_prepared;
            $report->within_scope_items = $sow->within_scope_items ?? [];
            $report->out_of_scope_items = $sow->out_of_scope_items ?? [];
            $report->client_confirmation_name = $sow->client_confirmation_name;
            $report->internal_approval = $sow->internal_approval ?? [];
        }

        $report->fill([
            'status_summary' => $this->buildReportStatusSummary($validated),
            'project_completion_percentage' => $validated['project_completion_percentage'] ?? null,
            'key_issues' => $validated['key_issues'] ?? null,
            'recommendations' => $validated['recommendations'] ?? null,
            'way_forward' => $validated['way_forward'] ?? null,
        ]);
        $report->project_id = $project->id;
        $report->save();
    }

    private function buildReportStatusSummary(array $validated): array
    {
        return [
            'total_main_tasks' => (int) ($validated['total_main_tasks'] ?? 0),
            'open' => (int) ($validated['open'] ?? 0),
            'in_progress' => (int) ($validated['in_progress'] ?? 0),
            'delayed' => (int) ($validated['delayed'] ?? 0),
            'completed' => (int) ($validated['completed'] ?? 0),
            'on_hold' => (int) ($validated['on_hold'] ?? 0),
        ];
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
        $notes = (array) $request->input("{$prefix}_notes", []);
        $purposes = (array) $request->input("{$prefix}_purpose", []);
        $providedBy = (array) $request->input("{$prefix}_provided_by", []);
        $submittedTo = (array) $request->input("{$prefix}_submitted_to", []);
        $assigned = (array) $request->input("{$prefix}_assigned_to", []);
        $timelines = (array) $request->input("{$prefix}_timeline", []);

        return collect($requirements)->map(function ($value, $index) use ($notes, $purposes, $providedBy, $submittedTo, $assigned, $timelines) {
            $requirement = trim((string) $value);
            if ($requirement === '') {
                return null;
            }

            return [
                'number' => $index + 1,
                'requirement' => $requirement,
                'notes' => trim((string) ($notes[$index] ?? '')),
                'purpose' => trim((string) ($purposes[$index] ?? '')),
                'provided_by' => trim((string) ($providedBy[$index] ?? '')),
                'submitted_to' => trim((string) ($submittedTo[$index] ?? '')),
                'assigned_to' => trim((string) ($assigned[$index] ?? '')),
                'timeline' => trim((string) ($timelines[$index] ?? '')),
            ];
        })->filter()->values()->all();
    }

    private function buildKycRequirementsPayload(Request $request, array $existing = []): array
    {
        $buildGroup = function (string $prefix) use ($request): array {
            $labels = (array) $request->input("kyc_{$prefix}_label", []);
            $statuses = (array) $request->input("kyc_{$prefix}_status", []);

            return collect($labels)->map(function ($label, $index) use ($statuses) {
                $clean = trim((string) $label);
                if ($clean === '') {
                    return null;
                }

                return [
                    'label' => $clean,
                    'status' => in_array($statuses[$index] ?? 'pending', ['provided', 'pending'], true) ? (string) $statuses[$index] : 'pending',
                ];
            })->filter()->values()->all();
        };

        return [
            'organization_type' => (string) ($existing['organization_type'] ?? 'unknown'),
            'sole' => $buildGroup('sole'),
            'juridical' => $buildGroup('juridical'),
        ];
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

    private function buildApprovalRows(Request $request): array
    {
        $requirements = (array) $request->input('approval_requirement', []);
        $responsibles = (array) $request->input('approval_responsible_person', []);
        $signatures = (array) $request->input('approval_name_and_signature', []);
        $dates = (array) $request->input('approval_date_time_done', []);

        return collect($requirements)->map(function ($value, $index) use ($responsibles, $signatures, $dates) {
            $requirement = trim((string) $value);
            if ($requirement === '') {
                return null;
            }

            return [
                'requirement' => $requirement,
                'responsible_person' => trim((string) ($responsibles[$index] ?? '')),
                'name_and_signature' => trim((string) ($signatures[$index] ?? '')),
                'date_time_done' => trim((string) ($dates[$index] ?? '')),
            ];
        })->filter()->values()->all();
    }

    private function buildClearancePayload(array $validated): array
    {
        return [
            'assigned_team_lead' => $validated['clearance_assigned_team_lead'] ?? null,
            'assigned_team_lead_signature' => $validated['clearance_assigned_team_lead_signature'] ?? null,
            'lead_consultant_confirmed' => $validated['clearance_lead_consultant_confirmed'] ?? null,
            'lead_consultant_signature' => $validated['clearance_lead_consultant_signature'] ?? null,
            'lead_associate_assigned' => $validated['clearance_lead_associate_assigned'] ?? null,
            'lead_associate_signature' => $validated['clearance_lead_associate_signature'] ?? null,
            'sales_marketing' => $validated['clearance_sales_marketing'] ?? null,
            'sales_marketing_signature' => $validated['clearance_sales_marketing_signature'] ?? null,
            'record_custodian_name' => $validated['clearance_record_custodian_name'] ?? null,
            'record_custodian_signature' => $validated['clearance_record_custodian_signature'] ?? null,
            'date_recorded' => $validated['clearance_date_recorded'] ?? null,
            'date_signed' => $validated['clearance_date_signed'] ?? null,
        ];
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

    private function buildProjectDocumentPayload(Project $project): array
    {
        $project->load([
            'deal:id,deal_code,engagement_type',
            'contact:id,first_name,last_name,email,phone,company_name',
            'company.primaryContact:id,first_name,middle_name,last_name,email,phone,company_name,cif_status,organization_type,business_type_organization,ownership_flag,foreign_business_nature',
            'company.latestBif',
            'starts' => fn ($query) => $query->latest(),
            'sows' => fn ($query) => $query->latest(),
            'sowReports' => fn ($query) => $query->latest(),
        ]);

        $start = $project->starts->first();
        if ($start) {
            $start->kyc_requirements = $this->defaultStartKycRequirementsForProject($project);
        }

        return [
            'project' => $project,
            'start' => $start,
            'sow' => $project->sows->first(),
            'report' => $project->sowReports->first(),
        ];
    }

    private function defaultStartKycRequirementsForProject(Project $project): array
    {
        $company = $project->company;
        $organization = $this->resolveStartOrganizationType($project);
        $kycContext = $this->buildStartKycContext($project);
        $contactApproved = $kycContext['contact_approved'];
        $bifApproved = strtolower((string) ($company?->latestBif?->status ?? '')) === 'approved';
        $contactDocs = $kycContext['contact_documents'];
        $bifDocs = (array) ($company?->latestBif?->client_requirement_documents ?? []);
        $showForeignRows = $kycContext['show_foreign_rows'];

        $hasBif = fn (string $key): bool => $bifApproved && filled(data_get($bifDocs, $key.'.path'));
        $status = fn (bool $complete): string => $complete ? 'provided' : 'pending';

        $sole = [
            ['label' => 'Client Contact Form', 'status' => $status((bool) $project->contact_id)],
            ['label' => 'Client Information Form', 'status' => $status($contactApproved)],
            ['label' => '2 Valid Government IDs', 'status' => $status($contactDocs['two_valid_ids'])],
            ['label' => 'TIN ID', 'status' => $status($contactDocs['tin_proof'])],
            ['label' => 'DTI Certificate of Registration (if Sole Prop)', 'status' => $status($hasBif('sole_dti_certificate_document'))],
            ['label' => 'BIR Certificate of Registration (COR)', 'status' => $status($hasBif('sole_bir_cor_document'))],
            ['label' => 'Business Permit / Mayor\'s Permit', 'status' => $status($hasBif('sole_business_permit_document'))],
            ['label' => 'Proof of Billing (Residential)', 'status' => $status($hasBif('sole_proof_of_billing_residential_document'))],
            ['label' => 'Proof of Billing (Business Address if different)', 'status' => $status($hasBif('sole_proof_of_billing_business_document'))],
            ['label' => 'Special Power of Attorney (if representative)', 'status' => $status($hasBif('sole_spa_document'))],
            ['label' => 'Representative\'s 2 Valid IDs (if applicable)', 'status' => $status($hasBif('sole_representative_ids_document'))],
        ];
        if ($showForeignRows) {
            $sole[] = ['label' => 'If Foreigner: Passport (Bio Page)', 'status' => $status($contactDocs['passport_proof'])];
            $sole[] = ['label' => 'If Foreigner: Valid Visa / ACR I-Card', 'status' => $status($contactDocs['visa_or_acr'])];
        }

        $juridical = [
            ['label' => 'Client Contact Form', 'status' => $status((bool) $project->contact_id)],
            ['label' => 'Business Information Form', 'status' => $status($bifApproved)],
            ['label' => '2 Valid Government IDs (Authorized Signatory)', 'status' => $status($contactDocs['two_valid_ids'])],
            ['label' => 'TIN ID (Authorized Signatory)', 'status' => $status($contactDocs['tin_proof'])],
            ['label' => 'SEC / CDA Certificate of Registration', 'status' => $status($hasBif('juridical_sec_cda_certificate_document'))],
            ['label' => 'BIR Certificate of Registration (COR)', 'status' => $status($hasBif('juridical_bir_cor_document'))],
            ['label' => 'Business Permit / Mayor\'s Permit', 'status' => $status($hasBif('juridical_business_permit_document'))],
            ['label' => 'Articles of Incorporation / Partnership', 'status' => $status($hasBif('juridical_articles_document'))],
            ['label' => 'By-Laws', 'status' => $status($hasBif('juridical_bylaws_document'))],
            ['label' => 'Latest General Information Sheet (GIS)', 'status' => $status($hasBif('juridical_gis_document'))],
            ['label' => 'Appointment of Officers (for OPC, if applicable)', 'status' => $status($hasBif('juridical_appointment_of_officers_document'))],
            ['label' => 'Secretary Certificate OR Board Resolution', 'status' => $status($hasBif('juridical_secretary_certificate_document'))],
            ['label' => 'Ultimate Beneficial Owner (UBO) Declaration', 'status' => $status($hasBif('juridical_ubo_declaration_document'))],
            ['label' => 'Proof of Billing (Company Address)', 'status' => $status($hasBif('juridical_company_billing_document'))],
            ['label' => 'Proof of Billing (Authorized Representative, if applicable)', 'status' => $status($hasBif('juridical_representative_billing_document'))],
        ];
        if ($showForeignRows) {
            $juridical[] = ['label' => 'If Foreign Signatory/Director: Passport (Bio Page)', 'status' => $status($contactDocs['passport_proof'])];
            $juridical[] = ['label' => 'If Foreign Signatory/Director: Valid Visa / ACR I-Card', 'status' => $status($contactDocs['visa_or_acr'])];
        }

        return [
            'organization_type' => $organization,
            'sole' => $organization === 'sole_proprietorship' ? $sole : [],
            'juridical' => $this->isJuridicalOrganization($organization) ? $juridical : [],
        ];
    }

    private function resolveStartOrganizationType(Project $project): string
    {
        $company = $project->company;
        $contact = $project->contact;
        $organization = strtolower(trim((string) ($company?->latestBif?->business_organization
            ?: $company?->primaryContact?->business_type_organization
            ?: $company?->primaryContact?->organization_type
            ?: $contact?->business_type_organization
            ?: $contact?->organization_type
            ?: '')));

        if ($organization === '' && $company) {
            return 'corporation';
        }

        return $organization === '' ? 'unknown' : $organization;
    }

    private function isJuridicalOrganization(string $organization): bool
    {
        return in_array($organization, ['partnership', 'corporation', 'cooperative', 'ngo', 'other', 'opc'], true);
    }

    private function buildStartKycContext(Project $project): array
    {
        $company = $project->company;
        $organization = $this->resolveStartOrganizationType($project);
        $contacts = collect();

        if ($project->contact) {
            $contacts->push($project->contact);
        }

        if ($company?->primaryContact && (! $project->contact || $company->primaryContact->id !== $project->contact->id)) {
            $contacts->push($company->primaryContact);
        }

        $contactDocuments = [
            'two_valid_ids' => false,
            'tin_proof' => false,
            'passport_proof' => false,
            'visa_or_acr' => false,
        ];
        $contactApproved = false;
        $showForeignRows = false;

        foreach ($contacts as $contact) {
            $contactApproved = $contactApproved || strtolower((string) ($contact?->cif_status ?? '')) === 'approved';
            $contactDocumentsForRecord = $this->projectContactRequirementState((int) $contact->id);

            foreach (array_keys($contactDocuments) as $key) {
                $contactDocuments[$key] = $contactDocuments[$key] || $contactDocumentsForRecord[$key];
            }

            $showForeignRows = $showForeignRows || $this->contactHasForeignDetails((int) $contact->id, $contact);
        }

        if ($this->isJuridicalOrganization($organization)) {
            $showForeignRows = $showForeignRows
                || strtolower((string) ($company?->latestBif?->nationality_status ?? '')) === 'foreign'
                || $this->valueImpliesForeign((string) ($company?->latestBif?->authorized_signatory_nationality ?? ''));
        }

        return [
            'contact_documents' => $contactDocuments,
            'contact_approved' => $contactApproved,
            'show_foreign_rows' => $showForeignRows,
        ];
    }

    private function projectContactRequirementState(int $contactId): array
    {
        $documents = [];
        if ($contactId > 0) {
            $kycPath = 'contact-kyc-data/'.$contactId.'-requirements.json';
            if (Storage::disk('local')->exists($kycPath)) {
                $documents = json_decode((string) Storage::disk('local')->get($kycPath), true) ?: [];
            }

            $cifPath = 'contact-cif-data/'.$contactId.'-documents.json';
            if (Storage::disk('local')->exists($cifPath)) {
                $cifDocuments = json_decode((string) Storage::disk('local')->get($cifPath), true) ?: [];
                if (empty($documents['two_valid_ids']) && isset($cifDocuments['valid_id'])) {
                    $documents['two_valid_ids'] = [$cifDocuments['valid_id']];
                }
                if (empty($documents['tin_proof']) && isset($cifDocuments['tin_document'])) {
                    $documents['tin_proof'] = [$cifDocuments['tin_document']];
                }
            }
        }

        $countDocuments = function (string $key) use ($documents): int {
            return count(array_values(array_filter(
                $this->normalizeProjectDocuments($documents[$key] ?? []),
                fn (array $item): bool => filled($item['file_name'] ?? $item['path'] ?? $item['file_path'] ?? null)
            )));
        };

        return [
            'two_valid_ids' => $countDocuments('two_valid_ids') > 0,
            'tin_proof' => $countDocuments('tin_proof') > 0,
            'passport_proof' => $countDocuments('passport_proof') > 0,
            'visa_or_acr' => $countDocuments('visa_proof') > 0 || $countDocuments('acr_card_proof') > 0,
        ];
    }

    private function normalizeProjectDocuments(mixed $documents): array
    {
        if (! is_array($documents)) {
            return [];
        }

        if (array_is_list($documents)) {
            return array_values(array_filter($documents, 'is_array'));
        }

        return filled($documents['file_name'] ?? $documents['path'] ?? $documents['file_path'] ?? null)
            ? [$documents]
            : [];
    }

    private function contactHasForeignDetails(int $contactId, mixed $contact = null): bool
    {
        $contact = is_object($contact) ? $contact : null;
        $cifData = [];

        if ($contactId > 0) {
            $cifPath = 'contact-cif-data/'.$contactId.'-data.json';
            if (Storage::disk('local')->exists($cifPath)) {
                $cifData = json_decode((string) Storage::disk('local')->get($cifPath), true) ?: [];
            }
        }

        return in_array(strtolower((string) ($cifData['citizenship_type'] ?? '')), ['foreigner', 'dual_citizen'], true)
            || strtolower((string) ($contact?->ownership_flag ?? '')) === 'foreign-owned business'
            || filled($contact?->foreign_business_nature);
    }

    private function valueImpliesForeign(string $value): bool
    {
        $normalized = strtolower(trim($value));

        return $normalized !== '' && ! in_array($normalized, ['filipino', 'philippines', 'philippine', 'ph'], true);
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

    private function buildManualRequirementRows(string $rawRequirements, string $assignedTo, string $purpose): array
    {
        $rows = collect(preg_split('/[\r\n]+/', trim($rawRequirements)))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->map(fn (string $item, int $index): array => [
                'number' => $index + 1,
                'requirement' => $item,
                'notes' => '',
                'purpose' => $purpose,
                'provided_by' => 'Client',
                'submitted_to' => 'Sales & Marketing',
                'assigned_to' => $assignedTo,
                'timeline' => 'To be scheduled',
            ])
            ->all();

        if ($rows !== []) {
            return $rows;
        }

        return [[
            'number' => 1,
            'requirement' => 'Initial intake and supporting documents',
            'notes' => '',
            'purpose' => $purpose,
            'provided_by' => 'Client',
            'submitted_to' => 'Sales & Marketing',
            'assigned_to' => $assignedTo,
            'timeline' => 'To be scheduled',
        ]];
    }

    private function manualStartChecklist(): array
    {
        return [
            ['label' => 'Client Contact Form', 'status' => 'pending'],
            ['label' => 'Deal Form', 'status' => 'pending'],
            ['label' => 'Business Information Form', 'status' => 'pending'],
            ['label' => 'Client Information Form', 'status' => 'pending'],
            ['label' => 'Service Task Activation & Routing Tracker (Start)', 'status' => 'provided'],
            ['label' => 'Others', 'status' => 'pending'],
        ];
    }

    private function manualStartApprovalSteps(?string $assignedConsultant, ?string $assignedAssociate): array
    {
        $leadConsultant = $assignedConsultant ?: 'Lead Consultant';
        $leadAssociate = $assignedAssociate ?: 'Lead Associate';

        return [
            ['requirement' => 'Client Contact Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Deal Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Business Information Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Client Information Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Service Task Activation & Routing Tracker (Start)', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Engagement-Specific Requirement', 'responsible_person' => 'Sales & Marketing/'.$leadConsultant.'/'.$leadAssociate, 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Proposal/Contract', 'responsible_person' => 'Sales & Marketing/'.$leadConsultant.'/'.$leadAssociate, 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Final Quote', 'responsible_person' => $leadConsultant.'/'.$leadAssociate, 'name_and_signature' => '', 'date_time_done' => ''],
        ];
    }

    private function manualRoutingRows(): array
    {
        return [
            ['role' => 'Admin', 'status' => 'pending'],
            ['role' => 'Lead Consultant', 'status' => 'pending'],
            ['role' => 'Lead Associate', 'status' => 'pending'],
            ['role' => 'Sales & Marketing', 'status' => 'pending'],
        ];
    }

    private function manualClearancePayload(?string $assignedConsultant, ?string $assignedAssociate): array
    {
        return [
            'assigned_team_lead' => '',
            'assigned_team_lead_signature' => '',
            'lead_consultant_confirmed' => $assignedConsultant ?? '',
            'lead_consultant_signature' => '',
            'lead_associate_assigned' => $assignedAssociate ?? '',
            'lead_associate_signature' => '',
            'sales_marketing' => 'Sales & Marketing',
            'sales_marketing_signature' => '',
            'record_custodian_name' => 'Record Custodian',
            'record_custodian_signature' => '',
            'date_recorded' => now()->toDateString(),
            'date_signed' => null,
        ];
    }

    private function manualInternalApprovalPayload(?string $assignedProjectManager, ?string $assignedConsultant, ?string $assignedAssociate): array
    {
        return [
            'prepared_by' => $assignedProjectManager ?: $assignedConsultant,
            'reviewed_by' => 'Admin',
            'referred_by_closed_by' => null,
            'sales_marketing' => 'Sales & Marketing',
            'lead_consultant' => $assignedConsultant,
            'lead_associate_assigned' => $assignedAssociate,
            'finance' => 'Finance',
            'president' => 'President',
            'record_custodian' => 'Record Custodian',
            'date_recorded' => now()->toDateString(),
            'date_signed' => null,
        ];
    }

    private function manualScopeRows(array $validated): array
    {
        $items = collect(preg_split('/[\r\n,;]+/', (string) ($validated['services'] ?? '')))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();

        if ($items->isEmpty() && filled($validated['scope_summary'] ?? null)) {
            $items = collect(preg_split('/[\r\n,;]+/', (string) $validated['scope_summary']))
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values();
        }

        return $items
            ->take(5)
            ->map(fn (string $item): array => [
                'main_task_description' => $item,
                'sub_task_description' => 'To be defined during planning',
                'responsible' => $validated['assigned_consultant'] ?? ($validated['assigned_project_manager'] ?? 'Project Team'),
                'duration' => 'TBD',
                'start_date' => $validated['planned_start_date'] ?? null,
                'end_date' => $validated['target_completion_date'] ?? null,
                'status' => 'Pending',
                'remarks' => null,
            ])
            ->values()
            ->all();
    }
}
