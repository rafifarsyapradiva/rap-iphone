<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontUtilityController extends Controller
{
    /**
     * Halaman Cek Status IMEI & Garansi Unit Rap-iPhone (/cek-garansi).
     */
    public function warrantyCheck(Request $request): View
    {
        $query = trim((string) $request->query('imei', ''));
        $variantResult = null;
        $orderItemResult = null;

        if ($query !== '') {
            $variantResult = ProductVariant::with('product')
                ->where('imei', $query)
                ->orWhere('sku', $query)
                ->first();

            $orderItemResult = OrderItem::with(['order', 'variant.product'])
                ->where('allocated_imei', $query)
                ->latest()
                ->first();
        }

        return view('customer.utilities', [
            'activeTab' => 'warranty-check',
            'imeiQuery' => $query,
            'variantResult' => $variantResult,
            'orderItemResult' => $orderItemResult,
            'trackedOrder' => null,
            'orderQuery' => '',
        ]);
    }

    /**
     * Halaman Lacak Pesanan & Resi Pengiriman (/lacak-pesanan).
     */
    public function trackOrder(Request $request): View
    {
        $orderQuery = strtoupper(trim((string) $request->query('order_number', '')));
        $trackedOrder = null;

        if ($orderQuery !== '') {
            $trackedOrder = Order::with(['items', 'payment'])
                ->where('order_number', $orderQuery)
                ->first();
        }

        return view('customer.utilities', [
            'activeTab' => 'track-order',
            'orderQuery' => $orderQuery,
            'trackedOrder' => $trackedOrder,
            'imeiQuery' => '',
            'variantResult' => null,
            'orderItemResult' => null,
        ]);
    }

    /**
     * Halaman Syarat & Ketentuan Garansi Resmi & Garansi Toko (/syarat-garansi).
     */
    public function warrantyTerms(): View
    {
        return view('customer.utilities', [
            'activeTab' => 'warranty-terms',
            'imeiQuery' => '',
            'variantResult' => null,
            'orderItemResult' => null,
            'orderQuery' => '',
            'trackedOrder' => null,
        ]);
    }
}

