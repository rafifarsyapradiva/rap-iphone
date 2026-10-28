<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
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

// Home / Design System Showroom
Route::get('/', function () {
    $products = Product::with(['variants', 'images', 'category'])->where('is_active', true)->get();
    return view('design-system', compact('products'));
})->name('home');

// Dedicated Design System Showcase
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
        $orders = Order::where('user_id', auth()->id())->with('items')->latest()->get();
        return view('customer.orders', compact('orders'));
    })->name('orders');

    Route::get('/profile', function () {
        return view('customer.orders', ['orders' => collect([])]);
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
