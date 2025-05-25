<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductAPIController;
use App\Http\Controllers\API\UserProfileAPIController;
use App\Http\Controllers\API\CustomProductAPIController;
use App\Http\Controllers\API\OrderAPIController;

// Public API routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductAPIController::class, 'index']);
    Route::get('/search', [ProductAPIController::class, 'search']);
    Route::get('/recommendations', [ProductAPIController::class, 'getRecommendations']);
    Route::get('/{productId}', [ProductAPIController::class, 'show']);
    Route::get('/{productId}/frequently-bought', [ProductAPIController::class, 'getFrequentlyBoughtTogether']);
    Route::get('/{productId}/sales-stats', [ProductAPIController::class, 'getSalesStats']);
});

// Protected API routes
Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Profile routes
    Route::prefix('profiles')->group(function () {
        Route::get('/', [UserProfileAPIController::class, 'index']);
        Route::post('/', [UserProfileAPIController::class, 'store']);
        Route::get('/latest', [UserProfileAPIController::class, 'show']);
        Route::get('/{profileId}', [UserProfileAPIController::class, 'show']);
        Route::put('/{profileId}', [UserProfileAPIController::class, 'update']);
        Route::delete('/{profileId}', [UserProfileAPIController::class, 'destroy']);
        
        // Special routes
        Route::post('/create-or-update', [UserProfileAPIController::class, 'createOrUpdate']);
        Route::get('/options/all', [UserProfileAPIController::class, 'getOptions']);
        Route::get('/recommendations', [UserProfileAPIController::class, 'getRecommendations']);
        
        // Step-by-step validation routes
        Route::post('/validate/skin-type', [UserProfileAPIController::class, 'validateSkinType']);
        Route::post('/validate/skin-concerns', [UserProfileAPIController::class, 'validateSkinConcerns']);
        Route::post('/validate/environmental-factors', [UserProfileAPIController::class, 'validateEnvironmentalFactors']);
    });

    // Custom products routes
    Route::apiResource('custom-products', CustomProductAPIController::class);
    
    // Orders routes
    Route::apiResource('orders', OrderAPIController::class);
    
    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::prefix('admin')->group(function () {
            Route::post('/products', [ProductAPIController::class, 'store']);
            Route::put('/products/{productId}', [ProductAPIController::class, 'update']);
            Route::delete('/products/{productId}', [ProductAPIController::class, 'destroy']);
            Route::get('/products/analytics', [ProductAPIController::class, 'getAnalytics']);
        });
    });
});