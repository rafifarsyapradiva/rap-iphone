@extends('emails.layouts.apple-mail', ['title' => 'Pesanan Dikirim #' . $order->order_number])

@section('content')
    <div class="badge-pill badge-blue">Paket Dalam Pengiriman</div>
    <h1 class="hero-title">Unit iPhone Anda Sedang Menuju Lokasi Anda.</h1>
    <p class="lead-text">
        Halo <strong style="color: #FFFFFF;">{{ $order->recipient_name }}</strong>, pesanan Anda dengan nomor nota
        <strong style="color: #4DA3FF;">#{{ $order->order_number }}</strong> telah selesai melewati inspeksi kualitas dan kini telah diserahkan ke ekspedisi dengan perlindungan asuransi penuh.
    </p>

    <div class="glass-panel">
        <div class="panel-title">Informasi Pelacakan Resi Kurir</div>
        <table class="data-table">
            <tr>
                <td class="td-label">Kurir Pengiriman</td>
                <td class="td-value">{{ $order->shipping_courier }}</td>
            </tr>
            <tr>
                <td class="td-label">Nomor Resi Logistik</td>
                <td class="td-value total-highlight">{{ $order->shipping_tracking_number ?: 'MENUNGGU-RESI' }}</td>
            </tr>
            <tr>
                <td class="td-label">Penerima</td>
                <td class="td-value">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</td>
            </tr>
            <tr>
                <td class="td-label">Alamat Tujuan</td>
                <td class="td-value">{{ $order->shipping_address }}</td>
            </tr>
        </table>
    </div>

    <div class="glass-panel">
        <div class="panel-title">Daftar Unit yang Dikirim</div>
        <table class="data-table">
            @foreach($order->items as $item)
                <tr>
                    <td class="td-label" style="width: 70%; color: #E5E5EA;">
                        <strong>{{ $item->product_name }}</strong><br>
                        <span style="font-size: 12px; color: #8E8E93;">
                            {{ $item->variant_details }}
                            @if($item->allocated_imei)
                                • IMEI: {{ $item->allocated_imei }}
                            @endif
                        </span>
                    </td>
                    <td class="td-value">x{{ $item->quantity }} Unit</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="cta-wrapper">
        <a href="{{ route('checkout.success', $order->order_number) }}" class="cta-button">
            Lacak Detail Pesanan
        </a>
    </div>
@endsection
