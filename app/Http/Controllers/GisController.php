<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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

    private function canEditRecord(GisRecord $gis): bool
    {
        if ($this->canApproveCorporate()) {
            return true;
        }

        return (int) $gis->submitted_by === (int) Auth::id()
            && in_array($gis->workflow_status, ['Uploaded', 'Reverted']);
    }

    public function index()
    {
        if (!Schema::hasTable('gis_records')) {
            return view('corporate.gis', ['gis' => collect()]);
        }

        if ($this->canApproveCorporate()) {
            $gis = GisRecord::latest()->get();
        } else {
            $gis = GisRecord::where('submitted_by', Auth::id())->latest()->get();
        }

        return view('corporate.gis', compact('gis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'uploaded_by'         => 'nullable|string|max:255',
            'submission_status'   => 'nullable|string|max:255',
            'receive_on'          => 'nullable|date',
            'period_date'         => 'nullable|string|max:255',
            'company_reg_no'      => 'nullable|string|max:255',
            'corporation_name'    => 'nullable|string|max:255',
            'annual_meeting'      => 'nullable|date',
            'meeting_type'        => 'nullable|string|max:255',
            'draft_file_upload'   => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'notary_file_upload'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $draftPath = null;
        $notaryPath = null;

        if ($request->hasFile('draft_file_upload')) {
            $file = $request->file('draft_file_upload');
            $fileName = time() . '_draft_' . $file->getClientOriginalName();
            $file->storeAs('gis_files', $fileName, 'public');
            $draftPath = 'gis_files/' . $fileName;
        }

        if ($request->hasFile('notary_file_upload')) {
            $file = $request->file('notary_file_upload');
            $fileName = time() . '_notary_' . $file->getClientOriginalName();
            $file->storeAs('gis_files', $fileName, 'public');
            $notaryPath = 'gis_files/' . $fileName;
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
            'file'              => $draftPath,
            'notary_file_path'  => $notaryPath,
            'approval_status'   => $isApprover ? 'Approved' : 'Pending',
            'workflow_status'   => $isApprover ? 'Accepted' : 'Uploaded',
            'submitted_by'      => Auth::id(),
            'approved_by'       => $isApprover ? Auth::id() : null,
            'approved_at'       => $isApprover ? now() : null,
        ]);

        return redirect()->route('corporate.gis')
            ->with('success', $isApprover ? 'GIS saved successfully.' : 'GIS saved as uploaded record.');
    }

    public function companyInfo()
    {
        if (!Schema::hasTable('gis_records')) {
            $gis = new GisRecord();

            return view('corporate.company-general-information', compact('gis'));
        }

        $gis = GisRecord::where('approval_status', 'Approved')->latest()->first();

        if (!$gis) {
            $gis = new GisRecord();
        }

        return view('corporate.company-general-information', compact('gis'));
    }

    public function companyInfoById($id)
    {
        abort_unless(Schema::hasTable('gis_records'), 404);

        $gis = GisRecord::findOrFail($id);

        if ($gis->approval_status !== 'Approved' && !$this->canApproveCorporate()) {
            abort(403, 'This record is still pending approval.');
        }

        return view('corporate.company-general-information', compact('gis'));
    }

    public function updateCompanyInfo(Request $request, $id)
    {
        abort_unless(Schema::hasTable('gis_records'), 404);

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
        abort_unless(Schema::hasTable('gis_records'), 404);

        $gis = GisRecord::with([
            'authorizedCapital',
            'subscribedCapital',
            'paidUpCapital',
            'directors',
            'stockholders'
        ])->findOrFail($id);

        if (!$this->canApproveCorporate() && (int) $gis->submitted_by !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('corporate.gis-show', compact('gis'));
    }

    public function uploadDraftFile(Request $request, $id)
    {
        abort_unless(Schema::hasTable('gis_records'), 404);

        $request->validate([
            'draft_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $gis = GisRecord::findOrFail($id);

        if (!$this->canEditRecord($gis)) {
            abort(403, 'This record can no longer be edited.');
        }

        $file = $request->file('draft_file');
        $fileName = time() . '_draft_' . $file->getClientOriginalName();
        $file->storeAs('gis_files', $fileName, 'public');
        $filePath = 'gis_files/' . $fileName;

        $gis->update([
            'file' => $filePath,
        ]);

        return back()->with('success', 'Draft file attached successfully.');
    }

    public function uploadNotaryFile(Request $request, $id)
    {
        abort_unless(Schema::hasTable('gis_records'), 404);

        $request->validate([
            'notary_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $gis = GisRecord::findOrFail($id);

        if (!$this->canEditRecord($gis)) {
            abort(403, 'This record can no longer be edited.');
        }

        $file = $request->file('notary_file');
        $fileName = time() . '_notary_' . $file->getClientOriginalName();
        $file->storeAs('gis_files', $fileName, 'public');
        $filePath = 'gis_files/' . $fileName;

        $gis->update([
            'notary_file_path' => $filePath,
        ]);

        return back()->with('success', 'Notary file attached successfully.');
    }

    public function submit($id)
    {
        abort_unless(Schema::hasTable('gis_records'), 404);

        $gis = GisRecord::findOrFail($id);

        if (!$this->canEditRecord($gis)) {
            abort(403, 'This record cannot be submitted.');
        }

        if (empty($gis->file) || empty($gis->notary_file_path)) {
            return back()->with('success', 'You must upload both Draft and Notary files before submitting.');
        }

        $gis->update([
            'workflow_status' => 'Submitted',
            'approval_status' => 'Pending',
            'review_note'     => null,
        ]);

        return back()->with('success', 'GIS submitted for approval.');
    }
}
