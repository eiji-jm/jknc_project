<?php

use App\Http\Controllers\ContactsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('corporate.company-general-information');
});

Route::get('/contacts', [ContactsController::class, 'index'])->name('contacts.index');
Route::post('/contacts', [ContactsController::class, 'store'])->name('contacts.store');
Route::get('/contacts/{contact}', [ContactsController::class, 'show'])->name('contacts.show');
