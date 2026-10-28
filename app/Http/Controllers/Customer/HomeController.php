<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the interactive storefront home page.
     */
    public function index(): View
    {
        // 1. Apple Flagship Hero Unit (iPhone 16 Pro Max or highest priced active unit)
        $featuredProduct = Product::with(['variants', 'images', 'category'])
            ->where('is_active', true)
            ->where('condition', 'new')
            ->orderByDesc('base_price')
            ->first();

        // 2. Available Series for category bar
        $seriesList = Product::where('is_active', true)
            ->distinct()
            ->orderBy('series', 'desc')
            ->pluck('series')
            ->filter()
            ->values();

        // 3. Brand New Garansi Resmi (BNIB iBox / Digimap)
        $newProducts = Product::with(['variants', 'images', 'category'])
            ->where('is_active', true)
            ->where('condition', 'new')
            ->orderBy('id', 'asc')
            ->take(8)
            ->get();

        // 4. Pre-owned / Second Like New Teruji (Grade A+ with Battery Health info)
        $secondProducts = Product::with(['variants', 'images', 'category'])
            ->where('is_active', true)
            ->where('condition', 'second')
            ->orderBy('id', 'asc')
            ->take(8)
            ->get();

        // 5. Categories with counts
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        return view('customer.home', compact(
            'featuredProduct',
            'seriesList',
            'newProducts',
            'secondProducts',
            'categories'
        ));
    }
}

