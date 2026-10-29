@props([
    'status' => null,
    'tone' => 'gray',
    'label' => null,
    'dot' => true,
])

@php
    $map = [
        // Pesanan (PRD Order State Machine)
        'pending'       => ['amber',  'Menunggu Pembayaran'],
        'paid'          => ['green',  'Lunas'],
        'processing'    => ['blue',   'Diproses Admin'],
        'shipped'       => ['purple', 'Dikirim Kurir'],
        'completed'     => ['green',  'Pesanan Selesai'],
        'cancelled'     => ['red',    'Dibatalkan'],
        // Trade-in
        'submitted'     => ['amber',  'Diajukan'],
        'unit_received' => ['blue',   'Unit Diterima'],
        'inspected'     => ['purple', 'Terinspeksi'],
        'approved'      => ['green',  'Disetujui'],
        'rejected'      => ['red',    'Ditolak'],
        'paid_out'      => ['green',  'Dana Cair'],
        // Role
        'admin'         => ['blue',   'Admin Operasional'],
        'owner'         => ['purple', 'Owner'],
        'customer'      => ['gray',   'Customer'],
        // Notifikasi
        'sent'          => ['green',  'Terkirim'],
        'failed'        => ['red',    'Gagal'],
    ];

    [$resolvedTone, $resolvedLabel] = $map[$status] ?? [$tone, $status ? ucfirst(str_replace('_', ' ', $status)) : ''];
    $resolvedLabel = $label ?? $resolvedLabel;
@endphp

<span {{ $attributes->merge(['class' => "adm-badge adm-tone-{$resolvedTone}"]) }}>
    @if($dot)<span class="adm-badge-dot"></span>@endif
    <span>{{ $resolvedLabel !== '' ? $resolvedLabel : $slot }}</span>
</span>
