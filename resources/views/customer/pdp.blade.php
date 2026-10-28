@extends('layouts.customer')

@php
    $defaultVariant = $product->variants->first();
    $isSecond = $product->condition === 'second';
    
    $warrantyBadgeLabel = match($product->warranty_type ?? 'resmi_ibox') {
        'resmi_ibox' => 'Garansi Resmi Apple Indonesia (iBox / TAM)',
        'resmi_digimap' => 'Garansi Resmi Digimap',
        'inter_all_operator' => 'Inter All Operator (Sinyal Permanen)',
        default => 'Garansi Resmi Toko Rap-iPhone',
    };
@endphp

@section('title', $product->name . ' — Rap-iPhone Authorized Retail')
@section('meta_description', 'Beli ' . $product->name . ' dengan garansi terpercaya. Tersedia varian kapasitas ' . $storages->join(', ') . ' dan warna resmi Apple.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-16">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-[#8E8E93]">
        <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="{{ url('/katalog') }}" class="hover:text-white transition-colors">Katalog iPhone</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="{{ url('/katalog?series=' . urlencode($product->series)) }}" class="hover:text-white transition-colors">{{ $product->series }}</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-white font-medium truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    <!-- Main PDP Container (2-Column Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start" id="pdp-interactive-container">
        
        <!-- =========================================================================
             LEFT COLUMN: Interactive Multi-Angle Gallery
             ========================================================================= -->
        <div class="lg:col-span-6 space-y-4 lg:sticky lg:top-28">
            <div class="glass-card-elevated rounded-3xl p-8 sm:p-12 relative flex items-center justify-center overflow-hidden min-h-[420px] sm:min-h-[500px]">
                
                <!-- Dynamic Backlight Glow Aura -->
                <div id="pdp-glow-aura" class="absolute w-72 h-72 rounded-full blur-3xl opacity-35 transition-all duration-700 pointer-events-none" style="background-color: {{ $colors->first()->color_hex ?? '#0071E3' }};"></div>

                <!-- Main Display Image / Render Visual -->
                <div id="pdp-main-image-wrapper" class="relative z-10 w-full flex items-center justify-center transition-all duration-500">
                    @php
                        $primaryImg = $product->primaryImage->image_path ?? ($product->images->first()->image_path ?? null);
                    @endphp

                    @if($primaryImg && file_exists(public_path($primaryImg)))
                        <img id="pdp-active-image" src="{{ asset($primaryImg) }}" alt="{{ $product->name }}" width="480" height="480" fetchpriority="high" decoding="async" class="max-h-[380px] w-auto object-contain transition-transform duration-500 hover:scale-105">
                    @else
                        <!-- Apple Vector Stylized iPhone 3D Mockup -->
                        <div id="pdp-vector-device" class="w-56 h-80 sm:w-64 sm:h-96 rounded-[44px] bg-gradient-to-tr from-[#161B22] via-[#21262D] to-[#30363D] border-[5px] border-[#8E8E93]/40 shadow-2xl relative flex flex-col items-center justify-between p-3.5 transition-all duration-500 hover:scale-105">
                            
                            <!-- Dynamic Island Sensor Pill -->
                            <div class="w-20 h-4 bg-black rounded-full border border-white/10 flex items-center justify-end px-2">
                                <div class="w-2 h-2 rounded-full bg-[#0071E3] animate-pulse"></div>
                            </div>

                            <!-- Screen Display -->
                            <div class="w-full flex-grow my-2.5 rounded-[32px] bg-gradient-to-br from-[#0B0C10] to-[#1C1F26] flex flex-col items-center justify-center text-center p-4 border border-white/5 relative overflow-hidden">
                                <i data-lucide="smartphone" class="w-14 h-14 text-white/50 mb-3"></i>
                                <span class="text-sm font-bold text-white">{{ $product->name }}</span>
                                <span id="pdp-vector-color-name" class="text-xs text-[#8E8E93] mt-1">{{ $colors->first()->color_name ?? 'Original Finish' }}</span>
                                
                                <div class="mt-4 px-3 py-1 rounded-full text-[10px] font-bold {{ $isSecond ? 'bg-[#34C759]/20 text-[#34C759] border border-[#34C759]/30' : 'bg-[#0071E3]/20 text-[#5AC8FA] border border-[#0071E3]/30' }}">
                                    {{ $isSecond ? 'Unit Pre-Owned Grade A+' : 'BNIB Segel Garansi Resmi' }}
                                </div>
                            </div>

                            <!-- Bottom Navigation Bar -->
                            <div class="w-20 h-1.5 bg-white/40 rounded-full"></div>
                        </div>
                    @endif
                </div>

                <!-- Condition Badge Overlay -->
                <div class="absolute top-5 left-5 z-20">
                    @if($isSecond)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#34C759]/20 text-[#34C759] border border-[#34C759]/40 backdrop-blur-md">
                            <span class="w-2 h-2 rounded-full bg-[#34C759]"></span>
                            <span>{{ $product->grade ?? 'Grade A+ Like New' }}</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#0071E3]/20 text-[#5AC8FA] border border-[#0071E3]/40 backdrop-blur-md">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>Garansi Resmi</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Multi-Angle Thumbnails Gallery -->
            @php
                $existingImages = $product->images->filter(fn ($img) => !empty($img->image_path) && file_exists(public_path($img->image_path)))->values();
            @endphp
            <div class="flex items-center gap-3 overflow-x-auto pb-2">
                @if($existingImages->isNotEmpty())
                    @foreach($existingImages as $idx => $img)
                        <button type="button"
                                class="pdp-thumb-btn {{ $idx === 0 ? 'active border-[#0071E3]' : 'border-white/10 hover:border-white/30' }} glass-card p-1.5 rounded-xl border shrink-0 w-16 h-16 flex items-center justify-center overflow-hidden"
                                data-image-src="{{ asset($img->image_path) }}"
                                data-angle="angle-{{ $img->order_index }}"
                                title="Sudut #{{ $img->order_index }}">
                            <img src="{{ asset($img->image_path) }}" alt="{{ $product->name }} Angle {{ $idx + 1 }}" width="64" height="64" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain">
                        </button>
                    @endforeach
                @else
                    <button type="button" class="pdp-thumb-btn active glass-card p-2 rounded-xl border border-[#0071E3] shrink-0 w-16 h-16 flex items-center justify-center" data-angle="front" title="Tampak Depan">
                        <i data-lucide="smartphone" class="w-7 h-7 text-white"></i>
                    </button>
                    <button type="button" class="pdp-thumb-btn glass-card p-2 rounded-xl border border-white/10 shrink-0 w-16 h-16 flex items-center justify-center hover:border-white/30" data-angle="back" title="Modul Kamera">
                        <i data-lucide="camera" class="w-7 h-7 text-[#8E8E93]"></i>
                    </button>
                    <button type="button" class="pdp-thumb-btn glass-card p-2 rounded-xl border border-white/10 shrink-0 w-16 h-16 flex items-center justify-center hover:border-white/30" data-angle="side" title="Rangka Titanium">
                        <i data-lucide="layers" class="w-7 h-7 text-[#8E8E93]"></i>
                    </button>
                    <button type="button" class="pdp-thumb-btn glass-card p-2 rounded-xl border border-white/10 shrink-0 w-16 h-16 flex items-center justify-center hover:border-white/30" data-angle="box" title="Kelengkapan Box">
                        <i data-lucide="package" class="w-7 h-7 text-[#8E8E93]"></i>
                    </button>
                @endif
            </div>
        </div>


        <!-- =========================================================================
             RIGHT COLUMN: Product Configurator & Buy Form
             ========================================================================= -->
        <div class="lg:col-span-6 space-y-6">
            
            <!-- Series & Title -->
            <div class="space-y-2">
                <span class="text-xs font-bold text-[#8E8E93] tracking-widest uppercase">
                    {{ $product->series }}
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    {{ $product->name }}
                </h1>
                <div class="flex items-center gap-3 pt-1 text-xs text-[#8E8E93]">
                    <span class="flex items-center gap-1 text-[#FF9F0A]">
                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                        <span class="font-bold text-white">4.9</span>
                        <span>(128 Ulasan Terverifikasi)</span>
                    </span>
                    <span>·</span>
                    <span class="text-[#34C759] font-medium flex items-center gap-1">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        Unit Asli Terverifikasi
                    </span>
                </div>
            </div>

            <!-- Dynamic Price Showcase (Live Price Update) -->
            <div class="glass-card p-5 rounded-2xl border border-white/10 space-y-2 bg-gradient-to-r from-white/5 to-transparent">
                <span class="text-xs text-[#8E8E93] uppercase font-semibold tracking-wider block">Harga Unit Resmi</span>
                <div class="flex items-baseline gap-3">
                    <span id="pdp-live-price" class="text-3xl sm:text-4xl font-black text-white tracking-tight bg-gradient-to-r from-white to-[#E5E5EA] bg-clip-text text-transparent">
                        Rp {{ number_format($defaultVariant->selling_price ?? $product->base_price, 0, ',', '.') }}
                    </span>
                </div>
                <p id="pdp-installment-calc" class="text-xs text-[#8E8E93]">
                    Cicilan 0% mulai dari <strong class="text-[#5AC8FA]">Rp {{ number_format(($defaultVariant->selling_price ?? $product->base_price) / 24, 0, ',', '.') }}/bln</strong> dengan kartu kredit mitra.
                </p>
            </div>

            <!-- Pre-Owned / Second Transparency Widget -->
            @if($isSecond)
                <div class="glass-card p-5 rounded-2xl border border-[#34C759]/30 bg-[#34C759]/5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-white/10">
                        <span class="text-xs font-bold text-[#34C759] flex items-center gap-2">
                            <i data-lucide="badge-check" class="w-4 h-4"></i>
                            Transparansi Unit Second Like New
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#34C759]/20 text-[#34C759]">
                            Lolos 32 Titik QC
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <!-- Battery Health Meter -->
                        <div class="space-y-1.5">
                            <span class="text-[#8E8E93] block">Battery Health (BH)</span>
                            <div class="flex items-center gap-2">
                                <span id="pdp-bh-percentage" class="text-lg font-black text-[#34C759]">
                                    {{ $defaultVariant->battery_health ?? 96 }}%
                                </span>
                                <span class="text-[10px] text-[#34C759] bg-[#34C759]/10 px-2 py-0.5 rounded-full font-bold">
                                    Sangat Prima
                                </span>
                            </div>
                        </div>

                        <!-- IMEI & Sinyal Status -->
                        <div class="space-y-1.5">
                            <span class="text-[#8E8E93] block">Status IMEI & Jaringan</span>
                            <span class="font-bold text-white flex items-center gap-1.5">
                                <i data-lucide="signal" class="w-3.5 h-3.5 text-[#34C759]"></i>
                                All Operator Permanen
                            </span>
                        </div>

                        <!-- Physical Condition Grade -->
                        <div class="space-y-1.5">
                            <span class="text-[#8E8E93] block">Kondisi Fisik</span>
                            <span class="font-bold text-white flex items-center gap-1.5">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#FF9F0A]"></i>
                                {{ $product->grade ?? 'Grade A+ Mulus 99%' }}
                            </span>
                        </div>

                        <!-- Kelengkapan -->
                        <div class="space-y-1.5">
                            <span class="text-[#8E8E93] block">Kelengkapan Paket</span>
                            <span class="font-bold text-white flex items-center gap-1.5">
                                <i data-lucide="box" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                                Fullset Box & Kabel
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Buy & Add to Cart -->
            <form id="pdp-buy-form" action="{{ route('cart.add') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="product_variant_id" id="selected-variant-id" value="{{ $defaultVariant->id ?? '' }}">

                <!-- 1. Color Dot Finishes (Selector Warna Dinamis) -->
                @if($colors->isNotEmpty())
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#8E8E93] uppercase tracking-wider">
                                Pilihan Warna Apple Finish:
                            </label>
                            <span id="pdp-active-color-label" class="text-xs font-bold text-white">
                                {{ $colors->first()->color_name }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/5 border border-white/10" id="pdp-color-picker-group">
                            @foreach($colors as $index => $color)
                                <button type="button" 
                                        class="pdp-color-dot {{ $index === 0 ? 'active' : '' }}" 
                                        data-color-name="{{ $color->color_name }}"
                                        data-color-hex="{{ $color->color_hex }}"
                                        style="background-color: {{ $color->color_hex }};"
                                        title="{{ $color->color_name }}"
                                        aria-label="Warna {{ $color->color_name }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 2. Storage Capacity Chips (Selector Kapasitas Penyimpanan) -->
                @if($storages->isNotEmpty())
                    <div class="space-y-2.5">
                        <label class="text-xs font-bold text-[#8E8E93] uppercase tracking-wider block">
                            Kapasitas Penyimpanan:
                        </label>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5" id="pdp-storage-group">
                            @foreach($storages as $index => $storage)
                                <button type="button" 
                                        class="pdp-storage-btn {{ $index === 0 ? 'active' : '' }}"
                                        data-storage="{{ $storage }}">
                                    <span class="text-sm font-bold block">{{ $storage }}</span>
                                    <span class="text-[10px] text-[#8E8E93] block mt-0.5 pdp-storage-price-hint"></span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 3. Stock Status & Quantity Stepper -->
                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-white/5 border border-white/10">
                    <div class="space-y-0.5">
                        <span class="text-xs text-[#8E8E93] block font-medium">Ketersediaan Stok:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#34C759]"></span>
                            <span id="pdp-stock-status" class="text-xs font-bold text-[#34C759]">
                                Tersedia ({{ $defaultVariant->stock ?? 10 }} Unit Siap Kirim)
                            </span>
                        </div>
                    </div>

                    <!-- Quantity Stepper -->
                    <div class="flex items-center gap-2">
                        <button type="button" id="pdp-qty-dec" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold flex items-center justify-center transition-colors">
                            -
                        </button>
                        <input type="number" name="quantity" id="pdp-quantity-input" value="1" min="1" max="{{ $defaultVariant->stock ?? 10 }}" class="w-12 text-center text-sm font-bold bg-transparent text-white border-0 focus:ring-0">
                        <button type="button" id="pdp-qty-inc" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold flex items-center justify-center transition-colors">
                            +
                        </button>
                    </div>
                </div>

                <!-- 4. Action Buttons (Beli Sekarang & Tambah ke Keranjang) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <!-- Tambah ke Keranjang Button (AJAX) -->
                    <button type="submit" id="pdp-add-cart-btn" class="glass-btn glass-btn-secondary glass-btn-lg w-full group">
                        <i data-lucide="shopping-bag" class="w-4 h-4 text-[#0071E3] transition-transform group-hover:scale-110"></i>
                        <span>Tambah ke Keranjang</span>
                    </button>

                    <!-- Beli Sekarang Button (Direct Checkout Flow) -->
                    <button type="button" id="pdp-buy-now-btn" class="glass-btn glass-btn-primary glass-btn-lg w-full group shadow-lg">
                        <i data-lucide="zap" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
                        <span>Beli Sekarang</span>
                    </button>
                </div>

            </form>

            <!-- Store Guarantee Trust Card -->
            <div class="glass-card p-4 rounded-2xl border border-white/10 space-y-2 text-xs text-[#8E8E93]">
                <div class="flex items-center gap-2 text-[#E5E5EA]">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-[#0071E3]"></i>
                    <span class="font-semibold">{{ $warrantyBadgeLabel }}</span>
                </div>
                <p class="leading-relaxed">
                    Pengiriman dengan proteksi asuransi penuh 100% aman. Jaminan ganti unit baru bila ada kendala fungsi dalam 7 hari pertama sejak barang diterima.
                </p>
            </div>

        </div>
    </div>


    <!-- =========================================================================
         TECHNICAL SPECIFICATIONS TABLE (Standar Apple Retail)
         ========================================================================= -->
    <section class="space-y-6 pt-10 border-t border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-3 h-8 rounded-full bg-[#0071E3]"></div>
            <div>
                <h2 class="text-2xl font-black text-white">Spesifikasi Teknis & Rincian</h2>
                <p class="text-xs text-[#8E8E93] mt-0.5">Spesifikasi perangkat keras resmi dari Apple</p>
            </div>
        </div>

        <div class="glass-card rounded-3xl border border-white/10 overflow-hidden">
            <div class="divide-y divide-white/10 text-xs sm:text-sm">
                
                <div class="grid grid-cols-1 sm:grid-cols-3 p-4 sm:p-5">
                    <span class="font-semibold text-[#8E8E93]">Layar Tampilan</span>
                    <span class="sm:col-span-2 text-white font-medium">
                        Super Retina XDR OLED, ProMotion 120Hz adaptif, Always-On Display, Kecerahan Puncak hingga 2000 nits.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-4 sm:p-5">
                    <span class="font-semibold text-[#8E8E93]">Chipset & Pemrosesan</span>
                    <span class="sm:col-span-2 text-white font-medium">
                        Chip Apple Silicon dengan Neural Engine generasi terbaru untuk Apple Intelligence dan grafis ray tracing.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-4 sm:p-5">
                    <span class="font-semibold text-[#8E8E93]">Sistem Kamera</span>
                    <span class="sm:col-span-2 text-white font-medium">
                        Kamera Utama Fusion 48 MP, Ultra Wide, Telefoto dengan sensor quad-pixel dan Camera Control intuitif.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-4 sm:p-5">
                    <span class="font-semibold text-[#8E8E93]">Bahan & Desain Fisik</span>
                    <span class="sm:col-span-2 text-white font-medium">
                        Rangka Titanium Kelas Dirgantara Grade 5, Bagian Belakang Kaca Bertekstur Matte, Pelindung Depan Ceramic Shield.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-4 sm:p-5">
                    <span class="font-semibold text-[#8E8E93]">Konektivitas & Port</span>
                    <span class="sm:col-span-2 text-white font-medium">
                        Port USB-C (kecepatan transfer hingga 10 Gbps), 5G All Operator, Wi-Fi 7, Bluetooth 5.3, Ultra Wideband chip.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-4 sm:p-5">
                    <span class="font-semibold text-[#8E8E93]">Ketahanan Air & Debu</span>
                    <span class="sm:col-span-2 text-white font-medium">
                        Level IP68 (kedalaman maksimum 6 meter hingga selama 30 menit) menurut standar IEC 60529.
                    </span>
                </div>

            </div>
        </div>
    </section>


    <!-- =========================================================================
         RELATED IPHONES RECOMMENDATIONS
         ========================================================================= -->
    @if($relatedProducts->isNotEmpty())
        <section class="space-y-6 pt-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-white">Rekomendasi Unit Terkait</h2>
                    <p class="text-xs text-[#8E8E93] mt-0.5">Pilihan lain dalam seri {{ $product->series }}</p>
                </div>
                <a href="{{ url('/katalog?series=' . urlencode($product->series)) }}" class="text-xs font-semibold text-[#5AC8FA] hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    <x-product-card :product="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</div>

<!-- Pass dynamic variants data into JavaScript state -->
<script>
    window.RAP_VARIANTS_DATA = {!! $variantsJson !!};
    window.RAP_CURRENT_PRODUCT_ID = {{ $product->id }};
    window.RAP_BUY_NOW_URL = "{{ route('cart.buyNow') }}";
</script>
@endsection

