<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManpowerRequest;
use App\Models\JobPosting;
use App\Models\CandidateApplication;
use App\Models\CandidateAssessment;
use App\Models\CandidateInterview;
use App\Models\JobOffer;
use Illuminate\Support\Facades\Mail;
use App\Mail\AssessmentProceedingMail;
use App\Mail\AssessmentTestMail;

class RecruitmentController extends Controller
{
    public function index()
    {
        $mrfData = ManpowerRequest::latest()->get();
        $jpfData = JobPosting::latest()->get();
        $cafData = CandidateApplication::latest()->get();
        $assessmentData = CandidateAssessment::latest()->get();
        $interviewData = CandidateInterview::latest()->get();
        $jobOfferData = JobOffer::latest()->get();

        return view('human-capital.recruitment', compact(
            'mrfData', 'jpfData', 'cafData', 'assessmentData', 'interviewData', 'jobOfferData'
        ));
    }

    public function showPublicApplicationForm()
    {
        $positions = JobPosting::where('status', 'Posted')->pluck('position')->unique();
        if ($positions->isEmpty()) {
            $positions = JobPosting::pluck('position')->unique(); // Fallback to all if none posted
        }
        return view('careers.apply', compact('positions'));
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
        
        if (!isset($data['request_id'])) {
            $year = date('Y');
            $count = ManpowerRequest::whereYear('created_at', $year)->count() + 1;
            $data['request_id'] = "MRF-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $mrf = ManpowerRequest::create($data);
        return response()->json(['success' => true, 'data' => $mrf]);
    }

    public function updateMRF(Request $request, $id)
    {
        $mrf = ManpowerRequest::findOrFail($id);
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
            'request_status'     => $request->requestStatus ?: $mrf->request_status,
            'charged_to'         => $request->chargedTo,
            'breakdown_details'  => $request->breakdownDetails,
            'hired_personnel'    => $request->hiredPersonnel,
            'date_hired'         => $request->dateHired,
            'processed_by'       => $request->processedBy,
            'checked_by'         => $request->checkedBy,
        ];

        $mrf->update($data);
        return response()->json(['success' => true, 'data' => $mrf]);
    }

    public function approveMRF($id)
    {
        $mrf = ManpowerRequest::findOrFail($id);
        $mrf->update(['request_status' => 'Approved']);
        return response()->json(['success' => true, 'data' => $mrf]);
    }

    public function cancelMRF($id)
    {
        $mrf = ManpowerRequest::findOrFail($id);
        $mrf->update(['request_status' => 'Cancelled']);
        return response()->json(['success' => true, 'data' => $mrf]);
    }

