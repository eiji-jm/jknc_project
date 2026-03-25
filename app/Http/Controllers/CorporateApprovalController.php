<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\SecCoi;
use App\Models\SecAoi;
use App\Models\Bylaw;
use App\Models\GisRecord;
use App\Models\Permit;
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
            'bylaws'  => 'Bylaws',
            'gis'     => 'GIS',
            'lgu'     => 'LGU',
            default   => 'Corporate Module',
        };
    }

    private function getCorporationName($record, string $module): string
    {
        return match ($module) {
            'sec-coi' => $record->corporate_name ?? '',
            'sec-aoi' => $record->corporation_name ?? '',
            'bylaws'  => $record->corporation_name ?? '',
            'gis'     => $record->corporation_name ?? '',
            'lgu'     => ($record->permit_type ?? 'LGU Permit') . ' - ' . ($record->document_type ?? ''),
            default   => '',
        };
    }

    private function getReferenceNumber($record, string $module): string
    {
        return match ($module) {
            'lgu'     => $record->permit_number ?? '',
            default   => $record->company_reg_no ?? '',
        };
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

            if (!$this->canAppearInAdminDashboard($workflow)) {
                continue;
            }

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

            if (!$this->canAppearInAdminDashboard($workflow)) {
                continue;
            }

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

            if (!$this->canAppearInAdminDashboard($workflow)) {
                continue;
            }

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

            if (!$this->canAppearInAdminDashboard($workflow)) {
                continue;
            }

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

            if (!$this->canAppearInAdminDashboard($workflow)) {
                continue;
            }

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
            'bylaws'  => Bylaw::findOrFail($id),
            'gis'     => GisRecord::findOrFail($id),
            'lgu'     => Permit::findOrFail($id),
            default   => abort(404),
        };
    }

    public function approve($module, $id)
    {
        $this->authorizeApprover();

        $record = $this->resolveModel($module, $id);

        if ($response = $this->ensureSubmittedForDecision($record)) {
            return $response;
        }

        $payload = [
            'approval_status' => 'Approved',
            'workflow_status' => 'Accepted',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'review_note' => null,
        ];

        if ($module === 'lgu' && empty($record->approved_date_of_registration)) {
            $payload['approved_date_of_registration'] = $record->date_of_registration ?: now()->toDateString();
        }

        $record->update($payload);

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