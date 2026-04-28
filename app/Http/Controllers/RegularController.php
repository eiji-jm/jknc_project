<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesPdfPreview;
use App\Models\Deal;
use App\Models\Project;
use App\Models\ProjectSowReport;
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
            'engagement_requirements_text' => ['nullable', 'string', 'max:4000'],
        ]);

        $regular = Project::query()->create([
            'name' => $validated['name'],
            'engagement_type' => 'Regular Retainer',
            'status' => 'RSAT',
            'current_phase' => 'RSAT',
            'current_step' => 'RSAT Checklist',
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
            'client_confirmation_name' => $validated['client_confirmation_name'] ?? ($validated['client_name'] ?? null),
            'metadata' => [
                'created_from' => 'regular_dashboard_manual',
            ],
            'opened_at' => now(),
        ]);

        ProjectStart::query()->create([
            'project_id' => $regular->id,
            'form_date' => now()->toDateString(),
            'date_started' => $validated['planned_start_date'] ?? now()->toDateString(),
            'status' => 'pending',
            'engagement_requirements' => $this->buildManualRequirementRows(
                $validated['engagement_requirements_text'] ?? '',
                $validated['assigned_associate'] ?? ($validated['assigned_consultant'] ?? ($validated['assigned_project_manager'] ?? 'Assigned team'))
            ),
            'approval_steps' => $this->buildManualApprovalSteps(
                $validated['assigned_consultant'] ?? null,
                $validated['assigned_associate'] ?? null
            ),
            'clearance' => $this->buildManualClearancePayload(
                $validated['assigned_project_manager'] ?? ($validated['assigned_consultant'] ?? null),
                $validated['assigned_consultant'] ?? null,
                $validated['assigned_associate'] ?? null
            ),
        ]);

        ProjectSowReport::query()->create([
            'project_id' => $regular->id,
            'report_number' => '1.0',
            'date_prepared' => now()->toDateString(),
            'within_scope_items' => [],
            'out_of_scope_items' => [],
            'client_confirmation_name' => $regular->client_confirmation_name,
            'internal_approval' => [
                'report_period' => null,
                'prepared_by' => $validated['assigned_project_manager'] ?? ($validated['assigned_consultant'] ?? null),
                'prepared_by_name' => null,
                'prepared_by_date' => null,
                'reviewed_by' => 'Admin',
                'reviewed_by_name' => null,
                'reviewed_by_date' => null,
                'referred_by_closed_by' => null,
                'sales_marketing' => 'Sales & Marketing',
                'lead_consultant' => $validated['assigned_consultant'] ?? null,
                'lead_associate_assigned' => $validated['assigned_associate'] ?? null,
                'finance' => 'Finance',
                'president' => 'President',
                'record_custodian' => 'Record Custodian',
                'date_recorded' => now()->toDateString(),
                'date_signed' => null,
                'transmittal_no' => null,
                'date_submitted_for_transmittal' => null,
            ],
        ]);

        return redirect()
            ->route('regular.show', ['regular' => $regular, 'tab' => 'rsat'])
            ->with('success', 'Regular engagement created and opened in RSAT form.');
    }

    public function show(Request $request, Project $regular): View
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);

        $regular->load([
            'deal:id,deal_code,engagement_type',
            'contact:id,first_name,last_name,email,phone,company_name',
            'company:id,company_name',
            'company.latestBif',
            'starts' => fn ($query) => $query->latest(),
        ]);

        $rsat = $regular->starts->first() ?: new ProjectStart(['project_id' => $regular->id]);
        $report = $this->resolveDraftReport($regular);
        $generatedReports = $regular->sowReports()
            ->whereJsonContains('internal_approval->__meta->generated', true)
            ->latest('date_prepared')
            ->latest()
            ->get();
        $tab = in_array((string) $request->query('tab', 'rsat'), ['rsat', 'report'], true)
            ? (string) $request->query('tab', 'rsat')
            : 'rsat';

        return view('regular.show', compact('regular', 'rsat', 'report', 'generatedReports', 'tab'));
    }

    public function updateRsat(Request $request, Project $regular): RedirectResponse
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);

        $validated = $this->validateRsatPayload($request);
        $this->persistRsatDocument($request, $regular, $validated);

        return redirect()->route('regular.show', ['regular' => $regular, 'tab' => 'rsat'])->with('success', 'RSAT form updated successfully.');
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

    public function updateReport(Request $request, Project $regular): RedirectResponse
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);

        $validated = $this->validateReportPayload($request);
        $this->persistDraftReport($request, $regular, $validated);

        return redirect()->route('regular.show', ['regular' => $regular, 'tab' => 'report'])->with('success', 'RSAT report updated successfully.');
    }

    public function generateReport(Request $request, Project $regular): RedirectResponse
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);

        $validated = $this->validateRsatPayload($request);
        $rsat = $this->persistRsatDocument($request, $regular, $validated);

        ProjectSowReport::query()->create([
            'project_id' => $regular->id,
            'report_number' => null,
            'date_prepared' => now()->toDateString(),
            'within_scope_items' => $this->buildReportRowsFromRsat($rsat),
            'out_of_scope_items' => [],
            'client_confirmation_name' => $regular->client_name,
            'internal_approval' => array_merge($this->buildReportApprovalFromRsat($regular, $rsat), [
                '__meta' => [
                    'generated' => true,
                    'generated_at' => now()->toDateTimeString(),
                ],
            ]),
        ]);

        return redirect()->route('regular.show', ['regular' => $regular, 'tab' => 'report'])->with('success', 'RSAT report generated and recorded successfully.');
    }

    public function showGeneratedReport(Project $regular, ProjectSowReport $report): View
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);
        abort_unless($report->project_id === $regular->id, 404);
        abort_unless((bool) data_get($report->internal_approval, '__meta.generated', false), 404);

        $regular->loadMissing([
            'deal:id,deal_code,customer_type,engagement_type',
            'contact:id,first_name,last_name,email,phone,company_name',
            'company:id,company_name',
            'company.latestBif',
            'starts' => fn ($query) => $query->latest(),
        ]);

        $rsat = $regular->starts->first() ?: new ProjectStart(['project_id' => $regular->id]);

        return view('regular.report-preview', compact('regular', 'rsat', 'report'));
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

    private function validateRsatPayload(Request $request): array
    {
        return $request->validate([
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
    }

    private function persistRsatDocument(Request $request, Project $regular, array $validated): ProjectStart
    {
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

        return $rsat;
    }

    private function validateReportPayload(Request $request): array
    {
        return $request->validate([
            'report_number' => ['nullable', 'string', 'max:50'],
            'date_prepared' => ['nullable', 'date'],
            'report_period' => ['nullable', 'string', 'max:255'],
            'report_service' => ['nullable', 'array'],
            'report_service.*' => ['nullable', 'string', 'max:255'],
            'report_activity_output' => ['nullable', 'array'],
            'report_activity_output.*' => ['nullable', 'string', 'max:255'],
            'report_frequency' => ['nullable', 'array'],
            'report_frequency.*' => ['nullable', 'string', 'max:255'],
            'report_reminder_lead_time' => ['nullable', 'array'],
            'report_reminder_lead_time.*' => ['nullable', 'string', 'max:255'],
            'report_deadline' => ['nullable', 'array'],
            'report_deadline.*' => ['nullable', 'string', 'max:255'],
            'client_confirmation_name' => ['nullable', 'string', 'max:255'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'prepared_by_name' => ['nullable', 'string', 'max:255'],
            'prepared_by_date' => ['nullable', 'string', 'max:255'],
            'reviewed_by' => ['nullable', 'string', 'max:255'],
            'reviewed_by_name' => ['nullable', 'string', 'max:255'],
            'reviewed_by_date' => ['nullable', 'string', 'max:255'],
            'referred_by_closed_by' => ['nullable', 'string', 'max:255'],
            'sales_marketing' => ['nullable', 'string', 'max:255'],
            'lead_consultant' => ['nullable', 'string', 'max:255'],
            'lead_associate_assigned' => ['nullable', 'string', 'max:255'],
            'finance' => ['nullable', 'string', 'max:255'],
            'president' => ['nullable', 'string', 'max:255'],
            'record_custodian' => ['nullable', 'string', 'max:255'],
            'date_recorded' => ['nullable', 'date'],
            'date_signed' => ['nullable', 'date'],
            'transmittal_no' => ['nullable', 'string', 'max:255'],
            'date_submitted_for_transmittal' => ['nullable', 'date'],
        ]);
    }

    private function resolveDraftReport(Project $regular): ProjectSowReport
    {
        return $regular->sowReports()
            ->where(function ($query) {
                $query->whereNull('internal_approval->__meta->generated')
                    ->orWhere('internal_approval->__meta->generated', false);
            })
            ->latest()
            ->first() ?: new ProjectSowReport(['project_id' => $regular->id]);
    }

    private function persistDraftReport(Request $request, Project $regular, array $validated): ProjectSowReport
    {
        $report = $this->resolveDraftReport($regular);
        $approval = [
            'report_period' => $validated['report_period'] ?? null,
            'prepared_by' => $validated['prepared_by'] ?? null,
            'prepared_by_name' => $validated['prepared_by_name'] ?? null,
            'prepared_by_date' => $validated['prepared_by_date'] ?? null,
            'reviewed_by' => $validated['reviewed_by'] ?? null,
            'reviewed_by_name' => $validated['reviewed_by_name'] ?? null,
            'reviewed_by_date' => $validated['reviewed_by_date'] ?? null,
            'referred_by_closed_by' => $validated['referred_by_closed_by'] ?? null,
            'sales_marketing' => $validated['sales_marketing'] ?? null,
            'lead_consultant' => $validated['lead_consultant'] ?? null,
            'lead_associate_assigned' => $validated['lead_associate_assigned'] ?? null,
            'finance' => $validated['finance'] ?? null,
            'president' => $validated['president'] ?? null,
            'record_custodian' => $validated['record_custodian'] ?? null,
            'date_recorded' => $validated['date_recorded'] ?? null,
            'date_signed' => $validated['date_signed'] ?? null,
            'transmittal_no' => $validated['transmittal_no'] ?? null,
            'date_submitted_for_transmittal' => $validated['date_submitted_for_transmittal'] ?? null,
            '__meta' => [
                'generated' => false,
            ],
        ];

        $report->fill([
            'report_number' => $validated['report_number'] ?? null,
            'date_prepared' => $validated['date_prepared'] ?? null,
            'within_scope_items' => $this->buildReportRows($request),
            'out_of_scope_items' => [],
            'client_confirmation_name' => $validated['client_confirmation_name'] ?? null,
            'internal_approval' => $approval,
        ]);
        $report->project_id = $regular->id;
        $report->save();

        return $report;
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

    private function buildReportRows(Request $request): array
    {
        $services = (array) $request->input('report_service', []);
        $activities = (array) $request->input('report_activity_output', []);
        $frequencies = (array) $request->input('report_frequency', []);
        $reminders = (array) $request->input('report_reminder_lead_time', []);
        $deadlines = (array) $request->input('report_deadline', []);

        return collect($activities)->map(function ($value, $index) use ($services, $frequencies, $reminders, $deadlines) {
            $service = trim((string) ($services[$index] ?? ''));
            $activity = trim((string) $value);
            $frequency = trim((string) ($frequencies[$index] ?? ''));
            $reminder = trim((string) ($reminders[$index] ?? ''));
            $deadline = trim((string) ($deadlines[$index] ?? ''));

            if ($service === '' && $activity === '' && $frequency === '' && $reminder === '' && $deadline === '') {
                return null;
            }

            return [
                'service' => $service,
                'activity_output' => $activity,
                'frequency' => $frequency,
                'reminder_lead_time' => $reminder,
                'deadline' => $deadline,
            ];
        })->filter()->values()->all();
    }

    private function buildReportRowsFromRsat(ProjectStart $rsat): array
    {
        return collect($rsat->engagement_requirements ?? [])
            ->map(fn (array $row): array => [
                'service' => trim((string) ($row['purpose'] ?? '')),
                'activity_output' => trim((string) ($row['requirement'] ?? '')),
                'frequency' => trim((string) ($row['notes'] ?? '')),
                'reminder_lead_time' => trim((string) ($row['timeline'] ?? '')),
                'deadline' => trim((string) ($row['submitted_to'] ?? '')),
            ])
            ->filter(fn (array $row): bool => collect($row)->contains(fn ($value) => $value !== ''))
            ->values()
            ->all();
    }

    private function buildReportApprovalFromRsat(Project $regular, ProjectStart $rsat): array
    {
        $clearance = (array) ($rsat->clearance ?? []);

        return [
            'report_period' => null,
            'prepared_by' => $clearance['assigned_team_lead'] ?? null,
            'prepared_by_name' => null,
            'prepared_by_date' => null,
            'reviewed_by' => $clearance['lead_consultant_confirmed'] ?? null,
            'reviewed_by_name' => null,
            'reviewed_by_date' => null,
            'referred_by_closed_by' => $rsat->rejection_reason,
            'sales_marketing' => $clearance['sales_marketing'] ?? null,
            'lead_consultant' => $regular->assigned_consultant,
            'lead_associate_assigned' => $clearance['lead_associate_assigned'] ?? null,
            'finance' => null,
            'president' => null,
            'record_custodian' => $clearance['record_custodian_name'] ?? null,
            'date_recorded' => $clearance['date_recorded'] ?? null,
            'date_signed' => $clearance['date_signed'] ?? null,
            'transmittal_no' => null,
            'date_submitted_for_transmittal' => null,
        ];
    }

    private function buildManualRequirementRows(string $rawRequirements, string $assignedTo): array
    {
        $rows = collect(preg_split('/[\r\n]+/', trim($rawRequirements)))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->map(fn (string $item, int $index): array => [
                'number' => $index + 1,
                'requirement' => $item,
                'notes' => '',
                'purpose' => 'Regular service activity',
                'provided_by' => 'Client',
                'submitted_to' => 'Sales & Marketing',
                'assigned_to' => $assignedTo,
                'timeline' => 'Recurring schedule',
            ])
            ->all();

        if ($rows !== []) {
            return $rows;
        }

        return [[
            'number' => 1,
            'requirement' => 'Initial RSAT intake and service routing',
            'notes' => '',
            'purpose' => 'Regular service activity',
            'provided_by' => 'Client',
            'submitted_to' => 'Sales & Marketing',
            'assigned_to' => $assignedTo,
            'timeline' => 'Recurring schedule',
        ]];
    }

    private function buildManualApprovalSteps(?string $assignedConsultant, ?string $assignedAssociate): array
    {
        $leadConsultant = $assignedConsultant ?: 'Lead Consultant';
        $leadAssociate = $assignedAssociate ?: 'Lead Associate';

        return [
            ['requirement' => 'Client Contact Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Business Information Form', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Regular Service Activity Tracker (RSAT)', 'responsible_person' => 'Sales & Marketing', 'name_and_signature' => '', 'date_time_done' => ''],
            ['requirement' => 'Engagement-Specific Requirement', 'responsible_person' => 'Sales & Marketing/'.$leadConsultant.'/'.$leadAssociate, 'name_and_signature' => '', 'date_time_done' => ''],
        ];
    }

    private function buildManualClearancePayload(?string $preparedBy, ?string $assignedConsultant, ?string $assignedAssociate): array
    {
        return [
            'assigned_team_lead' => $preparedBy ?? '',
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
}
