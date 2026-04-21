<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\CorrespondenceController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\BankingController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\OrganizationalController;
use App\Http\Controllers\RecruitmentController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/careers/apply', [RecruitmentController::class, 'showPublicApplicationForm'])->name('careers.apply');
Route::post('/careers/apply', [RecruitmentController::class, 'storeCAF'])->name('careers.apply.submit');

Route::middleware('auth')->group(function () {
    Route::get('/permits', [PermitController::class, 'index']);
    Route::post('/permits', [PermitController::class, 'store']);

    Route::get('/permits/template/mayors-permit/{id}', [PermitController::class, 'showMayorPermitTemplate'])->name('permits.template.mayors-permit');
    Route::get('/permits/template/barangay-business-permit/{id}', [PermitController::class, 'showBarangayBusinessPermitTemplate'])->name('permits.template.barangay-business-permit');
    Route::get('/permits/template/fire-permit/{id}', [PermitController::class, 'showFirePermitTemplate'])->name('permits.template.fire-permit');
    Route::get('/permits/template/sanitary-permit/{id}', [PermitController::class, 'showSanitaryPermitTemplate'])->name('permits.template.sanitary-permit');
    Route::get('/permits/template/obo-permit/{id}', [PermitController::class, 'showOboPermitTemplate'])->name('permits.template.obo-permit');

    Route::get('/correspondence/draft-preview/{slug}', [CorrespondenceController::class, 'showDraftPreview'])->name('correspondence.draft-preview');
    Route::get('/correspondence/template/{slug}/{id}', [CorrespondenceController::class, 'showTemplate'])->name('correspondence.template');
    Route::get('/correspondence/data', [CorrespondenceController::class, 'index'])->name('correspondence.data');
    Route::post('/correspondence', [CorrespondenceController::class, 'store'])->name('correspondence.store');

    Route::get('/legal/data', [LegalController::class, 'index'])->name('legal.index');
    Route::post('/legal/store', [LegalController::class, 'store'])->name('legal.store');

    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::post('/accounting', [AccountingController::class, 'store'])->name('accounting.store');

    Route::get('/banking/data', [BankingController::class, 'index'])->name('banking.index');
    Route::post('/banking/store', [BankingController::class, 'store'])->name('banking.store');

    Route::get('/operations/data', [OperationController::class, 'index'])->name('operations.index');
    Route::post('/operations/store', [OperationController::class, 'store'])->name('operations.store');

    Route::get('/corporate', function () {
        return view('corporate.company-general-information');
    })->name('corporate');

    Route::get('/lgu', function () {
        return view('corporate.lgu');
    })->name('lgu');

    Route::get('/accounting-page', function () {
        return view('corporate.accounting');
    })->name('accounting');

    Route::get('/banking', function () {
        return view('corporate.banking');
    })->name('banking');

    Route::get('/legal', function () {
        return view('corporate.legal');
    })->name('legal');

    Route::get('/operations', function () {
        return view('corporate.operations');
    })->name('operations');

    Route::get('/correspondence', function () {
        return view('corporate.correspondence');
    })->name('correspondence');

    /*
    |--------------------------------------------------------------------------
    | HUMAN CAPITAL
    |--------------------------------------------------------------------------
    */

    Route::prefix('human-capital')->name('human-capital.')->group(function () {
        Route::view('/', 'human-capital')->name('dashboard');

        Route::get('/organizational', [OrganizationalController::class, 'index'])->name('organizational');
        Route::post('/organizational', [OrganizationalController::class, 'store'])->name('organizational.store');

        Route::view('/payroll', 'human-capital.payroll')->name('payroll');
        Route::view('/employee-profile', 'human-capital.employee-profile')->name('employee-profile');
        Route::get('/recruitment', [RecruitmentController::class, 'index'])->name('recruitment');
        Route::post('/recruitment/mrf', [RecruitmentController::class, 'storeMRF'])->name('recruitment.store_mrf');
        Route::put('/recruitment/mrf/{id}', [RecruitmentController::class, 'updateMRF'])->name('recruitment.update_mrf');
        Route::post('/recruitment/jpf', [RecruitmentController::class, 'storeJPF'])->name('recruitment.store_jpf');
        Route::put('/recruitment/jpf/{id}', [RecruitmentController::class, 'updateJPF'])->name('recruitment.update_jpf');
        Route::post('/recruitment/caf', [RecruitmentController::class, 'storeCAF'])->name('recruitment.store_caf');
        Route::put('/recruitment/caf/{id}', [RecruitmentController::class, 'updateCAF'])->name('recruitment.update_caf');
        Route::post('/recruitment/assessment', [RecruitmentController::class, 'storeAssessment'])->name('recruitment.store_assessment');
        Route::post('/recruitment/assessment/{id}/status', [RecruitmentController::class, 'updateAssessmentStatus'])->name('recruitment.update_assessment_status');
        Route::delete('/recruitment/assessment/{id}', [RecruitmentController::class, 'deleteAssessment'])->name('recruitment.delete_assessment');
        Route::post('/recruitment/interview', [RecruitmentController::class, 'storeInterview'])->name('recruitment.store_interview');
        Route::delete('/recruitment/interview/{id}', [RecruitmentController::class, 'deleteInterview'])->name('recruitment.delete_interview');
        Route::post('/recruitment/job-offer', [RecruitmentController::class, 'storeJobOffer'])->name('recruitment.store_job_offer');
        Route::delete('/recruitment/job-offer/{id}', [RecruitmentController::class, 'deleteJobOffer'])->name('recruitment.delete_job_offer');
        Route::delete('/recruitment/caf/{id}', [RecruitmentController::class, 'deleteCAF'])->name('recruitment.delete_caf');
        Route::delete('/recruitment/mrf/{id}', [RecruitmentController::class, 'deleteMRF'])->name('recruitment.delete_mrf');
        Route::delete('/recruitment/jpf/{id}', [RecruitmentController::class, 'deleteJPF'])->name('recruitment.delete_jpf');
        Route::view('/onboarding', 'human-capital.onboarding')->name('onboarding');
        Route::view('/deployment', 'human-capital.deployment')->name('deployment');
        Route::view('/attendance', 'human-capital.attendance')->name('attendance');
        Route::view('/employee-requests', 'human-capital.employee-requests')->name('employee-requests');
        Route::view('/employee-relations', 'human-capital.employee-relations')->name('employee-relations');
        Route::view('/training', 'human-capital.training')->name('training');
        Route::view('/performance', 'human-capital.performance')->name('performance');
        Route::view('/offboarding', 'human-capital.offboarding')->name('offboarding');
    });
});