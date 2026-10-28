<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Faktur Resmi #{{ $order->order_number }} - Rap-iPhone</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1D1D1F;
            background-color: #FFFFFF;
            padding: 28px 34px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1D1D1F;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #0B0C10;
        }
        .brand-subtitle {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #0071E3;
            margin-top: 2px;
        }
        .store-info {
            font-size: 9.5px;
            color: #515154;
            margin-top: 6px;
            line-height: 1.4;
        }
        .invoice-badge-box {
            text-align: right;
        }
        .invoice-label {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #1D1D1F;
        }
        .invoice-number {
            font-size: 12px;
            font-weight: 700;
            color: #0071E3;
            margin-top: 3px;
        }
        .status-pill {
            display: inline-block;
            margin-top: 6px;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .status-paid {
            background-color: #E8F8EE;
            color: #167940;
            border: 1px solid #34C759;
        }
        .status-pending {
            background-color: #FFF6E5;
            color: #B26A00;
            border: 1px solid #FF9F0A;
        }
        .status-cancelled {
            background-color: #FDECEC;
            color: #C5221F;
            border: 1px solid #FF3B30;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            background-color: #F5F5F7;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .meta-col-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #86868B;
            margin-bottom: 4px;
        }
        .meta-value {
            font-size: 11px;
            color: #1D1D1F;
            font-weight: 600;
        }
        .meta-sub {
            font-size: 10px;
            color: #515154;
            margin-top: 2px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .items-table th {
            background-color: #0B0C10;
            color: #FFFFFF;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 9px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #E5E5EA;
            vertical-align: top;
        }
        .product-name {
            font-size: 11.5px;
            font-weight: 700;
            color: #1D1D1F;
        }
        .variant-info {
            font-size: 10px;
            color: #515154;
            margin-top: 2px;
        }
        .imei-chip {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 6px;
            background-color: #EEF6FF;
            border: 1px solid #B6D8FF;
            color: #0051A8;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9.5px;
            font-weight: 700;
            border-radius: 3px;
        }
        .summary-wrapper {
            width: 100%;
            margin-bottom: 22px;
        }
        .summary-table {
            width: 280px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px 8px;
            font-size: 10.5px;
        }
        .summary-total-row td {
            border-top: 2px solid #1D1D1F;
            border-bottom: 2px solid #1D1D1F;
            padding: 8px;
            font-size: 12.5px;
            font-weight: 700;
            color: #0071E3;
        }
        .footer-grid {
            width: 100%;
            border-top: 1px solid #D2D2D7;
            padding-top: 14px;
            margin-top: 10px;
        }
        .warranty-box {
            background-color: #FBFBFD;
            border: 1px solid #E5E5EA;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 9px;
            color: #515154;
        }
        .warranty-title {
            font-size: 9.5px;
            font-weight: 700;
            color: #1D1D1F;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .qr-box {
            border: 1px solid #D2D2D7;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            background-color: #F5F5F7;
        }
        .qr-matrix {
            font-family: 'Courier New', Courier, monospace;
            font-size: 8px;
            line-height: 1.1;
            letter-spacing: 1px;
            color: #0B0C10;
            margin-bottom: 4px;
        }
        .stamp-paid {
            display: inline-block;
            border: 2px solid #0071E3;
            color: #0071E3;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 4px;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <!-- Header Resmi Store -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="brand-title">RAP-iPHONE</div>
                <div class="brand-subtitle">Premium Apple Retail &amp; Trade-In Partner</div>
                <div class="store-info">
                    Flagship Store: Jl. Jenderal Sudirman Kav. 52-53, SCBD, Jakarta Selatan 12190<br>
                    WhatsApp Official: +62 811-9000-8899 | Email: official@rap-iphone.com<br>
                    NPWP: 09.254.881.4-012.000 | Izin Usaha: NIB-9120300491821
                </div>
            </td>
            <td class="invoice-badge-box" style="width: 40%; vertical-align: top;">
                <div class="invoice-label">NOTA FAKTUR RESMI</div>
                <div class="invoice-number">#{{ $order->order_number }}</div>
                @php
                    $isPaid = in_array($order->status, ['paid', 'processing', 'shipped', 'completed'], true);
                    $statusClass = $isPaid ? 'status-paid' : ($order->status === 'cancelled' ? 'status-cancelled' : 'status-pending');
                    $statusText = match ($order->status) {
                        'paid' => 'LUNAS TERVERIFIKASI (PAID)',
                        'processing' => 'DIPROSES QC (PAID)',
                        'shipped' => 'DIKIRIM EKSPEDISI (SHIPPED)',
                        'completed' => 'TRANSAKSI SELESAI (COMPLETED)',
                        'cancelled' => 'DIBATALKAN (CANCELLED)',
                        default => 'MENUNGGU PEMBAYARAN (PENDING)',
                    };
                @endphp
                <div class="status-pill {{ $statusClass }}">{{ $statusText }}</div>
            </td>
        </tr>
    </table>

    <!-- Metadata Pelanggan & Transaksi -->
    <table class="meta-table">
        <tr>
            <td style="width: 36%; vertical-align: top; padding-right: 12px;">
                <div class="meta-col-title">Diterbitkan Untuk (Pelanggan)</div>
                <div class="meta-value">{{ $order->recipient_name }}</div>
                <div class="meta-sub">
                    Telp/WA: {{ $order->recipient_phone }}<br>
                    Email: {{ $order->user->email ?? '-' }}
                </div>
            </td>
            <td style="width: 34%; vertical-align: top; padding-right: 12px;">
                <div class="meta-col-title">Alamat Pengiriman &amp; Kurir</div>
                <div class="meta-value">{{ $order->shipping_courier }}</div>
                <div class="meta-sub">
                    {{ $order->shipping_address }}
                    @if($order->shipping_tracking_number)
                        <br><strong>No. Resi: {{ $order->shipping_tracking_number }}</strong>
                    @endif
                </div>
            </td>
            <td style="width: 30%; vertical-align: top;">
                <div class="meta-col-title">Metadata Transaksi</div>
                <div class="meta-sub">
                    <strong>Tgl Transaksi:</strong> {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB<br>
                    <strong>Metode Bayar:</strong> {{ strtoupper(str_replace('_', ' ', $order->payment->payment_type ?? 'Midtrans Snap')) }}<br>
                    <strong>ID Referensi:</strong> {{ $order->payment->midtrans_transaction_id ?? $order->order_number }}<br>
                    <strong>Kasir / Sistem:</strong> Auto-Checkout Engine v2.0
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian Produk iPhone -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 44%;">Seri iPhone &amp; Spesifikasi Varian</th>
                <th style="width: 18%;">Status Garansi</th>
                <th style="width: 8%; text-align: center;">Qty</th>
                <th style="width: 12%; text-align: right;">Harga Satuan</th>
                <th style="width: 12%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                @php
                    $product = $item->variant->product ?? null;
                    $warrantyLabel = match($product->warranty_type ?? 'resmi_ibox') {
                        'resmi_ibox' => 'Resmi Apple Indonesia (iBox)',
                        'resmi_digimap' => 'Resmi Apple Indonesia (Digimap)',
                        'inter_all_operator' => 'Inter Terdaftar Bea Cukai',
                        'toko' => 'Garansi Toko Rap-iPhone',
                        default => 'Garansi Resmi Rap-iPhone',
                    };
                    $conditionLabel = ($product && $product->condition === 'second')
                        ? 'Second Like New (' . ($product->grade ?? 'Grade A+') . ')'
                        : 'Brand New In Box (BNIB)';
                    $imeiDisplay = $item->allocated_imei ?? ($item->variant->imei ?? 'Terlampir pada Segel Box');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                        <div class="variant-info">Varian: {{ $item->variant_details }} | Kondisi: {{ $conditionLabel }}</div>
                        <div class="imei-chip">IMEI / SN: {{ $imeiDisplay }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; font-size: 10px;">{{ $warrantyLabel }}</div>
                        <div style="font-size: 9px; color: #515154;">Proteksi Asuransi Aktif</div>
                    </td>
                    <td style="text-align: center; font-weight: 600;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: 700;">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Ringkasan Pembayaran -->
    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td style="color: #515154;">Subtotal Harga Produk</td>
                <td style="text-align: right; font-weight: 600;">Rp {{ number_format((float) $order->total_items_price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="color: #515154;">Ongkos Kirim &amp; Asuransi</td>
                <td style="text-align: right; font-weight: 600;">
                    @if((float) $order->shipping_cost > 0)
                        Rp {{ number_format((float) $order->shipping_cost, 0, ',', '.') }}
                    @else
                        GRATIS (Rp 0)
                    @endif
                </td>
            </tr>
            @if((float) $order->discount_amount > 0)
            <tr>
                <td style="color: #167940;">Potongan Diskon / Trade-In</td>
                <td style="text-align: right; font-weight: 600; color: #167940;">-Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="summary-total-row">
                <td>TOTAL PEMBAYARAN</td>
                <td style="text-align: right;">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Syarat Garansi & QR Validasi Keaslian -->
    <table class="footer-grid">
        <tr>
            <td style="width: 68%; vertical-align: top; padding-right: 14px;">
                <div class="warranty-box">
                    <div class="warranty-title">Syarat &amp; Ketentuan Klaim Garansi Resmi &amp; Garansi Toko</div>
                    1. Nota Faktur ini merupakan bukti pembelian yang sah di seluruh jaringan layanan purna jual Rap-iPhone.<br>
                    2. Garansi Unit Baru (BNIB) mengikuti ketentuan penuh Apple Authorized Service Provider (iBox / Digimap) selama 12 bulan.<br>
                    3. Garansi Unit Second Berkualitas mencakup jaminan 7 hari tukar unit baru apabila ditemukan cacat fungsi pabrik (wajib menyertakan video unboxing utuh &amp; segel baut utuh).<br>
                    4. Kerusakan akibat cairan (water damage), jatuh/benturan fisik, human error, atau modifikasi sistem di luar toko menggugurkan garansi.
                </div>
            </td>
            <td style="width: 32%; vertical-align: top;">
                <div class="qr-box">
                    <div style="font-size: 8.5px; font-weight: 700; color: #515154; margin-bottom: 4px;">QR VALIDASI KEASLIAN NOTA</div>
                    <div class="qr-matrix">
                        [■■■■■ □■□ ■■■■■]<br>
                        [■□□□■ ■□■ ■□□□■]<br>
                        [■□■□■ □□□ ■□■□■]<br>
                        [■■■■■ ■□■ ■■■■■]
                    </div>
                    <div style="font-size: 8.5px; font-family: monospace; font-weight: 700; color: #1D1D1F;">
                        VERIF-ID: {{ $verificationHash }}
                    </div>
                    <div class="stamp-paid">AUTHORIZED DIGITAL STAMP</div>
                    <div style="font-size: 8px; color: #86868B; margin-top: 4px;">Dicetak: {{ $generatedAt }}</div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
