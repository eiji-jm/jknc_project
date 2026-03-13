<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bylaw;

class BylawController extends Controller
{

public function index()
{

$records = Bylaw::latest()->get();

return view('corporate.bylaws', compact('records'));

}


public function store(Request $request)
{

$path = null;

if($request->hasFile('file_upload')){
$path = $request->file('file_upload')->store('bylaws','public');
}

Bylaw::create([

'corporation_name'=>$request->corporation_name,
'company_reg_no'=>$request->company_reg_no,
'type_of_formation'=>$request->type_of_formation,

'aoi_version'=>$request->aoi_version,
'aoi_type'=>$request->aoi_type,
'aoi_date'=>$request->aoi_date,

'regular_asm'=>$request->regular_asm,
'asm_notice'=>$request->asm_notice,

'regular_bodm'=>$request->regular_bodm,
'bodm_notice'=>$request->bodm_notice,

'uploaded_by'=>$request->uploaded_by,
'date_upload'=>$request->date_upload,

'file_path'=>$path

]);

return redirect()->route('corporate.bylaws');

}


public function show($id)
{

$record = Bylaw::findOrFail($id);

return view('corporate.bylaws-preview', compact('record'));

}

public function uploadFile(Request $request, $id)
{
    $request->validate([
        'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
    ]);

    $record = \App\Models\Bylaw::findOrFail($id);

    $filePath = $request->file('file')->store('bylaw_files', 'public');

    $record->update([
        'file_path' => $filePath,
    ]);

    return back()->with('success', 'File attached successfully.');
}

}