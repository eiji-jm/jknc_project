<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GisRecord;

class GisController extends Controller
{

    public function index()
    {
        $gis = GisRecord::latest()->get();

        return view('corporate.gis', compact('gis'));
    }

    public function store(Request $request)
    {

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('gis_files', 'public');
        }

        GisRecord::create([
            'uploaded_by' => $request->uploaded_by,
            'submission_status' => $request->submission_status,
            'receive_on' => $request->receive_on,
            'period_date' => $request->period_date,
            'company_reg_no' => $request->company_reg_no,
            'corporation_name' => $request->corporation_name,
            'annual_meeting' => $request->annual_meeting,
            'meeting_type' => $request->meeting_type,
            'file' => $filePath
        ]);

        return redirect()->route('corporate.gis');
    }

    public function companyInfo()
    {
        // GET LATEST GIS RECORD
        $gis = GisRecord::latest()->first();

        return view('corporate.company-general-information', compact('gis'));
    }

    public function capitalStructure()
    {
        return view('corporate.gis-capital-structure');
    }

    public function directorsOfficers()
    {
        return view('corporate.gis-directors-officers');
    }

    public function stockholders()
    {
        return view('corporate.gis-stockholders');
    }

}