@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'adm-page-header']) }}>
    <div class="min-w-0">
        @if($eyebrow)
            <div class="adm-eyebrow">
                @if($icon)<i data-lucide="{{ $icon }}" class="w-3.5 h-3.5"></i>@endif
                <span>{{ $eyebrow }}</span>
            </div>
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
