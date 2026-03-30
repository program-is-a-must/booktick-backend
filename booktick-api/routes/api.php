<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Sessions
    Route::get('/sessions',              [SessionController::class, 'index']);
    Route::post('/sessions',             [SessionController::class, 'store']);
    Route::delete('/sessions/{session}', [SessionController::class, 'destroy']);
    Route::get('/stats',                 [SessionController::class, 'stats']);

    // Challenges (read for all users)
    Route::get('/challenges', [ChallengeController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // Challenge management by admin
    Route::post('/challenges',            [ChallengeController::class, 'store']);
    Route::put('/challenges/{challenge}', [ChallengeController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'super_admin'])
    ->group(function () {

        Route::get('/users',                     [AdminController::class, 'users']);
        Route::get('/stats',                     [AdminController::class, 'stats']);
        Route::patch('/users/{user}/toggle-ban', [AdminController::class, 'toggleBan']);
        Route::delete('/users/{user}',           [AdminController::class, 'deleteUser']);

        // If ONLY super_admin should manage challenges, keep these here
        Route::post('/challenges',            [ChallengeController::class, 'store']);
        Route::put('/challenges/{challenge}', [ChallengeController::class, 'update']);
});