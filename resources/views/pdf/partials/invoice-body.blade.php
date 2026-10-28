{{-- Isi satu nota faktur resmi. Membutuhkan variabel $invoice dari InvoicePdfService::buildViewData() --}}
@php
    $order = $invoice['order'];
    $store = $invoice['store'];
    $status = $invoice['status'];
    $payment = $order->payment;
    $rp = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp
<div class="invoice">
    @if($status['watermark'])
        <div class="watermark">{{ $status['watermark'] }}</div>
    @endif

    {{-- ============ HEADER ============ --}}
    <table class="header">
        <tr>
            <td style="width: 60%;">
                <table>
                    <tr>
                        <td style="padding: 0 10px 0 0; vertical-align: middle;">
                            <span class="brand-mark">R</span>
                        </td>
                        <td style="padding: 0; vertical-align: middle;">
                            <div class="brand-title">{{ $store['name'] }}</div>
                            <div class="brand-tagline">{{ $store['tagline'] }}</div>
                        </td>
                    </tr>
                </table>
                <div class="store-info">
                    {{ $store['address'] }}<br>
                    WhatsApp: {{ $store['whatsapp'] }} &nbsp;&middot;&nbsp; Email: {{ $store['email'] }}<br>
                    NPWP: {{ $store['npwp'] }} &nbsp;&middot;&nbsp; NIB: {{ $store['nib'] }}
                </div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="doc-label">Invoice &middot; Tax Receipt</div>
                <div class="doc-title">NOTA FAKTUR RESMI</div>
                <div class="doc-number">#{{ $order->order_number }}</div>
                <div class="status-pill {{ $status['class'] }}">{{ $status['label'] }}</div>
            </td>
        </tr>
    </table>

    {{-- ============ METADATA ============ --}}
    <table class="meta">
        <tr>
            <td style="width: 33%;">
                <div class="caption">Diterbitkan Untuk</div>
                <div class="meta-value">{{ $order->recipient_name }}</div>
                <div class="meta-sub">
                    WA: {{ $order->recipient_phone }}<br>
                    {{ $order->user->email ?? '-' }}
                </div>
            </td>
            <td style="width: 34%;">
                <div class="caption">Pengiriman</div>
                <div class="meta-value">{{ $order->shipping_courier ?: '-' }}</div>
                <div class="meta-sub">
                    {{ $order->shipping_address }}
                    @if($order->shipping_tracking_number)
                        <br><strong style="color: #1D1D1F;">No. Resi: {{ $order->shipping_tracking_number }}</strong>
                    @endif
                </div>
            </td>
            <td style="width: 33%;">
                <div class="caption">Detail Transaksi</div>
                <table class="kv">
                    <tr>
                        <td class="k">Tgl Pesanan</td>
                        <td class="v">{{ optional($order->created_at)->format('d M Y, H:i') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="k">Tgl Lunas</td>
                        <td class="v">{{ $invoice['paidAt'] ? $invoice['paidAt']->format('d M Y, H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="k">Metode Bayar</td>
                        <td class="v">{{ $payment && $payment->payment_type ? strtoupper(str_replace('_', ' ', $payment->payment_type)) : 'MIDTRANS SNAP' }}</td>
                    </tr>
                    <tr>
                        <td class="k">Ref. Midtrans</td>
                        <td class="v" style="font-size: 8px;">{{ $payment->midtrans_transaction_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="k">Kasir</td>
                        <td class="v">Sistem Online</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ============ RINCIAN PRODUK ============ --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 39%;">Seri iPhone &amp; Spesifikasi</th>
                <th style="width: 23%;">Garansi</th>
                <th style="width: 6%; text-align: center;">Qty</th>
                <th style="width: 14%; text-align: right;">Harga</th>
                <th style="width: 14%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice['items'] as $index => $row)
                @php
                    $item = $row['model'];
                    $warranty = $row['warranty'];
                @endphp
                <tr>
                    <td style="color: #86868B; font-weight: 700;">{{ $index + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                        <div class="variant">{{ str_replace(' / ', ' · ', $item->variant_details) }}</div>
                        <div class="condition">
                            {{ $row['condition'] }}
                            @if($row['battery_health'])
                                &middot; Battery Health {{ $row['battery_health'] }}%
                            @endif
                        </div>
                        @if($row['imei'])
                            <span class="imei-chip">IMEI {{ $row['imei'] }}</span>
                        @else
                            <span class="imei-chip imei-pending">IMEI dialokasikan saat QC</span>
                        @endif
                    </td>
                    <td>
                        <div class="warranty-name">{{ $warranty['label'] }}</div>
                        <span class="warranty-period">{{ $warranty['months'] }} bulan</span>
                        <div class="warranty-until">
                            @if($warranty['ends_at'])
                                Berlaku s/d <strong>{{ $warranty['ends_at']->format('d M Y') }}</strong>
                            @else
                                Aktif setelah pelunasan
                            @endif
                        </div>
                    </td>
                    <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                    <td class="num">{{ $rp($item->price) }}</td>
                    <td class="num" style="font-weight: 700;">{{ $rp($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ============ RINGKASAN ============ --}}
    <table class="summary-wrap">
        <tr>
            <td style="width: 56%; padding-right: 24px;">
                <div class="words-box">
                    <div class="caption" style="margin-bottom: 0;">Terbilang</div>
                    <div class="words">{{ $invoice['amountInWords'] }}</div>
                </div>
                @if($order->customer_notes)
                    <div class="meta-sub" style="margin-top: 8px;">
                        <strong style="color: #1D1D1F;">Catatan:</strong> {{ $order->customer_notes }}
                    </div>
                @endif
            </td>
            <td style="width: 44%;">
                <table class="summary">
                    <tr>
                        <td class="label">Subtotal Produk</td>
                        <td class="value">{{ $rp($order->total_items_price) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Ongkos Kirim &amp; Asuransi</td>
                        <td class="value">{{ (float) $order->shipping_cost > 0 ? $rp($order->shipping_cost) : 'GRATIS' }}</td>
                    </tr>
                    @if((float) $order->discount_amount > 0)
                        <tr class="discount">
                            <td class="label">Potongan Diskon / Trade-In{{ $order->tradeIn ? ' (' . $order->tradeIn->trade_in_code . ')' : '' }}</td>
                            <td class="value">- {{ $rp($order->discount_amount) }}</td>
                        </tr>
                    @endif
                    <tr class="total">
                        <td class="label">TOTAL PEMBAYARAN</td>
                        <td class="value">{{ $rp($order->grand_total) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ============ S&K + VERIFIKASI ============ --}}
    <table class="footer">
        <tr>
            <td style="width: 70%; padding-right: 18px;">
                <div class="terms-title">Syarat &amp; Ketentuan Klaim Garansi</div>
                <table class="terms">
                    @foreach($invoice['terms'] as $i => $term)
                        <tr>
                            <td class="n">{{ $i + 1 }}.</td>
                            <td>{{ $term }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td style="width: 30%;">
                <div class="verify-box">
                    <div class="caption" style="margin-bottom: 6px;">Validasi Keaslian Nota</div>
                    <img src="{{ $invoice['qrDataUri'] }}" alt="QR Verifikasi">
                    <div class="verify-id">{{ $invoice['verificationCode'] }}</div>
                    <div class="verify-hint">Scan QR untuk memastikan nota ini terdaftar di sistem {{ $store['name'] }}</div>
                    @if($status['is_paid'])
                        <div class="stamp">
                            <div class="stamp-title">Lunas &middot; Sah</div>
                            <div class="stamp-sub">{{ $store['name'] }} &middot; {{ optional($invoice['paidAt'] ?? $order->created_at)->format('d.m.Y') }}</div>
                        </div>
                    @else
                        <div class="stamp stamp-void">
                            <div class="stamp-title">{{ $status['watermark'] }}</div>
                            <div class="stamp-sub">Bukan bukti pembelian sah</div>
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Dokumen ini diterbitkan secara elektronik oleh sistem {{ $store['name'] }} dan sah tanpa tanda tangan basah.
        &nbsp;&middot;&nbsp; Dicetak {{ $invoice['generatedAt'] }}
    </div>
</div>

