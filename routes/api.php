<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-preferences', [UserPreferenceController::class, 'show']);
    Route::post('/user-preferences', [UserPreferenceController::class, 'upsert']);
    Route::get('/user-feed', [UserPreferenceController::class, 'feed']);
});
