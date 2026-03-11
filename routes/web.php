<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login',[LoginController::class,'showLoginForm'])->name('login');
Route::post('/login',[LoginController::class,'login'])->name('login.post');

Route::get('/register',[RegisterController::class,'show'])->name('register');
Route::post('/register',[RegisterController::class,'submit'])->name('register.post');

Route::post('/logout',[LoginController::class,'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/corporate',function(){
        return view('corporate.company-general-information');
    })->name('corporate');

    // Ultimate Beneficial Owner static form
    Route::get('/corporate/ubo', function () {
        return view('corporate.ubo-form');
    })->name('corporate.ubo');

    // Contacts list
    Route::get('/contacts', function () {
        return view('corporate.contacts-list');
    })->name('contacts');

    // Stock and Transfer Book sections
    Route::get('/stock-transfer-book', function () {
        return redirect()->route('stock-transfer-book.journal');
    })->name('stock-transfer-book');

    Route::get('/stock-transfer-book/ledger', function () {
        return view('corporate.stock-transfer-book.ledger');
    })->name('stock-transfer-book.ledger');

    Route::get('/stock-transfer-book/journal', function () {
        return view('corporate.stock-transfer-book.journal');
    })->name('stock-transfer-book.journal');

    Route::get('/stock-transfer-book/index', function () {
        return view('corporate.stock-transfer-book.stb-index');
    })->name('stock-transfer-book.index');

    Route::get('/stock-transfer-book/installment', function () {
        return view('corporate.stock-transfer-book.installment');
    })->name('stock-transfer-book.installment');

    Route::get('/stock-transfer-book/certificates', function () {
        return view('corporate.stock-transfer-book.certificates');
    })->name('stock-transfer-book.certificates');

});

// Notices of Meeting
Route::get('/notices', function () {
    return view('corporate.notices.index');
})->name('notices');

// Minutes of Meeting
Route::get('/minutes', function () {
    return view('corporate.minutes.index');
})->name('minutes');

Route::get('/minutes/preview', function () {
    return view('corporate.minutes.preview');
})->name('minutes.preview');
