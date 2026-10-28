<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan & Laba Rugi — {{ $store['name'] ?? 'RAP-iPHONE' }}</title>
    <style>
        @page {
            margin: 28px 32px;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1D1D1F;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0071E3;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #0B0C10;
            letter-spacing: -0.3px;
        }
        .brand-sub {
            font-size: 9px;
            color: #515154;
            margin-top: 2px;
        }
        .doc-badge {
            display: inline-block;
            background-color: #0071E3;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0B0C10;
            text-transform: uppercase;
            border-left: 3px solid #0071E3;
            padding-left: 7px;
            margin: 14px 0 8px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th {
            background-color: #F5F5F7;
            color: #1D1D1F;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 7px;
            border: 1px solid #D2D2D7;
            text-align: left;
        }
        table.data-table td {
            padding: 5.5px 7px;
            border: 1px solid #E5E5EA;
            font-size: 9.5px;
            vertical-align: top;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-green {
            color: #15803D;
            font-weight: bold;
        }
        .text-amber {
            color: #B45309;
        }
        .text-blue {
            color: #0071E3;
            font-weight: bold;
        }
        .highlight-row td {
            background-color: #F0FDF4;
            font-weight: bold;
        }
        .footer-box {
            margin-top: 20px;
            border-top: 1px solid #D2D2D7;
            padding-top: 10px;
            font-size: 8.5px;
            color: #6E6E73;
        }
    </style>
</head>
<body>

    <!-- Document Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="brand-title">{{ $store['name'] ?? 'RAP-iPHONE' }}</div>
                <div class="brand-sub">{{ $store['tagline'] ?? 'Premium Apple Retail & Trade-In Partner' }}</div>
                <div class="brand-sub">{{ $store['address'] ?? '' }}</div>
                <div class="brand-sub">NPWP: {{ $store['npwp'] ?? '-' }} | NIB: {{ $store['nib'] ?? '-' }}</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="doc-badge">Laporan Laba Rugi &amp; Arus Kas</div>
                <div style="margin-top: 6px; font-size: 10px; font-weight: bold;">
                    Periode: {{ $dateRange['label'] ?? 'Seluruh Periode' }}
                </div>
                <div class="brand-sub">Dicetak oleh: {{ $generatedBy ?? 'Owner Rap-iPhone' }}</div>
                <div class="brand-sub">Tanggal Cetak: {{ $generatedAt ?? now()->format('d M Y, H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- I. Ringkasan Eksekutif Laba Rugi & Arus Kas -->
    <div class="section-title">I. Ringkasan Eksekutif Laba Rugi (P&amp;L) &amp; Arus Kas Trade-In</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50%;">Komponen Laporan Laba Rugi Penjualan</th>
                <th style="width: 50%;" class="text-right">Nilai (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Transaksi Lunas &amp; Unit iPhone Terjual Keluar</td>
                <td class="text-right font-bold">{{ $summary['orders_count'] ?? 0 }} Pesanan ({{ $summary['units_sold'] ?? 0 }} Unit)</td>
            </tr>
            <tr>
                <td>Penjualan Kotor Produk (Gross Item Sales)</td>
                <td class="text-right">Rp {{ number_format($summary['gross_items_sales'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan Diskon / Voucher Trade-In</td>
                <td class="text-right">&minus; Rp {{ number_format($summary['discount_total'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pendapatan Ongkos Kirim</td>
                <td class="text-right">+ Rp {{ number_format($summary['shipping_revenue'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Total Omzet Penjualan (Grand Total Lunas)</td>
                <td class="text-right font-bold">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Harga Pokok Penjualan / HPP Unit Terjual (COGS)</td>
                <td class="text-right text-amber">&minus; Rp {{ number_format($summary['cogs'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="highlight-row">
                <td>Laba Bersih Penjualan Otomatis (Margin {{ $summary['profit_margin'] ?? 0 }}%)</td>
                <td class="text-right text-green">Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Akuisisi Unit Trade-In Masuk ({{ $cashFlow['trade_in_units_in'] ?? 0 }} Unit)</td>
                <td class="text-right text-amber">
                    Rp {{ number_format($cashFlow['trade_in_total_value'] ?? 0, 0, ',', '.') }}
                    (Tunai: Rp {{ number_format($cashFlow['trade_in_cash_out'] ?? 0, 0, ',', '.') }} | Voucher: Rp {{ number_format($cashFlow['trade_in_voucher_out'] ?? 0, 0, ',', '.') }})
                </td>
            </tr>
            <tr>
                <td class="font-bold">Surplus Arus Kas Bersih (Penjualan Keluar &minus; Trade-In Masuk)</td>
                <td class="text-right text-blue">Rp {{ number_format($cashFlow['net_cash_flow'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- II. Analisis Produk iPhone Terlaris -->
    <div class="section-title">II. Analisis Produk iPhone Terlaris (Top Selling Models)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 7%;">Rank</th>
                <th style="width: 33%;">Model iPhone &amp; Varian Terpopuler</th>
                <th style="width: 12%;" class="text-center">Unit Terjual</th>
                <th style="width: 16%;" class="text-right">Total Omzet</th>
                <th style="width: 16%;" class="text-right">Total HPP</th>
                <th style="width: 16%;" class="text-right">Laba Produk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $prod)
                <tr>
                    <td class="text-center font-bold">#{{ $prod['rank'] }}</td>
                    <td>
                        <strong>{{ $prod['product_name'] }}</strong><br>
                        <span style="font-size: 8.5px; color: #515154;">{{ $prod['series'] }} | Favorit: {{ $prod['popular_variant'] }}</span>
                    </td>
                    <td class="text-center font-bold">{{ $prod['units_sold'] }} Unit ({{ $prod['share_pct'] }}%)</td>
                    <td class="text-right">Rp {{ number_format($prod['revenue'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($prod['cogs'], 0, ',', '.') }}</td>
                    <td class="text-right text-green">Rp {{ number_format($prod['gross_profit'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data produk terjual pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- III. Rincian Transaksi Penjualan & Laba Bersih per Nota -->
    <div class="section-title">III. Rincian Transaksi Penjualan &amp; Kalkulasi Laba Bersih ({{ $orders->count() }} Nota)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 16%;">Nomor Nota</th>
                <th style="width: 13%;">Tanggal</th>
                <th style="width: 16%;">Pelanggan</th>
                <th style="width: 23%;">Rincian Unit</th>
                <th style="width: 11%;" class="text-right">Omzet (Rp)</th>
                <th style="width: 10%;" class="text-right">HPP (Rp)</th>
                <th style="width: 11%;" class="text-right">Laba Bersih</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="font-bold">{{ $order->order_number }}</td>
                    <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->recipient_name ?: ($order->user?->name ?? 'Pelanggan') }}</td>
                    <td>{{ $order->items_summary }}</td>
                    <td class="text-right">{{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format((float) $order->calculated_cogs, 0, ',', '.') }}</td>
                    <td class="text-right text-green">{{ number_format((float) $order->calculated_net_profit, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi lunas pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Signature -->
    <div class="footer-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;">
                    Dokumen ini diterbitkan secara otomatis oleh Sistem Manajemen Keuangan Eksekutif <strong>{{ $store['name'] ?? 'RAP-iPHONE' }}</strong>.<br>
                    Seluruh nilai Laba Bersih dihitung berdasarkan selisih Harga Jual aktual terhadap snapshot HPP varian unit pada saat transaksi terjadi.
                </td>
                <td style="width: 30%; text-align: right;">
                    <strong>Disahkan Oleh Pemilik Usaha (Owner):</strong><br><br><br>
                    <u>{{ $generatedBy ?? 'Owner Rap-iPhone' }}</u>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

