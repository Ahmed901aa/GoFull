<?php

use App\Http\Controllers\API\AppSettingController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Driver\IncomeController;
use App\Http\Controllers\API\Driver\RatingController;
use App\Http\Controllers\API\Driver\ServiceRequestController;
use App\Http\Controllers\API\Driver\VehicleController;
use App\Http\Controllers\API\FuelPriceController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\ProfileController as UserProfileController;
use App\Http\Controllers\API\Provider\AnalyticsController;
use App\Http\Controllers\API\Provider\ProfileController;
use App\Http\Controllers\API\Provider\RequestController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // OTP (SMS verification via iSend)
    Route::post('/otp/send', [OtpController::class, 'send'])->middleware('throttle:3,1');
    Route::post('/otp/verify', [OtpController::class, 'verify'])->middleware('throttle:10,1');

    // Forgot password: send OTP with purpose=password_reset, then call this
    // directly with {phone, otp_code, password, password_confirmation}.
    // (Do NOT pre-verify via /otp/verify — codes are single-use.)
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
});

// ─── Public Content ──────────────────────────────────────────
Route::get('/fuel/prices', [FuelPriceController::class, 'index']);
Route::get('/app/settings', [AppSettingController::class, 'index']);

// ─── Broadcasting Auth (Sanctum token) ──────────────────────────
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// ─── Authenticated ─────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    // Driver
    Route::middleware('role:driver')->prefix('driver')->group(function () {
        Route::get('/requests', [ServiceRequestController::class, 'index']);
        Route::get('/requests/unrated', [ServiceRequestController::class, 'unrated']);
        Route::post('/requests/fuel', [ServiceRequestController::class, 'storeFuel']);
        Route::post('/requests/towing', [ServiceRequestController::class, 'storeTowing']);
        Route::get('/requests/{request}', [ServiceRequestController::class, 'show']);
        Route::patch('/requests/{request}/cancel', [ServiceRequestController::class, 'cancel']);
        Route::post('/requests/{serviceRequest}/rate', [RatingController::class, 'store']);

        Route::get('/vehicle', [VehicleController::class, 'show']);
        Route::post('/vehicle', [VehicleController::class, 'store']);

        Route::get('/income', [IncomeController::class, 'summary']);
    });

    // Provider
    Route::middleware(['role:provider', 'provider.approved'])->prefix('provider')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::patch('/profile/availability', [ProfileController::class, 'updateAvailability']);
        Route::patch('/profile/location', [ProfileController::class, 'updateLocation']);
        Route::get('/requests', [RequestController::class, 'index']);
        Route::get('/requests/active', [RequestController::class, 'getActive']);
        Route::get('/requests/history', [RequestController::class, 'history']);
        Route::get('/requests/{request}', [RequestController::class, 'show']);
        Route::patch('/requests/{request}/accept', [RequestController::class, 'accept']);
        Route::patch('/requests/{request}/reject', [RequestController::class, 'reject']);
        Route::patch('/requests/{request}/cancel', [RequestController::class, 'cancel']);
        Route::patch('/requests/{serviceRequest}/status', [RequestController::class, 'updateStatus']);
        Route::post('/requests/{serviceRequest}/rate', [RequestController::class, 'rateCustomer']);
        Route::get('/analytics', [AnalyticsController::class, 'summary']);
    });

    // Shared
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/fcm-token', [NotificationController::class, 'updateFcmToken']);
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/profile', [UserProfileController::class, 'show']);
    Route::patch('/profile', [UserProfileController::class, 'update']);
});
