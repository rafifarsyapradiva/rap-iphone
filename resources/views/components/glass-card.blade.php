@props([
    'elevated' => false,
    'interactive' => false,
])

@php
    $baseClass = $elevated ? 'glass-card-elevated' : 'glass-card';
    if ($interactive) {
        $baseClass .= ' glass-card-interactive';
    }
@endphp

<div {{ $attributes->merge(['class' => "{$baseClass} p-6 relative"]) }}>
    {{ $slot }}
</div>
