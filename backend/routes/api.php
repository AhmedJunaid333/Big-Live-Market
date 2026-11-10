<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsentController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\UserDeviceController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Health check
    Route::get('/health', HealthController::class);

    // Public authentication endpoints
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/otp/send', [AuthController::class, 'sendOtp']);
        Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
    });

    // Public listing discovery
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{listing}', [ListingController::class, 'show']);

    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {
        // Auth session management
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);

        // Profile & Account Management
        Route::get('/me', [AuthController::class, 'me']);
        Route::patch('/me', [AuthController::class, 'updateProfile']);
        Route::delete('/me', [AuthController::class, 'deleteAccount']);

        // Devices
        Route::get('/me/devices', [UserDeviceController::class, 'index']);
        Route::delete('/me/devices/{device}', [UserDeviceController::class, 'destroy']);

        // Consents
        Route::post('/me/consents', [ConsentController::class, 'store']);

        // Verification
        Route::post('/me/verification', [VerificationController::class, 'store']);

        // Listings creation
        Route::post('/listings', [ListingController::class, 'store']);
    });
});
