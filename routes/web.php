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

Route::get('/notices/preview', function () {
    return view('corporate.notices.preview');
})->name('notices.preview');

// Minutes of Meeting
Route::get('/minutes', function () {
    return view('corporate.minutes.index');
})->name('minutes');

Route::get('/minutes/preview', function () {
    return view('corporate.minutes.preview');
})->name('minutes.preview');

// Resolutions
Route::get('/resolutions', function () {
    return view('corporate.resolutions.index');
})->name('resolutions');

Route::get('/resolutions/preview', function () {
    return view('corporate.resolutions.preview');
})->name('resolutions.preview');

// Secretary Certificates
Route::get('/secretary-certificates', function () {
    return view('corporate.secretary-certificates.index');
})->name('secretary-certificates');

Route::get('/secretary-certificates/preview', function () {
    return view('corporate.secretary-certificates.preview');
})->name('secretary-certificates.preview');

// BIR & Tax
Route::get('/bir-tax', function () {
    return view('corporate.bir-tax.index');
})->name('bir-tax');

Route::get('/bir-tax/preview', function () {
    return view('corporate.bir-tax.preview');
})->name('bir-tax.preview');

// NatGov
Route::get('/natgov', function () {
    return view('corporate.natgov.index');
})->name('natgov');

Route::get('/natgov/preview', function () {
    return view('corporate.natgov.preview');
})->name('natgov.preview');
