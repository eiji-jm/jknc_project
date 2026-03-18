<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SecAoi;

class SecAoiController extends Controller
{
    private function canApproveCorporate(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user && $user->hasPermission('approve_corporate');
    }

    public function index()
    {
        $records = SecAoi::where('approval_status', 'Approved')->latest()->get();
        return view('corporate.sec-aoi', compact('records'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'corporation_name'         => 'required',
            'company_reg_no'           => 'required',
            'principal_address'        => 'required',
            'par_value'                => 'nullable|string',
            'authorized_capital_stock' => 'nullable|string',
            'directors'                => 'nullable|integer',
            'type_of_formation'        => 'nullable|string',
            'aoi_version'              => 'nullable|string',
            'aoi_type'                 => 'nullable|string',
            'uploaded_by'              => 'nullable|string',
            'date_upload'              => 'nullable|date',
            'draft_file_upload'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'notary_file_upload'       => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $draftPath = null;
        $notaryPath = null;

        if ($request->hasFile('draft_file_upload')) {
            $file = $request->file('draft_file_upload');
            $fileName = time() . '_draft_' . $file->getClientOriginalName();
            $file->storeAs('sec_aoi', $fileName, 'public');
            $draftPath = 'sec_aoi/' . $fileName;
        }

        if ($request->hasFile('notary_file_upload')) {
            $file = $request->file('notary_file_upload');
            $fileName = time() . '_notary_' . $file->getClientOriginalName();
            $file->storeAs('sec_aoi', $fileName, 'public');
            $notaryPath = 'sec_aoi/' . $fileName;
        }

        $isApprover = $this->canApproveCorporate();

        SecAoi::create([
            'corporation_name'         => $request->corporation_name,
            'company_reg_no'           => $request->company_reg_no,
            'principal_address'        => $request->principal_address,
            'par_value'                => $request->par_value,
            'authorized_capital_stock' => $request->authorized_capital_stock,
            'directors'                => $request->directors,
            'type_of_formation'        => $request->type_of_formation,
            'aoi_version'              => $request->aoi_version,
            'aoi_type'                 => $request->aoi_type,
            'uploaded_by'              => $request->uploaded_by,
            'date_upload'              => $request->date_upload,
            'file_path'                => $draftPath,
            'notary_file_path'         => $notaryPath,
            'approval_status'          => $isApprover ? 'Approved' : 'Pending',
            'submitted_by'             => Auth::id(),
            'approved_by'              => $isApprover ? Auth::id() : null,
            'approved_at'              => $isApprover ? now() : null,
        ]);

        return redirect()->route('corporate.sec_aoi')
            ->with('success', $isApprover ? 'SEC-AOI saved successfully.' : 'SEC-AOI submitted for approval.');
    }

    public function show($id)
    {
        $record = SecAoi::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        return view('corporate.sec-aoi-preview', compact('record'));
    }

    public function uploadDraftFile(Request $request, $id)
    {
        $request->validate([
            'draft_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $record = SecAoi::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        $file = $request->file('draft_file');
        $fileName = time() . '_draft_' . $file->getClientOriginalName();
        $file->storeAs('sec_aoi_files', $fileName, 'public');
        $filePath = 'sec_aoi_files/' . $fileName;

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

        $record = SecAoi::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        $file = $request->file('notary_file');
        $fileName = time() . '_notary_' . $file->getClientOriginalName();
        $file->storeAs('sec_aoi_files', $fileName, 'public');
        $filePath = 'sec_aoi_files/' . $fileName;

        $record->update([
            'notary_file_path' => $filePath,
        ]);

        return back()->with('success', 'Notary file attached successfully.');
    }
}