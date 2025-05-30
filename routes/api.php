<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CylendarController;
use App\Http\Controllers\SynDriveController;
use App\Http\Controllers\MyleBookController;
use App\Http\Controllers\NikaTalkController;
use App\Http\Controllers\JatrAIlController;
use App\Http\Controllers\AuthController;

Route::prefix('api')->group(function () {

    // Public login route
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);


    // Protected routes
    Route::middleware('auth:api')->group(function () {

        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);

        // SynDrive routes
        Route::post('SynDrive/upload', [SynDriveController::class, 'uploadFile']);
        Route::get('SynDrive/list', [SynDriveController::class, 'listFiles']);
        Route::delete('SynDrive/delete/{fileId}', [SynDriveController::class, 'deleteFile']);

        // Google Calendar routes
        Route::get('cylendar/events', [CylendarController::class, 'getEvents']);
        Route::get('cylendar/event/{eventId}', [CylendarController::class, 'getEvent']);
        Route::post('cylendar/event', [CylendarController::class, 'createEvent']);
        Route::put('cylendar/event/{eventId}', [CylendarController::class, 'updateEvent']);
        Route::delete('cylendar/event/{eventId}', [CylendarController::class, 'deleteEvent']);

        // Dictionary route
        Route::get('myle-book/define', [MyleBookController::class, 'define']);

        // OpenAI route
        Route::post('jatrail/chat', [JatrAIlController::class, 'chat']);

        // Slack (NikaTalk) routes
        Route::prefix('nika-talk')->group(function () {
            Route::post('send', [NikaTalkController::class, 'sendMessage']);
            Route::get('messages', [NikaTalkController::class, 'getMessages']);
            Route::post('update', [NikaTalkController::class, 'updateMessage']);
            Route::post('delete', [NikaTalkController::class, 'deleteMessage']);
        });
    });
});
