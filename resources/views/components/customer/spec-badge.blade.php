@props([
    'variant' => 'official', // official, healthy, promo, gray
    'icon' => null,
])

@php
    $classMap = [
        'official' => 'cust-badge-official',
        'healthy'  => 'cust-badge-healthy',
        'promo'    => 'cust-badge-promo',
        'gray'     => 'cust-badge-gray',
    ];
    $badgeClass = $classMap[$variant] ?? 'cust-badge-official';
@endphp

<span {{ $attributes->merge(['class' => 'cust-badge ' . $badgeClass]) }}>
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-3 h-3"></i>
    @endif
    <span>{{ $slot }}</span>
</span>

