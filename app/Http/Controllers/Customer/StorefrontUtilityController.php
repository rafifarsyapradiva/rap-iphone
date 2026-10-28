<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
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

    /**
     * Halaman Konfigurasi Sistem & Status Integrasi API Pihak Ketiga (/admin/settings).
     */
    public function adminSettings(): View
    {
        $midtransKey = (string) config('services.midtrans.server_key', '');
        $fonnteToken = (string) config('services.fonnte.token', '');
        $googleClientId = (string) config('services.google.client_id', '');
        $smtpHost = (string) config('mail.mailers.smtp.host', '');

        $integrations = [
            'midtrans' => [
                'name' => 'Midtrans Payment Gateway (Snap API & Webhook)',
                'mode' => config('services.midtrans.is_production') ? 'Production' : 'Sandbox Mode',
                'configured' => !empty($midtransKey) && !str_contains($midtransKey, 'your_midtrans'),
                'webhook_url' => route('api.midtrans.callback'),
            ],
            'fonnte' => [
                'name' => 'Fonnte WhatsApp Notification Gateway',
                'endpoint' => config('services.fonnte.api_url', 'https://api.fonnte.com/send'),
                'configured' => !empty($fonnteToken) && !str_contains($fonnteToken, 'your_fonnte'),
            ],
            'smtp' => [
                'name' => 'Laravel Mailer & DomPDF Invoice Engine',
                'driver' => config('mail.default', 'log'),
                'host' => $smtpHost ?: 'localhost / log fallback',
            ],
            'google' => [
                'name' => 'Google Console OAuth 2.0 (Socialite)',
                'redirect' => config('services.google.redirect'),
                'configured' => !empty($googleClientId) && !str_contains($googleClientId, 'your_google'),
            ],
        ];

        $recentLogs = NotificationLog::latest('created_at')->take(8)->get();

        return view('admin.settings', compact('integrations', 'recentLogs'));
    }
}

