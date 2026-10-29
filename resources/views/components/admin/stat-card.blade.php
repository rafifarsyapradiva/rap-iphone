@props([
    'label',
    'value',
    'icon' => null,
    'tone' => 'blue',
    'hint' => null,
    'href' => null,
    'active' => false,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class(['adm-stat', 'is-active' => $active]) }}>
    <div class="adm-stat-top">
        <div class="min-w-0">
            <p class="adm-stat-label">{{ $label }}</p>
            <p class="adm-stat-value">{{ $value }}</p>
        </div>
        @if($icon)
            <span class="adm-stat-icon adm-tone-{{ $tone }}">
                <i data-lucide="{{ $icon }}"></i>
            </span>
        @endif
    </div>
    @if($hint || !$slot->isEmpty())
        <p class="adm-stat-hint">{{ $hint }}{{ $slot }}</p>
    @endif
</{{ $tag }}>
