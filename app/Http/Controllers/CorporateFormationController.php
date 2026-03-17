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
            'corporate_name' => 'required',
            'company_reg_no' => 'required',
            'issued_by' => 'required',
            'issued_on' => 'required',
            'date_upload' => 'required',
            'file_upload' => 'nullable|file'
        ]);

        $filePath = null;

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sec-coi'), $fileName);
            $filePath = 'uploads/sec-coi/' . $fileName;
        }

        $isApprover = $this->canApproveCorporate();

        SecCoi::create([
            'corporate_name'   => $request->corporate_name,
            'company_reg_no'   => $request->company_reg_no,
            'issued_by'        => $request->issued_by,
            'issued_on'        => $request->issued_on,
            'date_upload'      => $request->date_upload,
            'file_path'        => $filePath,
            'approval_status'  => $isApprover ? 'Approved' : 'Pending',
            'submitted_by'     => Auth::id(),
            'approved_by'      => $isApprover ? Auth::id() : null,
            'approved_at'      => $isApprover ? now() : null,
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

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $record = SecCoi::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        $filePath = $request->file('file')->store('formation_files', 'public');

        $record->update([
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'File attached successfully.');
    }
}