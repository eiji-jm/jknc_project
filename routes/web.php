<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TownHallController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\CorporateFormationController;
use App\Http\Controllers\SecAoiController;
use App\Http\Controllers\BylawController;
use App\Http\Controllers\CapitalStructureController;
use App\Http\Controllers\DirectorOfficerController;
use App\Http\Controllers\StockholderController;
use App\Http\Controllers\UltimateBeneficialOwnerController;
use App\Http\Controllers\CorporateApprovalController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AdminUserPermissionController;
use App\Http\Controllers\StockTransferBookController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\CorrespondenceController;

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

    Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/role-permissions', [RolePermissionController::class, 'index'])->name('admin.role-permissions');
    Route::post('/admin/role-permissions/{id}', [RolePermissionController::class, 'update'])->name('admin.role-permissions.update');
    Route::get('/admin/user-permissions', [AdminUserPermissionController::class, 'index'])->name('admin.user-permissions');
    Route::get('/admin/user-permissions/{id}', [AdminUserPermissionController::class, 'edit'])->name('admin.user-permissions.edit');
    Route::post('/admin/user-permissions/{id}', [AdminUserPermissionController::class, 'update'])->name('admin.user-permissions.update');

    Route::get('/townhall', [TownHallController::class, 'index'])->name('townhall');
    Route::post('/townhall', [TownHallController::class, 'store'])->name('townhall.store');
    Route::get('/townhall/{id}', [TownHallController::class, 'show'])->name('townhall.show');
    Route::post('/townhall/{id}/approve', [TownHallController::class, 'approve'])->name('townhall.approve');
    Route::post('/townhall/{id}/reject', [TownHallController::class, 'reject'])->name('townhall.reject');
    Route::post('/townhall/{id}/revise', [TownHallController::class, 'revise'])->name('townhall.revise');

    Route::get('/corporate', [GisController::class, 'companyInfo'])->name('corporate');
    Route::get('/corporate/company-general-information', [GisController::class, 'companyInfo'])->name('corporate.companyinfo');

    Route::get('/corporate/gis', [GisController::class, 'index'])->name('corporate.gis');
    Route::post('/corporate/gis/store', [GisController::class, 'store'])->name('gis.store');
    Route::get('/corporate/gis/{id}/show', [GisController::class, 'show'])->name('gis.show');
    Route::get('/corporate/gis/{id}/company-info', [GisController::class, 'companyInfoById'])->name('gis.company.info');
    Route::put('/gis/company-info/{id}', [GisController::class, 'updateCompanyInfo'])->name('gis.company.update');
    Route::get('/corporate/gis/capital-structure', [GisController::class, 'capitalStructure'])->name('gis.capital');
    Route::get('/corporate/gis/directors-officers', [GisController::class, 'directorsOfficers'])->name('gis.directors');
    Route::get('/corporate/gis/stockholders', [GisController::class, 'stockholders'])->name('gis.stockholders');

    Route::post('/gis/authorized/store', [CapitalStructureController::class, 'storeAuthorized'])->name('authorized.store');
    Route::post('/gis/subscribed/store', [CapitalStructureController::class, 'storeSubscribed'])->name('subscribed.store');
    Route::post('/gis/paidup/store', [CapitalStructureController::class, 'storePaidup'])->name('paidup.store');
    Route::post('/gis/director/store', [DirectorOfficerController::class, 'store'])->name('director.store');
    Route::post('/gis/stockholder/store', [StockholderController::class, 'store'])->name('stockholder.store');
    Route::post('/gis/ubo/store', [UltimateBeneficialOwnerController::class, 'store'])->name('ubo.store');

    Route::post('/corporate/gis/{id}/upload-draft-file', [GisController::class, 'uploadDraftFile'])->name('corporate.gis.upload.draft');
    Route::post('/corporate/gis/{id}/upload-notary-file', [GisController::class, 'uploadNotaryFile'])->name('corporate.gis.upload.notary');
    Route::post('/corporate/gis/{id}/submit', [GisController::class, 'submit'])->name('corporate.gis.submit');

    Route::get('/corporate/formation', [CorporateFormationController::class, 'index'])->name('corporate.formation');
    Route::post('/corporate/formation/store', [CorporateFormationController::class, 'store'])->name('corporate.formation.store');
    Route::get('/corporate/formation/{id}', [CorporateFormationController::class, 'show'])->name('corporate.formation.show');
    Route::post('/corporate/formation/{id}/upload-draft-file', [CorporateFormationController::class, 'uploadDraftFile'])->name('corporate.formation.upload.draft');
    Route::post('/corporate/formation/{id}/upload-notary-file', [CorporateFormationController::class, 'uploadNotaryFile'])->name('corporate.formation.upload.notary');
    Route::post('/corporate/formation/{id}/submit', [CorporateFormationController::class, 'submit'])->name('corporate.formation.submit');
    Route::put('/corporate/formation/{id}/update', [CorporateFormationController::class, 'update'])->name('corporate.formation.update');

    Route::post('/admin/corporate-approvals/{module}/{id}/archive', [CorporateApprovalController::class, 'archive'])->name('corporate.approvals.archive');

    Route::get('/corporate/sec-aoi', [SecAoiController::class, 'index'])->name('corporate.sec_aoi');
    Route::post('/corporate/sec-aoi/store', [SecAoiController::class, 'store'])->name('corporate.sec_aoi.store');
    Route::get('/corporate/sec-aoi/{id}', [SecAoiController::class, 'show'])->name('corporate.sec_aoi.show');
    Route::post('/corporate/sec-aoi/{id}/upload-draft-file', [SecAoiController::class, 'uploadDraftFile'])->name('corporate.sec_aoi.upload.draft');
    Route::post('/corporate/sec-aoi/{id}/upload-notary-file', [SecAoiController::class, 'uploadNotaryFile'])->name('corporate.sec_aoi.upload.notary');
    Route::post('/corporate/sec-aoi/{id}/submit', [SecAoiController::class, 'submit'])->name('corporate.sec_aoi.submit');

    Route::get('/corporate/bylaws', [BylawController::class, 'index'])->name('corporate.bylaws');
    Route::post('/corporate/bylaws/store', [BylawController::class, 'store'])->name('corporate.bylaws.store');
    Route::get('/corporate/bylaws/{id}', [BylawController::class, 'show'])->name('corporate.bylaws.show');
    Route::post('/corporate/bylaws/{id}/upload-draft-file', [BylawController::class, 'uploadDraftFile'])->name('corporate.bylaws.upload.draft');
    Route::post('/corporate/bylaws/{id}/upload-notary-file', [BylawController::class, 'uploadNotaryFile'])->name('corporate.bylaws.upload.notary');
    Route::post('/corporate/bylaws/{id}/submit', [BylawController::class, 'submit'])->name('corporate.bylaws.submit');

    Route::get('/admin/corporate-dashboard', [CorporateApprovalController::class, 'dashboard'])->name('admin.corporate.dashboard');
    Route::post('/admin/corporate-approvals/{module}/{id}/approve', [CorporateApprovalController::class, 'approve'])->name('corporate.approvals.approve');
    Route::post('/admin/corporate-approvals/{module}/{id}/reject', [CorporateApprovalController::class, 'reject'])->name('corporate.approvals.reject');
    Route::post('/admin/corporate-approvals/{module}/{id}/revise', [CorporateApprovalController::class, 'revise'])->name('corporate.approvals.revise');

    Route::get('/corporate/stock-transfer-book', [StockTransferBookController::class, 'index'])->name('stock-transfer-book');
    Route::get('/corporate/stock-transfer-book/index', [StockTransferBookController::class, 'index'])->name('stock-transfer-book.index');
    Route::post('/corporate/stock-transfer-book/index/store', [StockTransferBookController::class, 'storeIndex'])->name('stock-transfer-book.index.store');
    Route::get('/corporate/stock-transfer-book/index/lookup', [StockTransferBookController::class, 'lookupIndex'])->name('stock-transfer-book.index.lookup');
    Route::get('/corporate/stock-transfer-book/journal', [StockTransferBookController::class, 'journal'])->name('stock-transfer-book.journal');
    Route::get('/corporate/stock-transfer-book/ledger', [StockTransferBookController::class, 'ledger'])->name('stock-transfer-book.ledger');
    Route::get('/corporate/stock-transfer-book/installment', [StockTransferBookController::class, 'installment'])->name('stock-transfer-book.installment');
    Route::get('/corporate/stock-transfer-book/certificates', [StockTransferBookController::class, 'certificates'])->name('stock-transfer-book.certificates');
    Route::post('/corporate/stock-transfer-book/ledger/store', [StockTransferBookController::class, 'storeLedger'])->name('stock-transfer-book.ledger.store');
    Route::post('/corporate/stock-transfer-book/journal/store', [StockTransferBookController::class, 'storeJournal'])->name('stock-transfer-book.journal.store');
    Route::post('/corporate/stock-transfer-book/certificates/store', [StockTransferBookController::class, 'storeCertificate'])->name('stock-transfer-book.certificates.store');
    Route::post('/corporate/stock-transfer-book/certificates/request/store', [StockTransferBookController::class, 'storeIssuanceRequest'])->name('stock-transfer-book.certificates.request.store');
    Route::post('/corporate/stock-transfer-book/certificates/cancellation/store', [StockTransferBookController::class, 'storeCertificateCancellation'])->name('stock-transfer-book.certificates.cancellation.store');
    Route::put('/corporate/stock-transfer-book/certificates/voucher/{id}/update', [StockTransferBookController::class, 'updateCertificateVoucher'])->name('stock-transfer-book.certificates.voucher.update');
    Route::post('/corporate/stock-transfer-book/installment/store', [StockTransferBookController::class, 'storeInstallment'])->name('stock-transfer-book.installment.store');

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

    Route::get('/lgu', function () {
        return view('corporate.lgu');
    })->name('lgu');

    Route::get('/accounting', function () {
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