    public function storeJPF(Request $request)
    {
        $data = [
            'position'               => $request->position,
            'employment_type'        => $request->employmentType,
            'location'               => $request->workLocation,
            'salary_range'           => $request->minSalary . ' - ' . $request->maxSalary,
            'job_description'        => $request->duties,
            'requirements'           => $request->education,
            'posted_date'            => $request->postingStartDate ?: date('Y-m-d'),
            'status'                 => $request->status ?: 'Draft',

            'related_mrf_no'         => $request->relatedMrfNo,
            'date_opened'            => $request->dateOpened,
            'hiring_status'          => $request->hiringStatus,
            'company_name'           => $request->companyName,
            'office_branch_site'     => $request->officeBranchSite,
            'department_unit'        => $request->departmentUnit,
            'hiring_manager'         => $request->hiringManager,
            'department_superior'    => $request->departmentSuperior,
            'no_of_vacancies'        => $request->noOfVacancies,
            'position_level'         => $request->positionLevel,
            'reports_to'             => $request->reportsTo,
            'min_salary_offer'       => $request->minSalary,
            'max_salary_offer'       => $request->maxSalary,
            'salary_grade'           => $request->salaryGrade,
            'applicable_region'      => $request->applicableRegion ?: 'Central Visayas',
            'applicable_area'        => $request->applicableArea,
            'current_daily_min_wage' => $request->dailyMinWage,
            'monthly_equivalent'     => $request->monthlyEquivalent,
            'wage_compliance'        => $request->wageCompliance,
            'benefits_package'       => $request->benefits,
            'work_schedule'          => $request->workSchedule,
            'rest_days'              => $request->restDays,
            'education_req'          => $request->education,
            'experience_req'         => $request->experience,
            'skills_req'             => $request->skills,
            'licenses_req'           => $request->licenses,
            'preferred_qualifications'=> $request->preferredQualifications,
            'duties_responsibilities'=> $request->duties,
            'recruitment_channels'   => $request->channels,
            'screening_flow'         => $request->screeningFlow,
            'date_needed'            => $request->dateNeeded,
            'posting_start_date'     => $request->postingStartDate,
            'target_hire_date'       => $request->targetHireDate,
            'human_capital_approval' => $request->humanCapitalApproval,
            'hiring_manager_approval'=> $request->hiringManagerApproval,
            'finance_approval'       => $request->financeApproval,
            'president_approval'     => $request->presidentApproval,
        ];

        if (!isset($data['job_id'])) {
            $year = date('Y');
            $count = JobPosting::whereYear('created_at', $year)->count() + 1;
            $data['job_id'] = "JPF-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $jpf = JobPosting::create($data);
        return response()->json(['success' => true, 'data' => $jpf]);
    }

    public function updateJPF(Request $request, $id)
    {
        $jpf = JobPosting::findOrFail($id);
        $data = [
            'position'               => $request->position,
            'employment_type'        => $request->employmentType,
            'location'               => $request->workLocation,
            'salary_range'           => $request->minSalary . ' - ' . $request->maxSalary,
            'job_description'        => $request->duties,
            'requirements'           => $request->education,
            'status'                 => $request->status ?: $jpf->status,

            'related_mrf_no'         => $request->relatedMrfNo,
            'date_opened'            => $request->dateOpened,
            'hiring_status'          => $request->hiringStatus,
            'company_name'           => $request->companyName,
            'office_branch_site'     => $request->officeBranchSite,
            'department_unit'        => $request->departmentUnit,
            'hiring_manager'         => $request->hiringManager,
            'department_superior'    => $request->departmentSuperior,
            'no_of_vacancies'        => $request->noOfVacancies,
            'position_level'         => $request->positionLevel,
            'reports_to'             => $request->reportsTo,
            'min_salary_offer'       => $request->minSalary,
            'max_salary_offer'       => $request->maxSalary,
            'salary_grade'           => $request->salaryGrade,
            'applicable_region'      => $request->applicableRegion,
            'applicable_area'        => $request->applicableArea,
            'current_daily_min_wage' => $request->dailyMinWage,
            'monthly_equivalent'     => $request->monthlyEquivalent,
            'wage_compliance'        => $request->wageCompliance,
            'benefits_package'       => $request->benefits,
            'work_schedule'          => $request->workSchedule,
            'rest_days'              => $request->restDays,
            'education_req'          => $request->education,
            'experience_req'         => $request->experience,
            'skills_req'             => $request->skills,
            'licenses_req'           => $request->licenses,
            'preferred_qualifications'=> $request->preferredQualifications,
            'duties_responsibilities'=> $request->duties,
            'recruitment_channels'   => $request->channels,
            'screening_flow'         => $request->screeningFlow,
            'date_needed'            => $request->dateNeeded,
            'posting_start_date'     => $request->postingStartDate,
            'target_hire_date'       => $request->targetHireDate,
            'human_capital_approval' => $request->humanCapitalApproval,
            'hiring_manager_approval'=> $request->hiringManagerApproval,
            'finance_approval'       => $request->financeApproval,
            'president_approval'     => $request->presidentApproval,
        ];
        $jpf->update($data);
        return response()->json(['success' => true, 'data' => $jpf]);
    }

    public function storeCAF(Request $request)
    {
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('resumes', 'public');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $coverLetterPath = null;
        if ($request->hasFile('cover_letter_file')) {
            $coverLetterPath = $request->file('cover_letter_file')->store('cover_letters', 'public');
        }

        $caf = CandidateApplication::create([
            'name' => $request->fullName,
            'position' => $request->positionApplied,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo_path' => $photoPath,
            'cv_path' => $cvPath,
            'cover_letter_path' => $coverLetterPath,
            'cover_letter' => $request->coverLetter,
            'status' => 'Pending',
            'applied_date' => date('Y-m-d')
        ]);

        return response()->json(['success' => true, 'data' => $caf]);
    }

    public function updateCAF(Request $request, $id)
    {
        $caf = CandidateApplication::findOrFail($id);
        $data = [
            'name' => $request->fullName,
            'position' => $request->positionApplied,
            'email' => $request->email,
            'phone' => $request->phone,
            'cover_letter' => $request->coverLetter,
            'status' => $request->status ?: $caf->status,
        ];

        if ($request->hasFile('cv')) {
            $data['cv_path'] = $request->file('cv')->store('resumes', 'public');
        }

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('photos', 'public');
        }

        if ($request->hasFile('cover_letter_file')) {
            $data['cover_letter_path'] = $request->file('cover_letter_file')->store('cover_letters', 'public');
        }

        $caf->update($data);
        return response()->json(['success' => true, 'data' => $caf]);
    }

    public function proceedToAssessment($id)
    {
        $caf = CandidateApplication::findOrFail($id);
        $caf->update(['status' => 'Assessment']);

        // Check if assessment already exists to avoid duplicates
        $assessment = CandidateAssessment::where('name', $caf->name)
            ->where('position', $caf->position)
            ->where('status', 'Pending Assessment')
            ->first();

        if (!$assessment) {
            $assessment = CandidateAssessment::create([
                'name' => $caf->name,
                'email' => $caf->email,
                'position' => $caf->position,
                'photo_path' => $caf->photo_path,
                'cv_path' => $caf->cv_path,
                'cover_letter_path' => $caf->cover_letter_path,
                'test_type' => 'Technical Test', // Default
                'assessment_date' => date('Y-m-d'),
                'notes' => 'Automatically created from CAF',
                'status' => 'Pending Assessment'
            ]);
        }

        if ($caf->email) {
            try {
                Mail::to($caf->email)->send(new AssessmentProceedingMail($caf->name, $caf->position));
            } catch (\Exception $e) {
                \Log::error("Failed to send assessment mail to {$caf->email}: " . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'assessment' => $assessment]);
    }

    public function storeAssessment(Request $request)
    {
        $assessment = CandidateAssessment::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'test_type' => $request->test,
            'assessment_date' => $request->date,
            'notes' => $request->notes,
            'status' => 'Pending Assessment'
        ]);

