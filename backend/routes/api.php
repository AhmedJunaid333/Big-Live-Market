<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
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

    // Public catalog taxonomy & geography
    Route::get('/countries', [CatalogController::class, 'countries']);
    Route::get('/regions', [CatalogController::class, 'regions']);
    Route::get('/cities', [CatalogController::class, 'cities']);
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/categories/{slug}', [CatalogController::class, 'categoryDetails']);
    Route::get('/categories/{id}/attributes', [CatalogController::class, 'categoryAttributes']);
    Route::get('/brands', [CatalogController::class, 'brands']);
    Route::get('/models', [CatalogController::class, 'models']);

    // Public monetization plans & products
    Route::get('/plans', [\App\Http\Controllers\Api\BillingController::class, 'plans']);
    Route::get('/promotion-products', [\App\Http\Controllers\Api\BillingController::class, 'promotionProducts']);
    Route::post('/payments/{provider}/webhook', [\App\Http\Controllers\Api\BillingController::class, 'handleWebhook']);

    // Public business storefronts
    Route::get('/businesses/{slug}', [\App\Http\Controllers\Api\BusinessController::class, 'show']);

    // Public listing discovery & search
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{listing}', [ListingController::class, 'show']);
    Route::get('/search', [\App\Http\Controllers\Api\SearchController::class, 'search']);
    Route::get('/search/suggestions', [\App\Http\Controllers\Api\SearchController::class, 'suggestions']);
    Route::get('/search/map', [\App\Http\Controllers\Api\SearchController::class, 'map']);
    Route::get('/sellers/{id}', [\App\Http\Controllers\Api\EngagementController::class, 'sellerProfile']);

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

        // Search & Engagement
        Route::get('/saved-searches', [\App\Http\Controllers\Api\SearchController::class, 'savedSearches']);
        Route::post('/saved-searches', [\App\Http\Controllers\Api\SearchController::class, 'storeSavedSearch']);
        Route::delete('/saved-searches/{savedSearch}', [\App\Http\Controllers\Api\SearchController::class, 'deleteSavedSearch']);
        Route::get('/favorites', [\App\Http\Controllers\Api\EngagementController::class, 'favorites']);
        Route::post('/favorites/{listingId}', [\App\Http\Controllers\Api\EngagementController::class, 'addFavorite']);
        Route::delete('/favorites/{listingId}', [\App\Http\Controllers\Api\EngagementController::class, 'removeFavorite']);
        Route::post('/sellers/{id}/follow', [\App\Http\Controllers\Api\EngagementController::class, 'followSeller']);
        Route::delete('/sellers/{id}/follow', [\App\Http\Controllers\Api\EngagementController::class, 'unfollowSeller']);
        Route::get('/recent-views', [\App\Http\Controllers\Api\EngagementController::class, 'recentViews']);

        // Chat & Offers
        Route::get('/conversations', [\App\Http\Controllers\Api\ChatController::class, 'conversations']);
        Route::post('/conversations', [\App\Http\Controllers\Api\ChatController::class, 'startConversation']);
        Route::get('/conversations/{conversation}', [\App\Http\Controllers\Api\ChatController::class, 'showConversation']);
        Route::get('/conversations/{conversation}/messages', [\App\Http\Controllers\Api\ChatController::class, 'messages']);
        Route::post('/conversations/{conversation}/messages', [\App\Http\Controllers\Api\ChatController::class, 'sendMessage']);
        Route::post('/conversations/{conversation}/read', [\App\Http\Controllers\Api\ChatController::class, 'markRead']);
        Route::get('/conversations/{conversation}/offers', [\App\Http\Controllers\Api\ChatController::class, 'offers']);
        Route::post('/conversations/{conversation}/offers', [\App\Http\Controllers\Api\ChatController::class, 'createOffer']);
        Route::post('/offers/{offer}/accept', [\App\Http\Controllers\Api\ChatController::class, 'acceptOffer']);
        Route::post('/offers/{offer}/reject', [\App\Http\Controllers\Api\ChatController::class, 'rejectOffer']);
        Route::post('/offers/{offer}/counter', [\App\Http\Controllers\Api\ChatController::class, 'counterOffer']);
        Route::post('/users/{id}/block', [\App\Http\Controllers\Api\ChatController::class, 'blockUser']);
        Route::delete('/users/{id}/block', [\App\Http\Controllers\Api\ChatController::class, 'unblockUser']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllRead']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markRead']);
        Route::get('/notification-preferences', [\App\Http\Controllers\Api\NotificationController::class, 'getPreferences']);
        Route::patch('/notification-preferences', [\App\Http\Controllers\Api\NotificationController::class, 'updatePreferences']);

        // Businesses & Storefronts
        Route::post('/businesses', [\App\Http\Controllers\Api\BusinessController::class, 'store']);
        Route::patch('/businesses/{business}', [\App\Http\Controllers\Api\BusinessController::class, 'update']);
        Route::get('/businesses/{business}/members', [\App\Http\Controllers\Api\BusinessController::class, 'members']);
        Route::post('/businesses/{business}/members', [\App\Http\Controllers\Api\BusinessController::class, 'addMember']);
        Route::delete('/businesses/{business}/members/{userId}', [\App\Http\Controllers\Api\BusinessController::class, 'removeMember']);

        // Billing & Monetization
        Route::post('/promotions/checkout', [\App\Http\Controllers\Api\BillingController::class, 'checkoutPromotion']);
        Route::post('/subscriptions/checkout', [\App\Http\Controllers\Api\BillingController::class, 'checkoutSubscription']);
        Route::get('/orders', [\App\Http\Controllers\Api\BillingController::class, 'orders']);
        Route::get('/orders/{order}', [\App\Http\Controllers\Api\BillingController::class, 'showOrder']);
        Route::get('/invoices/{invoice}', [\App\Http\Controllers\Api\BillingController::class, 'showInvoice']);
        Route::post('/refund-requests', [\App\Http\Controllers\Api\BillingController::class, 'requestRefund']);

        // Reports & Support
        Route::post('/reports', [\App\Http\Controllers\Api\SupportController::class, 'submitReport']);
        Route::get('/support/tickets', [\App\Http\Controllers\Api\SupportController::class, 'tickets']);
        Route::post('/support/tickets', [\App\Http\Controllers\Api\SupportController::class, 'createTicket']);
        Route::get('/support/tickets/{ticket}', [\App\Http\Controllers\Api\SupportController::class, 'showTicket']);
        Route::post('/support/tickets/{ticket}/messages', [\App\Http\Controllers\Api\SupportController::class, 'sendMessage']);

        // Admin & Moderation (RBAC)
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\AdminController::class, 'dashboard']);
            Route::get('/listings/review', [\App\Http\Controllers\Api\AdminController::class, 'listingsReview']);
            Route::post('/listings/{listing}/approve', [\App\Http\Controllers\Api\AdminController::class, 'approveListing']);
            Route::post('/listings/{listing}/reject', [\App\Http\Controllers\Api\AdminController::class, 'rejectListing']);
            Route::get('/reports', [\App\Http\Controllers\Api\AdminController::class, 'reports']);
            Route::post('/reports/{report}/resolve', [\App\Http\Controllers\Api\AdminController::class, 'resolveReport']);
            Route::get('/users', [\App\Http\Controllers\Api\AdminController::class, 'users']);
            Route::patch('/users/{user}/status', [\App\Http\Controllers\Api\AdminController::class, 'updateUserStatus']);
            Route::get('/verifications', [\App\Http\Controllers\Api\AdminController::class, 'verifications']);
            Route::post('/verifications/{id}/decision', [\App\Http\Controllers\Api\AdminController::class, 'decideVerification']);
            Route::get('/audit-logs', [\App\Http\Controllers\Api\AdminController::class, 'auditLogs']);
        });

        // Listings creation and lifecycle
        Route::post('/listings', [ListingController::class, 'store']);
        Route::get('/me/listings', [ListingController::class, 'myListings']);
        Route::post('/listings/{listing}/submit', [ListingController::class, 'submit']);
        Route::post('/listings/{listing}/pause', [ListingController::class, 'pause']);
        Route::post('/listings/{listing}/resume', [ListingController::class, 'resume']);
        Route::post('/listings/{listing}/mark-sold', [ListingController::class, 'markSold']);
        Route::post('/listings/{listing}/media', [ListingController::class, 'attachMedia']);
        Route::post('/listings/{listing}/attributes', [ListingController::class, 'saveAttributes']);
    });
});
