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
        $path = null;

        if ($request->hasFile('file_upload')) {
            $path = $request->file('file_upload')->store('sec_aoi', 'public');
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
            'file_path'                => $path,
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

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $record = SecAoi::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        $filePath = $request->file('file')->store('sec_aoi_files', 'public');

        $record->update([
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'File attached successfully.');
    }
}