@props([
    'title',
    'subtitle' => null,
    'tag' => null,
    'actionHref' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-end justify-between gap-3 pb-4 mb-6 border-b border-[var(--cust-rule-border)]']) }}>
    <div>
        @if($tag)
            <p class="text-[11px] font-medium text-[var(--cust-ink-muted)] mb-1">{{ $tag }}</p>
        @endif
        <h2 class="font-serif text-2xl sm:text-3xl font-light text-[var(--cust-ink)] tracking-tight">
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="text-xs sm:text-sm text-[var(--cust-ink-muted)] mt-1 max-w-xl">
                {{ $subtitle }}
            </p>
        @endif
    </div>
    @if($actionHref && $actionLabel)
        <a href="{{ $actionHref }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[var(--cust-ink)] hover:text-[var(--cust-signal)] transition-colors self-start sm:self-auto">
            <span>{{ $actionLabel }}</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    @endif
</div>

