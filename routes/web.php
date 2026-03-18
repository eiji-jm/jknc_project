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
use App\Http\Controllers\CorporateApprovalController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AdminUserPermissionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/role-permissions', [RolePermissionController::class, 'index'])->name('admin.role-permissions');
    Route::post('/admin/role-permissions/{id}', [RolePermissionController::class, 'update'])->name('admin.role-permissions.update');
    Route::get('/admin/user-permissions', [AdminUserPermissionController::class, 'index'])->name('admin.user-permissions');
    Route::get('/admin/user-permissions/{id}', [AdminUserPermissionController::class, 'edit'])->name('admin.user-permissions.edit');
    Route::post('/admin/user-permissions/{id}', [AdminUserPermissionController::class, 'update'])->name('admin.user-permissions.update');

    /*
    |--------------------------------------------------------------------------
    | TOWN HALL
    |--------------------------------------------------------------------------
    */
    Route::get('/townhall', [TownHallController::class, 'index'])->name('townhall');
    Route::post('/townhall', [TownHallController::class, 'store'])->name('townhall.store');
    Route::get('/townhall/{id}', [TownHallController::class, 'show'])->name('townhall.show');
    Route::post('/townhall/{id}/approve', [TownHallController::class, 'approve'])->name('townhall.approve');
    Route::post('/townhall/{id}/reject', [TownHallController::class, 'reject'])->name('townhall.reject');
    Route::post('/townhall/{id}/revise', [TownHallController::class, 'revise'])->name('townhall.revise');

    /*
    |--------------------------------------------------------------------------
    | CORPORATE MODULE
    |--------------------------------------------------------------------------
    */
    Route::get('/corporate', [GisController::class, 'companyInfo'])->name('corporate');

    Route::get('/corporate/company-general-information', [GisController::class, 'companyInfo'])
        ->name('corporate.companyinfo');

    /*
    |--------------------------------------------------------------------------
    | GIS
    |--------------------------------------------------------------------------
    */
    Route::get('/corporate/gis', [GisController::class, 'index'])->name('corporate.gis');
    Route::post('/corporate/gis/store', [GisController::class, 'store'])->name('gis.store');
    Route::get('/corporate/gis/{id}/show', [GisController::class, 'show'])->name('gis.show');

    Route::get('/corporate/gis/{id}/company-info', [GisController::class, 'companyInfoById'])
        ->name('gis.company.info');

    Route::put('/gis/company-info/{id}', [GisController::class, 'updateCompanyInfo'])
        ->name('gis.company.update');

    Route::get('/corporate/gis/capital-structure', [GisController::class, 'capitalStructure'])
        ->name('gis.capital');

    Route::get('/corporate/gis/directors-officers', [GisController::class, 'directorsOfficers'])
        ->name('gis.directors');

    Route::get('/corporate/gis/stockholders', [GisController::class, 'stockholders'])
        ->name('gis.stockholders');

    Route::post('/gis/authorized/store', [CapitalStructureController::class, 'storeAuthorized'])->name('authorized.store');
    Route::post('/gis/subscribed/store', [CapitalStructureController::class, 'storeSubscribed'])->name('subscribed.store');
    Route::post('/gis/paidup/store', [CapitalStructureController::class, 'storePaidup'])->name('paidup.store');
    Route::post('/gis/director/store', [DirectorOfficerController::class, 'store'])->name('director.store');
    Route::post('/gis/stockholder/store', [StockholderController::class, 'store'])->name('stockholder.store');

    Route::post('/corporate/gis/{id}/upload-draft-file', [GisController::class, 'uploadDraftFile'])
    ->name('corporate.gis.upload.draft');

    Route::post('/corporate/gis/{id}/upload-notary-file', [GisController::class, 'uploadNotaryFile'])
    ->name('corporate.gis.upload.notary');
    /*
    |--------------------------------------------------------------------------
    | CORPORATE DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/corporate/formation', [CorporateFormationController::class, 'index'])->name('corporate.formation');
    Route::post('/corporate/formation/store', [CorporateFormationController::class, 'store'])->name('corporate.formation.store');
    Route::get('/corporate/formation/{id}', [CorporateFormationController::class, 'show'])->name('corporate.formation.show');
    Route::post('/corporate/formation/{id}/upload-draft-file', [CorporateFormationController::class, 'uploadDraftFile'])
    ->name('corporate.formation.upload.draft');

    Route::post('/corporate/formation/{id}/upload-notary-file', [CorporateFormationController::class, 'uploadNotaryFile'])
    ->name('corporate.formation.upload.notary');

    Route::get('/corporate/sec-aoi', [SecAoiController::class, 'index'])->name('corporate.sec_aoi');
    Route::post('/corporate/sec-aoi/store', [SecAoiController::class, 'store'])->name('corporate.sec_aoi.store');
    Route::get('/corporate/sec-aoi/{id}', [SecAoiController::class, 'show'])->name('corporate.sec_aoi.show');
    Route::post('/corporate/sec-aoi/{id}/upload-draft-file', [SecAoiController::class, 'uploadDraftFile'])
    ->name('corporate.sec_aoi.upload.draft');
    Route::post('/corporate/sec-aoi/{id}/upload-notary-file', [SecAoiController::class, 'uploadNotaryFile'])
    ->name('corporate.sec_aoi.upload.notary');

    Route::get('/corporate/bylaws', [BylawController::class, 'index'])->name('corporate.bylaws');
    Route::post('/corporate/bylaws/store', [BylawController::class, 'store'])->name('corporate.bylaws.store');
    Route::get('/corporate/bylaws/{id}', [BylawController::class, 'show'])->name('corporate.bylaws.show');
    Route::post('/corporate/bylaws/{id}/upload-draft-file', [BylawController::class, 'uploadDraftFile'])
    ->name('corporate.bylaws.upload.draft');

    Route::post('/corporate/bylaws/{id}/upload-notary-file', [BylawController::class, 'uploadNotaryFile'])
    ->name('corporate.bylaws.upload.notary');

    /*
    |--------------------------------------------------------------------------
    | CORPORATE APPROVAL DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/corporate-dashboard', [CorporateApprovalController::class, 'dashboard'])
        ->name('admin.corporate.dashboard');

    Route::post('/admin/corporate-approvals/{module}/{id}/approve', [CorporateApprovalController::class, 'approve'])
        ->name('corporate.approvals.approve');

    Route::post('/admin/corporate-approvals/{module}/{id}/reject', [CorporateApprovalController::class, 'reject'])
        ->name('corporate.approvals.reject');

    Route::post('/admin/corporate-approvals/{module}/{id}/revise', [CorporateApprovalController::class, 'revise'])
        ->name('corporate.approvals.revise');

    Route::get('/admin/corporate-dashboard', [CorporateApprovalController::class, 'dashboard'])
    ->name('admin.corporate.dashboard');
});