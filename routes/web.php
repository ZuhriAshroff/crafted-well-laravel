<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomProductController;
use App\Http\Controllers\BaseFormulationController;
use App\Http\Controllers\AdminController;
use App\Livewire\SurveyComponent;

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

// Custom product routes for authenticated users
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/custom-products/{id}', [CustomProductController::class, 'show'])->name('custom-products.show');
});

// ========================================
// PROTECTED WEB ROUTES (Authentication Required)
// ========================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // ========================================
    // DASHBOARD ROUTES - ROLE BASED ROUTING
    // ========================================
    
    // Main dashboard route - redirects based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('user.dashboard');
    })->name('dashboard');
    
    // User Dashboard - separate route for regular users
    Route::get('/user/dashboard', function () {
        $user = auth()->user();
        
        // Get user stats
        $stats = [
            'total_products' => \App\Models\CustomProduct::getUserProductsCount($user->id),
            'recent_count' => \App\Models\CustomProduct::where('user_id', $user->id)
                ->whereMonth('formulation_date', now()->month)
                ->count(),
            'skin_type' => 'Not Set', // You can get this from user profile if exists
            'total_orders' => 0, // Add order count when you have orders working
        ];
        
        // Get recent products
        $recentProducts = \App\Models\CustomProduct::getRecentForUser($user->id, 5);
        
        return view('user.dashboard', compact('stats', 'recentProducts'));
    })->name('user.dashboard');

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

    // ========================================
    // CUSTOM PRODUCTS WEB ROUTES
    // ========================================
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
    // CART & CHECKOUT ROUTES (uncomment as needed)
    // ========================================
    /*
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    */

    // ========================================
    // WISHLIST & ACCOUNT ROUTES (uncomment as needed)
    // ========================================
    /*
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/add/{product}', [WishlistController::class, 'add'])->name('add');
        Route::delete('/remove/{product}', [WishlistController::class, 'remove'])->name('remove');
    });

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
        Route::put('/settings', [AccountController::class, 'updateSettings'])->name('settings.update');
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    });
    */
});

// ========================================
// ADMIN AUTHENTICATION & MANAGEMENT ROUTES
// ========================================

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Admin login routes (no middleware required)
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('/check-auth', [AdminController::class, 'checkAuth'])->name('check-auth');

    // Protected admin routes
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        
        // ========================================
        // ADMIN DASHBOARD & ANALYTICS
        // ========================================
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard'); // Main admin dashboard at /admin
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.alt'); // Alternative route
        Route::get('/analytics', [AdminController::class, 'dashboardAnalytics'])->name('analytics');
        Route::get('/system-health', [AdminController::class, 'systemHealth'])->name('system-health');
        Route::get('/user-management', [AdminController::class, 'userManagement'])->name('user-management');
        Route::get('/settings', [AdminController::class, 'platformSettings'])->name('settings');
        Route::post('/export', [AdminController::class, 'exportData'])->name('export');

        // ========================================
        // ADMIN PRODUCT MANAGEMENT
        // ========================================
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'adminIndex'])->name('index');
            Route::get('/data', [ProductController::class, 'getAdminProducts'])->name('data');
            Route::get('/options', [ProductController::class, 'getProductOptions'])->name('options');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/create', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::post('/{productId}/update', [ProductController::class, 'update'])->name('update.json');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('/{productId}/delete', [ProductController::class, 'destroy'])->name('destroy.json');
            Route::get('/analytics/overview', [ProductController::class, 'analytics'])->name('analytics');
        });

        // ========================================
        // ADMIN CUSTOM PRODUCTS MANAGEMENT (FIXED)
        // ========================================
        Route::prefix('custom-products')->name('custom-products.')->group(function () {
            Route::get('/', [CustomProductController::class, 'adminIndex'])->name('index');
            Route::get('/{customProduct}', [CustomProductController::class, 'adminShow'])->name('show');
            Route::get('/analytics/overview', [CustomProductController::class, 'analytics'])->name('analytics');
            Route::get('/export/data', [CustomProductController::class, 'exportData'])->name('export');
            
            // Additional admin custom product routes
            Route::put('/{customProduct}/status', [CustomProductController::class, 'updateStatus'])->name('update-status');
            Route::post('/{customProduct}/notes', [CustomProductController::class, 'addAdminNotes'])->name('add-notes');
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

        // ========================================
        // ADMIN BASE FORMULATIONS MANAGEMENT
        // ========================================
        Route::prefix('base-formulations')->name('base-formulations.')->group(function () {
            Route::get('/', [BaseFormulationController::class, 'index'])->name('index');
            Route::get('/create', [BaseFormulationController::class, 'create'])->name('create');
            Route::post('/', [BaseFormulationController::class, 'store'])->name('store');
            Route::get('/{baseFormulation}', [BaseFormulationController::class, 'show'])->name('show');
            Route::get('/{baseFormulation}/edit', [BaseFormulationController::class, 'edit'])->name('edit');
            Route::put('/{baseFormulation}', [BaseFormulationController::class, 'update'])->name('update');
            Route::delete('/{baseFormulation}', [BaseFormulationController::class, 'destroy'])->name('destroy');
            Route::post('/{baseFormulation}/deactivate', [BaseFormulationController::class, 'deactivate'])->name('deactivate');
            Route::get('/{baseFormulation}/clone', [BaseFormulationController::class, 'showCloneForm'])->name('clone-form');
            Route::post('/{baseFormulation}/clone', [BaseFormulationController::class, 'clone'])->name('clone');
            Route::get('/analytics', [BaseFormulationController::class, 'analytics'])->name('analytics');
        });

        // ========================================
        // ADDITIONAL ADMIN ROUTES (uncomment as needed)
        // ========================================
        /*
        // Admin User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'adminIndex'])->name('index');
            Route::get('/{user}', [UserController::class, 'adminShow'])->name('show');
            Route::put('/{user}', [UserController::class, 'adminUpdate'])->name('update');
            Route::delete('/{user}', [UserController::class, 'adminDestroy'])->name('destroy');
        });

        // Admin Analytics & Reports
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [AnalyticsController::class, 'sales'])->name('sales');
            Route::get('/users', [AnalyticsController::class, 'users'])->name('users');
            Route::get('/products', [AnalyticsController::class, 'products'])->name('products');
        });

        // Admin System Management
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/settings', [SystemController::class, 'settings'])->name('settings');
            Route::put('/settings', [SystemController::class, 'updateSettings'])->name('settings.update');
            Route::get('/logs', [SystemController::class, 'logs'])->name('logs');
            Route::post('/cache/clear', [SystemController::class, 'clearCache'])->name('cache.clear');
            Route::get('/backup', [SystemController::class, 'backup'])->name('backup');
        });
        */
    });
});

// ========================================
// LIVEWIRE COMPONENT ROUTES (If needed)
// ========================================
/*
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/product-customizer', App\Livewire\ProductCustomizer::class)->name('product-customizer');
    Route::get('/shopping-cart', App\Livewire\ShoppingCart::class)->name('shopping-cart');
    Route::get('/profile-wizard', App\Livewire\ProfileWizard::class)->name('profile-wizard');
});
*/