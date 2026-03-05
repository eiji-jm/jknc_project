<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('corporate.company-general-information');
});

Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
Route::get('/company/{company}', [CompanyController::class, 'show'])->name('company.show');
