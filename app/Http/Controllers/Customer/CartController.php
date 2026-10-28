<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart page.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        
        $subtotal = 0;
        $totalQuantity = 0;

        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
            $totalQuantity += $item['quantity'];
        }

        session()->put('cart_count', $totalQuantity);

        return view('customer.cart', compact('cart', 'subtotal', 'totalQuantity'));
    }

    /**
     * Add an iPhone unit variant to the shopping cart.
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $variantId = (int) $validated['product_variant_id'];
        $quantity = (int) ($validated['quantity'] ?? 1);

        $variant = ProductVariant::with(['product.images', 'product.primaryImage'])->findOrFail($variantId);

        $cart = session()->get('cart', []);
        $currentQty = isset($cart[$variantId]) ? $cart[$variantId]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        // Stock availability verification
        if ($newQty > $variant->stock) {
            $errorMessage = "Maaf, stok unit ini hanya tersisa {$variant->stock} unit.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'stock' => $variant->stock,
                ], 422);
            }
            return back()->with('error', $errorMessage);
        }

        // Image resolution
        $imagePath = null;
        if ($variant->product) {
            $imagePath = $variant->product->primaryImage->image_path 
                ?? ($variant->product->images->first()->image_path ?? null);
        }

        $cart[$variantId] = [
            'variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_name' => $variant->product->name,
            'product_slug' => $variant->product->slug,
            'storage_capacity' => $variant->storage_capacity,
            'color_name' => $variant->color_name,
            'color_hex' => $variant->color_hex,
            'price' => (float) $variant->selling_price,
            'cost_price' => (float) $variant->cost_price,
            'quantity' => $newQty,
            'stock' => $variant->stock,
            'condition' => $variant->product->condition,
            'grade' => $variant->product->grade,
            'warranty_type' => $variant->product->warranty_type,
            'battery_health' => $variant->battery_health,
            'imei' => $variant->imei,
            'image' => $imagePath,
        ];

        session()->put('cart', $cart);

        $totalCount = array_sum(array_column($cart, 'quantity'));
        session()->put('cart_count', $totalCount);

        $successMessage = "{$variant->product->name} ({$variant->storage_capacity} - {$variant->color_name}) berhasil ditambahkan ke keranjang!";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'cart_count' => $totalCount,
                'item' => $cart[$variantId],
            ]);
        }

        return redirect()->route('cart.index')->with('success', $successMessage);
    }

    /**
     * Direct purchase shortcut ("Beli Sekarang"): adds to cart and redirects to cart/checkout.
     */
    public function buyNow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $variantId = (int) $validated['product_variant_id'];
        $quantity = (int) ($validated['quantity'] ?? 1);

        $variant = ProductVariant::with(['product.images', 'product.primaryImage'])->findOrFail($variantId);

        if ($quantity > $variant->stock) {
            return back()->with('error', "Stok unit tidak mencukupi (sisa {$variant->stock} unit).");
        }

        $cart = session()->get('cart', []);

        $imagePath = null;
        if ($variant->product) {
            $imagePath = $variant->product->primaryImage->image_path 
                ?? ($variant->product->images->first()->image_path ?? null);
        }

        $cart[$variantId] = [
            'variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'product_name' => $variant->product->name,
            'product_slug' => $variant->product->slug,
            'storage_capacity' => $variant->storage_capacity,
            'color_name' => $variant->color_name,
            'color_hex' => $variant->color_hex,
            'price' => (float) $variant->selling_price,
            'cost_price' => (float) $variant->cost_price,
            'quantity' => $quantity,
            'stock' => $variant->stock,
            'condition' => $variant->product->condition,
            'grade' => $variant->product->grade,
            'warranty_type' => $variant->product->warranty_type,
            'battery_health' => $variant->battery_health,
            'imei' => $variant->imei,
            'image' => $imagePath,
        ];

        session()->put('cart', $cart);
        session()->put('cart_count', array_sum(array_column($cart, 'quantity')));

        return redirect()->route('cart.index')->with('success', 'Unit siap untuk diproses checkout!');
    }

    /**
     * Update item quantity in the cart with live recalculation.
     */
    public function update(Request $request, int $variantId): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $newQty = (int) $validated['quantity'];
        $cart = session()->get('cart', []);

        if (!isset($cart[$variantId])) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan di keranjang.'], 404);
        }

        $variant = ProductVariant::find($variantId);
        if (!$variant || $newQty > $variant->stock) {
            return response()->json([
                'success' => false, 
                'message' => "Stok maksimal yang tersedia: " . ($variant ? $variant->stock : 0) . " unit.",
                'available_stock' => $variant ? $variant->stock : 0,
            ], 422);
        }

        $cart[$variantId]['quantity'] = $newQty;
        session()->put('cart', $cart);

        $subtotal = 0;
        $totalQuantity = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
            $totalQuantity += $item['quantity'];
        }
        session()->put('cart_count', $totalQuantity);

        $itemSubtotal = $cart[$variantId]['price'] * $newQty;

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kuantitas keranjang berhasil diperbarui.',
                'item_subtotal_formatted' => 'Rp ' . number_format($itemSubtotal, 0, ',', '.'),
                'grand_total_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'cart_count' => $totalQuantity,
                'quantity' => $newQty,
            ]);
        }

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request, int $variantId): JsonResponse|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            session()->put('cart', $cart);
        }

        $subtotal = 0;
        $totalQuantity = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
            $totalQuantity += $item['quantity'];
        }
        session()->put('cart_count', $totalQuantity);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang.',
                'cart_count' => $totalQuantity,
                'grand_total_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'is_empty' => empty($cart),
            ]);
        }

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        session()->put('cart_count', 0);

        return redirect()->route('cart.index')->with('success', 'Keranjang belanja telah dikosongkan.');
    }
}

