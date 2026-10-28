<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TradeIn;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteService
{
    protected string $token;
    protected string $apiUrl;

    public function __construct()
    {
        $this->token = (string) config('services.fonnte.token', '');
        $this->apiUrl = (string) config('services.fonnte.api_url', 'https://api.fonnte.com/send');
    }

    /**
     * Normalize Indonesian phone numbers to international format (628xxx).
     */
    public function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone)) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0062')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '08')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Send WhatsApp message via Fonnte HTTP API.
     *
     * @return array{success: bool, recipient: string, response: array<string, mixed>|null, error: string|null}
     */
    public function sendMessage(string $phone, string $message): array
    {
        $formattedPhone = $this->formatPhoneNumber($phone);

        if ($formattedPhone === '') {
            return [
                'success' => false,
                'recipient' => $phone,
                'response' => null,
                'error' => 'Nomor WhatsApp penerima kosong atau tidak valid.',
            ];
        }

        $token = (string) config('services.fonnte.token', $this->token);
        $apiUrl = (string) config('services.fonnte.api_url', $this->apiUrl);

        $isTokenEmpty = $token === ''
            || str_contains($token, 'your_fonnte')
            || str_contains($token, 'dummy');

        // Graceful sandbox simulation when FONNTE_TOKEN is not configured in local/testing env
        if ($isTokenEmpty) {
            return [
                'success' => true,
                'recipient' => $formattedPhone,
                'response' => [
                    'status' => true,
                    'simulated' => true,
                    'target' => $formattedPhone,
                    'detail' => 'Simulated WhatsApp delivery (FONNTE_TOKEN belum dikonfigurasi)',
                ],
                'error' => null,
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->asForm()
                ->post($apiUrl, [
                    'target' => $formattedPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $payload = $response->json();
            if (!is_array($payload)) {
                $payload = ['raw_body' => $response->body()];
            }

            $apiStatus = $payload['status'] ?? true;
            $isSuccess = $response->successful() && $apiStatus !== false;

            if ($isSuccess) {
                return [
                    'success' => true,
                    'recipient' => $formattedPhone,
                    'response' => $payload,
                    'error' => null,
                ];
            }

            $errorReason = (string) ($payload['reason'] ?? $payload['message'] ?? ('Fonnte HTTP ' . $response->status()));

            Log::warning("FonnteService: Gagal mengirim WhatsApp ke {$formattedPhone}: {$errorReason}", $payload);

            return [
                'success' => false,
                'recipient' => $formattedPhone,
                'response' => $payload,
                'error' => $errorReason,
            ];
        } catch (Throwable $e) {
            Log::error("FonnteService Exception ({$formattedPhone}): " . $e->getMessage());

            return [
                'success' => false,
                'recipient' => $formattedPhone,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Template 1: Tagihan Pesanan Baru (Order Created).
     */
    public function buildOrderCreatedMessage(Order $order): string
    {
        $order->loadMissing(['items', 'payment']);

        $lines = [];
        $lines[] = "🍎 *RAP-iPHONE OFFICIAL STORE*";
        $lines[] = "Halo *{$order->recipient_name}*, terima kasih telah berbelanja di Rap-iPhone!";
        $lines[] = "";
        $lines[] = "📋 *RINCIAN TAGIHAN PESANAN BARU*";
        $lines[] = "• No. Nota: *{$order->order_number}*";
        $lines[] = "• Tanggal: " . ($order->created_at ? $order->created_at->format('d M Y, H:i') : now()->format('d M Y, H:i')) . " WIB";
        $lines[] = "• Status: *MENUNGGU PEMBAYARAN*";
        $lines[] = "";
        $lines[] = "📱 *Unit yang Dipesan:*";

        foreach ($order->items as $item) {
            $subtotalFormatted = 'Rp ' . number_format((float) $item->subtotal, 0, ',', '.');
            $lines[] = "- {$item->product_name} ({$item->variant_details}) x{$item->quantity} : {$subtotalFormatted}";
        }

        if ((float) $order->discount_amount > 0) {
            $discountFormatted = 'Rp ' . number_format((float) $order->discount_amount, 0, ',', '.');
            $lines[] = "• Potongan Diskon: -{$discountFormatted}";
        }

        $shippingFormatted = (float) $order->shipping_cost > 0
            ? 'Rp ' . number_format((float) $order->shipping_cost, 0, ',', '.')
            : 'GRATIS (Asuransi Penuh)';

        $grandTotalFormatted = 'Rp ' . number_format((float) $order->grand_total, 0, ',', '.');

        $lines[] = "• Pengiriman ({$order->shipping_courier}): {$shippingFormatted}";
        $lines[] = "💰 *TOTAL TAGIHAN: {$grandTotalFormatted}*";
        $lines[] = "";
        $lines[] = "🔗 *Selesaikan Pembayaran melalui tautan resmi berikut:*";
        $lines[] = route('checkout.success', $order->order_number);
        $lines[] = "";
        $lines[] = "_Mohon selesaikan pembayaran melalui Midtrans (QRIS / Virtual Account / E-Wallet) agar unit pilihan Anda segera kami proses._";

        return implode("\n", $lines);
    }

    /**
     * Template 2: Pembayaran Lunas Terverifikasi (Payment Successful).
     */
    public function buildPaymentSuccessMessage(Order $order): string
    {
        $order->loadMissing(['items', 'payment']);

        $grandTotalFormatted = 'Rp ' . number_format((float) $order->grand_total, 0, ',', '.');
        $paymentType = strtoupper(str_replace('_', ' ', (string) ($order->payment->payment_type ?? 'Midtrans Snap')));
        $paidTime = $order->payment && $order->payment->payment_time
            ? \Carbon\Carbon::parse($order->payment->payment_time)->format('d M Y, H:i') . ' WIB'
            : now()->format('d M Y, H:i') . ' WIB';

        $lines = [];
        $lines[] = "✅ *PEMBAYARAN LUNAS TERVERIFIKASI*";
        $lines[] = "🍎 *RAP-iPHONE OFFICIAL STORE*";
        $lines[] = "";
        $lines[] = "Halo *{$order->recipient_name}*, pembayaran Anda telah kami terima dan terverifikasi secara otomatis!";
        $lines[] = "";
        $lines[] = "🧾 *RINGKASAN PEMBAYARAN*";
        $lines[] = "• No. Nota: *{$order->order_number}*";
        $lines[] = "• Status: *LUNAS (PAID)*";
        $lines[] = "• Metode Bayar: {$paymentType}";
        $lines[] = "• Waktu Lunas: {$paidTime}";
        $lines[] = "• Total Dibayar: *{$grandTotalFormatted}*";
        $lines[] = "";
        $lines[] = "📱 *Rincian Unit iPhone:*";

        foreach ($order->items as $item) {
            $imeiInfo = $item->allocated_imei ? " [IMEI: {$item->allocated_imei}]" : "";
            $lines[] = "- {$item->product_name} ({$item->variant_details}) x{$item->quantity}{$imeiInfo}";
        }

        $lines[] = "";
        $lines[] = "📄 *Nota Faktur Resmi (PDF)* telah kami lampirkan melalui email Anda.";
        $lines[] = "Tim Admin Operasional Rap-iPhone sedang melakukan QC akhir dan pengemasan unit Anda.";
        $lines[] = "";
        $lines[] = "🔍 Detail Pesanan: " . route('checkout.success', $order->order_number);

        return implode("\n", $lines);
    }

    /**
     * Template 3: Nomor Resi Pengiriman (Order Shipped).
     */
    public function buildOrderShippedMessage(Order $order): string
    {
        $order->loadMissing(['items']);

        $trackingNumber = $order->shipping_tracking_number ?: 'MENUNGGU-RESI';

        $lines = [];
        $lines[] = "🚚 *PESANAN ANDA TELAH DIKIRIM*";
        $lines[] = "🍎 *RAP-iPHONE OFFICIAL STORE*";
        $lines[] = "";
        $lines[] = "Halo *{$order->recipient_name}*, kabar gembira! Unit iPhone pesanan Anda telah diserahkan ke pihak ekspedisi dengan perlindungan asuransi penuh.";
        $lines[] = "";
        $lines[] = "📦 *INFORMASI PENGIRIMAN LOGISTIK*";
        $lines[] = "• No. Nota: *{$order->order_number}*";
        $lines[] = "• Kurir Ekspedisi: *{$order->shipping_courier}*";
        $lines[] = "• Nomor Resi: *{$trackingNumber}*";
        $lines[] = "• Alamat Tujuan: {$order->shipping_address}";
        $lines[] = "";
        $lines[] = "📱 *Unit dalam Paket:*";

        foreach ($order->items as $item) {
            $imeiInfo = $item->allocated_imei ? " (IMEI: {$item->allocated_imei})" : "";
            $lines[] = "- {$item->product_name} - {$item->variant_details} x{$item->quantity}{$imeiInfo}";
        }

        $lines[] = "";
        $lines[] = "_Harap rekam video unboxing tanpa jeda saat paket tiba sebagai syarat perlindungan asuransi & garansi resmi Rap-iPhone._";

        return implode("\n", $lines);
    }

    /**
     * Template 4: Status Program Jual / Tukar Tambah (Trade-In Status).
     */
    public function buildTradeInStatusMessage(TradeIn $tradeIn): string
    {
        $tradeIn->loadMissing('user');

        $customerName = $tradeIn->user->name ?? $tradeIn->customer_account_holder ?? 'Pelanggan Rap-iPhone';
        $statusLabel = $tradeIn->status_label;
        $estimatedFormatted = 'Rp ' . number_format((float) $tradeIn->estimated_price, 0, ',', '.');
        $effectiveFormatted = 'Rp ' . number_format($tradeIn->effective_price, 0, ',', '.');

        $lines = [];
        $lines[] = "🔄 *UPDATE STATUS TRADE-IN / BUYBACK*";
        $lines[] = "🍎 *RAP-iPHONE OFFICIAL STORE*";
        $lines[] = "";
        $lines[] = "Halo *{$customerName}*, berikut pembaruan status pengajuan Jual / Tukar Tambah iPhone Anda:";
        $lines[] = "";
        $lines[] = "📋 *RINCIAN PENGAJUAN*";
        $lines[] = "• Kode Trade-In: *{$tradeIn->trade_in_code}*";
        $lines[] = "• Unit iPhone: *{$tradeIn->phone_model} ({$tradeIn->storage_capacity})*";
        $lines[] = "• Kondisi Fisik: {$tradeIn->physical_condition} | BH: {$tradeIn->battery_health}%";
        $lines[] = "• Status Saat Ini: *{$statusLabel}*";
        $lines[] = "• Estimasi Awal: {$estimatedFormatted}";

        if (!is_null($tradeIn->final_price)) {
            $lines[] = "• *Harga Penawaran Final: {$effectiveFormatted}*";
        }

        $payoutMethodText = $tradeIn->isVoucherPayout()
            ? "Kupon Potongan Belanja ({$tradeIn->customer_account_number})"
            : "Transfer Bank {$tradeIn->customer_bank_name} - {$tradeIn->customer_account_number} a.n {$tradeIn->customer_account_holder}";

        $lines[] = "• Metode Pencairan: {$payoutMethodText}";

        if (!empty($tradeIn->admin_notes)) {
            $lines[] = "";
            $lines[] = "📝 *Catatan Teknisi / Admin:*";
            $lines[] = "\"{$tradeIn->admin_notes}\"";
        }

        $lines[] = "";
        $lines[] = match ($tradeIn->status) {
            'submitted' => "Silakan bawa atau kirimkan unit iPhone Anda ke Store Resmi Rap-iPhone untuk inspeksi fisik 15 menit.",
            'unit_received' => "Unit Anda telah diterima di Store Rap-iPhone dan sedang menunggu giliran pengecekan teknisi.",
            'inspected' => "Inspeksi teknis telah selesai dilakukan. Silakan cek penawaran harga final pada akun Anda.",
            'approved' => "Selamat! Penawaran harga final sebesar *{$effectiveFormatted}* telah DISETUJUI dan siap dicairkan.",
            'paid_out' => "Proses pencairan dana / kupon Trade-In sebesar *{$effectiveFormatted}* telah SELESAI.",
            'rejected' => "Mohon maaf, pengajuan Trade-In Anda belum dapat kami setujui. Silakan hubungi tim kami untuk informasi pengambilan unit.",
            default => "Pantau status lengkap pengajuan Anda melalui portal pelanggan Rap-iPhone.",
        };

        $lines[] = "";
        $lines[] = "🔗 Detail Trade-In: " . route('trade-in.show', $tradeIn->trade_in_code);

        return implode("\n", $lines);
    }
}
