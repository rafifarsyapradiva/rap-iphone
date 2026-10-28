<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\FinancialAnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReportController extends Controller
{
    public function __construct(protected FinancialAnalyticsService $analyticsService)
    {
    }

    /**
     * Tampilkan halaman Laporan Penjualan, Laba Rugi, Arus Kas, dan Panel Ekspor Data.
     */
    public function index(Request $request): View
    {
        $dateRange = $this->resolveFilterFromRequest($request);
        $report = $this->analyticsService->getDetailedFinancialReport(
            $dateRange['start'],
            $dateRange['end']
        );

        return view('owner.financial-report', [
            'dateRange' => $dateRange,
            'summary' => $report['summary'],
            'cashFlow' => $report['cash_flow'],
            'topProducts' => $report['top_products'],
            'conversion' => $report['conversion'],
            'orders' => $report['orders'],
            'tradeIns' => $report['trade_ins'],
        ]);
    }

    /**
     * Ekspor Laporan Penjualan dan Laba Rugi ke format Excel / CSV (UTF-8 BOM).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $dateRange = $this->resolveFilterFromRequest($request);
        $report = $this->analyticsService->getDetailedFinancialReport(
            $dateRange['start'],
            $dateRange['end']
        );

        $filename = 'Laporan-Laba-Rugi-Rap-iPhone-' . now()->format('Ymd-His') . '.csv';
        $store = config('invoice.store');
        $ownerName = Auth::user()?->name ?? 'Owner Rap-iPhone';

        return response()->streamDownload(function () use ($dateRange, $report, $store, $ownerName) {
            $handle = fopen('php://output', 'w');

            // Tulis UTF-8 BOM agar Microsoft Excel otomatis mengenali encoding & pemisah kolom
            fwrite($handle, "\xEF\xBB\xBF");

            // 1. Header Identitas Laporan
            fputcsv($handle, [$store['name'] . ' — LAPORAN PENJUALAN & LABA RUGI EKSEKUTIF']);
            fputcsv($handle, ['Tagline', $store['tagline'] ?? 'Premium Apple Retail & Trade-In Partner']);
            fputcsv($handle, ['NPWP / NIB', ($store['npwp'] ?? '-') . ' / ' . ($store['nib'] ?? '-')]);
            fputcsv($handle, ['Periode Laporan', $dateRange['label']]);
            fputcsv($handle, ['Dicetak Oleh', $ownerName]);
            fputcsv($handle, ['Waktu Ekspor', now()->format('d M Y H:i:s') . ' WIB']);
            fputcsv($handle, []);

            // 2. Ringkasan Eksekutif Laba Rugi (P&L Summary)
            $summary = $report['summary'];
            $cashFlow = $report['cash_flow'];

            fputcsv($handle, ['=== I. RINGKASAN LABA RUGI & ARUS KAS ===']);
            fputcsv($handle, ['Metrik Keuangan', 'Nilai (IDR / Satuan)']);
            fputcsv($handle, ['Total Transaksi Lunas', $summary['orders_count'] . ' Pesanan']);
            fputcsv($handle, ['Total Unit iPhone Terjual (Keluar)', $summary['units_sold'] . ' Unit']);
            fputcsv($handle, ['Penjualan Kotor Produk (Gross Sales)', $summary['gross_items_sales']]);
            fputcsv($handle, ['Total Potongan Diskon / Voucher', $summary['discount_total']]);
            fputcsv($handle, ['Pendapatan Ongkos Kirim', $summary['shipping_revenue']]);
            fputcsv($handle, ['Total Omzet Penjualan (Grand Total)', $summary['revenue']]);
            fputcsv($handle, ['Total Harga Pokok Penjualan (HPP / COGS)', $summary['cogs']]);
            fputcsv($handle, ['Laba Kotor Produk (Harga Jual - HPP)', $summary['gross_item_profit']]);
            fputcsv($handle, ['Laba Bersih Otomatis (Laba Kotor - Diskon)', $summary['net_profit']]);
            fputcsv($handle, ['Rasio Margin Laba Bersih (%)', $summary['profit_margin'] . '%']);
            fputcsv($handle, ['Unit Trade-In Masuk (Disetujui / Lunas)', $cashFlow['trade_in_units_in'] . ' Unit']);
            fputcsv($handle, ['Arus Kas Keluar Trade-In (Transfer Bank)', $cashFlow['trade_in_cash_out']]);
            fputcsv($handle, ['Nilai Kupon Trade-In (Voucher Belanja)', $cashFlow['trade_in_voucher_out']]);
            fputcsv($handle, ['Total Akuisisi Unit Trade-In Masuk', $cashFlow['trade_in_total_value']]);
            fputcsv($handle, ['Surplus Arus Kas Bersih (Penjualan - Trade-In)', $cashFlow['net_cash_flow']]);
            fputcsv($handle, []);

            // 3. Analisis Produk iPhone Terlaris
            fputcsv($handle, ['=== II. ANALISIS PRODUK IPHONE TERLARIS (TOP SELLING) ===']);
            fputcsv($handle, [
                'Peringkat',
                'Model iPhone',
                'Seri',
                'Varian Terpopuler',
                'Unit Terjual',
                'Jumlah Pesanan',
                'Total Omzet (Rp)',
                'Total HPP (Rp)',
                'Laba Produk (Rp)',
                'Margin (%)',
                'Pangsa Unit (%)',
            ]);

            foreach ($report['top_products'] as $prod) {
                fputcsv($handle, [
                    '#' . $prod['rank'],
                    $prod['product_name'],
                    $prod['series'],
                    $prod['popular_variant'],
                    $prod['units_sold'],
                    $prod['orders_count'],
                    $prod['revenue'],
                    $prod['cogs'],
                    $prod['gross_profit'],
                    $prod['profit_margin'] . '%',
                    $prod['share_pct'] . '%',
                ]);
            }
            fputcsv($handle, []);

            // 4. Rincian Transaksi Penjualan & Laba Rugi per Nota
            fputcsv($handle, ['=== III. RINCIAN TRANSAKSI PENJUALAN & KALKULASI LABA BERSIH ===']);
            fputcsv($handle, [
                'No',
                'Nomor Nota',
                'Tanggal Transaksi',
                'Nama Pelanggan',
                'Status Pesanan',
                'Metode Pembayaran',
                'Rincian Unit iPhone',
                'Qty Unit',
                'Harga Produk (Rp)',
                'Diskon (Rp)',
                'Ongkir (Rp)',
                'Total Omzet / Grand Total (Rp)',
                'Total HPP Unit (Rp)',
                'Laba Bersih (Rp)',
                'Margin (%)',
            ]);

            foreach ($report['orders'] as $idx => $order) {
                fputcsv($handle, [
                    $idx + 1,
                    $order->order_number,
                    $order->created_at?->format('Y-m-d H:i'),
                    $order->recipient_name ?: ($order->user?->name ?? 'Pelanggan'),
                    strtoupper($order->status),
                    strtoupper($order->payment?->payment_type ?? 'midtrans_snap'),
                    $order->items_summary,
                    $order->calculated_units,
                    (float) $order->total_items_price,
                    (float) $order->discount_amount,
                    (float) $order->shipping_cost,
                    (float) $order->grand_total,
                    (float) $order->calculated_cogs,
                    (float) $order->calculated_net_profit,
                    $order->calculated_margin . '%',
                ]);
            }
            fputcsv($handle, []);

            // 5. Rincian Akuisisi Unit Trade-In Masuk
            fputcsv($handle, ['=== IV. RINCIAN ARUS KAS AKUISISI TRADE-IN MASUK ===']);
            fputcsv($handle, [
                'No',
                'Kode Trade-In',
                'Tanggal',
                'Nama Pelanggan',
                'Model iPhone Masuk',
                'Kapasitas',
                'Battery Health (%)',
                'Kondisi Fisik',
                'Metode Pencairan',
                'Status',
                'Harga Estimasi (Rp)',
                'Harga Deal / Final (Rp)',
            ]);

            foreach ($report['trade_ins'] as $idx => $tradeIn) {
                fputcsv($handle, [
                    $idx + 1,
                    $tradeIn->trade_in_code,
                    $tradeIn->created_at?->format('Y-m-d H:i'),
                    $tradeIn->user?->name ?? $tradeIn->customer_account_holder ?? 'Pelanggan',
                    $tradeIn->phone_model,
                    $tradeIn->storage_capacity,
                    $tradeIn->battery_health . '%',
                    $tradeIn->physical_condition,
                    $tradeIn->isVoucherPayout() ? 'Voucher Potongan Belanja' : ('Transfer Bank (' . ($tradeIn->customer_bank_name ?: '-') . ')'),
                    strtoupper($tradeIn->status),
                    (float) $tradeIn->estimated_price,
                    (float) $tradeIn->effective_price,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Ekspor Laporan Penjualan dan Laba Rugi ke format PDF Resmi (DomPDF A4).
     */
    public function exportPdf(Request $request): Response
    {
        $dateRange = $this->resolveFilterFromRequest($request);
        $report = $this->analyticsService->getDetailedFinancialReport(
            $dateRange['start'],
            $dateRange['end']
        );

        $pdf = Pdf::loadView('pdf.financial-report', [
            'store' => config('invoice.store'),
            'dateRange' => $dateRange,
            'summary' => $report['summary'],
            'cashFlow' => $report['cash_flow'],
            'topProducts' => $report['top_products'],
            'conversion' => $report['conversion'],
            'orders' => $report['orders'],
            'tradeIns' => $report['trade_ins'],
            'generatedBy' => Auth::user()?->name ?? 'Owner Rap-iPhone',
            'generatedAt' => now()->format('d M Y, H:i') . ' WIB',
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan-Laba-Rugi-Rap-iPhone-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Helper untuk mengekstrak parameter filter periode dari HTTP Request.
     *
     * @return array{period: string, start: ?\Carbon\Carbon, end: ?\Carbon\Carbon, label: string, start_input: string, end_input: string}
     */
    protected function resolveFilterFromRequest(Request $request): array
    {
        $period = (string) $request->query('period', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!in_array($period, ['today', 'week', 'month', 'year', 'all', 'custom'], true)) {
            $period = 'all';
        }

        return $this->analyticsService->resolveDateRange(
            $period,
            is_string($startDate) ? $startDate : null,
            is_string($endDate) ? $endDate : null
        );
    }
}

