@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'meta' => null,
    'icon' => null,
])

@php
    $leadMeta = $meta ?? $eyebrow;
@endphp

<div {{ $attributes->merge(['class' => 'adm-page-header']) }}>
    <div class="min-w-0">
        @if($leadMeta)
            <p class="adm-meta-lead flex items-center gap-1.5">
                @if($icon)<i data-lucide="{{ $icon }}" class="w-3.5 h-3.5 text-[var(--adm-ink-muted)]"></i>@endif
                <span>{{ $leadMeta }}</span>
            </p>
        @endif
        <h1 class="adm-title">{!! $title !!}</h1>
        @if($subtitle)
            <p class="adm-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="adm-actions">{{ $actions }}</div>
    @endisset
</div>
