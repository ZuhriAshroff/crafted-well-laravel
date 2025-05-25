<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CustomProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Livewire\ProductCustomizer;
use App\Livewire\ShoppingCart;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Protected routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::middleware(['auth:sanctum', 'verified', 'active.user'])->group(function () {
        Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/create', [UserProfileController::class, 'create'])->name('profile.create');
        Route::post('/profile', [UserProfileController::class, 'store'])->name('profile.store');
        Route::get('/profile/{profileId}/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/{profileId}', [UserProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/{profileId}', [UserProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile/recommendations', [UserProfileController::class, 'recommendations'])->name('profile.recommendations');
        Route::get('/profile/analytics', [UserProfileController::class, 'analytics'])->name('profile.analytics');
    });

    Route::resource('custom-products', CustomProductController::class);
    Route::resource('orders', OrderController::class);

    Route::get('/product-customizer', ProductCustomizer::class)->name('product-customizer');
    Route::get('/cart', ShoppingCart::class)->name('cart');
});

// Admin routes
Route::middleware(['auth:sanctum', 'verified', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Add placeholder routes for the buttons in the dashboard
        Route::get('/products', function () {
            return view('admin.products');
        })->name('products');

        Route::get('/orders', function () {
            return view('admin.orders');
        })->name('orders');

        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');
    });