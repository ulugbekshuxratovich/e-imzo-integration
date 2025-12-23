<?php

use App\Http\Controllers\Api\EimzoAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| E-IMZO API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('eimzo')->group(function () {
    Route::get('/challenge', [EimzoAuthController::class, 'getChallenge'])
        ->name('eimzo.challenge');
    
    Route::post('/login', [EimzoAuthController::class, 'login'])
        ->name('eimzo.login');
});

// Protected routes
Route::middleware('auth:sanctum')->prefix('eimzo')->group(function () {
    Route::post('/logout', [EimzoAuthController::class, 'logout'])
        ->name('eimzo.logout');
    
    Route::get('/me', [EimzoAuthController::class, 'me'])
        ->name('eimzo.me');
});
