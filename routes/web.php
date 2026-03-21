<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\CorrespondenceController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login',[LoginController::class,'showLoginForm'])->name('login');
Route::post('/login',[LoginController::class,'login'])->name('login.post');

Route::get('/register',[RegisterController::class,'show'])->name('register');
Route::post('/register',[RegisterController::class,'submit'])->name('register.post');

Route::post('/logout',[LoginController::class,'logout'])->name('logout');
Route::get('/permits/{permitType}', [PermitController::class, 'index']);
Route::post('/permits', [PermitController::class, 'store']);
Route::get('/correspondence/{type}', [CorrespondenceController::class, 'index']);
Route::post('/correspondence', [CorrespondenceController::class, 'store']);

Route::middleware('auth')->group(function () {

    Route::get('/corporate',function(){
        return view('corporate.company-general-information');
    })->name('corporate');

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