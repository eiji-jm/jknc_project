<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\SecCoi;
use App\Models\SecAoi;
use App\Models\Bylaw;
use App\Models\BirTax;
use App\Models\GisRecord;
use App\Models\Minute;
use App\Models\NatGov;
use App\Models\Notice;
use App\Models\Permit;
use App\Models\Resolution;
use App\Models\SecretaryCertificate;
use App\Models\Accounting;
use App\Models\Banking;
use App\Models\Operation;
use App\Models\Correspondence;
use App\Models\Legal;
use App\Models\StockTransferCertificate;
use App\Models\StockTransferInstallment;
use App\Models\StockTransferJournal;
use App\Models\StockTransferLedger;
use App\Mail\CorporateStatusNotificationMail;

class CorporateApprovalController extends Controller
{
    private function authorizeApprover()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->hasPermission('approve_corporate')) {
            abort(403, 'Unauthorized');
        }
    }

    private function normalizeWorkflow($record): string
    {
        if (!empty($record->workflow_status)) {
            return $record->workflow_status;
        }

        return match ($record->approval_status) {
            'Approved' => 'Accepted',
            'Needs Revision', 'Rejected' => 'Reverted',
            default => 'Uploaded',
        };
    }

    private function getModuleName(string $module): string
    {
        return match ($module) {
            'sec-coi' => 'SEC-COI',
            'sec-aoi' => 'SEC-AOI',
            'bylaws' => 'Bylaws',
            'gis' => 'GIS',
            'lgu' => 'LGU',
            'accounting' => 'Accounting',
            'banking' => 'Banking',
            'operations' => 'Operations',
            'correspondence' => 'Correspondence',
            'legal' => 'Legal',
            default => 'Corporate Module',
        };
    }

    private function getCorporationName($record, string $module): string
    {
        return match ($module) {
            'sec-coi' => $record->corporate_name ?? '',
            'sec-aoi' => $record->corporation_name ?? '',
            'bylaws' => $record->corporation_name ?? '',
            'gis' => $record->corporation_name ?? '',
            'lgu' => ($record->permit_type ?? 'LGU Permit') . ' - ' . ($record->document_type ?? ''),
            'accounting' => ($record->client ?? 'Accounting Record') . ' - ' . ($record->statement_type ?? ''),
            'banking' => ($record->client ?? 'Banking Record') . ' - ' . ($record->bank ?? ''),
            'operations' => ($record->client ?? 'Operations Record') . ' - ' . ($record->operation_type ?? ''),
            'correspondence' => ($record->type ?? 'Correspondence') . ' - ' . ($record->subject ?? ''),
            'legal' => ($record->client ?? 'Legal Record') . ' - ' . ($record->legal_type ?? ''),
            default => '',
        };
    }

    private function getReferenceNumber($record, string $module): string
    {
        return match ($module) {
            'lgu' => $record->permit_number ?? '',
            'accounting' => $record->tin ?? '',
            'banking' => $record->tin ?? '',
            'operations' => $record->tin ?? '',
            'correspondence' => $record->tin ?? '',
            'legal' => $record->tin ?? '',
            default => $record->company_reg_no ?? '',
        };
    }

    private function dashboardStatusBadge(string $status): string
    {
        $normalized = strtolower(trim($status));

        return match ($status) {
            'Accepted', 'Approved', 'Released', 'Issued', 'Completed', 'Paid' => 'Accepted',
            'Reverted', 'Rejected', 'Cancelled', 'Voided' => 'Reverted',
            'Archived' => 'Archived',
            'Submitted', 'Pending', 'Draft' => 'Submitted',
            default => in_array($normalized, ['active', 'open', 'released', 'issued', 'approved', 'completed', 'paid'], true)
                ? 'Accepted'
                : (in_array($normalized, ['cancelled', 'canceled', 'voided', 'rejected', 'reverted'], true)
                    ? 'Reverted'
                    : (in_array($normalized, ['submitted', 'pending', 'draft'], true)
                        ? 'Submitted'
                        : ($status ?: 'Submitted'))),
        };
    }

    private function addDocumentWorkflowItem(\Illuminate\Support\Collection $items, array $data): void
    {
        $data['status'] = $this->dashboardStatusBadge($data['status'] ?? 'Submitted');
        $data['supports_actions'] = $data['supports_actions'] ?? false;
        $data['show_route'] = $data['show_route'] ?? '#';
        $items->push((object) $data);
    }

    private function sendStatusEmail($record, string $module, string $decision, ?string $reviewNote = null): void
    {
        if (empty($record->submitted_by)) {
            return;
        }

        $employee = User::find($record->submitted_by);

        if (!$employee || empty($employee->email)) {
            return;
        }

        Mail::to($employee->email)->send(
            new CorporateStatusNotificationMail(
                $employee->name,
                $this->getModuleName($module),
                $this->getCorporationName($record, $module),
                $this->getReferenceNumber($record, $module),
                $decision,
                $reviewNote
            )
        );
    }

    private function canAppearInAdminDashboard(string $workflow): bool
    {
        return in_array($workflow, ['Submitted', 'Accepted', 'Reverted', 'Archived'], true);
    }

    private function ensureSubmittedForDecision($record)
    {
        $workflow = $this->normalizeWorkflow($record);

        if ($workflow !== 'Submitted') {
            return back()->with('error', 'Only submitted records can be approved, rejected, or revised.');
        }

        return null;
    }

    public function dashboard()
    {
        $this->authorizeApprover();

        $items = collect();

        foreach (SecCoi::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'SEC-COI',
                'title' => $row->corporate_name,
                'company_reg_no' => $row->company_reg_no,
                'uploaded_by' => $row->submitted_by,
                'date_uploaded' => $row->date_upload,
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('corporate.formation.show', $row->id),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'sec-coi', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'sec-coi', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'sec-coi', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'sec-coi', 'id' => $row->id]),
            ]);
        }

        foreach (SecAoi::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'SEC-AOI',
                'title' => $row->corporation_name,
                'company_reg_no' => $row->company_reg_no,
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_upload,
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('corporate.sec_aoi.show', $row->id),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'sec-aoi', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'sec-aoi', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'sec-aoi', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'sec-aoi', 'id' => $row->id]),
            ]);
        }

        foreach (Bylaw::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'Bylaws',
                'title' => $row->corporation_name,
                'company_reg_no' => $row->company_reg_no,
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_upload,
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('corporate.bylaws.show', $row->id),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'bylaws', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'bylaws', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'bylaws', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'bylaws', 'id' => $row->id]),
            ]);
        }

        foreach (GisRecord::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'GIS',
                'title' => $row->corporation_name,
                'company_reg_no' => $row->company_reg_no,
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->created_at ? $row->created_at->format('Y-m-d') : '',
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('gis.show', $row->id),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'gis', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'gis', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'gis', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'gis', 'id' => $row->id]),
            ]);
        }

        foreach (Permit::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'LGU',
                'title' => ($row->permit_type ?? 'LGU Permit') . ' - ' . ($row->document_type ?? ''),
                'company_reg_no' => $row->permit_number,
                'uploaded_by' => $row->user,
                'date_uploaded' => $row->created_at ? $row->created_at->format('Y-m-d') : '',
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('corporate.lgu'),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'lgu', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'lgu', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'lgu', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'lgu', 'id' => $row->id]),
            ]);
        }

        foreach (Accounting::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'Accounting',
                'title' => ($row->client ?? 'Accounting Record') . ' - ' . ($row->statement_type ?? ''),
                'company_reg_no' => $row->tin ?? '',
                'uploaded_by' => $row->user,
                'date_uploaded' => $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : '',
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('accounting', ['record' => $row->id, 'tab' => strtolower($workflow)]),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'accounting', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'accounting', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'accounting', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'accounting', 'id' => $row->id]),
            ]);
        }

        foreach (Banking::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'Banking',
                'title' => ($row->client ?? 'Banking Record') . ' - ' . ($row->bank ?? ''),
                'company_reg_no' => $row->tin ?? '',
                'uploaded_by' => $row->user,
                'date_uploaded' => $row->date_uploaded ? \Carbon\Carbon::parse($row->date_uploaded)->format('Y-m-d') : '',
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('banking', ['record' => $row->id, 'tab' => strtolower($workflow)]),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'banking', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'banking', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'banking', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'banking', 'id' => $row->id]),
            ]);
        }

        foreach (Operation::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'Operations',
                'title' => ($row->client ?? 'Operations Record') . ' - ' . ($row->operation_type ?? ''),
                'company_reg_no' => $row->tin ?? '',
                'uploaded_by' => $row->user,
                'date_uploaded' => $row->date_uploaded ? \Carbon\Carbon::parse($row->date_uploaded)->format('Y-m-d') : '',
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('operations', ['record' => $row->id, 'tab' => strtolower($workflow)]),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'operations', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'operations', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'operations', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'operations', 'id' => $row->id]),
            ]);
        }

        foreach (Correspondence::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'Correspondence',
                'title' => ($row->type ?? 'Correspondence') . ' - ' . ($row->subject ?? ''),
                'company_reg_no' => $row->tin ?? '',
                'uploaded_by' => $row->user,
                'date_uploaded' => $row->uploaded_date ? \Carbon\Carbon::parse($row->uploaded_date)->format('Y-m-d') : '',
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('correspondence', ['record' => $row->id, 'tab' => strtolower($workflow)]),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'correspondence', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'correspondence', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'correspondence', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'correspondence', 'id' => $row->id]),
            ]);
        }

        foreach (Legal::latest()->get() as $row) {
            $workflow = $this->normalizeWorkflow($row);
            if (!$this->canAppearInAdminDashboard($workflow)) continue;

            $items->push((object) [
                'id' => $row->id,
                'module' => 'Legal',
                'title' => ($row->client ?? 'Legal Record') . ' - ' . ($row->legal_type ?? ''),
                'company_reg_no' => $row->tin ?? '',
                'uploaded_by' => $row->user,
                'date_uploaded' => $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : '',
                'status' => $workflow,
                'approval_status' => $row->approval_status,
                'show_route' => route('legal', ['record' => $row->id, 'tab' => strtolower($workflow)]),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'legal', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'legal', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'legal', 'id' => $row->id]),
                'archive_route' => route('corporate.approvals.archive', ['module' => 'legal', 'id' => $row->id]),
            ]);
        }

        foreach (Notice::latest()->get() as $row) {
            if (empty($row->document_path)) {
                continue;
            }

            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'Notices',
                'title' => $row->notice_number ?: ('Notice #' . $row->id),
                'company_reg_no' => $row->notice_number ?? '',
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_updated ? \Carbon\Carbon::parse($row->date_updated)->format('Y-m-d') : ($row->date_of_notice ? $row->date_of_notice->format('Y-m-d') : ''),
                'status' => 'Submitted',
                'approval_status' => 'Pending',
                'show_route' => route('notices.preview', $row),
            ]);
        }

        foreach (Minute::latest()->get() as $row) {
            if (empty($row->document_path) && empty($row->approved_minutes_path)) {
                continue;
            }

            $status = $row->approved_minutes_path ? 'Accepted' : 'Submitted';

            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'Minutes',
                'title' => $row->minutes_ref ?: ('Minutes #' . $row->id),
                'company_reg_no' => $row->minutes_ref ?? '',
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_uploaded ? $row->date_uploaded->format('Y-m-d') : '',
                'status' => $status,
                'approval_status' => $status === 'Accepted' ? 'Approved' : 'Pending',
                'show_route' => route('minutes.preview', $row),
            ]);
        }

        foreach (Resolution::latest()->get() as $row) {
            if (empty($row->draft_file_path) && empty($row->notarized_file_path)) {
                continue;
            }

            $status = $row->notarized_file_path ? 'Accepted' : 'Submitted';

            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'Resolutions',
                'title' => $row->resolution_no ?: ('Resolution #' . $row->id),
                'company_reg_no' => $row->resolution_no ?? '',
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_uploaded ? $row->date_uploaded->format('Y-m-d') : '',
                'status' => $status,
                'approval_status' => $status === 'Accepted' ? 'Approved' : 'Pending',
                'show_route' => route('resolutions.preview', $row),
            ]);
        }

        foreach (SecretaryCertificate::latest()->get() as $row) {
            if (empty($row->document_path)) {
                continue;
            }

            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'Secretary Certificates',
                'title' => $row->certificate_no ?: ('Secretary Certificate #' . $row->id),
                'company_reg_no' => $row->certificate_no ?? '',
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_uploaded ? $row->date_uploaded->format('Y-m-d') : '',
                'status' => 'Submitted',
                'approval_status' => 'Pending',
                'show_route' => route('secretary-certificates.preview', $row),
            ]);
        }

        foreach (BirTax::latest()->get() as $row) {
            if (empty($row->document_path) && empty($row->approved_document_path)) {
                continue;
            }

            $status = $row->approved_document_path ? 'Accepted' : 'Submitted';

            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'BIR & Tax',
                'title' => $row->tax_payer ?: ($row->tin ?: ('BIR & Tax #' . $row->id)),
                'company_reg_no' => $row->tin ?? '',
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_uploaded ? $row->date_uploaded->format('Y-m-d') : '',
                'status' => $status,
                'approval_status' => $status === 'Accepted' ? 'Approved' : 'Pending',
                'show_route' => route('bir-tax.preview', $row),
            ]);
        }

        foreach (NatGov::latest()->get() as $row) {
            if (empty($row->document_path) && empty($row->approved_document_path)) {
                continue;
            }

            $status = $row->approved_document_path ? 'Accepted' : 'Submitted';

            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'NatGov',
                'title' => $row->client ?: ($row->registration_no ?: ('NatGov #' . $row->id)),
                'company_reg_no' => $row->registration_no ?? '',
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_uploaded ? $row->date_uploaded->format('Y-m-d') : '',
                'status' => $status,
                'approval_status' => $status === 'Accepted' ? 'Approved' : 'Pending',
                'show_route' => route('natgov.preview', $row),
            ]);
        }

        foreach (StockTransferLedger::query()->latest()->get() as $row) {
            $status = $this->dashboardStatusBadge($row->status ?: 'Submitted');
            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'Stock Transfer Book - Index',
                'title' => trim(collect([$row->first_name, $row->middle_name, $row->family_name])->filter()->implode(' ')) ?: ('Index #' . $row->id),
                'company_reg_no' => $row->certificate_no ?? '',
                'uploaded_by' => $row->created_by ?? '',
                'date_uploaded' => $row->date_registered ? $row->date_registered->format('Y-m-d') : '',
                'status' => $status,
                'approval_status' => $status === 'Accepted' ? 'Approved' : ($status === 'Reverted' ? 'Rejected' : 'Pending'),
                'show_route' => route('stock-transfer-book.ledger.show', $row),
            ]);
        }

        foreach (StockTransferJournal::latest()->get() as $row) {
            $status = $this->dashboardStatusBadge($row->status ?: 'Submitted');
            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'Stock Transfer Book - Journal',
                'title' => $row->journal_no ?: ('Journal #' . $row->id),
                'company_reg_no' => $row->certificate_no ?? '',
                'uploaded_by' => '',
                'date_uploaded' => $row->entry_date ? $row->entry_date->format('Y-m-d') : '',
                'status' => $status,
                'approval_status' => $status === 'Accepted' ? 'Approved' : ($status === 'Reverted' ? 'Rejected' : 'Pending'),
                'show_route' => route('stock-transfer-book.journal.show', $row),
            ]);
        }

        foreach (StockTransferInstallment::latest()->get() as $row) {
            $status = $this->dashboardStatusBadge($row->status ?: 'Submitted');
            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'Stock Transfer Book - Installment',
                'title' => $row->stock_number ?: ('Installment #' . $row->id),
                'company_reg_no' => $row->stock_number ?? '',
                'uploaded_by' => '',
                'date_uploaded' => $row->installment_date ? $row->installment_date->format('Y-m-d') : '',
                'status' => $status,
                'approval_status' => $status === 'Accepted' ? 'Approved' : ($status === 'Reverted' ? 'Rejected' : 'Pending'),
                'show_route' => route('stock-transfer-book.installment.show', $row),
            ]);
        }

        foreach (StockTransferCertificate::latest()->get() as $row) {
            $status = $this->dashboardStatusBadge($row->status ?: 'Submitted');
            $this->addDocumentWorkflowItem($items, [
                'id' => $row->id,
                'module' => 'Stock Transfer Book - Certificate',
                'title' => $row->stock_number ?: ('Certificate #' . $row->id),
                'company_reg_no' => $row->stock_number ?? '',
                'uploaded_by' => $row->uploaded_by ?? '',
                'date_uploaded' => $row->date_uploaded ? $row->date_uploaded->format('Y-m-d') : '',
                'status' => $status,
                'approval_status' => $status === 'Accepted' ? 'Approved' : ($status === 'Reverted' ? 'Rejected' : 'Pending'),
                'show_route' => route('stock-transfer-book.certificates.show', $row),
            ]);
        }

        $items = $items->sortByDesc('id')->values();

        $pendingCount = $items->where('status', 'Submitted')->count();
        $approvedCount = $items->where('status', 'Accepted')->count();
        $rejectedCount = $items->where('approval_status', 'Rejected')->count();
        $revisionCount = $items->where('status', 'Reverted')->count();

        return view('admin.corporate-dashboard', compact(
            'items',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'revisionCount'
        ));
    }

    private function resolveModel($module, $id)
    {
        return match ($module) {
            'sec-coi' => SecCoi::findOrFail($id),
            'sec-aoi' => SecAoi::findOrFail($id),
            'bylaws' => Bylaw::findOrFail($id),
            'gis' => GisRecord::findOrFail($id),
            'lgu' => Permit::findOrFail($id),
            'accounting' => Accounting::findOrFail($id),
            'banking' => Banking::findOrFail($id),
            'operations' => Operation::findOrFail($id),
            'correspondence' => Correspondence::findOrFail($id),
            'legal' => Legal::findOrFail($id),
            default => abort(404),
        };
    }

    public function approve($module, $id)
    {
        $this->authorizeApprover();

        $record = $this->resolveModel($module, $id);

        if ($response = $this->ensureSubmittedForDecision($record)) {
            return $response;
        }

        $record->update([
            'approval_status' => 'Approved',
            'workflow_status' => 'Accepted',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'review_note' => null,
        ]);

        $this->sendStatusEmail($record, $module, 'Approved', null);

        return back()->with('success', 'Record approved successfully.');
    }

    public function reject(Request $request, $module, $id)
    {
        $this->authorizeApprover();

        $record = $this->resolveModel($module, $id);

        if ($response = $this->ensureSubmittedForDecision($record)) {
            return $response;
        }

        $record->update([
            'approval_status' => 'Rejected',
            'workflow_status' => 'Reverted',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'review_note' => $request->review_note,
        ]);

        $this->sendStatusEmail($record, $module, 'Rejected', $request->review_note);

        return back()->with('success', 'Record rejected successfully.');
    }

    public function revise(Request $request, $module, $id)
    {
        $this->authorizeApprover();

        $record = $this->resolveModel($module, $id);

        if ($response = $this->ensureSubmittedForDecision($record)) {
            return $response;
        }

        $record->update([
            'approval_status' => 'Needs Revision',
            'workflow_status' => 'Reverted',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'review_note' => $request->review_note,
        ]);

        $this->sendStatusEmail($record, $module, 'Needs Revision', $request->review_note);

        return back()->with('success', 'Record marked as needs revision.');
    }

    public function archive($module, $id)
    {
        $this->authorizeApprover();

        $record = $this->resolveModel($module, $id);

        $workflow = $this->normalizeWorkflow($record);

        if ($workflow === 'Uploaded') {
            return back()->with('error', 'Draft records cannot be archived from the admin dashboard.');
        }

        $record->update([
            'workflow_status' => 'Archived',
        ]);

        return back()->with('success', 'Record archived successfully.');
    }
}
