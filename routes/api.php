<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserProfileController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CustomProductController;
use App\Http\Controllers\API\BaseFormulationController;
use App\Http\Controllers\API\AuthController; // ADD THIS LINE

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========================================
// AUTHENTICATION API ROUTES (Public)
// ========================================

Route::prefix('auth')->group(function () {
    // POST /api/auth/register - Register new user
    // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/auth/register" -H "Content-Type: application/json" -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'
    Route::post('/register', [AuthController::class, 'register']);

    // POST /api/auth/login - Login user
    // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/auth/login" -H "Content-Type: application/json" -d '{"email":"john@example.com","password":"password123"}'
    Route::post('/login', [AuthController::class, 'login']);

    // Protected auth routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // POST /api/auth/logout - Logout current session
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/auth/logout" -H "Authorization: Bearer YOUR_TOKEN"
        Route::post('/logout', [AuthController::class, 'logout']);

        // POST /api/auth/logout-all - Logout from all devices
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/auth/logout-all" -H "Authorization: Bearer YOUR_TOKEN"
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);

        // GET /api/auth/me - Get current user info
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/auth/me" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/me', [AuthController::class, 'me']);

        // POST /api/auth/change-password - Change password
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/auth/change-password" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"current_password":"old123","new_password":"new123","new_password_confirmation":"new123"}'
        Route::post('/change-password', [AuthController::class, 'changePassword']);

        // DELETE /api/auth/delete-account - Delete user account
        // Test: curl -X DELETE "https://crafted-well-laravel.up.railway.app/api/auth/delete-account" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"password":"password123","confirmation":"DELETE_MY_ACCOUNT"}'
        Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

        // GET /api/auth/test - Test authentication
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/auth/test" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/test', [AuthController::class, 'test']);

        // GET /api/auth/tokens - Get user's tokens
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/auth/tokens" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/tokens', [AuthController::class, 'tokens']);

        // DELETE /api/auth/tokens/{id} - Delete specific token
        // Test: curl -X DELETE "https://crafted-well-laravel.up.railway.app/api/auth/tokens/1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::delete('/tokens/{tokenId}', [AuthController::class, 'deleteToken']);
    });
});

// ========================================
// PUBLIC API ROUTES (No Authentication)
// ========================================

// Product public routes
Route::prefix('products')->group(function () {
    // GET /api/products - Get all products with pagination
    // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/products?page=1&per_page=10"
    Route::get('/', [ProductController::class, 'index']);

    // GET /api/products/search - Search products by name, category, etc.
    // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/products/search?q=protein&category=supplements"
    Route::get('/search', [ProductController::class, 'search']);

    // GET /api/products/options - Get product filter options (categories, brands, etc.)
    // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/products/options"
    Route::get('/options', [ProductController::class, 'getOptions']);

    // GET /api/products/{id} - Get single product details
    // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/products/1"
    Route::get('/{productId}', [ProductController::class, 'show']);

    // GET /api/products/{id}/related - Get frequently bought together products
    // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/products/1/related"
    Route::get('/{productId}/related', [ProductController::class, 'getFrequentlyBoughtTogether']);
});

// ========================================
// PROTECTED API ROUTES (Authentication Required)
// ========================================

