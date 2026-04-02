<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Driver\RatingController;
use App\Http\Controllers\API\Driver\ServiceRequestController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\Provider\ProfileController;
use App\Http\Controllers\API\Provider\RequestController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ─── Authenticated ─────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']); // ← هنا


    // Driver
    Route::middleware('role:driver')->prefix('driver')->group(function () {
        Route::get('/requests',                    [ServiceRequestController::class, 'index']);
        Route::post('/requests/fuel',              [ServiceRequestController::class, 'storeFuel']);
        Route::post('/requests/towing',            [ServiceRequestController::class, 'storeTowing']);
        Route::get('/requests/{request}',          [ServiceRequestController::class, 'show']);
        Route::patch('/requests/{request}/cancel', [ServiceRequestController::class, 'cancel']);
        Route::post('/requests/{request}/rate',    [RatingController::class, 'store']);
    });

    // Provider
    Route::middleware(['role:provider', 'provider.approved'])->prefix('provider')->group(function () {
        Route::patch('/profile/availability',      [ProfileController::class, 'updateAvailability']);
        Route::get('/requests',                    [RequestController::class, 'index']);
        Route::patch('/requests/{request}/accept', [RequestController::class, 'accept']);
        Route::patch('/requests/{request}/reject', [RequestController::class, 'reject']);
        Route::patch('/requests/{request}/status', [RequestController::class, 'updateStatus']);
    });

    // Shared
    Route::get('/notifications', [NotificationController::class, 'index']);
});