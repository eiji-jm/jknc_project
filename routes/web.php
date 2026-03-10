<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\CorporateFormationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login',[LoginController::class,'showLoginForm'])->name('login');
Route::post('/login',[LoginController::class,'login'])->name('login.post');

Route::get('/register',[RegisterController::class,'show'])->name('register');
Route::post('/register',[RegisterController::class,'submit'])->name('register.post');

Route::post('/logout',[LoginController::class,'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // COMPANY GENERAL INFORMATION
    Route::get('/corporate', [GisController::class,'companyInfo'])->name('corporate');

    Route::get('/corporate/company-general-information', [GisController::class,'companyInfo'])
        ->name('corporate.companyinfo');

    // GIS PAGE
    Route::get('/corporate/gis', [GisController::class,'index'])->name('corporate.gis');

    Route::post('/corporate/gis/store', [GisController::class,'store'])->name('gis.store');

    // OTHER CORPORATE PAGES
    Route::get('/corporate/formation', function () {
        return view('corporate.corporate-formation');
    })->name('corporate.formation');

    Route::get('/corporate/sec-aoi', function () {
        return view('corporate.sec-aoi');
    })->name('corporate.sec_aoi');

    Route::get('/corporate/bylaws', function () {
        return view('corporate.bylaws');
    })->name('corporate.bylaws');

    Route::get('/corporate/gis/capital-structure', [GisController::class,'capitalStructure'])
    ->name('gis.capital');

    Route::get('/corporate/gis/directors-officers', [GisController::class,'directorsOfficers'])
    ->name('gis.directors');

    Route::get('/corporate/gis/stockholders', [GisController::class,'stockholders'])
    ->name('gis.stockholders');

    Route::get('/corporate/formation',[CorporateFormationController::class,'index'])->name('corporate.formation');

    Route::post('/corporate/formation/store',[CorporateFormationController::class,'store'])->name('corporate.formation.store');

    Route::get('/corporate/formation', [CorporateFormationController::class, 'index'])
    ->name('corporate.formation');

Route::post('/corporate/formation/store', [CorporateFormationController::class, 'store'])
    ->name('corporate.formation.store');

Route::get('/corporate/formation/{id}', [CorporateFormationController::class, 'show'])
    ->name('corporate.formation.show');
});