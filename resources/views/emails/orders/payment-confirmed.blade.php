@extends('emails.layouts.apple-mail', ['title' => 'Pembayaran Terverifikasi #' . $order->order_number])

@section('content')
    <div class="badge-pill badge-green">Pembayaran Lunas Terverifikasi</div>
    <h1 class="hero-title">Pembayaran Anda Telah Kami Terima.</h1>
    <p class="lead-text">
        Halo <strong style="color: #FFFFFF;">{{ $order->recipient_name }}</strong>, pembayaran untuk pesanan
        <strong style="color: #4DA3FF;">#{{ $order->order_number }}</strong> telah berhasil diverifikasi secara otomatis.
        Kami juga telah melampirkan <strong>Nota Faktur Resmi (PDF)</strong> pada email ini sebagai bukti pembelian dan klaim garansi yang sah.
    </p>

    <div class="glass-panel">
        <div class="panel-title">Rincian Pembayaran &amp; Faktur</div>
        <table class="data-table">
            <tr>
                <td class="td-label">Nomor Nota Resmi</td>
                <td class="td-value">#{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td class="td-label">Status Transaksi</td>
                <td class="td-value" style="color: #34C759;">LUNAS (PAID)</td>
            </tr>
            <tr>
                <td class="td-label">Metode Pembayaran</td>
                <td class="td-value">{{ strtoupper(str_replace('_', ' ', $order->payment->payment_type ?? 'Midtrans Snap')) }}</td>
            </tr>
            <tr>
                <td class="td-label">Total Dibayar</td>
                <td class="td-value total-highlight">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="glass-panel">
        <div class="panel-title">Unit iPhone dalam Pesanan</div>
        <table class="data-table">
            @foreach($order->items as $item)
                <tr>
                    <td class="td-label" style="width: 65%; color: #E5E5EA;">
                        <strong>{{ $item->product_name }}</strong><br>
                        <span style="font-size: 12px; color: #8E8E93;">
                            Varian: {{ $item->variant_details }} (x{{ $item->quantity }})
                            @if($item->allocated_imei)
                                | IMEI: {{ $item->allocated_imei }}
                            @endif
                        </span>
                    </td>
                    <td class="td-value">
                        Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <p style="font-size: 13px; color: #A1A1A6; line-height: 1.6; text-align: center;">
        📎 <strong>Lampiran Tersemat:</strong> <code>Nota-Rap-iPhone-{{ $order->order_number }}.pdf</code><br>
        Tim Admin Operasional sedang menyiapkan unit dan segera mengirimkan nomor resi kurir setelah paket diserahkan ke ekspedisi.
    </p>

    <div class="cta-wrapper">
        <a href="{{ route('checkout.success', $order->order_number) }}" class="cta-button">
            Pantau Status Pesanan
        </a>
    </div>
@endsection
