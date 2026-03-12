<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyCifController;
use App\Http\Controllers\CompanyServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('corporate.company-general-information');
});

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
