@extends('emails.layouts.apple-mail', ['title' => 'Update Status Trade-In #' . $tradeIn->trade_in_code])

@section('content')
    @php
        $badgeClass = match($tradeIn->status) {
            'approved', 'paid_out' => 'badge-green',
            'rejected' => 'badge-amber',
            default => 'badge-blue',
        };
    @endphp
    <div class="badge-pill {{ $badgeClass }}">{{ $tradeIn->status_label }}</div>
    <h1 class="hero-title">Pembaruan Status Trade-In iPhone Anda.</h1>
    <p class="lead-text">
        Halo <strong style="color: #FFFFFF;">{{ $tradeIn->user->name ?? $tradeIn->customer_account_holder ?? 'Pelanggan Setia' }}</strong>,
        berikut adalah informasi terbaru mengenai pengajuan program Jual / Tukar Tambah iPhone Anda dengan kode
        <strong style="color: #4DA3FF;">#{{ $tradeIn->trade_in_code }}</strong>.
    </p>

    <div class="glass-panel">
        <div class="panel-title">Spesifikasi Unit &amp; Penawaran Nilai</div>
        <table class="data-table">
            <tr>
                <td class="td-label">Kode Pengajuan</td>
                <td class="td-value">#{{ $tradeIn->trade_in_code }}</td>
            </tr>
            <tr>
                <td class="td-label">Model &amp; Kapasitas</td>
                <td class="td-value">{{ $tradeIn->phone_model }} ({{ $tradeIn->storage_capacity }})</td>
            </tr>
            <tr>
                <td class="td-label">Kondisi Fisik &amp; Layar</td>
                <td class="td-value">{{ $tradeIn->physical_condition }} • {{ $tradeIn->screen_condition }}</td>
            </tr>
            <tr>
                <td class="td-label">Battery Health &amp; Box</td>
                <td class="td-value">{{ $tradeIn->battery_health }}% • {{ $tradeIn->accessories_status }}</td>
            </tr>
            <tr>
                <td class="td-label">Status Saat Ini</td>
                <td class="td-value" style="color: #4DA3FF;">{{ $tradeIn->status_label }}</td>
            </tr>
            <tr>
                <td class="td-label">Estimasi Sistem</td>
                <td class="td-value">Rp {{ number_format((float) $tradeIn->estimated_price, 0, ',', '.') }}</td>
            </tr>
            @if(!is_null($tradeIn->final_price))
                <tr>
                    <td class="td-label" style="font-weight: 700; color: #FFFFFF;">Harga Penawaran Final</td>
                    <td class="td-value total-highlight">Rp {{ number_format((float) $tradeIn->final_price, 0, ',', '.') }}</td>
                </tr>
            @endif
        </table>
    </div>

    @if(!empty($tradeIn->admin_notes))
        <div class="glass-panel">
            <div class="panel-title">Catatan Inspeksi Teknisi / Admin</div>
            <p style="margin: 0; font-size: 14px; color: #E5E5EA; line-height: 1.6;">
                "{{ $tradeIn->admin_notes }}"
            </p>
        </div>
    @endif

    <div class="cta-wrapper">
        <a href="{{ route('trade-in.show', $tradeIn->trade_in_code) }}" class="cta-button">
            Lihat Detail Trade-In
        </a>
    </div>
@endsection
