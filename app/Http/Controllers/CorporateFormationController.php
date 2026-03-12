<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SecCoi;

class CorporateFormationController extends Controller
{

    public function index()
    {
        $records = SecCoi::latest()->get();
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

            $fileName = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('uploads/sec-coi'), $fileName);

            $filePath = 'uploads/sec-coi/'.$fileName;
        }


        SecCoi::create([
            'corporate_name' => $request->corporate_name,
            'company_reg_no' => $request->company_reg_no,
            'issued_by' => $request->issued_by,
            'issued_on' => $request->issued_on,
            'date_upload' => $request->date_upload,
            'file_path' => $filePath
        ]);


        return redirect()->route('corporate.formation');
    }

    public function show($id)
{
    $record = SecCoi::findOrFail($id);

    return view('corporate.sec-coi-preview', compact('record'));
}

}