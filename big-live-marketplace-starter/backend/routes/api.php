<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ListingController;
Route::prefix('v1')->group(function () {
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{listing}', [ListingController::class, 'show']);
    Route::middleware('auth:sanctum')->post('/listings', [ListingController::class, 'store']);
});
