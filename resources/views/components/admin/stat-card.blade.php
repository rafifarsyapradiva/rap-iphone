@props([
    'label',
    'value',
    'icon' => null,
    'tone' => 'gray',
    'hint' => null,
    'href' => null,
    'active' => false,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class(['adm-stat', 'is-active' => $active]) }}>
    <div class="adm-stat-top">
        <p class="adm-stat-label">{{ $label }}</p>
        @if($icon)
            <span class="adm-stat-icon">
                <i data-lucide="{{ $icon }}"></i>
            </span>
        @endif
    </div>
    <p class="adm-stat-value">{{ $value }}</p>
    @if($hint || !$slot->isEmpty())
        <p class="adm-stat-hint">{{ $hint }}{{ $slot }}</p>
    @endif
</{{ $tag }}>
