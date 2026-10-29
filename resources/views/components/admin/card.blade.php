@props([
    'title' => null,
    'description' => null,
    'icon' => null,
    'flush' => false,
])

<section {{ $attributes->merge(['class' => 'adm-card']) }}>
    @if($title || isset($actions))
        <header class="adm-card-head">
            <div class="min-w-0">
                @if($title)
                    <h2 class="adm-card-title">
                        @if($icon)<i data-lucide="{{ $icon }}"></i>@endif
                        <span>{!! $title !!}</span>
                    </h2>
                @endif
                @if($description)
                    <p class="adm-card-desc">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="adm-actions">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    @if($flush)
        {{ $slot }}
    @else
        <div class="adm-card-body">{{ $slot }}</div>
    @endif

    @isset($footer)
        <footer class="adm-card-foot">{{ $footer }}</footer>
    @endisset
</section>
