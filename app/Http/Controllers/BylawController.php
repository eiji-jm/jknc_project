<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bylaw;

class BylawController extends Controller
{
    public function index()
    {
        $records = Bylaw::where('approval_status', 'Approved')->latest()->get();
        return view('corporate.bylaws', compact('records'));
    }

    public function store(Request $request)
    {
        $path = null;

        if ($request->hasFile('file_upload')) {
            $path = $request->file('file_upload')->store('bylaws', 'public');
        }

        $isApprover = in_array(Auth::user()->role, ['admin', 'super_admin']);

        Bylaw::create([
            'corporation_name' => $request->corporation_name,
            'company_reg_no'   => $request->company_reg_no,
            'type_of_formation'=> $request->type_of_formation,
            'aoi_version'      => $request->aoi_version,
            'aoi_type'         => $request->aoi_type,
            'aoi_date'         => $request->aoi_date,
            'regular_asm'      => $request->regular_asm,
            'asm_notice'       => $request->asm_notice,
            'regular_bodm'     => $request->regular_bodm,
            'bodm_notice'      => $request->bodm_notice,
            'uploaded_by'      => $request->uploaded_by,
            'date_upload'      => $request->date_upload,
            'file_path'        => $path,
            'approval_status'  => $isApprover ? 'Approved' : 'Pending',
            'submitted_by'     => Auth::id(),
            'approved_by'      => $isApprover ? Auth::id() : null,
            'approved_at'      => $isApprover ? now() : null,
        ]);

        return redirect()->route('corporate.bylaws')
            ->with('success', $isApprover ? 'Bylaws saved successfully.' : 'Bylaws submitted for approval.');
    }

    public function show($id)
    {
        $record = Bylaw::findOrFail($id);

        if ($record->approval_status !== 'Approved' && !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            abort(403, 'This record is still pending approval.');
        }

        return view('corporate.bylaws-preview', compact('record'));
    }

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $record = Bylaw::findOrFail($id);

        $filePath = $request->file('file')->store('bylaw_files', 'public');

        $record->update([
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'File attached successfully.');
    }
}