@props([
    'title' => 'Belum ada data',
    'text' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->merge(['class' => 'adm-empty']) }}>
    <span class="adm-empty-icon"><i data-lucide="{{ $icon }}" class="w-5 h-5"></i></span>
    <p class="adm-empty-title">{{ $title }}</p>
    @if($text)
        <p class="adm-empty-text">{{ $text }}</p>
    @endif
    {{ $slot }}
</div>
