<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfInstance;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class InvoicePdfService
{
    /**
     * Ukuran kertas label thermal 100 x 150 mm dalam satuan point (1 mm = 2.83465 pt).
     */
    public const LABEL_PAPER = [0, 0, 283.46, 425.20];

    public function __construct(protected QrCodeService $qrCodeService)
    {
    }

    /**
     * Generate DomPDF instance for the specified Order.
     */
    public function generatePdf(Order $order): DomPdfInstance
    {
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->buildViewData($order),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Render raw PDF binary string for email attachments.
     */
    public function generatePdfBinary(Order $order): string
    {
        return $this->generatePdf($order)->output();
    }

    /**
     * Generate satu dokumen PDF berisi banyak nota (cetak massal admin).
     *
     * @param  Collection<int, Order>  $orders
     */
    public function generateBulkPdf(Collection $orders): DomPdfInstance
    {
        $invoices = $orders->map(fn (Order $order) => $this->buildViewData($order))->values();

        $pdf = Pdf::loadView('pdf.invoices-bulk', ['invoices' => $invoices]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Generate label pengiriman thermal 100 x 150 mm, satu label per halaman.
     *
     * @param  Collection<int, Order>  $orders
     */
    public function generateShippingLabelsPdf(Collection $orders): DomPdfInstance
    {
        $orders->each->loadMissing(['items']);

        $labels = $orders->map(fn (Order $order) => [
            'order' => $order,
            'qr' => $this->qrCodeService->svgDataUri($order->order_number),
            'items_summary' => $order->items
                ->map(fn (OrderItem $item) => $item->quantity . 'x ' . $item->product_name . ' (' . $item->variant_details . ')')
                ->implode(', '),
            'total_units' => (int) $order->items->sum('quantity'),
        ])->values();

        $pdf = Pdf::loadView('pdf.shipping-labels', [
            'labels' => $labels,
            'store' => config('invoice.store'),
            'generatedAt' => now()->format('d M Y, H:i') . ' WIB',
        ]);
        $pdf->setPaper(self::LABEL_PAPER, 'portrait');

        return $pdf;
    }

    /**
     * Get standardized PDF filename for an Order.
     */
    public function getFilename(Order $order): string
    {
        return 'Nota-Rap-iPhone-' . $order->order_number . '.pdf';
    }

    /**
     * Nama file untuk dokumen cetak massal ('invoice' | 'label').
     */
    public function getBulkFilename(string $type): string
    {
        $prefix = $type === 'label' ? 'Label-Pengiriman' : 'Invoice-Massal';

        return $prefix . '-Rap-iPhone-' . now()->format('Ymd-His') . '.pdf';
    }

    /**
     * Apakah nota pesanan ini sudah sah diterbitkan untuk customer (status lunas).
     */
    public function isDownloadable(Order $order): bool
    {
        return in_array($order->status, config('invoice.downloadable_statuses', []), true);
    }

    /**
     * Kode verifikasi keaslian nota: HMAC-SHA256(order_number|grand_total, APP_KEY) 16 karakter.
     */
    public function verificationCode(Order $order): string
    {
        $payload = $order->order_number . '|' . number_format((float) $order->grand_total, 2, '.', '');

        return strtoupper(substr(hash_hmac('sha256', $payload, (string) config('app.key')), 0, 16));
    }

    /**
     * Validasi kode verifikasi secara timing-safe.
     */
    public function isValidVerificationCode(Order $order, string $code): bool
    {
        return hash_equals($this->verificationCode($order), strtoupper(trim($code)));
    }

    /**
     * URL publik halaman verifikasi keaslian nota (dikodekan ke dalam QR).
     */
    public function verificationUrl(Order $order): string
    {
        return route('invoice.verify', [
            'orderNumber' => $order->order_number,
            'code' => $this->verificationCode($order),
        ]);
    }

    /**
     * Hitung informasi garansi untuk satu item pesanan.
     *
     * @return array{label: string, months: int, starts_at: ?CarbonInterface, ends_at: ?CarbonInterface, is_active: bool}
     */
    public function warrantyFor(OrderItem $item, Order $order): array
    {
        $product = $item->variant?->product;
        $type = $product->warranty_type ?? 'toko';
        $rule = config("invoice.warranty.{$type}", config('invoice.warranty.toko'));

        $startsAt = null;
        $endsAt = null;

        if ($this->isDownloadable($order)) {
            $startsAt = $order->payment?->payment_time ?? $order->created_at ?? now();
            $endsAt = $startsAt->copy()->addMonthsNoOverflow((int) $rule['months']);
        }

        return [
            'label' => $rule['label'],
            'months' => (int) $rule['months'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_active' => $endsAt !== null && $endsAt->isFuture(),
        ];
    }

    /**
     * Siapkan seluruh data yang dibutuhkan template nota.
     *
     * @return array<string, mixed>
     */
    public function buildViewData(Order $order): array
    {
        $order->loadMissing(['user', 'items.variant.product', 'payment', 'tradeIn']);

        $items = $order->items->map(function (OrderItem $item) use ($order) {
            $product = $item->variant?->product;
            $isSecond = $product && $product->condition === 'second';
            $imei = $item->allocated_imei ?: $item->variant?->imei;

            return [
                'model' => $item,
                'condition' => $isSecond
                    ? 'Second Like New (' . ($product->grade ?: 'Grade A') . ')'
                    : 'Brand New In Box (BNIB)',
                'battery_health' => $isSecond ? $item->variant?->battery_health : null,
                'imei' => $imei ? $this->formatImei($imei) : null,
                'warranty' => $this->warrantyFor($item, $order),
            ];
        });

        $verificationCode = $this->verificationCode($order);
        $verificationUrl = $this->verificationUrl($order);

        return [
            'order' => $order,
            'items' => $items,
            'store' => config('invoice.store'),
            'terms' => $this->terms(),
            'status' => $this->statusMeta($order),
            'paidAt' => $order->payment?->payment_time,
            'verificationCode' => $verificationCode,
            'verificationUrl' => $verificationUrl,
            'qrDataUri' => $this->qrCodeService->svgDataUri($verificationUrl),
            'amountInWords' => $this->terbilang((int) round((float) $order->grand_total)) . ' rupiah',
            'generatedAt' => now()->format('d M Y, H:i') . ' WIB',
        ];
    }

    /**
     * Syarat & ketentuan klaim garansi dengan placeholder yang sudah diganti.
     *
     * @return array<int, string>
     */
    public function terms(): array
    {
        $days = (int) config('invoice.replacement_days', 7);

        return array_map(
            fn (string $term) => str_replace(':days', (string) $days, $term),
            config('invoice.terms', [])
        );
    }

    /**
     * Label, kelas CSS, dan watermark berdasarkan status pesanan.
     *
     * @return array{label: string, class: string, watermark: ?string, is_paid: bool}
     */
    public function statusMeta(Order $order): array
    {
        return match ($order->status) {
            'paid' => ['label' => 'LUNAS TERVERIFIKASI', 'class' => 'status-paid', 'watermark' => null, 'is_paid' => true],
            'processing' => ['label' => 'LUNAS - DIPROSES', 'class' => 'status-paid', 'watermark' => null, 'is_paid' => true],
            'shipped' => ['label' => 'LUNAS - DIKIRIM', 'class' => 'status-paid', 'watermark' => null, 'is_paid' => true],
            'completed' => ['label' => 'LUNAS - SELESAI', 'class' => 'status-paid', 'watermark' => null, 'is_paid' => true],
            'cancelled' => ['label' => 'DIBATALKAN', 'class' => 'status-cancelled', 'watermark' => 'DIBATALKAN', 'is_paid' => false],
            default => ['label' => 'MENUNGGU PEMBAYARAN', 'class' => 'status-pending', 'watermark' => 'BELUM LUNAS', 'is_paid' => false],
        };
    }

    /**
     * Format IMEI numerik menjadi grup 4 digit agar mudah dibaca: 3599 8877 6655 443.
     */
    public function formatImei(string $imei): string
    {
        $clean = preg_replace('/\s+/', '', $imei);

        return ctype_digit($clean) ? trim(chunk_split($clean, 4, ' ')) : $imei;
    }

    /**
     * Samarkan IMEI untuk halaman publik: 3599 **** **** 443.
     */
    public function maskImei(?string $imei): string
    {
        if (! $imei) {
            return '-';
        }

        $clean = preg_replace('/\s+/', '', $imei);
        $length = strlen($clean);

        if ($length <= 7) {
            return str_repeat('*', $length);
        }

        $masked = substr($clean, 0, 4) . str_repeat('*', $length - 7) . substr($clean, -3);

        return ctype_digit($clean) ? trim(chunk_split($masked, 4, ' ')) : $masked;
    }

    /**
     * Samarkan nama untuk halaman publik: "Reza Rahadian" -> "Re** Ra*****".
     */
    public function maskName(?string $name): string
    {
        if (! $name) {
            return '-';
        }

        return collect(preg_split('/\s+/', trim($name)))
            ->map(function (string $word) {
                $length = mb_strlen($word);

                return $length <= 2
                    ? mb_substr($word, 0, 1) . str_repeat('*', max($length - 1, 0))
                    : mb_substr($word, 0, 2) . str_repeat('*', $length - 2);
            })
            ->implode(' ');
    }

    /**
     * Konversi angka menjadi kalimat terbilang Bahasa Indonesia.
     */
    public function terbilang(int $number): string
    {
        if ($number === 0) {
            return 'nol';
        }

        return trim(preg_replace('/\s+/', ' ', $this->spell(abs($number))));
    }

    protected function spell(int $n): string
    {
        $words = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        return match (true) {
            $n < 12 => ' ' . $words[$n],
            $n < 20 => $this->spell($n - 10) . ' belas',
            $n < 100 => $this->spell(intdiv($n, 10)) . ' puluh' . $this->spell($n % 10),
            $n < 200 => ' seratus' . $this->spell($n - 100),
            $n < 1000 => $this->spell(intdiv($n, 100)) . ' ratus' . $this->spell($n % 100),
            $n < 2000 => ' seribu' . $this->spell($n - 1000),
            $n < 1000000 => $this->spell(intdiv($n, 1000)) . ' ribu' . $this->spell($n % 1000),
            $n < 1000000000 => $this->spell(intdiv($n, 1000000)) . ' juta' . $this->spell($n % 1000000),
            $n < 1000000000000 => $this->spell(intdiv($n, 1000000000)) . ' miliar' . $this->spell($n % 1000000000),
            default => $this->spell(intdiv($n, 1000000000000)) . ' triliun' . $this->spell($n % 1000000000000),
        };
    }
}
