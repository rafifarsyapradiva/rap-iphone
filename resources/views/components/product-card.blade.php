@props([
    'product',
])

@php
    $variants = $product->variants ?? collect();
    $colors = $variants->unique('color_name')->values();
    $storages = $variants->unique('storage_capacity')->pluck('storage_capacity')->values();
    $primaryImage = $product->primaryImage->image_path ?? ($product->images->first()->image_path ?? null);
    $minPrice = $variants->min('selling_price') ?? $product->base_price;
    $batteryHealth = $variants->whereNotNull('battery_health')->pluck('battery_health')->first();
    
    $warrantyBadgeLabel = match($product->warranty_type ?? 'resmi_ibox') {
        'resmi_ibox' => 'Garansi Resmi iBox',
        'resmi_digimap' => 'Garansi Resmi Digimap',
        'inter_all_operator' => 'Inter All Operator',
        default => 'Garansi Toko Resmi',
    };
    
    $isSecond = ($product->condition ?? 'new') === 'second';
@endphp

<div {{ $attributes->merge(['class' => 'glass-card glass-card-interactive product-card group flex flex-col justify-between p-5 relative rounded-3xl border border-white/10 hover:border-white/25 transition-all duration-300']) }}>
    
    <!-- Top Badges & Battery Health Bar -->
    <div class="flex items-center justify-between gap-2 mb-4 z-10">
        @if($isSecond)
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#34C759]/15 text-[#34C759] border border-[#34C759]/30">
                <span class="w-1.5 h-1.5 rounded-full bg-[#34C759]"></span>
                <span>{{ $product->grade ? $product->grade : 'Second Like New' }}</span>
            </div>
        @else
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#0071E3]/15 text-[#5AC8FA] border border-[#0071E3]/30">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>{{ $warrantyBadgeLabel }}</span>
            </div>
        @endif

        @if($isSecond && $batteryHealth)
            <x-battery-meter :percentage="$batteryHealth" :showLabel="false" />
        @else
            <span class="text-[11px] font-medium text-[#8E8E93] uppercase tracking-wider">
                {{ $product->series ?? 'iPhone' }}
            </span>
        @endif
    </div>

    <!-- Product Image Showcase with Glow & Zoom -->
    <div class="relative w-full aspect-square rounded-2xl bg-gradient-to-b from-white/5 to-transparent flex items-center justify-center p-4 overflow-hidden mb-4 group-hover:bg-white/10 transition-colors">
        
        <!-- Ambient behind-the-device glow -->
        <div class="absolute inset-0 bg-radial from-[#0071E3]/15 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>

        @if($primaryImage && file_exists(public_path($primaryImage)))
            <img src="{{ asset($primaryImage) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain transform group-hover:scale-105 transition-transform duration-500 ease-out z-10">
        @else
            <!-- Apple Stylized iPhone Vector Rendering Fallback -->
            <div class="w-36 h-48 rounded-[36px] bg-gradient-to-tr from-[#161B22] via-[#21262D] to-[#30363D] border-[4px] border-[#8E8E93]/40 shadow-2xl relative flex flex-col items-center justify-between p-2.5 group-hover:scale-105 transition-transform duration-500 z-10">
                <!-- Dynamic Island / Notch -->
                <div class="w-14 h-3.5 bg-black rounded-full border border-white/10 flex items-center justify-end px-1.5">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#0071E3]/80"></div>
                </div>

                <!-- Display Visual -->
                <div class="w-full flex-grow my-1.5 rounded-[24px] bg-gradient-to-br from-[#0B0C10] to-[#1C1F26] flex flex-col items-center justify-center text-center p-2 border border-white/5">
                    <i data-lucide="smartphone" class="w-8 h-8 text-[#E5E5EA]/40 mb-1"></i>
                    <span class="text-[10px] font-bold text-[#E5E5EA]/80">{{ $product->name }}</span>
                    <span class="text-[8px] text-[#8E8E93]">{{ $isSecond ? 'Unit Second' : 'BNIB Garansi' }}</span>
                </div>

                <!-- Bottom Bar -->
                <div class="w-12 h-1 bg-white/30 rounded-full"></div>
            </div>
        @endif
    </div>

    <!-- Product Title & Selected Color Label -->
    <div class="space-y-1 mb-3">
        <h3 class="text-base font-bold text-white group-hover:text-[#5AC8FA] transition-colors line-clamp-1">
            {{ $product->name }}
        </h3>
        <p class="text-xs text-[#8E8E93] flex items-center gap-1.5">
            <span>Warna:</span>
            <span class="selected-color-label font-medium text-[#E5E5EA]">
                {{ $colors->first()->color_name ?? 'Original Finish' }}
            </span>
        </p>
    </div>

    <!-- Color Selector Dots -->
    @if($colors->isNotEmpty())
        <div class="flex items-center gap-2 mb-3 py-1">
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
                <span class="text-[10px] text-[#8E8E93] font-medium">+{{ $colors->count() - 6 }}</span>
            @endif
        </div>
    @endif

    <!-- Storage Capacity Chips -->
    @if($storages->isNotEmpty())
        <div class="flex flex-wrap gap-1.5 mb-4">
            @foreach($storages as $index => $storage)
                <span class="storage-chip {{ $index === 0 ? 'active' : '' }}">
                    {{ $storage }}
                </span>
            @endforeach
        </div>
    @endif

    <!-- Price & Action Footer -->
    <div class="pt-3 border-t border-white/10 flex items-center justify-between gap-3">
        <div>
            <span class="text-[10px] text-[#8E8E93] block uppercase font-medium">Mulai Dari</span>
            <span class="text-base font-extrabold text-white tracking-tight bg-gradient-to-r from-white to-[#E5E5EA] bg-clip-text text-transparent">
                Rp {{ number_format($minPrice, 0, ',', '.') }}
            </span>
        </div>

        <div class="flex items-center gap-1.5">
            <a href="{{ url('/katalog/' . ($product->slug ?? $product->id)) }}" class="glass-btn glass-btn-primary glass-btn-sm" title="Lihat Spesifikasi Lengkap">
                <span>Detail</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
            <button type="button" class="glass-btn glass-btn-secondary glass-btn-sm !p-2" title="Tambah ke Keranjang" aria-label="Tambah ke Keranjang">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>
</div>
