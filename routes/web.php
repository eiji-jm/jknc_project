<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('corporate.company-general-information');
});

use App\Http\Controllers\ActivityController;

Route::get('/activities', function () {
    return view('activities.index');
})->name('activities');

Route::prefix('api')->group(function () {
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::post('/tasks', [ActivityController::class, 'storeTask']);
    Route::post('/events', [ActivityController::class, 'storeEvent']);
    Route::post('/calls', [ActivityController::class, 'storeCall']);
    Route::post('/meetings', [ActivityController::class, 'storeMeeting']);
    
    Route::put('/tasks/{id}', [ActivityController::class, 'updateTask']);
    Route::put('/events/{id}', [ActivityController::class, 'updateEvent']);
    Route::put('/calls/{id}', [ActivityController::class, 'updateCall']);
    Route::put('/meetings/{id}', [ActivityController::class, 'updateMeeting']);

    Route::delete('/tasks/{id}', [ActivityController::class, 'destroyTask']);
    Route::delete('/events/{id}', [ActivityController::class, 'destroyEvent']);
    Route::delete('/calls/{id}', [ActivityController::class, 'destroyCall']);
    Route::delete('/meetings/{id}', [ActivityController::class, 'destroyMeeting']);

    Route::post('/notes', [ActivityController::class, 'storeNote']);
    Route::put('/notes/{id}', [ActivityController::class, 'updateNote']);
    Route::delete('/notes/{id}', [ActivityController::class, 'destroyNote']);
    Route::post('/meetings/{id}/process', [ActivityController::class, 'processMeeting']);
});