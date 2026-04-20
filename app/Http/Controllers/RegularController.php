<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesPdfPreview;
use App\Models\Deal;
use App\Models\Project;
use App\Models\ProjectStart;
use App\Services\ProjectProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegularController extends Controller
{
    use GeneratesPdfPreview;

    public function __construct(private readonly ProjectProvisioner $projectProvisioner)
    {
    }

    public function index(): View
    {
        $this->provisionMissingRegularRecords();

        $regulars = collect();

        if (Schema::hasTable('projects')) {
            $regulars = Project::query()
                ->with(['deal:id,deal_code', 'company:id,company_name'])
                ->latest()
                ->get()
                ->filter(fn (Project $project): bool => $this->isRegularEngagement($project->engagement_type))
                ->values();
        }

        $stats = [
            'all' => $regulars->count(),
            'rsat' => $regulars->where('current_phase', 'RSAT')->count(),
            'planning' => $regulars->where('current_phase', 'Planning')->count(),
            'active' => $regulars->whereIn('status', ['RSAT', 'Planning', 'For NTP Approval', 'Execution', 'Reporting', 'Delivery'])->count(),
            'completed' => $regulars->where('status', 'Completed')->count(),
        ];

        return view('regular.index', compact('regulars', 'stats'));
    }

    public function show(Project $regular): View
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);

        $regular->load([
            'deal:id,deal_code,engagement_type',
            'contact:id,first_name,last_name,email,phone,company_name',
            'company:id,company_name',
            'starts' => fn ($query) => $query->latest(),
        ]);

        $rsat = $regular->starts->first() ?: new ProjectStart(['project_id' => $regular->id]);

        return view('regular.show', compact('regular', 'rsat'));
    }

    public function updateRsat(Request $request, Project $regular): RedirectResponse
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);

        $validated = $request->validate([
            'form_date' => ['nullable', 'date'],
            'date_started' => ['nullable', 'date'],
            'date_completed' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
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
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $rsat = $regular->starts()->latest()->first() ?: new ProjectStart(['project_id' => $regular->id]);
        $resolvedFormDate = $validated['form_date']
            ?? optional($rsat->form_date)->toDateString()
            ?? optional($rsat->created_at)->toDateString()
            ?? now()->toDateString();

        $rsat->fill([
            'form_date' => $resolvedFormDate,
            'date_started' => $validated['date_started'] ?? null,
            'date_completed' => $validated['date_completed'] ?? null,
            'status' => $validated['status'],
            'engagement_requirements' => $this->buildRequirementRows($request, 'engagement'),
            'approval_steps' => $this->buildApprovalRows($request),
            'clearance' => $this->buildClearancePayload($validated),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);
        $rsat->project_id = $regular->id;
        $rsat->save();

        return redirect()->route('regular.show', $regular)->with('success', 'RSAT form updated successfully.');
    }

    public function downloadRsatPdf(Project $regular)
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);

        $regular->load(['deal:id,deal_code', 'contact:id,first_name,last_name', 'starts' => fn ($query) => $query->latest()]);
        $rsat = $regular->starts->first();
        $rsatRequirements = collect($rsat?->engagement_requirements ?? []);
        $rsatApprovalSteps = collect($rsat?->approval_steps ?? []);
        $rsatClearance = (array) ($rsat?->clearance ?? []);

        $targetPath = 'generated-previews/regular/rsat/' . ($regular->project_code ?: $regular->id) . '-rsat-form.pdf';
        $pdfPath = $this->generatePdfPreview('regular.pdf.rsat', compact(
            'regular',
            'rsat',
            'rsatRequirements',
            'rsatApprovalSteps',
            'rsatClearance'
        ), $targetPath);

        abort_unless($pdfPath && Storage::disk('public')->exists($pdfPath), 500, 'Unable to generate RSAT PDF preview.');

        return redirect()->route('uploads.show', ['path' => $pdfPath, 'download' => 1]);
    }

    private function provisionMissingRegularRecords(): void
    {
        if (! Schema::hasTable('deals') || ! Schema::hasTable('projects')) {
            return;
        }

        Deal::query()
            ->whereDoesntHave('project')
            ->get()
            ->filter(fn (Deal $deal): bool => $this->isRegularEngagement($deal->engagement_type))
            ->each(fn (Deal $deal) => $this->projectProvisioner->createOrSyncFromDeal($deal));
    }

    private function isRegularEngagement(?string $engagementType): bool
    {
        return Str::contains(Str::lower(trim((string) $engagementType)), 'regular');
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
}
