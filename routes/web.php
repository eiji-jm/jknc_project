<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyCifController;
use App\Http\Controllers\CompanyServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GisController;
use App\Http\Controllers\CorporateFormationController;
use App\Http\Controllers\SecAoiController;
use App\Http\Controllers\BylawController;
use App\Http\Controllers\CapitalStructureController;
use App\Http\Controllers\DirectorOfficerController;
use App\Http\Controllers\StockholderController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login',[LoginController::class,'showLoginForm'])->name('login');
Route::post('/login',[LoginController::class,'login'])->name('login.post');

Route::get('/register',[RegisterController::class,'show'])->name('register');
Route::post('/register',[RegisterController::class,'submit'])->name('register.post');

Route::post('/logout',[LoginController::class,'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/company/{company}', [CompanyController::class, 'show'])->name('company.show');
    Route::get('/company/{company}/kyc', [CompanyController::class, 'show'])->name('company.kyc');
    Route::get('/company/{company}/history', [CompanyController::class, 'history'])->name('company.history');
    Route::get('/company/{company}/consultation-notes', [CompanyController::class, 'consultationNotes'])->name('company.consultation-notes');
    Route::get('/company/{company}/activities', [CompanyController::class, 'activities'])->name('company.activities');
    Route::get('/company/{company}/deals', [CompanyController::class, 'deals'])->name('company.deals');
    Route::get('/company/{company}/contacts', [CompanyController::class, 'contacts'])->name('company.contacts');
    Route::post('/company/{company}/contacts', [CompanyController::class, 'storeContact'])->name('company.contacts.store');
    Route::post('/company/{company}/contacts/custom-fields', [CompanyController::class, 'storeContactCustomField'])->name('company.contacts.custom-fields.store');
    Route::match(['put', 'patch'], '/company/{company}/contacts/{contact}', [CompanyController::class, 'updateContact'])->name('company.contacts.update');
    Route::delete('/company/{company}/contacts/{contact}', [CompanyController::class, 'destroyContact'])->name('company.contacts.destroy');
    Route::get('/company/{company}/projects', [CompanyController::class, 'projects'])->name('company.projects');
    Route::get('/company/{company}/regular', [CompanyController::class, 'regular'])->name('company.regular');
    Route::get('/company/{company}/products', [CompanyController::class, 'products'])->name('company.products');
    Route::get('/company/{company}/kyc/cif/create', [CompanyCifController::class, 'create'])->name('company.cif.create');
    Route::post('/company/{company}/kyc/cif', [CompanyCifController::class, 'store'])->name('company.cif.store');
    Route::get('/company/{company}/kyc/cif/{cif}', [CompanyCifController::class, 'show'])->name('company.cif.show');
    Route::get('/company/{company}/kyc/cif/{cif}/edit', [CompanyCifController::class, 'edit'])->name('company.cif.edit');
    Route::match(['put', 'patch'], '/company/{company}/kyc/cif/{cif}', [CompanyCifController::class, 'update'])->name('company.cif.update');
    Route::get('/company/{company}/services', [CompanyServiceController::class, 'index'])->name('company.services.index');
    Route::post('/company/{company}/services/link', [CompanyServiceController::class, 'link'])->name('company.services.link');
    Route::get('/company/{company}/services/{service}', [CompanyServiceController::class, 'show'])->name('company.services.show');

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

    Route::get('/corporate/sec-aoi', [SecAoiController::class,'index'])
    ->name('corporate.sec_aoi');

Route::post('/corporate/sec-aoi/store', [SecAoiController::class,'store'])
    ->name('corporate.sec_aoi.store');

Route::get('/corporate/sec-aoi/{id}', [SecAoiController::class,'show'])
    ->name('corporate.sec_aoi.show');

    Route::get('/corporate/bylaws',[BylawController::class,'index'])->name('corporate.bylaws');

Route::post('/corporate/bylaws/store',[BylawController::class,'store'])->name('corporate.bylaws.store');

Route::get('/corporate/bylaws/{id}',[BylawController::class,'show'])->name('corporate.bylaws.show');

Route::get('/corporate/gis/{id}/show',[GisController::class,'show'])
->name('gis.show');

Route::post('/gis/authorized/store',[CapitalStructureController::class,'storeAuthorized'])->name('authorized.store');

Route::post('/gis/subscribed/store',[CapitalStructureController::class,'storeSubscribed'])->name('subscribed.store');

Route::post('/gis/paidup/store',[CapitalStructureController::class,'storePaidup'])->name('paidup.store');

Route::post('/gis/director/store',[DirectorOfficerController::class,'store'])->name('director.store');

Route::post('/gis/stockholder/store',[StockholderController::class,'store'])->name('stockholder.store');
});
