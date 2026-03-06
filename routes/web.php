<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('corporate.company-general-information');
});

Route::get('/activities', function () {
    return view('activities.index');
})->name('activities');