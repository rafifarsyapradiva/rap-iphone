<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Web Routes - Rap-iPhone
|--------------------------------------------------------------------------
*/

// Home / Design System Showroom
Route::get('/', function () {
    $products = Product::with(['variants', 'images', 'category'])->where('is_active', true)->get();
    return view('design-system', compact('products'));
});

// Dedicated Design System Showcase
Route::get('/design-system', function () {
    $products = Product::with(['variants', 'images', 'category'])->where('is_active', true)->get();
    return view('design-system', compact('products'));
});

// Admin Master Layout Preview
Route::get('/admin-preview', function () {
    return view('admin-preview');
});
