<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\GisRecord;

class GisController extends Controller
{
    private function canApproveCorporate(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user && $user->hasPermission('approve_corporate');
    }

    public function index()
    {
        $gis = GisRecord::where('approval_status', 'Approved')->latest()->get();
        return view('corporate.gis', compact('gis'));
    }

    public function store(Request $request)
    {
        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('gis_files', 'public');
        }

        $isApprover = $this->canApproveCorporate();

        GisRecord::create([
            'uploaded_by'       => $request->uploaded_by,
            'submission_status' => $request->submission_status,
            'receive_on'        => $request->receive_on,
            'period_date'       => $request->period_date,
            'company_reg_no'    => $request->company_reg_no,
            'corporation_name'  => $request->corporation_name,
            'annual_meeting'    => $request->annual_meeting,
            'meeting_type'      => $request->meeting_type,
            'file'              => $filePath,
            'approval_status'   => $isApprover ? 'Approved' : 'Pending',
            'submitted_by'      => Auth::id(),
            'approved_by'       => $isApprover ? Auth::id() : null,
            'approved_at'       => $isApprover ? now() : null,
        ]);

        return redirect()->route('corporate.gis')
            ->with('success', $isApprover ? 'GIS saved successfully.' : 'GIS submitted for approval.');
    }

    public function companyInfo()
    {
        $gis = GisRecord::where('approval_status', 'Approved')->latest()->first();

        if (!$gis) {
            $gis = new GisRecord();
        }

        return view('corporate.company-general-information', compact('gis'));
    }

    public function companyInfoById($id)
    {
        $gis = GisRecord::findOrFail($id);

        if ($gis->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        return view('corporate.company-general-information', compact('gis'));
    }

    public function updateCompanyInfo(Request $request, $id)
    {
        $request->validate([
            'date_registered'         => 'nullable|date',
            'trade_name'              => 'nullable|string|max:255',
            'fiscal_year_end'         => 'nullable|string|max:255',
            'tin'                     => 'nullable|string|max:255',
            'website'                 => 'nullable|string|max:255',
            'email'                   => 'nullable|email|max:255',
            'principal_address'       => 'nullable|string',
            'business_address'        => 'nullable|string',
            'official_mobile'         => 'nullable|string|max:255',
            'alternate_mobile'        => 'nullable|string|max:255',
            'auditor'                 => 'nullable|string|max:255',
            'industry'                => 'nullable|string|max:255',
            'geo_code'                => 'nullable|string|max:255',
            'parent_company_name'     => 'nullable|string|max:255',
            'parent_company_sec_no'   => 'nullable|string|max:255',
            'parent_company_address'  => 'nullable|string|max:255',
            'subsidiary_name'         => 'nullable|string|max:255',
            'subsidiary_sec_no'       => 'nullable|string|max:255',
            'subsidiary_address'      => 'nullable|string|max:255',
        ]);

        $gis = GisRecord::findOrFail($id);

        if ($gis->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        $gis->update([
            'date_registered'         => $request->date_registered,
            'trade_name'              => $request->trade_name,
            'fiscal_year_end'         => $request->fiscal_year_end,
            'tin'                     => $request->tin,
            'website'                 => $request->website,
            'email'                   => $request->email,
            'principal_address'       => $request->principal_address,
            'business_address'        => $request->business_address,
            'official_mobile'         => $request->official_mobile,
            'alternate_mobile'        => $request->alternate_mobile,
            'auditor'                 => $request->auditor,
            'industry'                => $request->industry,
            'geo_code'                => $request->geo_code,
            'parent_company_name'     => $request->parent_company_name,
            'parent_company_sec_no'   => $request->parent_company_sec_no,
            'parent_company_address'  => $request->parent_company_address,
            'subsidiary_name'         => $request->subsidiary_name,
            'subsidiary_sec_no'       => $request->subsidiary_sec_no,
            'subsidiary_address'      => $request->subsidiary_address,
        ]);

        return redirect()->route('gis.show', $gis->id)
            ->with('success', 'GIS Company Information completed successfully.');
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

    public function show($id)
    {
        $gis = GisRecord::with([
            'authorizedCapital',
            'subscribedCapital',
            'paidUpCapital',
            'directors',
            'stockholders'
        ])->findOrFail($id);

        if ($gis->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        return view('corporate.gis-show', compact('gis'));
    }
}