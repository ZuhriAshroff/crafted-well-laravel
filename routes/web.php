<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomProductController;
use App\Livewire\SurveyComponent;
use App\Http\Controllers\BaseFormulationController;
// [ADD MORE WEB CONTROLLER IMPORTS HERE]
// use App\Http\Controllers\CustomProductController;
// use App\Http\Controllers\AdminController;
// use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========================================
// PUBLIC WEB ROUTES (No Authentication)
// ========================================

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public product routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
});

// Survey routes
Route::get('/survey', App\Livewire\SurveyComponent::class)->name('survey.index');

// Static pages
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Custom product routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/custom-products/{id}', [CustomProductController::class, 'show'])->name('custom-products.show');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


// [ADD MORE PUBLIC WEB ROUTES HERE]
// About us, contact, blog, etc.
// Route::get('/about', [PageController::class, 'about'])->name('about');
// Route::get('/contact', [PageController::class, 'contact'])->name('contact');
// Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');

// ========================================
// PROTECTED WEB ROUTES (Authentication Required)
// ========================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ========================================
    // USER PROFILE WEB ROUTES
    // ========================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [UserProfileController::class, 'index'])->name('index');
        Route::get('/create', [UserProfileController::class, 'create'])->name('create');
        Route::post('/', [UserProfileController::class, 'store'])->name('store');
        Route::get('/edit/{profileId?}', [UserProfileController::class, 'edit'])->name('edit');
        Route::put('/{profileId}', [UserProfileController::class, 'update'])->name('update');
        Route::delete('/{profileId}', [UserProfileController::class, 'destroy'])->name('destroy');
        Route::get('/recommendations', [UserProfileController::class, 'recommendations'])->name('recommendations');
        Route::get('/analytics', [UserProfileController::class, 'analytics'])->name('analytics');
    });

    // ========================================
    // PRODUCT WEB ROUTES (Authenticated)
    // ========================================
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/recommendations', [ProductController::class, 'recommendations'])->name('recommendations');
        // [ADD MORE AUTHENTICATED PRODUCT ROUTES HERE]
    });

    // ========================================
    // ORDER WEB ROUTES
    // ========================================
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/tracking', [OrderController::class, 'tracking'])->name('tracking');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('custom-products')->name('custom-products.')->group(function () {
        Route::get('/', [CustomProductController::class, 'index'])->name('index');
        Route::get('/create', [CustomProductController::class, 'create'])->name('create');
        Route::post('/', [CustomProductController::class, 'store'])->name('store');
        Route::get('/{customProduct}', [CustomProductController::class, 'show'])->name('show');
        Route::get('/{customProduct}/edit', [CustomProductController::class, 'edit'])->name('edit');
        Route::put('/{customProduct}', [CustomProductController::class, 'update'])->name('update');
        Route::delete('/{customProduct}', [CustomProductController::class, 'destroy'])->name('destroy');
        Route::get('/{customProduct}/reformulate', [CustomProductController::class, 'reformulate'])->name('reformulate');
        Route::post('/{customProduct}/reformulate', [CustomProductController::class, 'processReformulation'])->name('process-reformulation');
        Route::get('/allergy-alternatives', [CustomProductController::class, 'allergyAlternatives'])->name('allergy-alternatives');
    });

    // ========================================
    // [ADD MORE AUTHENTICATED WEB ROUTES HERE]
    // ========================================
    // Custom Products Routes
    // Route::resource('custom-products', CustomProductController::class);

    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout functionality
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // Wishlist Routes
    // Route::prefix('wishlist')->name('wishlist.')->group(function () {
    //     Route::get('/', [WishlistController::class, 'index'])->name('index');
    //     Route::post('/add/{product}', [WishlistController::class, 'add'])->name('add');
    //     Route::delete('/remove/{product}', [WishlistController::class, 'remove'])->name('remove');
    // });

    // Account Management Routes
    // Route::prefix('account')->name('account.')->group(function () {
    //     Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
    //     Route::put('/settings', [AccountController::class, 'updateSettings'])->name('settings.update');
    //     Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
    //     Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    // });
});

