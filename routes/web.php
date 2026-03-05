<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UboController;

Route::get('/', [UboController::class, 'index'])->name('ubo.index');
Route::resource('ubo', UboController::class);
