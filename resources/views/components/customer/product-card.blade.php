@props([
    'product',
])

@php
    $variants = $product->variants ?? collect();
    $colors = $variants->unique('color_name')->values();
    $storages = $variants->unique('storage_capacity')->pluck('storage_capacity')->values();
    $primaryImage = $product->primaryImage->image_path ?? ($product->images->first()->image_path ?? null);
    $minPrice = $variants->min('selling_price') ?? $product->base_price;
    $monthlyInstallment = round($minPrice / 24);
    $batteryHealth = $variants->whereNotNull('battery_health')->pluck('battery_health')->first();
    
    $isSecond = ($product->condition ?? 'new') === 'second';
    $warrantyBadgeLabel = match($product->warranty_type ?? 'resmi_ibox') {
        'resmi_ibox' => 'Resmi iBox',
        'resmi_digimap' => 'Resmi Digimap',
        'inter_all_operator' => 'Inter All Operator',
        default => 'Garansi Resmi',
    };
@endphp

<div {{ $attributes->merge(['class' => 'cust-card glass-card group']) }}>
    
    <!-- 1. Top Bar: Condition & Battery Health -->
    <div class="flex items-center justify-between gap-2 p-4 pb-0 z-10">
        @if($isSecond)
            <x-customer.spec-badge variant="healthy" icon="check">
                Second · {{ $product->grade ? $product->grade : 'Grade A+' }}
            </x-customer.spec-badge>
        @else
            <x-customer.spec-badge variant="official" icon="shield-check">
                {{ $warrantyBadgeLabel }}
            </x-customer.spec-badge>
        @endif

        @if($isSecond && $batteryHealth)
            <span class="text-[11px] font-medium text-[var(--cust-tone-healthy-ink)] bg-[var(--cust-tone-healthy-bg)] px-2 py-0.5 rounded cust-tabular">
                BH {{ $batteryHealth }}%
            </span>
        @else
            <span class="text-[11px] font-medium text-[var(--cust-ink-faint)]">
                Baru
            </span>
        @endif
    </div>

    <!-- 2. Studio Image Area -->
    <a href="{{ url('/katalog/' . ($product->slug ?? $product->id)) }}" class="cust-card-media group-hover:bg-white transition-colors" aria-label="Lihat detail {{ $product->name }}">
        @if($primaryImage && file_exists(public_path($primaryImage)))
            <img src="{{ asset($primaryImage) }}" 
                 alt="{{ $product->name }}" 
                 width="280" 
                 height="280" 
                 loading="lazy" 
                 decoding="async" 
                 class="max-h-full max-w-full object-contain transform group-hover:scale-105 transition-transform duration-300 ease-out">
        @else
            <div class="w-32 h-44 rounded-[28px] bg-[var(--cust-ink)] border-2 border-[var(--cust-accent)] flex flex-col items-center justify-between p-2 shadow-sm group-hover:scale-105 transition-transform duration-300">
                <div class="w-10 h-2.5 bg-black rounded-full flex items-center justify-end px-1">
                    <div class="w-1 h-1 rounded-full bg-[var(--cust-signal)]"></div>
                </div>
                <div class="w-full flex-grow my-1 rounded-[20px] bg-[var(--cust-paper)] flex flex-col items-center justify-center text-center p-2">
                    <i data-lucide="smartphone" class="w-6 h-6 text-[var(--cust-ink)] mb-1"></i>
                    <span class="text-[10px] font-semibold text-[var(--cust-ink)] leading-tight">{{ $product->name }}</span>
                </div>
                <div class="w-8 h-1 bg-white/40 rounded-full"></div>
            </div>
        @endif
    </a>

    <!-- 3. Body: Title, Swatches, Storage & Price -->
    <div class="cust-card-body">
        
        <!-- Swatches Row -->
        @if($colors->isNotEmpty())
            <div class="flex items-center justify-between gap-2">
                <div class="cust-swatches">
                    @foreach($colors->take(5) as $index => $color)
                        <x-customer.color-swatch 
                            :hex="$color->color_hex ?? '#9A958E'" 
                            :name="$color->color_name" 
                            :active="$index === 0" />
                    @endforeach
                    @if($colors->count() > 5)
                        <span class="text-[10px] text-[var(--cust-ink-muted)]">+{{ $colors->count() - 5 }}</span>
                    @endif
                </div>
                <span class="text-[11px] text-[var(--cust-ink-muted)] truncate max-w-[100px]">
                    {{ $colors->first()->color_name ?? 'Finish' }}
                </span>
            </div>
        @endif

        <!-- Product Name & Series -->
        <div>
            <p class="text-[11px] text-[var(--cust-ink-muted)] mb-0.5">{{ $product->series ?? 'iPhone' }}</p>
            <a href="{{ url('/katalog/' . ($product->slug ?? $product->id)) }}" class="cust-card-title hover:text-[var(--cust-signal)] transition-colors line-clamp-1">
                {{ $product->name }}
            </a>
        </div>

        <!-- Storage Pills -->
        @if($storages->isNotEmpty())
            <div class="cust-pills">
                @foreach($storages->take(4) as $storage)
                    <span class="cust-pill {{ $loop->first ? 'active' : '' }}">
                        {{ $storage }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- Price & Action -->
        <div class="pt-2 mt-auto border-t border-[var(--cust-rule-border)] flex items-end justify-between gap-2">
            <div>
                <p class="text-[10px] text-[var(--cust-ink-muted)]">Mulai dari</p>
                <p class="cust-card-price">
                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                </p>
                <p class="cust-card-subprice cust-tabular">
                    Cicilan Rp {{ number_format($monthlyInstallment, 0, ',', '.') }}/bln
                </p>
            </div>
            <a href="{{ url('/katalog/' . ($product->slug ?? $product->id)) }}" class="cust-btn cust-btn-outline cust-btn-pill text-xs py-1.5 px-3 shrink-0">
                Pilih
            </a>
        </div>

    </div>

</div>
