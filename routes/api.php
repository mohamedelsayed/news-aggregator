<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:10,1');  // Allow 10 registrations per minute per IP

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:20,1');  // Allow 20 logins per minute per IP

Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:5,1');   // Allow 5 reset requests per minute per IP

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('throttle:5,1')    // Allow 5 reset submissions per minute per IP
    ->name('password.update');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/articles', [ArticleController::class, 'index'])
    ->middleware('throttle:100,1');  // Allow 100 article fetches per minute

Route::get('/articles/{id}', [ArticleController::class, 'show'])
    ->middleware('throttle:100,1');  // Same for individual articles

Route::middleware('auth:sanctum', 'throttle:60,1')->group(function () {
    Route::get('/user-preferences', [UserPreferenceController::class, 'show']);
    Route::post('/user-preferences', [UserPreferenceController::class, 'upsert']);
    Route::get('/user-feed', [UserPreferenceController::class, 'feed']);
});
