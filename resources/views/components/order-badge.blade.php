@props([
    'status' => 'pending',
])

@php
    $statusConfig = [
        'pending' => [
            'class' => 'badge-pending',
            'label' => 'Menunggu Pembayaran',
            'icon' => 'clock',
        ],
        'paid' => [
            'class' => 'badge-paid',
            'label' => 'Lunas',
            'icon' => 'check-circle-2',
        ],
        'processing' => [
            'class' => 'badge-processing',
            'label' => 'Diproses Admin',
            'icon' => 'package-check',
        ],
        'shipped' => [
            'class' => 'badge-shipped',
            'label' => 'Dikirim Kurir',
            'icon' => 'truck',
        ],
        'completed' => [
            'class' => 'badge-completed',
            'label' => 'Pesanan Selesai',
            'icon' => 'badge-check',
        ],
        'cancelled' => [
            'class' => 'badge-cancelled',
            'label' => 'Dibatalkan',
            'icon' => 'x-circle',
        ],
    ];

    $current = $statusConfig[$status] ?? [
        'class' => 'badge-pending',
        'label' => ucfirst($status),
        'icon' => 'help-circle',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'glass-badge ' . $current['class']]) }}>
    <span class="glass-badge-dot"></span>
    <i data-lucide="{{ $current['icon'] }}" class="w-3 h-3"></i>
    <span>{{ $current['label'] }}</span>
</span>
