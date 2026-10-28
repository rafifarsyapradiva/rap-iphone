<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display the filterable iPhone catalog page.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['variants', 'images', 'category'])
            ->where('is_active', true);

        // Filter: Series
        if ($request->filled('series')) {
            $query->where('series', $request->query('series'));
        }

        // Filter: Condition (new / second)
        if ($request->filled('condition')) {
            $query->where('condition', $request->query('condition'));
        }

        // Filter: Warranty Type
        if ($request->filled('warranty')) {
            $query->where('warranty_type', $request->query('warranty'));
        }

        // Filter: Minimum Price
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', (float) $request->query('min_price'));
        }

        // Filter: Maximum Price
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', (float) $request->query('max_price'));
        }

        // Filter: Keyword Search
        if ($request->filled('q')) {
            $keyword = trim($request->query('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('series', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Sorting
        $sort = $request->query('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Distinct series for filter sidebar
        $availableSeries = Product::where('is_active', true)
            ->distinct()
            ->orderBy('series', 'desc')
            ->pluck('series')
            ->filter()
            ->values();

        return view('customer.catalog', compact('products', 'availableSeries', 'sort'));
    }

    /**
     * Display the Product Detail Page (PDP) for a specific iPhone.
     */
    public function show(string $slug): View
    {
        $product = Product::with(['variants', 'images', 'category'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Extract available unique colors and capacities
        $variants = $product->variants;
        $colors = $variants->unique('color_name')->values();
        $storages = $variants->unique('storage_capacity')->pluck('storage_capacity')->values();

        // Prepare JSON payload for dynamic client-side state
        $variantsJson = $variants->map(function ($v) {
            return [
                'id' => $v->id,
                'storage_capacity' => $v->storage_capacity,
                'color_name' => $v->color_name,
                'color_hex' => $v->color_hex,
                'cost_price' => (float) $v->cost_price,
                'selling_price' => (float) $v->selling_price,
                'selling_price_formatted' => 'Rp ' . number_format($v->selling_price, 0, ',', '.'),
                'stock' => $v->stock,
                'sku' => $v->sku,
                'battery_health' => $v->battery_health,
                'imei' => $v->imei,
            ];
        })->values()->toJson();

        // Related iPhones (same series or category, excluding current)
        $relatedProducts = Product::with(['variants', 'images', 'category'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('series', $product->series)
                  ->orWhere('category_id', $product->category_id);
            })
            ->take(4)
            ->get();

        return view('customer.pdp', compact('product', 'colors', 'storages', 'variantsJson', 'relatedProducts'));
    }

    /**
     * Instant live search endpoint for the Ctrl+K search modal.
     */
    public function liveSearch(Request $request): JsonResponse
    {
        $keyword = trim($request->query('q', ''));

        if (mb_strlen($keyword) < 2) {
            return response()->json([]);
        }

        $results = Product::with(['variants', 'images'])
            ->where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('series', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%");
            })
            ->take(6)
            ->get()
            ->map(function ($item) {
                $primaryImg = $item->primaryImage->image_path ?? ($item->images->first()->image_path ?? null);
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'series' => $item->series,
                    'condition' => $item->condition,
                    'grade' => $item->grade,
                    'warranty_type' => $item->warranty_type,
                    'base_price' => (float) $item->base_price,
                    'formatted_price' => 'Rp ' . number_format($item->base_price, 0, ',', '.'),
                    'image_url' => $primaryImg ? asset($primaryImg) : null,
                    'url' => route('catalog.show', $item->slug),
                ];
            });

        return response()->json($results);
    }
}

