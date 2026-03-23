<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TownHallController;
use App\Http\Controllers\GisController;
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
    Route::get('/townhall/department', [TownHallController::class, 'department'])
        ->name('townhall.department');
    Route::get('/townhall/attachments', [TownHallController::class, 'attachments'])
        ->name('townhall.attachments');
    Route::post('/townhall', [TownHallController::class, 'store'])->name('townhall.store');
    Route::get('/townhall/{id}/edit', [TownHallController::class, 'edit'])->name('townhall.edit');
    Route::put('/townhall/{id}', [TownHallController::class, 'update'])->name('townhall.update');
    Route::get('/townhall/{id}', [TownHallController::class, 'show'])->name('townhall.show');
    Route::get('/townhall/{id}/download-pdf', [TownHallController::class, 'downloadPdf'])
        ->name('townhall.download.pdf');


    Route::post('/townhall/{id}/approve', [TownHallController::class, 'approve'])->name('townhall.approve');
    Route::post('/townhall/{id}/reject', [TownHallController::class, 'reject'])->name('townhall.reject');
    Route::post('/townhall/{id}/revise', [TownHallController::class, 'revise'])->name('townhall.revise');
    Route::post('/townhall/{id}/acknowledge', [TownHallController::class, 'acknowledge'])
        ->name('townhall.acknowledge');


    /*
    |--------------------------------------------------------------------------
    | CORPORATE MODULE
    |--------------------------------------------------------------------------
    */

    // COMPANY GENERAL INFORMATION
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


    /*
    |--------------------------------------------------------------------------
    | CORPORATE DOCUMENTS
    |--------------------------------------------------------------------------
    */

    Route::get('/corporate/formation', function () {
        return view('corporate.corporate-formation');
    })->name('corporate.formation');

    Route::get('/corporate/sec-aoi', function () {
        return view('corporate.sec-aoi');
    })->name('corporate.sec_aoi');

    Route::get('/corporate/bylaws', function () {
        return view('corporate.bylaws');
    })->name('corporate.bylaws');


    /*
    |--------------------------------------------------------------------------
    | GIS SUBMODULES
    |--------------------------------------------------------------------------
    */

    Route::get('/corporate/gis/capital-structure', [GisController::class, 'capitalStructure'])
        ->name('gis.capital');

    Route::get('/corporate/gis/directors-officers', [GisController::class, 'directorsOfficers'])
        ->name('gis.directors');

    Route::get('/corporate/gis/stockholders', [GisController::class, 'stockholders'])
        ->name('gis.stockholders');
});
