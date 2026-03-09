<?php

use Illuminate\Support\Facades\Route;

Route::get('/townhall', function () {
    return view('townhall');
})->name('townhall');
