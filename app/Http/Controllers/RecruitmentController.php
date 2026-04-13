<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManpowerRequest;
use App\Models\JobPosting;

class RecruitmentController extends Controller
{
    public function index()
    {
        $mrfData = ManpowerRequest::latest()->get();
        $jpfData = JobPosting::latest()->get();

        return view('human-capital.recruitment', compact('mrfData', 'jpfData'));
    }

    public function storeMRF(Request $request)
    {
        $data = [
            'department'         => $request->department,
            'date_requested'     => $request->dateRequested,
            'date_required'      => $request->dateRequired,
            'position'           => $request->position,
            'employment_type'    => $request->employmentType,
            'duties'             => $request->duties,
            'nature_of_request'  => $request->natureOfRequest,
            'age_range'          => $request->ageRange,
            'civil_status'       => $request->civilStatus,
            'gender'             => $request->gender,
            'headcount'          => $request->headcount,
            'education'          => $request->education,
            'qualifications'     => $request->qualifications,
            'requested_by'       => $request->requestedBy,
            'approved_by'        => $request->approvedBy,
            'remarks'            => $request->remarks,
            'request_status'     => $request->requestStatus ?: 'Pending',
            'charged_to'         => $request->chargedTo,
            'breakdown_details'  => $request->breakdownDetails,
            'hired_personnel'    => $request->hiredPersonnel,
            'date_hired'         => $request->dateHired,
            'processed_by'       => $request->processedBy,
            'checked_by'         => $request->checkedBy,
        ];
        
        // Generate ID if not provided
        if (!isset($data['request_id'])) {
            $year = date('Y');
            $count = ManpowerRequest::whereYear('created_at', $year)->count() + 1;
            $data['request_id'] = "MRF-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $mrf = ManpowerRequest::create($data);

        return response()->json([
            'success' => true,
            'data' => $mrf
        ]);
    }

    public function storeJPF(Request $request)
    {
        $data = [
            'position'         => $request->position,
            'employment_type'  => $request->employmentType,
            'location'         => $request->location,
            'salary_range'     => $request->salaryRange,
            'job_description'  => $request->jobDescription,
            'requirements'     => $request->requirements,
            'posted_date'      => $request->posted_date ?: date('Y-m-d'),
            'status'           => 'Open',
        ];

        if (!isset($data['job_id'])) {
            $year = date('Y');
            $count = JobPosting::whereYear('created_at', $year)->count() + 1;
            $data['job_id'] = "JPF-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $jpf = JobPosting::create($data);

        return response()->json([
            'success' => true,
            'data' => $jpf
        ]);
    }

    public function deleteMRF($id)
    {
        $mrf = ManpowerRequest::findOrFail($id);
        $mrf->delete();

        return response()->json(['success' => true]);
    }

    public function deleteJPF($id)
    {
        $jpf = JobPosting::findOrFail($id);
        $jpf->delete();

        return response()->json(['success' => true]);
    }
}
