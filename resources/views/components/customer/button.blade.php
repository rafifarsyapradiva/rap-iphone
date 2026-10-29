@props([
    'variant' => 'primary', // primary, secondary, outline, ghost
    'size' => 'md',        // sm, md, lg
    'pill' => false,
    'href' => null,
    'type' => 'button',
    'icon' => null,
])

@php
    $variantClass = match($variant) {
        'secondary' => 'cust-btn-secondary',
        'outline'   => 'cust-btn-outline',
        'ghost'     => 'cust-btn-outline bg-transparent border-transparent hover:bg-[var(--cust-paper-subtle)]',
        default     => 'cust-btn-primary',
    };

    $sizeClass = match($size) {
        'sm'    => 'text-xs py-1.5 px-3',
        'lg'    => 'text-sm py-3 px-6',
        default => 'text-[13px] py-2 px-4',
    };

    $pillClass = $pill ? 'cust-btn-pill' : '';
    $classes = "cust-btn {$variantClass} {$sizeClass} {$pillClass}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif

