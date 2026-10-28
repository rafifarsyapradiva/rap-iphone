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
    
    $warrantyBadgeLabel = match($product->warranty_type ?? 'resmi_ibox') {
        'resmi_ibox' => 'Baru • Resmi iBox',
        'resmi_digimap' => 'Baru • Resmi Digimap',
        'inter_all_operator' => 'Inter • All Operator',
        default => 'Baru • Garansi Resmi',
    };
    
    $isSecond = ($product->condition ?? 'new') === 'second';
@endphp

<div {{ $attributes->merge(['class' => 'glass-card glass-card-interactive product-card group flex flex-col justify-between p-6 relative rounded-[20px] bg-[#F5F5F7] border border-[#E5E5EA] hover:bg-[#EFEFF4] transition-all duration-300']) }}>
    
    <!-- 1. Top Row: Condition Badge & Battery Health / Series -->
    <div class="flex items-center justify-between gap-2 mb-4 z-10">
        @if($isSecond)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#E8F8EE] text-[#008040] border border-[#008040]/15">
                <span class="w-1.5 h-1.5 rounded-full bg-[#008040]"></span>
                <span>Second &bull; {{ $product->grade ? $product->grade : 'Grade A+' }}</span>
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#E8F2FF] text-[#0066CC] border border-[#0066CC]/15">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>{{ $warrantyBadgeLabel }}</span>
            </span>
        @endif

        @if($isSecond && $batteryHealth)
            <x-battery-meter :percentage="$batteryHealth" :showLabel="false" />
        @else
            <span class="text-[11px] font-semibold text-[#86868B] uppercase tracking-wider">
                Resmi ID
            </span>
        @endif
    </div>

    <!-- 2. Clean 1:1 Studio Image Area -->
    <a href="{{ url('/katalog/' . ($product->slug ?? $product->id)) }}" class="relative w-full aspect-square rounded-2xl bg-[#FFFFFF] border border-[#E8E8ED] flex items-center justify-center p-5 overflow-hidden mb-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
        @if($primaryImage && file_exists(public_path($primaryImage)))
            <img src="{{ asset($primaryImage) }}" alt="{{ $product->name }}" width="320" height="320" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain transform group-hover:scale-105 transition-transform duration-500 ease-out z-10">
        @else
            <!-- Clean Apple Studio Device Vector Rendering -->
            <div class="w-36 h-48 rounded-[34px] bg-gradient-to-b from-[#2D2F33] to-[#1D1D1F] border-[4px] border-[#9A958E]/60 shadow-lg relative flex flex-col items-center justify-between p-2.5 group-hover:scale-105 transition-transform duration-500 z-10">
                <!-- Dynamic Island -->
                <div class="w-12 h-3 bg-black rounded-full flex items-center justify-end px-1.5">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#0071E3]"></div>
                </div>

                <!-- Display Screen -->
                <div class="w-full flex-grow my-1.5 rounded-[22px] bg-gradient-to-br from-[#F5F5F7] via-[#E8F2FF] to-[#FFFFFF] flex flex-col items-center justify-center text-center p-2">
                    <i data-lucide="smartphone" class="w-7 h-7 text-[#1D1D1F]/70 mb-1"></i>
                    <span class="text-[10px] font-bold text-[#1D1D1F] leading-tight">{{ $product->name }}</span>
                    <span class="text-[8px] font-semibold text-[#0066CC] mt-0.5">{{ $isSecond ? 'Certified Pre-Owned' : 'Apple Official' }}</span>
                </div>

                <!-- Home Indicator -->
                <div class="w-10 h-1 bg-white/40 rounded-full"></div>
            </div>
        @endif
    </a>

    <!-- 3. Color Swatch Dots -->
    @if($colors->isNotEmpty())
        <div class="flex items-center justify-between gap-2 mb-3">
            <div class="flex items-center gap-1.5 py-0.5">
                @foreach($colors->take(6) as $index => $color)
                    <button type="button" 
                            class="color-dot-btn {{ $index === 0 ? 'active' : '' }}" 
                            style="background-color: {{ $color->color_hex ?? '#9A958E' }};"
                            data-color-name="{{ $color->color_name }}"
                            title="{{ $color->color_name }}"
                            aria-label="Warna {{ $color->color_name }}">
                    </button>
                @endforeach
                @if($colors->count() > 6)
                    <span class="text-[10px] text-[#6E6E73] font-semibold">+{{ $colors->count() - 6 }}</span>
                @endif
            </div>
            <span class="selected-color-label text-[11px] font-medium text-[#6E6E73] truncate max-w-[110px]">
                {{ $colors->first()->color_name ?? 'Original Finish' }}
            </span>
        </div>
    @endif

    <!-- 4. Series Eyebrow, Product Title & Storage Pills -->
    <div class="space-y-2 mb-4">
        <div>
            <span class="text-[11px] font-semibold text-[#6E6E73] uppercase tracking-wider block">
                {{ $product->series ?? 'Apple iPhone' }}
            </span>
            <h3 class="text-[18px] font-semibold text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors leading-snug line-clamp-1 mt-0.5">
                <a href="{{ url('/katalog/' . ($product->slug ?? $product->id)) }}">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        @if($storages->isNotEmpty())
            <div class="flex flex-wrap gap-1.5 pt-0.5">
                @foreach($storages as $index => $storage)
                    <span class="storage-chip {{ $index === 0 ? 'active' : '' }}">
                        {{ $storage }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 5. Pricing, Installment/Trade-In Subtext & Full-Width Action CTA -->
    <div class="pt-3 border-t border-[#E5E5EA] space-y-3">
        <div>
            <div class="flex items-baseline justify-between gap-2">
                <span class="text-[11px] text-[#6E6E73] font-medium">Mulai dari</span>
                @if($isSecond && $batteryHealth)
                    <span class="text-[11px] font-semibold text-[#008040]">BH {{ $batteryHealth }}%</span>
                @endif
            </div>
            <div class="text-[19px] font-bold text-[#1D1D1F] tracking-tight leading-tight mt-0.5">
                Rp {{ number_format($minPrice, 0, ',', '.') }}
            </div>
            <p class="text-[12px] text-[#6E6E73] font-medium mt-1 leading-snug">
                Cicilan mulai Rp {{ number_format($monthlyInstallment, 0, ',', '.') }}/bln atau Tukar Tambah
            </p>
        </div>

        <div class="flex items-center gap-2 pt-1">
            <a href="{{ url('/katalog/' . ($product->slug ?? $product->id)) }}" class="flex-1 py-2.5 px-4 rounded-full bg-[#FFFFFF] border border-[#D2D2D7] text-[#1D1D1F] hover:bg-[#1D1D1F] hover:border-[#1D1D1F] hover:text-white text-xs font-semibold text-center transition-all flex items-center justify-center gap-1.5">
                <span>Pilih Varian</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
            @if($variants->isNotEmpty())
                <form action="{{ route('cart.add') }}" method="POST" class="quick-add-cart-form inline-block shrink-0">
                    @csrf
                    <input type="hidden" name="product_variant_id" value="{{ $variants->first()->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-9 h-9 rounded-full bg-[#FFFFFF] border border-[#D2D2D7] text-[#1D1D1F] hover:bg-[#0071E3] hover:border-[#0071E3] hover:text-white flex items-center justify-center transition-all" title="Tambah Cepat ke Keranjang" aria-label="Tambah ke Keranjang">
                        <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