Route::middleware(['auth:sanctum', 'active.user'])->group(function () {

    // GET /api/user - Get current authenticated user (legacy endpoint)
    // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/user" -H "Authorization: Bearer YOUR_TOKEN"
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ========================================
    // USER PROFILE API ROUTES
    // ========================================
    Route::prefix('profiles')->group(function () {
        // GET /api/profiles - Get all user profiles for current user
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/profiles" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/', [UserProfileController::class, 'index']);

        // POST /api/profiles - Create new user profile
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/profiles" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"John Doe","age":30,"goals":"muscle_gain"}'
        Route::post('/', [UserProfileController::class, 'store']);

        // GET /api/profiles/latest - Get user's latest/active profile
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/profiles/latest" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/latest', [UserProfileController::class, 'show']);

        // GET /api/profiles/{id} - Get specific profile by ID
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/profiles/1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/{profileId}', [UserProfileController::class, 'show']);

        // PUT /api/profiles/{id} - Update specific profile
        // Test: curl -X PUT "https://crafted-well-laravel.up.railway.app/api/profiles/1" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"Jane Doe","age":25}'
        Route::put('/{profileId}', [UserProfileController::class, 'update']);

        // DELETE /api/profiles/{id} - Delete specific profile
        // Test: curl -X DELETE "https://crafted-well-laravel.up.railway.app/api/profiles/1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::delete('/{profileId}', [UserProfileController::class, 'destroy']);
    });

    // ========================================
    // PRODUCT API ROUTES (Authenticated)
    // ========================================
    Route::prefix('products')->group(function () {
        // GET /api/products/recommendations - Get personalized product recommendations
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/products/recommendations" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/recommendations', [ProductController::class, 'getRecommendations']);
    });

    // ========================================
    // ORDER API ROUTES
    // ========================================
    Route::prefix('orders')->group(function () {
        // GET /api/orders - Get all orders for current user
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/orders?status=pending&page=1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/', [OrderController::class, 'index']);

        // POST /api/orders - Create new order
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/orders" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"items":[{"product_id":1,"quantity":2}],"shipping_address":"123 Main St"}'
        Route::post('/', [OrderController::class, 'store']);

        // GET /api/orders/statistics - Get user's order statistics
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/orders/statistics" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/statistics', [OrderController::class, 'statistics']);

        // GET /api/orders/{id} - Get specific order details
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/orders/1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/{orderId}', [OrderController::class, 'show']);

        // PUT /api/orders/{id} - Update order (if allowed)
        // Test: curl -X PUT "https://crafted-well-laravel.up.railway.app/api/orders/1" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"shipping_address":"456 Oak Ave"}'
        Route::put('/{orderId}', [OrderController::class, 'update']);

        // POST /api/orders/{id}/cancel - Cancel specific order
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/orders/1/cancel" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"reason":"Changed mind"}'
        Route::post('/{orderId}/cancel', [OrderController::class, 'cancel']);
    });

    // ========================================
    // CUSTOM PRODUCTS API ROUTES
    // ========================================
    Route::prefix('custom-products')->group(function () {
        // GET /api/custom-products - Get all custom products for current user
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/custom-products?page=1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/', [CustomProductController::class, 'index']);

        // POST /api/custom-products - Create new custom product
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/custom-products" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"My Custom Blend","ingredients":[{"id":1,"amount":10}],"base_formulation_id":1}'
        Route::post('/', [CustomProductController::class, 'store']);

        // GET /api/custom-products/statistics - Get custom product statistics
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/custom-products/statistics" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/statistics', [CustomProductController::class, 'statistics']);

        // GET /api/custom-products/{id} - Get specific custom product
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/custom-products/1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/{customProductId}', [CustomProductController::class, 'show']);

        // PUT /api/custom-products/{id} - Update custom product
        // Test: curl -X PUT "https://crafted-well-laravel.up.railway.app/api/custom-products/1" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"Updated Blend Name"}'
        Route::put('/{customProductId}', [CustomProductController::class, 'update']);

        // DELETE /api/custom-products/{id} - Delete custom product
        // Test: curl -X DELETE "https://crafted-well-laravel.up.railway.app/api/custom-products/1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::delete('/{customProductId}', [CustomProductController::class, 'destroy']);

        // POST /api/custom-products/{id}/reformulate - Reformulate custom product
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/custom-products/1/reformulate" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"new_goals":["muscle_gain","energy"]}'
        Route::post('/{customProductId}/reformulate', [CustomProductController::class, 'reformulate']);
    });

    // ========================================
    // BASE FORMULATIONS API ROUTES
    // ========================================
    Route::prefix('base-formulations')->group(function () {
        // GET /api/base-formulations - Get all available base formulations
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/base-formulations?category=protein&page=1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/', [BaseFormulationController::class, 'index']);

        // GET /api/base-formulations/{id} - Get specific base formulation details
        // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/base-formulations/1" -H "Authorization: Bearer YOUR_TOKEN"
        Route::get('/{baseFormulationId}', [BaseFormulationController::class, 'show']);

        // POST /api/base-formulations/compatible - Get compatible formulations for mixing
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/base-formulations/compatible" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"formulation_ids":[1,2,3]}'
        Route::post('/compatible', [BaseFormulationController::class, 'getCompatibleFormulations']);

        // POST /api/base-formulations/recommendations - Get recommended base formulations
        // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/base-formulations/recommendations" -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"profile_id":1,"goals":["muscle_gain","energy"]}'
        Route::post('/recommendations', [BaseFormulationController::class, 'getRecommendations']);
    });

    // ========================================
    // ADMIN API ROUTES (Requires admin role)
    // ========================================
    Route::middleware('admin')->prefix('admin')->group(function () {

        // Product admin routes
        Route::prefix('products')->group(function () {
            // POST /api/admin/products - Create new product (admin only)
            // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/admin/products" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"name":"New Product","price":29.99,"category_id":1}'
            Route::post('/', [ProductController::class, 'store']);

            // PUT /api/admin/products/{id} - Update product (admin only)
            // Test: curl -X PUT "https://crafted-well-laravel.up.railway.app/api/admin/products/1" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"name":"Updated Product Name","price":34.99}'
            Route::put('/{productId}', [ProductController::class, 'update']);

            // DELETE /api/admin/products/{id} - Delete product (admin only)
            // Test: curl -X DELETE "https://crafted-well-laravel.up.railway.app/api/admin/products/1" -H "Authorization: Bearer ADMIN_TOKEN"
            Route::delete('/{productId}', [ProductController::class, 'destroy']);
        });

        // Order admin routes
        Route::prefix('orders')->group(function () {
            // GET /api/admin/orders - Get all orders (admin view)
            // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/admin/orders?status=pending&user_id=123&page=1" -H "Authorization: Bearer ADMIN_TOKEN"
            Route::get('/', [OrderController::class, 'adminIndex']);

            // PUT /api/admin/orders/{id} - Update order status (admin only)
            // Test: curl -X PUT "https://crafted-well-laravel.up.railway.app/api/admin/orders/1" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"status":"shipped","tracking_number":"ABC123"}'
            Route::put('/{orderId}', [OrderController::class, 'adminUpdate']);
        });

        // Custom products admin routes
        Route::prefix('custom-products')->group(function () {
            // GET /api/admin/custom-products - Get all custom products (admin view)
            // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/admin/custom-products?user_id=123&status=active&page=1" -H "Authorization: Bearer ADMIN_TOKEN"
            Route::get('/', [CustomProductController::class, 'adminIndex']);

            // GET /api/admin/custom-products/analytics - Get custom product analytics
            // Test: curl -X GET "https://crafted-well-laravel.up.railway.app/api/admin/custom-products/analytics?period=30days" -H "Authorization: Bearer ADMIN_TOKEN"
            Route::get('/analytics', [CustomProductController::class, 'analytics']);
        });

        // Base formulations admin routes
        Route::prefix('base-formulations')->group(function () {
            // POST /api/admin/base-formulations - Create new base formulation (admin only)
            // Test: curl -X POST "https://crafted-well-laravel.up.railway.app/api/admin/base-formulations" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"name":"New Base Formula","ingredients":[{"id":1,"amount":50}],"category":"protein"}'
            Route::post('/', [BaseFormulationController::class, 'store']);

            // PUT /api/admin/base-formulations/{id} - Update base formulation (admin only)
            // Test: curl -X PUT "https://crafted-well-laravel.up.railway.app/api/admin/base-formulations/1" -H "Authorization: Bearer ADMIN_TOKEN" -H "Content-Type: application/json" -d '{"name":"Updated Formula Name"}'
            Route::put('/{baseFormulationId}', [BaseFormulationController::class, 'update']);

            // DELETE /api/admin/base-formulations/{id} - Delete base formulation (admin only)
            // Test: curl -X DELETE "https://crafted-well-laravel.up.railway.app/api/admin/base-formulations/1" -H "Authorization: Bearer ADMIN_TOKEN"
            Route::delete('/{baseFormulationId}', [BaseFormulationController::class, 'destroy']);
        });
    });
});