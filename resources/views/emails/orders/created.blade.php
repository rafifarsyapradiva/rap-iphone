@extends('emails.layouts.apple-mail', ['title' => 'Tagihan Pesanan Baru #' . $order->order_number])

@section('content')
    <div class="badge-pill badge-amber">Menunggu Pembayaran</div>
    <h1 class="hero-title">Terima Kasih Atas Pesanan Anda.</h1>
    <p class="lead-text">
        Halo <strong style="color: #FFFFFF;">{{ $order->recipient_name }}</strong>, pesanan unit iPhone Anda dengan nomor nota
        <strong style="color: #4DA3FF;">#{{ $order->order_number }}</strong> telah berhasil tercatat. Silakan selesaikan pembayaran melalui Midtrans untuk memproses pengiriman unit Anda.
    </p>

    <div class="glass-panel">
        <div class="panel-title">Rincian Unit yang Dipesan</div>
        <table class="data-table">
            @foreach($order->items as $item)
                <tr>
                    <td class="td-label" style="width: 65%; color: #E5E5EA;">
                        <strong>{{ $item->product_name }}</strong><br>
                        <span style="font-size: 12px; color: #8E8E93;">Varian: {{ $item->variant_details }} (x{{ $item->quantity }})</span>
                    </td>
                    <td class="td-value">
                        Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="glass-panel">
        <div class="panel-title">Ringkasan Tagihan &amp; Pengiriman</div>
        <table class="data-table">
            <tr>
                <td class="td-label">Nomor Pesanan</td>
                <td class="td-value">#{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td class="td-label">Kurir Ekspedisi</td>
                <td class="td-value">{{ $order->shipping_courier }}</td>
            </tr>
            <tr>
                <td class="td-label">Alamat Pengiriman</td>
                <td class="td-value">{{ $order->shipping_address }}</td>
            </tr>
            <tr>
                <td class="td-label">Ongkos Kirim &amp; Asuransi</td>
                <td class="td-value">
                    @if((float) $order->shipping_cost > 0)
                        Rp {{ number_format((float) $order->shipping_cost, 0, ',', '.') }}
                    @else
                        GRATIS (Asuransi Penuh)
                    @endif
                </td>
            </tr>
            @if((float) $order->discount_amount > 0)
                <tr>
                    <td class="td-label">Potongan Diskon</td>
                    <td class="td-value" style="color: #34C759;">-Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td class="td-label" style="font-weight: 700; color: #FFFFFF;">Total Tagihan</td>
                <td class="td-value total-highlight">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="cta-wrapper">
        <a href="{{ route('checkout.success', $order->order_number) }}" class="cta-button">
            Bayar Sekarang via Midtrans
        </a>
    </div>
@endsection
