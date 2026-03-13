<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SecAoi;

class SecAoiController extends Controller
{

    public function index()
    {
        $records = SecAoi::latest()->get();
        return view('corporate.sec-aoi', compact('records'));
    }

    public function store(Request $request)
    {

        $path = null;

        if($request->hasFile('file_upload')){
            $path = $request->file('file_upload')->store('sec_aoi','public');
        }

        SecAoi::create([
            'corporation_name' => $request->corporation_name,
            'company_reg_no' => $request->company_reg_no,
            'principal_address' => $request->principal_address,
            'par_value' => $request->par_value,
            'authorized_capital_stock' => $request->authorized_capital_stock,
            'directors' => $request->directors,
            'type_of_formation' => $request->type_of_formation,
            'aoi_version' => $request->aoi_version,
            'aoi_type' => $request->aoi_type,
            'uploaded_by' => $request->uploaded_by,
            'date_upload' => $request->date_upload,
            'file_path' => $path
        ]);

        return redirect()->route('corporate.sec_aoi');
    }


    public function show($id)
    {
        $record = SecAoi::findOrFail($id);

        return view('corporate.sec-aoi-preview', compact('record'));
    }

    public function uploadFile(Request $request, $id)
{
    $request->validate([
        'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
    ]);

    $record = \App\Models\SecAoi::findOrFail($id);

    $filePath = $request->file('file')->store('sec_aoi_files', 'public');

    $record->update([
        'file_path' => $filePath,
    ]);

    return back()->with('success', 'File attached successfully.');
}

}