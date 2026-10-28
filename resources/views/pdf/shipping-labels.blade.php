<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Pengiriman - {{ $store['name'] }}</title>
    <style>
        @page {
            margin: 10px;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            line-height: 1.35;
            color: #000000;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .label {
            border: 1.5px solid #000000;
            height: 403px;
        }
        .page-break {
            page-break-after: always;
        }
        .section td {
            padding: 6px 8px;
            vertical-align: top;
        }
        .section + .section td,
        .bordered td {
            border-top: 1px dashed #000000;
        }
        .brand {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .brand-sub {
            font-size: 6.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .courier {
            display: inline-block;
            background-color: #000000;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: 700;
            padding: 4px 7px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .caption {
            font-size: 6.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .recipient-name {
            font-size: 13px;
            font-weight: 700;
        }
        .recipient-phone {
            font-size: 10px;
            font-weight: 700;
            margin-top: 1px;
        }
        .address {
            font-size: 9.5px;
            margin-top: 3px;
            line-height: 1.4;
        }
        .resi {
            font-family: 'Courier', monospace;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .resi-empty {
            font-size: 9px;
            font-weight: 700;
            border: 1px solid #000000;
            padding: 3px 6px;
            display: inline-block;
        }
        .order-no {
            font-family: 'Courier', monospace;
            font-size: 10px;
            font-weight: 700;
            margin-top: 4px;
        }
        .qr img {
            width: 66px;
            height: 66px;
        }
        .items {
            font-size: 8px;
            line-height: 1.4;
        }
        .tag {
            display: inline-block;
            border: 1px solid #000000;
            padding: 2px 5px;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 0.6px;
            margin-right: 3px;
        }
        .notes {
            font-size: 7.5px;
            font-style: italic;
            margin-top: 4px;
        }
        .footer {
            font-size: 6.3px;
            text-align: center;
        }
    </style>
</head>
<body>
    @foreach($labels as $label)
        @php $order = $label['order']; @endphp
        <div class="{{ $loop->last ? '' : 'page-break' }}">
            <div class="label">
                {{-- Brand & Kurir --}}
                <table class="section">
                    <tr>
                        <td style="width: 58%;">
                            <div class="brand">{{ $store['name'] }}</div>
                            <div class="brand-sub">Label Pengiriman Resmi</div>
                        </td>
                        <td style="width: 42%; text-align: right; vertical-align: middle;">
                            <span class="courier">{{ \Illuminate\Support\Str::limit($order->shipping_courier ?: 'Kurir', 22, '') }}</span>
                        </td>
                    </tr>
                </table>

                {{-- Pengirim --}}
                <table class="section">
                    <tr>
                        <td>
                            <div class="caption">Pengirim</div>
                            <strong>{{ $store['name'] }}</strong> &middot; {{ $store['whatsapp'] }}<br>
                            <span style="font-size: 7.5px;">{{ $store['address'] }}</span>
                        </td>
                    </tr>
                </table>

                {{-- Penerima --}}
                <table class="section">
                    <tr>
                        <td style="height: 118px;">
                            <div class="caption">Penerima</div>
                            <div class="recipient-name">{{ $order->recipient_name }}</div>
                            <div class="recipient-phone">{{ $order->recipient_phone }}</div>
                            <div class="address">{{ $order->shipping_address }}</div>
                        </td>
                    </tr>
                </table>

                {{-- Resi & Nomor Order --}}
                <table class="section">
                    <tr>
                        <td style="width: 70%;">
                            <div class="caption">No. Resi</div>
                            @if($order->shipping_tracking_number)
                                <div class="resi">{{ $order->shipping_tracking_number }}</div>
                            @else
                                <span class="resi-empty">RESI BELUM DIINPUT</span>
                            @endif
                            <div class="caption" style="margin-top: 6px;">No. Pesanan</div>
                            <div class="order-no" style="margin-top: 0;">{{ $order->order_number }}</div>
                        </td>
                        <td class="qr" style="width: 30%; text-align: right;">
                            <img src="{{ $label['qr'] }}" alt="QR Pesanan">
                        </td>
                    </tr>
                </table>

                {{-- Isi Paket --}}
                <table class="section">
                    <tr>
                        <td style="height: 74px;">
                            <div class="caption">Isi Paket ({{ $label['total_units'] }} unit)</div>
                            <div class="items">{{ \Illuminate\Support\Str::limit($label['items_summary'], 160) }}</div>
                            <div style="margin-top: 5px;">
                                <span class="tag">FRAGILE</span>
                                <span class="tag">ELEKTRONIK</span>
                                <span class="tag">ASURANSI</span>
                            </div>
                            @if($order->customer_notes)
                                <div class="notes">Catatan: {{ \Illuminate\Support\Str::limit($order->customer_notes, 90) }}</div>
                            @endif
                        </td>
                    </tr>
                </table>

                <table class="section">
                    <tr>
                        <td class="footer">
                            Wajib video unboxing untuk klaim &middot; Dicetak {{ $generatedAt }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
</body>
</html>