        // Update Candidate Application status if ID is provided
        if ($request->caf_id) {
            $caf = CandidateApplication::find($request->caf_id);
            if ($caf) {
                $caf->update(['status' => 'Assessment']);
                
                // Send email to the applicant
                if ($caf->email) {
                    try {
                        Mail::to($caf->email)->send(new AssessmentProceedingMail($caf->name, $caf->position));
                    } catch (\Exception $e) {
                        // Log error but don't fail the request
                        \Log::error("Failed to send assessment mail to {$caf->email}: " . $e->getMessage());
                    }
                }
            }
        }

        return response()->json(['success' => true, 'data' => $assessment]);
    }

    public function updateAssessmentStatus(Request $request, $id)
    {
        $assessment = CandidateAssessment::findOrFail($id);
        $assessment->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function storeInterview(Request $request)
    {
        try {
            $interview = CandidateInterview::create([
                'name' => $request->name,
                'position' => $request->position,
                'type' => $request->type,
                'round' => $request->type, // For backward compatibility
                'interviewer' => $request->interviewer,
                'interview_date' => $request->interview_date,
                'duration' => $request->duration,
                'meeting_link' => $request->meeting_link,
                'status' => 'Scheduled'
            ]);

            return response()->json(['success' => true, 'data' => $interview]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteInterview($id)
    {
        CandidateInterview::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function deleteCAF($id)
    {
        CandidateApplication::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function deleteAssessment($id)
    {
        CandidateAssessment::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function deleteMRF($id)
    {
        ManpowerRequest::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function deleteJPF($id)
    {
        JobPosting::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
    public function storeJobOffer(Request $request)
    {
        try {
            $jobOffer = JobOffer::create([
                'name' => $request->name,
                'position' => $request->position,
                'salary' => $request->salary,
                'start_date' => $request->startDate,
                'employment_type' => $request->employmentType,
                'department' => $request->department,
                'benefits' => $request->benefits,
                'status' => 'Pending'
            ]);

            return response()->json(['success' => true, 'data' => $jobOffer]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteJobOffer($id)
    {
        JobOffer::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function sendAssessmentTest(Request $request, $id)
    {
        $assessment = CandidateAssessment::findOrFail($id);
        
        // Update the test type if provided
        if ($request->has('test_type')) {
            $assessment->update(['test_type' => $request->test_type]);
        }

        // Update the email if provided (in case it was missing or corrected in the UI)
        if ($request->has('email')) {
            $assessment->update(['email' => $request->email]);
        }

        // Generate internal tracking URL
        $testUrl = route('recruitment.assessment.start', ['uuid' => $assessment->uuid]);

        if ($assessment->email) {
            try {
                Mail::to($assessment->email)->send(new AssessmentTestMail($assessment->name, $assessment->test_type, $testUrl));
                
                // Status remains "Pending Assessment" until they click the link
                
                return response()->json(['success' => true, 'message' => 'Test invitation sent', 'assessment' => $assessment]);
            } catch (\Exception $e) {
                \Log::error("Failed to send assessment test to {$assessment->email}: " . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email'], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'Candidate email not found'], 400);
    }

    public function startAssessment($uuid)
    {
        $assessment = CandidateAssessment::where('uuid', $uuid)->firstOrFail();
        
        // Move to In Progress when applicant clicks the link
        if ($assessment->status === 'Pending Assessment') {
            $assessment->update(['status' => 'In Progress']);
        }

        // Redirect to a dummy test interface or the actual platform
        $testUrls = [
            'Technical Test' => 'https://example.com/tests/technical',
            'Amplitude Test' => 'https://example.com/tests/amplitude',
            'Personality Test' => 'https://example.com/tests/personality',
        ];

        $redirectUrl = $testUrls[$assessment->test_type] ?? $testUrls['Technical Test'];

        return redirect()->away($redirectUrl);
    }

    public function updateAssessmentResult(Request $request, $id)
    {
        $assessment = CandidateAssessment::findOrFail($id);
        $request->validate([
            'score' => 'required|numeric|min:0|max:100'
        ]);

        $score = $request->score;
        $status = ($score >= 75) ? 'Passed' : 'Failed';

        $assessment->update([
            'score' => $score . '%',
            'status' => $status,
            'assessment_date' => date('Y-m-d') // Record the date of completion
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assessment result recorded.',
            'assessment' => $assessment
        ]);
    }
}
