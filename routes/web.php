<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CylendarController;
use App\Http\Controllers\SynDriveController;

// Existing routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google', [CylendarController::class, 'redirectToGoogle']);
Route::get('/google/callback', [CylendarController::class, 'handleGoogleCallback']);

// Add route for creating an event
Route::post('/calendar/event', [CylendarController::class, 'createEvent']);

// For Google Drive
Route::post('/drive/upload', [SynDriveController::class, 'uploadFile']);
Route::get('/drive/files', [SynDriveController::class, 'listFiles']);
Route::get('/syndrive/auth', [SynDriveController::class, 'redirectToGoogle']);
Route::get('/syndrive/callback', [SynDriveController::class, 'handleGoogleCallback']);
