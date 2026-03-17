<?php

use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('corporate.company-general-information');
});

Route::get('/contacts', [ContactsController::class, 'index'])->name('contacts.index');
Route::post('/contacts', [ContactsController::class, 'store'])->name('contacts.store');
Route::post('/contacts/custom-fields', [ContactsController::class, 'storeCustomField'])->name('contacts.custom-fields.store');
Route::post('/contacts/{contact}/cif', [ContactsController::class, 'saveCif'])->name('contacts.cif.save');
Route::post('/contacts/{contact}/cif/documents', [ContactsController::class, 'uploadCifDocument'])->name('contacts.cif.documents.upload');
Route::get('/contacts/{contact}/cif/preview', [ContactsController::class, 'previewCif'])->name('contacts.cif.preview');
Route::get('/contacts/{contact}/cif/download', [ContactsController::class, 'downloadCif'])->name('contacts.cif.download');
Route::get('/contacts/{contact}', [ContactsController::class, 'show'])->name('contacts.show');

Route::get('/deals', [DealController::class, 'index'])->name('deals.index');
Route::post('/deals/preview', [DealController::class, 'preview'])->name('deals.preview');
Route::get('/deals/preview', [DealController::class, 'previewPage'])->name('deals.preview.show');
Route::post('/deals/draft', [DealController::class, 'saveDraft'])->name('deals.draft');
Route::post('/deals', [DealController::class, 'store'])->name('deals.store');
Route::get('/deals/{id}', [DealController::class, 'show'])->name('deals.show');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::post('/products/change-owner', [ProductController::class, 'changeOwner'])->name('products.change-owner');
Route::post('/products/custom-fields', [ProductController::class, 'storeCustomField'])->name('products.custom-fields.store');