// ========================================
// ADMIN WEB ROUTES
// ========================================

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // ========================================
    // ADMIN PRODUCT MANAGEMENT
    // ========================================
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'adminIndex'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'adminShow'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/analytics/overview', [ProductController::class, 'analytics'])->name('analytics');
    });

    // ========================================
    // ADMIN ORDER MANAGEMENT
    // ========================================
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'adminIndex'])->name('index');
        Route::get('/{order}', [OrderController::class, 'adminShow'])->name('show');
        Route::put('/{order}', [OrderController::class, 'adminUpdate'])->name('update');
        Route::get('/analytics/overview', [OrderController::class, 'analytics'])->name('analytics');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/custom-products', [CustomProductController::class, 'adminIndex'])->name('custom-products.index');
        Route::get('/custom-products/{customProduct}', [CustomProductController::class, 'adminShow'])->name('custom-products.show');
        Route::get('/custom-products-analytics', [CustomProductController::class, 'analytics'])->name('custom-products.analytics');
    });

    Route::prefix('base-formulations')->name('orders.')->group(function () {
        Route::get('/base-formulations', [BaseFormulationController::class, 'index'])->name('base-formulations.index');
        Route::get('/base-formulations/{baseFormulation}', [BaseFormulationController::class, 'show'])->name('base-formulations.show');
    });

   
    


    // ========================================
    // [ADD MORE ADMIN WEB ROUTES HERE]
    // ========================================

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/base-formulations', [BaseFormulationController::class, 'index'])->name('base-formulations.index');
        Route::get('/base-formulations/create', [BaseFormulationController::class, 'create'])->name('base-formulations.create');
        Route::post('/base-formulations', [BaseFormulationController::class, 'store'])->name('base-formulations.store');
        Route::get('/base-formulations/{baseFormulation}', [BaseFormulationController::class, 'show'])->name('base-formulations.show');
        Route::get('/base-formulations/{baseFormulation}/edit', [BaseFormulationController::class, 'edit'])->name('base-formulations.edit');
        Route::put('/base-formulations/{baseFormulation}', [BaseFormulationController::class, 'update'])->name('base-formulations.update');
        Route::delete('/base-formulations/{baseFormulation}', [BaseFormulationController::class, 'destroy'])->name('base-formulations.destroy');
        Route::post('/base-formulations/{baseFormulation}/deactivate', [BaseFormulationController::class, 'deactivate'])->name('base-formulations.deactivate');
        Route::get('/base-formulations/{baseFormulation}/clone', [BaseFormulationController::class, 'showCloneForm'])->name('base-formulations.clone-form');
        Route::post('/base-formulations/{baseFormulation}/clone', [BaseFormulationController::class, 'clone'])->name('base-formulations.clone');
        Route::get('/base-formulations-analytics', [BaseFormulationController::class, 'analytics'])->name('base-formulations.analytics');
    });
    // Admin User Management
    // Route::prefix('users')->name('users.')->group(function () {
    //     Route::get('/', [UserController::class, 'adminIndex'])->name('index');
    //     Route::get('/{user}', [UserController::class, 'adminShow'])->name('show');
    //     Route::put('/{user}', [UserController::class, 'adminUpdate'])->name('update');
    //     Route::delete('/{user}', [UserController::class, 'adminDestroy'])->name('destroy');
    // });

    // Admin Analytics & Reports
    // Route::prefix('analytics')->name('analytics.')->group(function () {
    //     Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
    //     Route::get('/sales', [AnalyticsController::class, 'sales'])->name('sales');
    //     Route::get('/users', [AnalyticsController::class, 'users'])->name('users');
    //     Route::get('/products', [AnalyticsController::class, 'products'])->name('products');
    // });

    // Admin System Management
    // Route::prefix('system')->name('system.')->group(function () {
    //     Route::get('/settings', [SystemController::class, 'settings'])->name('settings');
    //     Route::put('/settings', [SystemController::class, 'updateSettings'])->name('settings.update');
    //     Route::get('/logs', [SystemController::class, 'logs'])->name('logs');
    //     Route::post('/cache/clear', [SystemController::class, 'clearCache'])->name('cache.clear');
    //     Route::get('/backup', [SystemController::class, 'backup'])->name('backup');
    // });

    // Admin Custom Products Management
    // Route::prefix('custom-products')->name('custom-products.')->group(function () {
    //     Route::get('/', [CustomProductController::class, 'adminIndex'])->name('index');
    //     Route::get('/{customProduct}', [CustomProductController::class, 'adminShow'])->name('show');
    //     Route::put('/{customProduct}', [CustomProductController::class, 'adminUpdate'])->name('update');
    // });

    // Admin Base Formulations Management
    // Route::prefix('base-formulations')->name('base-formulations.')->group(function () {
    //     Route::get('/', [BaseFormulationController::class, 'index'])->name('index');
    //     Route::get('/create', [BaseFormulationController::class, 'create'])->name('create');
    //     Route::post('/', [BaseFormulationController::class, 'store'])->name('store');
    //     Route::get('/{baseFormulation}/edit', [BaseFormulationController::class, 'edit'])->name('edit');
    //     Route::put('/{baseFormulation}', [BaseFormulationController::class, 'update'])->name('update');
    //     Route::delete('/{baseFormulation}', [BaseFormulationController::class, 'destroy'])->name('destroy');
    // });
});

// ========================================
// LIVEWIRE COMPONENT ROUTES (If needed)
// ========================================
// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/product-customizer', App\Livewire\ProductCustomizer::class)->name('product-customizer');
//     Route::get('/shopping-cart', App\Livewire\ShoppingCart::class)->name('shopping-cart');
//     Route::get('/profile-wizard', App\Livewire\ProfileWizard::class)->name('profile-wizard');
// });