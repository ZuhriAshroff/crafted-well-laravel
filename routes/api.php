<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserProfileController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CustomProductController;
use App\Http\Controllers\API\BaseFormulationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========================================
// PUBLIC API ROUTES (No Authentication)
// ========================================

// Product public routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);                        // GET /api/products
    Route::get('/search', [ProductController::class, 'search']);                 // GET /api/products/search
    Route::get('/options', [ProductController::class, 'getOptions']);            // GET /api/products/options
    Route::get('/{productId}', [ProductController::class, 'show']);              // GET /api/products/{id}
    Route::get('/{productId}/related', [ProductController::class, 'getFrequentlyBoughtTogether']); // GET /api/products/{id}/related
});

// ========================================
// PROTECTED API ROUTES (Authentication Required)
// ========================================

Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
    
    // Current user route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ========================================
    // USER PROFILE API ROUTES
    // ========================================
    Route::prefix('profiles')->group(function () {
        Route::get('/', [UserProfileController::class, 'index']);                
        Route::post('/', [UserProfileController::class, 'store']);               
        Route::get('/latest', [UserProfileController::class, 'show']);           
        Route::get('/{profileId}', [UserProfileController::class, 'show']);      
        Route::put('/{profileId}', [UserProfileController::class, 'update']);    
        Route::delete('/{profileId}', [UserProfileController::class, 'destroy']); 
    });

    // ========================================
    // PRODUCT API ROUTES (Authenticated)
    // ========================================
    Route::prefix('products')->group(function () {
        Route::get('/recommendations', [ProductController::class, 'getRecommendations']); 
    });

    // ========================================
    // ORDER API ROUTES
    // ========================================
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);                      
        Route::post('/', [OrderController::class, 'store']);                     
        Route::get('/statistics', [OrderController::class, 'statistics']);       
        Route::get('/{orderId}', [OrderController::class, 'show']);              
        Route::put('/{orderId}', [OrderController::class, 'update']);            
        Route::post('/{orderId}/cancel', [OrderController::class, 'cancel']);    
    });

    // ========================================
    // CUSTOM PRODUCTS API ROUTES
    // ========================================
    Route::prefix('custom-products')->group(function () {
        Route::get('/', [CustomProductController::class, 'index']);                           
        Route::post('/', [CustomProductController::class, 'store']);                          
        Route::get('/statistics', [CustomProductController::class, 'statistics']);           
        Route::get('/{customProductId}', [CustomProductController::class, 'show']);          
        Route::put('/{customProductId}', [CustomProductController::class, 'update']);        
        Route::delete('/{customProductId}', [CustomProductController::class, 'destroy']);    
        Route::post('/{customProductId}/reformulate', [CustomProductController::class, 'reformulate']); 
    });

    // ========================================
    // BASE FORMULATIONS API ROUTES
    // ========================================
    Route::prefix('base-formulations')->group(function () {
        Route::get('/', [BaseFormulationController::class, 'index']);                                           
        Route::get('/{baseFormulationId}', [BaseFormulationController::class, 'show']);                        
        Route::post('/compatible', [BaseFormulationController::class, 'getCompatibleFormulations']);           
        Route::post('/recommendations', [BaseFormulationController::class, 'getRecommendations']);             
    });

    // ========================================
    // ADMIN API ROUTES
    // ========================================
    Route::middleware('admin')->prefix('admin')->group(function () {
        
        // Product admin routes
        Route::prefix('products')->group(function () {
            Route::post('/', [ProductController::class, 'store']);               
            Route::put('/{productId}', [ProductController::class, 'update']);    
            Route::delete('/{productId}', [ProductController::class, 'destroy']); 
        });

        // Order admin routes
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'adminIndex']);             
            Route::put('/{orderId}', [OrderController::class, 'adminUpdate']);   
        });

        // Custom products admin routes
        Route::prefix('custom-products')->group(function () {
            Route::get('/', [CustomProductController::class, 'adminIndex']);  
            Route::get('/analytics', [CustomProductController::class, 'analytics']);         
        });

        // Base formulations admin routes
        Route::prefix('base-formulations')->group(function () {
            Route::post('/', [BaseFormulationController::class, 'store']);                                     
            Route::put('/{baseFormulationId}', [BaseFormulationController::class, 'update']);                 
            Route::delete('/{baseFormulationId}', [BaseFormulationController::class, 'destroy']);             
        });
    });
});