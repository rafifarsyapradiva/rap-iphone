@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => null,
    'iconRight' => null,
])

@php
    $sizeClasses = [
        'sm' => 'glass-btn-sm',
        'md' => 'glass-btn-md',
        'lg' => 'glass-btn-lg',
    ][$size] ?? 'glass-btn-md';

    $variantClasses = [
        'primary' => 'glass-btn-primary',
        'secondary' => 'glass-btn-secondary',
        'danger' => 'glass-btn-danger',
        'ghost' => 'glass-btn-ghost',
    ][$variant] ?? 'glass-btn-primary';

    $classes = "glass-btn {$sizeClasses} {$variantClasses} " . ($attributes->get('class') ?? '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
        @endif
        
        <span>{{ $slot }}</span>

        @if($iconRight)
            <i data-lucide="{{ $iconRight }}" class="w-4 h-4 shrink-0"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0"></i>
        @endif
        
        <span>{{ $slot }}</span>

        @if($iconRight)
            <i data-lucide="{{ $iconRight }}" class="w-4 h-4 shrink-0"></i>
        @endif
    </button>
@endif
