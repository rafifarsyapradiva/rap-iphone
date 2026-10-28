<?php

use App\Http\Controllers\Admin\TradeInVerificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\TradeInCustomerController;
use App\Models\Order;
use App\Models\Product;
use App\Models\TradeIn;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Rap-iPhone
|--------------------------------------------------------------------------
*/

// Customer Storefront Routes (Phase 5)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/api/products/search', [CatalogController::class, 'liveSearch'])->name('catalog.search');

// Shopping Cart Module
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/buy-now', [CartController::class, 'buyNow'])->name('buyNow');
    Route::patch('/update/{variantId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{variantId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

// Unified Checkout Module (Phase 6)
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order:order_number}', [CheckoutController::class, 'success'])->name('success');
});

// Trade-In & Buyback Program Module (Phase 7)
Route::prefix('trade-in')->name('trade-in.')->group(function () {
    Route::get('/', [TradeInCustomerController::class, 'index'])->name('index');
    Route::post('/calculate', [TradeInCustomerController::class, 'calculate'])->name('calculate');
    Route::middleware('auth')->group(function () {
        Route::post('/', [TradeInCustomerController::class, 'store'])->name('store');
        Route::get('/{tradeIn:trade_in_code}', [TradeInCustomerController::class, 'show'])->name('show');
    });
});

// Dedicated Design System Showcase (Preserved for Reference & Testing)
Route::get('/design-system', function () {
    $products = Product::with(['variants', 'images', 'category'])->where('is_active', true)->get();
    return view('design-system', compact('products'));
})->name('design-system');

// Admin Master Layout Preview
Route::get('/admin-preview', function () {
    return view('admin-preview');
})->name('admin.preview');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Conventional Auth
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Google Console OAuth (Laravel Socialite)
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

/*
|--------------------------------------------------------------------------
| Authenticated Common Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Customer Protected Workspace (Role: customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/orders', function () {
        $orders = Order::where('user_id', auth()->id())->with(['items', 'payment'])->latest()->get();
        $tradeIns = TradeIn::where('user_id', auth()->id())->latest()->get();
        return view('customer.orders', compact('orders', 'tradeIns'));
    })->name('orders');

    Route::get('/profile', function () {
        $tradeIns = TradeIn::where('user_id', auth()->id())->latest()->get();
        return view('customer.orders', ['orders' => collect([]), 'tradeIns' => $tradeIns]);
    })->name('profile');
});

/*
|--------------------------------------------------------------------------
| Admin & Owner Shared Workspace (Role: admin, owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $pendingTradeIns = TradeIn::where('status', 'submitted')->count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'pendingTradeIns', 'recentOrders'));
    })->name('dashboard');

    // Admin Trade-In Verification & Inspection Panel (Phase 7.2)
    Route::prefix('trade-ins')->name('trade-ins.')->group(function () {
        Route::get('/', [TradeInVerificationController::class, 'index'])->name('index');
        Route::get('/{tradeIn:trade_in_code}', [TradeInVerificationController::class, 'show'])->name('show');
        Route::put('/{tradeIn:trade_in_code}/inspect', [TradeInVerificationController::class, 'updateInspection'])->name('inspect');
        Route::patch('/{tradeIn:trade_in_code}/decision', [TradeInVerificationController::class, 'updateDecision'])->name('decision');
    });
});

/*
|--------------------------------------------------------------------------
| Owner Exclusive Workspace (Role: owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        $totalRevenue = Order::where('status', 'paid')->sum('grand_total');
        $totalAdmins = User::where('role', 'admin')->count();
        $totalCustomers = User::where('role', 'customer')->count();

        return view('owner.dashboard', compact('totalRevenue', 'totalAdmins', 'totalCustomers'));
    })->name('dashboard');

    Route::get('/financial-report', function () {
        $totalRevenue = Order::where('status', 'paid')->sum('grand_total');
        return view('owner.dashboard', compact('totalRevenue'));
    })->name('financial.report');
});
