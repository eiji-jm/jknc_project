<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\CorrespondenceController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\BankingController;
use App\Http\Controllers\OperationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/permits', [PermitController::class, 'index']);
    Route::post('/permits', [PermitController::class, 'store']);

    Route::get('/permits/template/mayors-permit/{id}', [PermitController::class, 'showMayorPermitTemplate'])->name('permits.template.mayors-permit');
    Route::get('/permits/template/barangay-business-permit/{id}', [PermitController::class, 'showBarangayBusinessPermitTemplate'])->name('permits.template.barangay-business-permit');
    Route::get('/permits/template/fire-permit/{id}', [PermitController::class, 'showFirePermitTemplate'])->name('permits.template.fire-permit');
    Route::get('/permits/template/sanitary-permit/{id}', [PermitController::class, 'showSanitaryPermitTemplate'])->name('permits.template.sanitary-permit');
    Route::get('/permits/template/obo-permit/{id}', [PermitController::class, 'showOboPermitTemplate'])->name('permits.template.obo-permit');

    Route::get('/correspondence/{type}', [CorrespondenceController::class, 'index']);
    Route::post('/correspondence', [CorrespondenceController::class, 'store']);

    Route::get('/correspondence/template/letters/{id}', [CorrespondenceController::class, 'showLettersTemplate'])->name('correspondence.template.letters');
    Route::get('/correspondence/template/demand-letter/{id}', [CorrespondenceController::class, 'showDemandLetterTemplate'])->name('correspondence.template.demand-letter');
    Route::get('/correspondence/template/request-letter/{id}', [CorrespondenceController::class, 'showRequestLetterTemplate'])->name('correspondence.template.request-letter');
    Route::get('/correspondence/template/follow-up-letter/{id}', [CorrespondenceController::class, 'showFollowUpLetterTemplate'])->name('correspondence.template.follow-up-letter');
    Route::get('/correspondence/template/memo/{id}', [CorrespondenceController::class, 'showMemoTemplate'])->name('correspondence.template.memo');
    Route::get('/correspondence/template/notice/{id}', [CorrespondenceController::class, 'showNoticeTemplate'])->name('correspondence.template.notice');

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

    Route::get('/operations', function () {
        return view('corporate.operations');
    })->name('operations');

    Route::get('/correspondence', function () {
        return view('corporate.correspondence');
    })->name('correspondence');
});