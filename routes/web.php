<?php

use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\ProductManagementController;
use App\Http\Controllers\Admin\TradeInVerificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\StorefrontUtilityController;
use App\Http\Controllers\Customer\TradeInCustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceVerificationController;
use App\Http\Controllers\Owner\ExecutiveDashboardController;
use App\Http\Controllers\Owner\FinancialReportController;
use App\Http\Controllers\Owner\StaffManagementController;
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

// Customer Storefront Routes (Phase 5 & Phase 12 Utilities)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/api/products/search', [CatalogController::class, 'liveSearch'])->name('catalog.search');
Route::get('/cek-garansi', [StorefrontUtilityController::class, 'warrantyCheck'])->name('warranty.check');
Route::get('/lacak-pesanan', [StorefrontUtilityController::class, 'trackOrder'])->name('orders.track');
Route::get('/syarat-garansi', [StorefrontUtilityController::class, 'warrantyTerms'])->name('warranty.terms');

// Shopping Cart Module
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/buy-now', [CartController::class, 'buyNow'])->name('buyNow');
    Route::patch('/update/{variantId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{variantId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

// Unified Checkout Module (Phase 6 & Phase 12 Sandbox Simulator)
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order:order_number}', [CheckoutController::class, 'success'])->name('success');
    Route::post('/{order:order_number}/simulate-sandbox', [CheckoutController::class, 'simulateSandboxPayment'])->name('simulate');
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

// Official Invoice PDF (Phase 9)
// Public authenticity verification page (QR Code target on the PDF invoice)
Route::get('/verifikasi-nota/{orderNumber}/{code}', [InvoiceVerificationController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('invoice.verify');

// Invoice download & print preview (authorized via OrderPolicy@downloadInvoice)
Route::middleware('auth')->prefix('orders/{order:order_number}/invoice')->name('orders.invoice.')->group(function () {
    Route::get('/', [InvoiceController::class, 'download'])->name('download');
    Route::get('/preview', [InvoiceController::class, 'preview'])->name('preview');
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

    // Admin Product & Inventory Management (Phase 10.1)
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductManagementController::class, 'index'])->name('index');
        Route::get('/create', [ProductManagementController::class, 'create'])->name('create');
        Route::post('/', [ProductManagementController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductManagementController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductManagementController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductManagementController::class, 'destroy'])->name('destroy');

        // Variants CRUD (Color, Capacity, Stock, HPP, Selling Price, BH, IMEI)
        Route::post('/{product}/variants', [ProductManagementController::class, 'storeVariant'])->name('variants.store');
        Route::put('/{product}/variants/{variant}', [ProductManagementController::class, 'updateVariant'])->name('variants.update');
        Route::delete('/{product}/variants/{variant}', [ProductManagementController::class, 'destroyVariant'])->name('variants.destroy');

        // Multi-Angle Product Photos Management
        Route::post('/{product}/images', [ProductManagementController::class, 'storeImages'])->name('images.store');
        Route::patch('/{product}/images/{image}/primary', [ProductManagementController::class, 'setPrimaryImage'])->name('images.primary');
        Route::patch('/{product}/images/{image}/order', [ProductManagementController::class, 'updateImageOrder'])->name('images.order');
        Route::delete('/{product}/images/{image}', [ProductManagementController::class, 'destroyImage'])->name('images.destroy');
    });

    // Admin Order Management, Logistics & Bulk Invoice / Shipping Label Printing (Phase 9.2 & 10.2)
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderManagementController::class, 'index'])->name('index');
        Route::post('/bulk/invoices', [OrderManagementController::class, 'bulkInvoices'])->name('bulk.invoices');
        Route::post('/bulk/labels', [OrderManagementController::class, 'bulkLabels'])->name('bulk.labels');
        Route::get('/{order:order_number}/invoice', [OrderManagementController::class, 'invoice'])->name('invoice');
        Route::get('/{order:order_number}', [OrderManagementController::class, 'show'])->name('show');
        Route::patch('/{order:order_number}/status', [OrderManagementController::class, 'updateStatus'])->name('status');
        Route::patch('/{order:order_number}/shipping', [OrderManagementController::class, 'updateShipping'])->name('shipping');
    });

    // Admin Trade-In Verification & Inspection Panel (Phase 7.2)
    Route::prefix('trade-ins')->name('trade-ins.')->group(function () {
        Route::get('/', [TradeInVerificationController::class, 'index'])->name('index');
        Route::get('/{tradeIn:trade_in_code}', [TradeInVerificationController::class, 'show'])->name('show');
        Route::put('/{tradeIn:trade_in_code}/inspect', [TradeInVerificationController::class, 'updateInspection'])->name('inspect');
        Route::patch('/{tradeIn:trade_in_code}/decision', [TradeInVerificationController::class, 'updateDecision'])->name('decision');
    });

    // System & Third-Party API Configuration Status (Phase 12)
    Route::get('/settings', [StorefrontUtilityController::class, 'adminSettings'])->name('settings');
});

/*
|--------------------------------------------------------------------------
| Owner Exclusive Workspace (Role: owner) — Phase 11
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    // 11.1 & 11.2: Executive Financial Metrics & Business Performance Dashboard
    Route::get('/dashboard', [ExecutiveDashboardController::class, 'index'])->name('dashboard');

    // 11.3: Financial Reports, Profit & Loss Statement, and Data Export (CSV/Excel & PDF)
    Route::get('/financial-report', [FinancialReportController::class, 'index'])->name('financial.report');
    Route::get('/financial-reports', [FinancialReportController::class, 'index'])->name('financial.reports');
    Route::get('/financial-report/export/csv', [FinancialReportController::class, 'exportCsv'])->name('financial.export.csv');
    Route::get('/financial-report/export/pdf', [FinancialReportController::class, 'exportPdf'])->name('financial.export.pdf');

    // 11.3: Store Staff & Admin Account Management
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffManagementController::class, 'index'])->name('index');
        Route::post('/', [StaffManagementController::class, 'store'])->name('store');
        Route::put('/{user}', [StaffManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [StaffManagementController::class, 'destroy'])->name('destroy');
    });
});
