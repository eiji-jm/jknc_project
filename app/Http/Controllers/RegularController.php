<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GeneratesPdfPreview;
use App\Models\Deal;
use App\Models\Project;
use App\Models\ProjectNtp;
use App\Models\ProjectSowReport;
use App\Models\ProjectStart;
use App\Services\ProjectProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegularController extends Controller
{
    use GeneratesPdfPreview;

    private const CLIENT_LINK_TTL_DAYS = 14;

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

    public function downloadNtpPdf(Project $regular): RedirectResponse
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);
        $ntpRecord = $this->generateAndSendRegularNtp($regular);

        return redirect()
            ->route('regular.show', ['regular' => $regular->id, 'tab' => 'rsat'])
            ->with('success', 'NTP generated successfully.'.($ntpRecord->client_form_sent_to_email ? ' NTP link sent to '.$ntpRecord->client_form_sent_to_email.'.' : ''))
            ->with('ntp_status', 'Waiting for client signed NTP upload.');
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

        $report = ProjectSowReport::query()->create([
            'project_id' => $regular->id,
            'report_number' => ProjectSowReport::generateNextRsatCode(),
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

        $recipientEmail = $this->resolveRegularClientEmail($regular);
        $emailSentMessage = null;

        if ($recipientEmail !== null && $this->supportsRegularReportClientPortal()) {
            $this->sendRsatReportClientLink($regular, $report, $recipientEmail);
            $emailSentMessage = " Client approval link sent to {$recipientEmail}.";
        }

        return redirect()
            ->route('regular.report.preview', ['regular' => $regular->id, 'report' => $report->id])
            ->with('success', 'RSAT report generated successfully.'.$emailSentMessage);
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
        $clientEmail = $this->resolveRegularClientEmail($regular);
        $contactName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' '))
            ?: ($regular->client_name ?: '-');

        return view('regular.report-preview', compact('regular', 'rsat', 'report', 'clientEmail', 'contactName'));
    }

    public function sendGeneratedReport(Project $regular, ProjectSowReport $report, Request $request): RedirectResponse
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);
        abort_unless($report->project_id === $regular->id, 404);

        if (! $this->supportsRegularReportClientPortal()) {
            return redirect()
                ->route('regular.report.preview', ['regular' => $regular->id, 'report' => $report->id])
                ->with('success', 'The report was generated, but client sending is not available until the latest project report migration is run.');
        }

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:255'],
        ]);

        $recipientEmail = trim((string) $validated['recipient_email']);
        $this->sendRsatReportClientLink($regular, $report, $recipientEmail);

        return redirect()
            ->route('regular.report.preview', ['regular' => $regular->id, 'report' => $report->id])
            ->with('success', "RSAT report client approval link sent to {$recipientEmail}.");
    }

    public function bulkDestroyGeneratedReports(Request $request, Project $regular): RedirectResponse
    {
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);
        $validated = $request->validate([
            'selected_reports' => ['required', 'array', 'min:1'],
            'selected_reports.*' => ['required', 'integer'],
        ]);

        $reports = $regular->sowReports()
            ->whereIn('id', $validated['selected_reports'])
            ->get();

        foreach ($reports as $report) {
            if ($report->client_attachment_path && Storage::disk('public')->exists($report->client_attachment_path)) {
                Storage::disk('public')->delete($report->client_attachment_path);
            }
        }

        $deletedCount = $regular->sowReports()
            ->whereIn('id', $validated['selected_reports'])
            ->delete();

        return redirect()
            ->route('regular.show', ['regular' => $regular->id, 'tab' => 'report'])
            ->with('success', $deletedCount === 1 ? '1 RSAT report deleted successfully.' : "{$deletedCount} RSAT reports deleted successfully.");
    }

    public function clientRsatReport(string $token): View
    {
        abort_unless($this->supportsRegularReportClientPortal(), 404);

        $report = $this->findRegularReportByClientToken($token);
        abort_if($report->client_access_expires_at && $report->client_access_expires_at->isPast(), 403, 'This RSAT report link has expired.');

        $regular = $report->project()->with(['deal:id,deal_code', 'contact:id,first_name,last_name,email', 'company:id,company_name'])->firstOrFail();
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);
        $contactName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' '))
            ?: ($regular->client_name ?: 'Client');

        return view('regular.client-report-form', [
            'regular' => $regular,
            'report' => $report,
            'contactName' => $contactName,
            'clientFormAction' => route('regular.report.client.submit', ['token' => $token]),
        ]);
    }

    public function submitClientRsatReport(Request $request, string $token): RedirectResponse
    {
        abort_unless($this->supportsRegularReportClientPortal(), 404);

        $report = $this->findRegularReportByClientToken($token);
        abort_if($report->client_access_expires_at && $report->client_access_expires_at->isPast(), 403, 'This RSAT report link has expired.');

        $validated = $request->validate([
            'client_approval_name' => ['required', 'string', 'max:255'],
            'client_approval' => ['accepted'],
            'client_response_notes' => ['nullable', 'string', 'max:4000'],
            'client_attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ], [
            'client_approval.accepted' => 'Please confirm your approval before submitting.',
        ]);

        if ($request->hasFile('client_attachment')) {
            if ($report->client_attachment_path && Storage::disk('public')->exists($report->client_attachment_path)) {
                Storage::disk('public')->delete($report->client_attachment_path);
            }

            $report->client_attachment_path = $request->file('client_attachment')->store("regular/{$report->project_id}/reports", 'public');
        }

        $report->fill([
            'client_response_status' => 'approved',
            'client_approved_at' => now(),
            'client_approved_name' => $validated['client_approval_name'],
            'client_response_notes' => $validated['client_response_notes'] ?? null,
            'client_confirmation_name' => $validated['client_approval_name'],
        ]);
        $report->save();

        return redirect()
            ->route('regular.report.client.show', ['token' => $token])
            ->with('success', 'Your approval has been recorded successfully.');
    }

    public function downloadClientRsatReport(string $token): RedirectResponse
    {
        abort_unless($this->supportsRegularReportClientPortal(), 404);

        $report = $this->findRegularReportByClientToken($token);
        abort_if($report->client_access_expires_at && $report->client_access_expires_at->isPast(), 403, 'This RSAT report link has expired.');

        $regular = $report->project()->with([
            'deal:id,deal_code,customer_type,engagement_type',
            'contact:id,first_name,last_name,email,phone,company_name',
            'company:id,company_name',
            'company.latestBif',
            'starts' => fn ($query) => $query->latest(),
        ])->firstOrFail();
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);

        $rsat = $regular->starts->first() ?: new ProjectStart(['project_id' => $regular->id]);
        $contactName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' '))
            ?: ($regular->client_name ?: '-');

        $targetPath = 'generated-previews/regular/rsat-report/' . ($regular->project_code ?: $regular->id) . '-' . ($report->report_number ?: $report->id) . '.pdf';
        $pdfPath = $this->generatePdfPreview('regular.pdf.rsat-report', compact('regular', 'rsat', 'report', 'contactName'), $targetPath);

        abort_unless($pdfPath && Storage::disk('public')->exists($pdfPath), 500, 'Unable to generate RSAT report PDF preview.');

        return redirect()->route('uploads.show', ['path' => $pdfPath, 'download' => 1]);
    }

    public function clientNtp(string $token): View
    {
        $ntpRecord = $this->findRegularNtpByClientToken($token);
        abort_if($ntpRecord->client_access_expires_at && $ntpRecord->client_access_expires_at->isPast(), 403, 'This NTP link has expired.');

        $regular = $ntpRecord->project()->with(['deal:id,deal_code', 'contact:id,first_name,last_name,email', 'company:id,company_name'])->firstOrFail();
        abort_unless($this->isRegularEngagement($regular->engagement_type), 404);
        $contactName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' '))
            ?: ($regular->client_name ?: 'Client');

        return view('documents.client-ntp-form', [
            'project' => $regular,
            'ntpRecord' => $ntpRecord,
            'ntp' => $ntpRecord->payload ?? [],
            'contactName' => $contactName,
            'clientFormAction' => route('regular.ntp.client.submit', ['token' => $token]),
            'clientDownloadUrl' => route('regular.ntp.client.download', ['token' => $token]),
        ]);
    }

    public function submitClientNtp(Request $request, string $token): RedirectResponse
    {
        $ntpRecord = $this->findRegularNtpByClientToken($token);
        abort_if($ntpRecord->client_access_expires_at && $ntpRecord->client_access_expires_at->isPast(), 403, 'This NTP link has expired.');

        $validated = $request->validate([
            'client_approval_name' => ['required', 'string', 'max:255'],
            'client_approval' => ['accepted'],
            'client_response_notes' => ['nullable', 'string', 'max:4000'],
            'client_attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ], [
            'client_approval.accepted' => 'Please confirm approval before submitting.',
            'client_attachment.required' => 'Please upload the signed NTP file before submitting.',
        ]);

        if ($ntpRecord->client_attachment_path && Storage::disk('public')->exists($ntpRecord->client_attachment_path)) {
            Storage::disk('public')->delete($ntpRecord->client_attachment_path);
        }

        $ntpRecord->fill([
            'client_response_status' => 'approved_to_proceed',
            'client_approved_at' => now(),
            'client_approved_name' => $validated['client_approval_name'],
            'client_response_notes' => $validated['client_response_notes'] ?? null,
            'client_attachment_path' => $request->file('client_attachment')->store("regular/{$ntpRecord->project_id}/ntp", 'public'),
        ]);
        $ntpRecord->save();

        return redirect()
            ->route('regular.ntp.client.show', ['token' => $token])
            ->with('success', 'Approved to proceed. The signed NTP was submitted successfully.');
    }

    public function downloadClientNtp(string $token): RedirectResponse
    {
        $ntpRecord = $this->findRegularNtpByClientToken($token);
        abort_if($ntpRecord->client_access_expires_at && $ntpRecord->client_access_expires_at->isPast(), 403, 'This NTP link has expired.');

        return $this->downloadRegularNtpPdfFromRecord($ntpRecord);
    }

    private function provisionMissingRegularRecords(): void
    {
        if (! Schema::hasTable('deals') || ! Schema::hasTable('projects')) {
            return;
        }

        Deal::query()
            ->whereDoesntHave('projects', fn ($query) => $query->whereRaw('LOWER(COALESCE(engagement_type, "")) LIKE ?', ['%regular%']))
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
            'engagement_status' => ['nullable', 'array'],
            'engagement_status.*' => ['nullable', 'in:open,in_progress,delayed,completed,on_hold'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,xls,xlsx,ppt,pptx,txt', 'max:10240'],
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
            'attachments' => $this->storeRsatAttachments($request, $regular, $rsat),
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
            'report_number' => $validated['report_number']
                ?: ($report->report_number ?: ProjectSowReport::generateNextRsatCode()),
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
        $statuses = (array) $request->input("{$prefix}_status", []);

        return collect($requirements)->map(function ($value, $index) use ($notes, $purposes, $providedBy, $submittedTo, $assigned, $timelines, $statuses) {
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
                'status' => in_array(($statuses[$index] ?? 'open'), ['open', 'in_progress', 'delayed', 'completed', 'on_hold'], true)
                    ? (string) ($statuses[$index] ?? 'open')
                    : 'open',
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
        $statuses = (array) $request->input('report_status', []);

        return collect($activities)->map(function ($value, $index) use ($services, $frequencies, $reminders, $deadlines, $statuses) {
            $service = trim((string) ($services[$index] ?? ''));
            $activity = trim((string) $value);
            $frequency = trim((string) ($frequencies[$index] ?? ''));
            $reminder = trim((string) ($reminders[$index] ?? ''));
            $deadline = trim((string) ($deadlines[$index] ?? ''));
            $status = trim((string) ($statuses[$index] ?? 'open'));

            if ($service === '' && $activity === '' && $frequency === '' && $reminder === '' && $deadline === '' && $status === '') {
                return null;
            }

            return [
                'service' => $service,
                'activity_output' => $activity,
                'frequency' => $frequency,
                'reminder_lead_time' => $reminder,
                'deadline' => $deadline,
                'status' => in_array($status, ['open', 'in_progress', 'delayed', 'completed', 'on_hold'], true) ? $status : 'open',
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
                'status' => trim((string) ($row['status'] ?? 'open')),
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

    private function storeRsatAttachments(Request $request, Project $regular, ProjectStart $rsat): array
    {
        $existing = collect($rsat->attachments ?? [])
            ->filter(fn ($item): bool => is_array($item) && filled($item['path'] ?? null))
            ->values();

        if (! $request->hasFile('attachments')) {
            return $existing->all();
        }

        $newFiles = collect($request->file('attachments', []))
            ->filter()
            ->map(function ($file) use ($regular): array {
                $path = $file->store("regular/{$regular->id}/rsat-attachments", 'public');

                return [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
            });

        return $existing->concat($newFiles)->values()->all();
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
                'status' => 'open',
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
            'status' => 'open',
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

    private function resolveRegularClientEmail(Project $regular): ?string
    {
        $regular->loadMissing([
            'contact:id,email,first_name,last_name',
            'company.primaryContact:id,email,first_name,last_name',
        ]);

        $email = trim((string) ($regular->contact?->email
            ?: $regular->company?->primaryContact?->email
            ?: ''));

        return $email !== '' ? $email : null;
    }

    private function supportsRegularReportClientPortal(): bool
    {
        return Schema::hasColumns('project_sow_reports', [
            'client_access_token',
            'client_access_expires_at',
            'client_form_sent_to_email',
            'client_form_sent_at',
            'client_response_status',
            'client_approved_at',
            'client_approved_name',
            'client_response_notes',
            'client_attachment_path',
        ]);
    }

    private function sendRsatReportClientLink(Project $regular, ProjectSowReport $report, string $recipientEmail): void
    {
        $regular->loadMissing([
            'deal:id,deal_code',
            'contact:id,first_name,last_name,email',
            'company:id,company_name',
        ]);

        $token = Str::random(64);
        $expiresAt = now()->addDays(self::CLIENT_LINK_TTL_DAYS);
        $clientUrl = route('regular.report.client.show', ['token' => $token]);
        $clientName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' '))
            ?: ($regular->client_name ?: 'Client');

        $report->fill([
            'client_access_token' => $token,
            'client_access_expires_at' => $expiresAt,
            'client_form_sent_to_email' => $recipientEmail,
            'client_form_sent_at' => now(),
        ]);
        $report->save();

        $emailHtml = view('emails.regular.rsat-report-client-link', [
            'regular' => $regular,
            'report' => $report,
            'clientName' => $clientName,
            'clientUrl' => $clientUrl,
            'expiresAt' => $expiresAt,
        ])->render();

        Mail::html($emailHtml, function ($message) use ($recipientEmail, $regular, $report) {
            $message
                ->from(config('mail.from.address'), 'John Kelly & Company')
                ->to($recipientEmail)
                ->subject("RSAT Report Approval for {$regular->name} ({$report->report_number})");
        });
    }

    private function findRegularReportByClientToken(string $token): ProjectSowReport
    {
        return ProjectSowReport::query()
            ->where('client_access_token', $token)
            ->firstOrFail();
    }

    private function buildRegularNtpPayload(Project $regular): array
    {
        $regular->loadMissing([
            'deal:id,deal_code',
            'contact:id,first_name,last_name',
            'sowReports' => fn ($query) => $query->latest(),
        ]);

        $report = $regular->sowReports()
            ->whereJsonContains('internal_approval->__meta->generated', true)
            ->latest('date_prepared')
            ->latest()
            ->first();
        $contactName = trim(collect([$regular->contact?->first_name, $regular->contact?->last_name])->filter()->implode(' '))
            ?: ($regular->client_name ?: '-');

        return [
            'title' => 'NOTICE TO PROCEED',
            'form_code' => 'ENG-F-001-v1.0-03.16.26',
            'date_issued' => now()->format('F d, Y'),
            'ntp_no' => null,
            'engagement_type' => $regular->engagement_type ?: 'Regular Retainer',
            'condeal_reference_no' => $regular->deal?->deal_code ?: '-',
            'client_name' => $contactName,
            'business_name' => $regular->business_name ?: '-',
            'engagement_reference_label' => 'RSAT Ref No.:',
            'engagement_reference_no' => $report?->report_number ?: '-',
            'approved_start_date' => optional($regular->planned_start_date)->format('F d, Y') ?: '-',
            'target_completion_date' => optional($regular->target_completion_date)->format('F d, Y') ?: '-',
            'client_representative' => $regular->client_name ?: $contactName,
            'lead_consultant' => $regular->assigned_consultant ?: ($regular->assigned_project_manager ?: ''),
            'associate' => $regular->assigned_associate ?: '',
        ];
    }

    private function generateAndSendRegularNtp(Project $regular): ProjectNtp
    {
        $payload = $this->buildRegularNtpPayload($regular);
        $report = $regular->sowReports()
            ->whereJsonContains('internal_approval->__meta->generated', true)
            ->latest('date_prepared')
            ->latest()
            ->first();

        $ntpRecord = $regular->ntps()->latest()->first() ?: new ProjectNtp(['project_id' => $regular->id]);
        $ntpRecord->fill([
            'reference_type' => 'rsat',
            'reference_number' => $report?->report_number,
            'date_issued' => now()->toDateString(),
            'payload' => $payload,
        ]);
        $ntpRecord->project_id = $regular->id;
        $ntpRecord->save();

        $payload = $ntpRecord->payload ?? [];
        $payload['ntp_no'] = $ntpRecord->ntp_number;
        $ntpRecord->payload = $payload;
        $ntpRecord->save();

        $recipientEmail = $this->resolveRegularClientEmail($regular);
        if ($recipientEmail !== null) {
            $this->sendRegularNtpClientLink($regular, $ntpRecord, $recipientEmail);
        }

        return $ntpRecord->fresh();
    }

    private function sendRegularNtpClientLink(Project $regular, ProjectNtp $ntpRecord, string $recipientEmail): void
    {
        $token = Str::random(64);
        $expiresAt = now()->addDays(self::CLIENT_LINK_TTL_DAYS);
        $clientUrl = route('regular.ntp.client.show', ['token' => $token]);
        $clientName = data_get($ntpRecord->payload, 'client_name', $regular->client_name ?: 'Client');

        $ntpRecord->fill([
            'client_access_token' => $token,
            'client_access_expires_at' => $expiresAt,
            'client_form_sent_to_email' => $recipientEmail,
            'client_form_sent_at' => now(),
        ]);
        $ntpRecord->save();

        $emailHtml = view('emails.documents.ntp-client-link', [
            'project' => $regular,
            'ntpRecord' => $ntpRecord,
            'clientName' => $clientName,
            'clientUrl' => $clientUrl,
            'expiresAt' => $expiresAt,
        ])->render();

        Mail::html($emailHtml, function ($message) use ($recipientEmail, $regular, $ntpRecord) {
            $message
                ->from(config('mail.from.address'), 'John Kelly & Company')
                ->to($recipientEmail)
                ->subject("Notice To Proceed for {$regular->name} ({$ntpRecord->ntp_number})");
        });
    }

    private function findRegularNtpByClientToken(string $token): ProjectNtp
    {
        return ProjectNtp::query()
            ->where('client_access_token', $token)
            ->firstOrFail();
    }

    private function downloadRegularNtpPdfFromRecord(ProjectNtp $ntpRecord): RedirectResponse
    {
        $ntp = $ntpRecord->payload ?? [];
        $ntp['ntp_no'] = $ntpRecord->ntp_number;
        $targetPath = 'generated-previews/regular/ntp/' . ($ntpRecord->project?->project_code ?: $ntpRecord->project_id) . '-' . $ntpRecord->ntp_number . '.pdf';
        $pdfPath = $this->generatePdfPreview('documents.pdf.ntp', compact('ntp'), $targetPath);

        abort_unless($pdfPath && Storage::disk('public')->exists($pdfPath), 500, 'Unable to generate NTP PDF preview.');

        return redirect()->route('uploads.show', ['path' => $pdfPath, 'download' => 1]);
    }
}
