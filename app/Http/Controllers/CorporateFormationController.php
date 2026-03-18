<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SecCoi;

class CorporateFormationController extends Controller
{
    private function canApproveCorporate(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user && $user->hasPermission('approve_corporate');
    }

    public function index()
    {
        $records = SecCoi::where('approval_status', 'Approved')->latest()->get();
        return view('corporate.corporate-formation', compact('records'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'corporate_name'      => 'required',
            'company_reg_no'      => 'required',
            'issued_by'           => 'required',
            'issued_on'           => 'required',
            'date_upload'         => 'required',
            'draft_file_upload'   => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'notary_file_upload'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $draftPath = null;
        $notaryPath = null;

        if ($request->hasFile('draft_file_upload')) {
            $file = $request->file('draft_file_upload');
            $fileName = time() . '_draft_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sec-coi'), $fileName);
            $draftPath = 'uploads/sec-coi/' . $fileName;
        }

        if ($request->hasFile('notary_file_upload')) {
            $file = $request->file('notary_file_upload');
            $fileName = time() . '_notary_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sec-coi'), $fileName);
            $notaryPath = 'uploads/sec-coi/' . $fileName;
        }

        $isApprover = $this->canApproveCorporate();

        SecCoi::create([
            'corporate_name'    => $request->corporate_name,
            'company_reg_no'    => $request->company_reg_no,
            'issued_by'         => $request->issued_by,
            'issued_on'         => $request->issued_on,
            'date_upload'       => $request->date_upload,
            'file_path'         => $draftPath, // Draft
            'notary_file_path'  => $notaryPath, // Notary
            'approval_status'   => $isApprover ? 'Approved' : 'Pending',
            'submitted_by'      => Auth::id(),
            'approved_by'       => $isApprover ? Auth::id() : null,
            'approved_at'       => $isApprover ? now() : null,
        ]);

        return redirect()->route('corporate.formation')
            ->with('success', $isApprover ? 'SEC-COI saved successfully.' : 'SEC-COI submitted for approval.');
    }

    public function show($id)
    {
        $record = SecCoi::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        return view('corporate.sec-coi-preview', compact('record'));
    }

    public function uploadDraftFile(Request $request, $id)
    {
        $request->validate([
            'draft_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $record = SecCoi::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        $file = $request->file('draft_file');
        $fileName = time() . '_draft_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/sec-coi'), $fileName);
        $filePath = 'uploads/sec-coi/' . $fileName;

        $record->update([
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Draft file attached successfully.');
    }

    public function uploadNotaryFile(Request $request, $id)
    {
        $request->validate([
            'notary_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $record = SecCoi::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        $file = $request->file('notary_file');
        $fileName = time() . '_notary_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/sec-coi'), $fileName);
        $filePath = 'uploads/sec-coi/' . $fileName;

        $record->update([
            'notary_file_path' => $filePath,
        ]);

        return back()->with('success', 'Notary file attached successfully.');
    }
}