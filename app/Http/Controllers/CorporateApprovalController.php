<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SecCoi;
use App\Models\SecAoi;
use App\Models\Bylaw;
use App\Models\GisRecord;

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

    public function dashboard()
    {
        $this->authorizeApprover();

        $items = collect();

        foreach (SecCoi::latest()->get() as $row) {
            $items->push((object) [
                'id' => $row->id,
                'module' => 'SEC-COI',
                'title' => $row->corporate_name,
                'company_reg_no' => $row->company_reg_no,
                'uploaded_by' => $row->submitted_by,
                'date_uploaded' => $row->date_upload,
                'status' => $row->approval_status,
                'show_route' => route('corporate.formation.show', $row->id),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'sec-coi', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'sec-coi', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'sec-coi', 'id' => $row->id]),
            ]);
        }

        foreach (SecAoi::latest()->get() as $row) {
            $items->push((object) [
                'id' => $row->id,
                'module' => 'SEC-AOI',
                'title' => $row->corporation_name,
                'company_reg_no' => $row->company_reg_no,
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_upload,
                'status' => $row->approval_status,
                'show_route' => route('corporate.sec_aoi.show', $row->id),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'sec-aoi', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'sec-aoi', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'sec-aoi', 'id' => $row->id]),
            ]);
        }

        foreach (Bylaw::latest()->get() as $row) {
            $items->push((object) [
                'id' => $row->id,
                'module' => 'Bylaws',
                'title' => $row->corporation_name,
                'company_reg_no' => $row->company_reg_no,
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->date_upload,
                'status' => $row->approval_status,
                'show_route' => route('corporate.bylaws.show', $row->id),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'bylaws', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'bylaws', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'bylaws', 'id' => $row->id]),
            ]);
        }

        foreach (GisRecord::latest()->get() as $row) {
            $items->push((object) [
                'id' => $row->id,
                'module' => 'GIS',
                'title' => $row->corporation_name,
                'company_reg_no' => $row->company_reg_no,
                'uploaded_by' => $row->uploaded_by,
                'date_uploaded' => $row->created_at ? $row->created_at->format('Y-m-d') : '',
                'status' => $row->approval_status,
                'show_route' => route('gis.show', $row->id),
                'approve_route' => route('corporate.approvals.approve', ['module' => 'gis', 'id' => $row->id]),
                'reject_route' => route('corporate.approvals.reject', ['module' => 'gis', 'id' => $row->id]),
                'revise_route' => route('corporate.approvals.revise', ['module' => 'gis', 'id' => $row->id]),
            ]);
        }

        $items = $items->sortByDesc('id')->values();

        $pendingCount = $items->where('status', 'Pending')->count();
        $approvedCount = $items->where('status', 'Approved')->count();
        $rejectedCount = $items->where('status', 'Rejected')->count();
        $revisionCount = $items->where('status', 'Needs Revision')->count();

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
            default   => abort(404),
        };
    }

    public function approve($module, $id)
    {
        $this->authorizeApprover();

        $record = $this->resolveModel($module, $id);

        $record->update([
            'approval_status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'review_note' => null,
        ]);

        return back()->with('success', 'Record approved successfully.');
    }

    public function reject(Request $request, $module, $id)
    {
        $this->authorizeApprover();

        $record = $this->resolveModel($module, $id);

        $record->update([
            'approval_status' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'review_note' => $request->review_note,
        ]);

        return back()->with('success', 'Record rejected successfully.');
    }

    public function revise(Request $request, $module, $id)
    {
        $this->authorizeApprover();

        $record = $this->resolveModel($module, $id);

        $record->update([
            'approval_status' => 'Needs Revision',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'review_note' => $request->review_note,
        ]);

        return back()->with('success', 'Record marked as needs revision.');
    }
}