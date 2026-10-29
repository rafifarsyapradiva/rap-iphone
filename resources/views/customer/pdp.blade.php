@extends('layouts.customer')

@php
    $defaultVariant = $product->variants->first();
    $isSecond = $product->condition === 'second';
    $defaultPrice = $defaultVariant->selling_price ?? $product->base_price;
    
    $warrantyBadgeLabel = match($product->warranty_type ?? 'resmi_ibox') {
        'resmi_ibox' => 'Garansi Resmi Apple Indonesia (iBox / TAM)',
        'resmi_digimap' => 'Garansi Resmi Digimap Indonesia',
        'inter_all_operator' => 'Inter All Operator (Sinyal Permanen)',
        default => 'Garansi Resmi Toko Rap-iPhone',
    };

    // Quick Trade-In reference values for Inline Trade-In Price Slasher
    $tradeInQuickModels = [
        ['label' => 'Tanpa Tukar Tambah (Harga Normal)', 'credit' => 0],
        ['label' => 'iPhone 15 Pro Max 256GB (Est. s/d Rp 16.500.000)', 'credit' => 16500000],
        ['label' => 'iPhone 15 Pro 128GB (Est. s/d Rp 14.200.000)', 'credit' => 14200000],
        ['label' => 'iPhone 14 Pro Max 256GB (Est. s/d Rp 13.500.000)', 'credit' => 13500000],
        ['label' => 'iPhone 14 Pro 128GB (Est. s/d Rp 11.800.000)', 'credit' => 11800000],
        ['label' => 'iPhone 13 Pro Max 128GB (Est. s/d Rp 10.200.000)', 'credit' => 10200000],
        ['label' => 'iPhone 13 128GB (Est. s/d Rp 7.500.000)', 'credit' => 7500000],
        ['label' => 'iPhone 12 128GB (Est. s/d Rp 5.200.000)', 'credit' => 5200000],
    ];
@endphp

@section('title', 'Beli ' . $product->name . ' — Rap-iPhone Authorized Style Partner')
@section('meta_description', 'Beli ' . $product->name . ' dengan garansi terpercaya, simulasi cicilan 0%, kalkulator tukar tambah instan, dan layanan Click & Collect.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24 space-y-16">

    <!-- 1. Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-[#6E6E73]">
        <a href="{{ url('/') }}" class="hover:text-[#1D1D1F] transition-colors">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#86868B]"></i>
        <a href="{{ url('/katalog') }}" class="hover:text-[#1D1D1F] transition-colors">Katalog iPhone</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#86868B]"></i>
        <a href="{{ url('/katalog?series=' . urlencode($product->series)) }}" class="hover:text-[#1D1D1F] transition-colors">{{ $product->series }}</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#86868B]"></i>
        <span class="text-[#1D1D1F] font-semibold truncate max-w-[220px]">{{ $product->name }}</span>
    </nav>

    <!-- 2. Main PDP 2-Column Digimap Layout (55% Left Sticky Studio Gallery / 45% Right Step Configurator) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start" id="pdp-interactive-container">
        
        <!-- =========================================================================
             LEFT COLUMN (7 Cols / ~58%): Sticky Studio Gallery (#F5F5F7)
             ========================================================================= -->
        <div class="lg:col-span-7 space-y-4 lg:sticky lg:top-24">
            
            <!-- Main Studio Frame -->
            <div class="rounded-[24px] bg-[#F5F5F7] border border-[#E5E5EA] p-8 sm:p-12 relative flex items-center justify-center overflow-hidden min-h-[420px] sm:min-h-[500px]">
                
                <!-- Subtle Color Tint Behind Device -->
                <div id="pdp-glow-aura" class="absolute w-72 h-72 rounded-full blur-3xl opacity-15 transition-all duration-700 pointer-events-none" style="background-color: {{ $colors->first()->color_hex ?? '#0066CC' }};"></div>

                <!-- Condition Pill Top-Left -->
                <div class="absolute top-5 left-5 z-20">
                    @if($isSecond)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F8EE] text-[#008040] border border-[#008040]/20">
                            <span class="w-2 h-2 rounded-full bg-[#008040]"></span>
                            <span>Second &bull; {{ $product->grade ?? 'Grade A+ Like New' }}</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F2FF] text-[#0066CC] border border-[#0066CC]/20">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>Baru &bull; Garansi Resmi</span>
                        </span>
                    @endif
                </div>

                <!-- Main Display Image / Vector Studio Mockup -->
                <div id="pdp-main-image-wrapper" class="relative z-10 w-full flex items-center justify-center transition-all duration-500">
                    @php
                        $primaryImg = $product->primaryImage->image_path ?? ($product->images->first()->image_path ?? null);
                    @endphp

                    @if($primaryImg && file_exists(public_path($primaryImg)))
                        <img id="pdp-active-image" src="{{ asset($primaryImg) }}" alt="{{ $product->name }}" width="480" height="480" fetchpriority="high" decoding="async" class="max-h-[390px] w-auto object-contain transition-transform duration-500 hover:scale-105">
                    @else
                        <div id="pdp-vector-device" class="w-56 h-80 sm:w-64 sm:h-96 rounded-[44px] bg-gradient-to-b from-[#2D2F33] to-[#1D1D1F] border-[5px] border-[#9A958E]/60 shadow-2xl relative flex flex-col items-center justify-between p-3.5 transition-all duration-500 hover:scale-105">
                            <!-- Dynamic Island -->
                            <div class="w-20 h-4 bg-black rounded-full flex items-center justify-end px-2">
                                <div class="w-2 h-2 rounded-full bg-[#0071E3]"></div>
                            </div>

                            <!-- Screen Display -->
                            <div class="w-full flex-grow my-2.5 rounded-[32px] bg-gradient-to-br from-[#FFFFFF] via-[#F5F5F7] to-[#E8F2FF] flex flex-col items-center justify-center text-center p-4 relative overflow-hidden">
                                <i data-lucide="smartphone" class="w-14 h-14 text-[#1D1D1F]/70 mb-3"></i>
                                <span class="text-sm font-bold text-[#1D1D1F]">{{ $product->name }}</span>
                                <span id="pdp-vector-color-name" class="text-xs text-[#6E6E73] font-medium mt-1">{{ $colors->first()->color_name ?? 'Original Finish' }}</span>
                                
                                <div class="mt-4 px-3 py-1 rounded-full text-[10px] font-bold {{ $isSecond ? 'bg-[#E8F8EE] text-[#008040]' : 'bg-[#E8F2FF] text-[#0066CC]' }}">
                                    {{ $isSecond ? 'Certified Pre-Owned QC 32 Titik' : 'BNIB Segel Garansi Resmi' }}
                                </div>
                            </div>

                            <!-- Bottom Bar -->
                            <div class="w-20 h-1.5 bg-white/40 rounded-full"></div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Multi-Angle Thumbnails Row -->
            @php
                $existingImages = $product->images->filter(fn ($img) => !empty($img->image_path) && file_exists(public_path($img->image_path)))->unique('image_path')->values();
            @endphp
            <div class="flex items-center gap-3 overflow-x-auto pb-1">
                @if($existingImages->isNotEmpty())
                    @foreach($existingImages as $idx => $img)
                        <button type="button"
                                class="pdp-thumb-btn {{ $idx === 0 ? 'active border-[#0066CC]' : 'border-[#E5E5EA] hover:border-[#86868B]' }} p-1.5 rounded-2xl border shrink-0 w-18 h-18 flex items-center justify-center overflow-hidden"
                                data-image-src="{{ asset($img->image_path) }}"
                                data-angle="angle-{{ $img->order_index }}"
                                title="Sudut #{{ $img->order_index }}">
                            <img src="{{ asset($img->image_path) }}" alt="{{ $product->name }} Angle {{ $idx + 1 }}" width="64" height="64" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain">
                        </button>
                    @endforeach
                @else
                    <button type="button" class="pdp-thumb-btn active p-2 rounded-2xl border border-[#0066CC] shrink-0 w-18 h-18 flex flex-col items-center justify-center gap-1" data-angle="front" title="Tampak Depan">
                        <i data-lucide="smartphone" class="w-6 h-6 text-[#1D1D1F]"></i>
                        <span class="text-[9px] font-semibold text-[#6E6E73]">Depan</span>
                    </button>
                    <button type="button" class="pdp-thumb-btn p-2 rounded-2xl border border-[#E5E5EA] shrink-0 w-18 h-18 flex flex-col items-center justify-center gap-1 hover:border-[#86868B]" data-angle="back" title="Modul Kamera">
                        <i data-lucide="camera" class="w-6 h-6 text-[#6E6E73]"></i>
                        <span class="text-[9px] font-semibold text-[#6E6E73]">Kamera</span>
                    </button>
                    <button type="button" class="pdp-thumb-btn p-2 rounded-2xl border border-[#E5E5EA] shrink-0 w-18 h-18 flex flex-col items-center justify-center gap-1 hover:border-[#86868B]" data-angle="side" title="Rangka Bodi">
                        <i data-lucide="layers" class="w-6 h-6 text-[#6E6E73]"></i>
                        <span class="text-[9px] font-semibold text-[#6E6E73]">Bodi</span>
                    </button>
                    <button type="button" class="pdp-thumb-btn p-2 rounded-2xl border border-[#E5E5EA] shrink-0 w-18 h-18 flex flex-col items-center justify-center gap-1 hover:border-[#86868B]" data-angle="box" title="Kelengkapan Kotak">
                        <i data-lucide="package" class="w-6 h-6 text-[#6E6E73]"></i>
                        <span class="text-[9px] font-semibold text-[#6E6E73]">Kotak</span>
                    </button>
                @endif
            </div>

            <!-- 3 Digimap Service Pillars Below Gallery -->
            <div class="grid grid-cols-3 gap-3 pt-2">
                <div class="p-3.5 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] text-center space-y-1">
                    <i data-lucide="truck" class="w-5 h-5 text-[#0066CC] mx-auto"></i>
                    <p class="text-[11px] font-bold text-[#1D1D1F]">Gratis Pengiriman</p>
                    <p class="text-[10px] text-[#6E6E73]">Berasuransi 100%</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] text-center space-y-1">
                    <i data-lucide="shield-check" class="w-5 h-5 text-[#008040] mx-auto"></i>
                    <p class="text-[11px] font-bold text-[#1D1D1F]">Garansi Terjamin</p>
                    <p class="text-[10px] text-[#6E6E73]">Resmi / QC 32 Titik</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] text-center space-y-1">
                    <i data-lucide="store" class="w-5 h-5 text-[#BF4800] mx-auto"></i>
                    <p class="text-[11px] font-bold text-[#1D1D1F]">Click &amp; Collect</p>
                    <p class="text-[10px] text-[#6E6E73]">Ambil di Gerai Toko</p>
                </div>
            </div>
        </div>


        <!-- =========================================================================
             RIGHT COLUMN (5 Cols / ~42%): Step-by-Step Configurator
             ========================================================================= -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Header Produk & Harga Dinamis -->
            <div class="space-y-3 pb-5 border-b border-[#E5E5EA]">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-[#BF4800] uppercase tracking-wider">
                        {{ $product->series }}
                    </span>
                    <span class="text-[#D2D2D7]">&bull;</span>
                    <span class="text-xs font-semibold text-[#0066CC]">
                        {{ $isSecond ? 'Certified Pre-Owned' : 'Authorized Retail Unit' }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-[34px] font-bold text-[#1D1D1F] tracking-tight leading-tight">
                    Beli {{ $product->name }}
                </h1>

                <!-- Main Price & Installment Modal Trigger -->
                <div class="pt-1 space-y-1.5">
                    <div class="flex items-baseline gap-3 flex-wrap">
                        <span id="pdp-live-price" class="text-[28px] sm:text-[30px] font-bold text-[#1D1D1F] tracking-tight">
                            Rp {{ number_format($defaultPrice, 0, ',', '.') }}
                        </span>
                        <span id="pdp-net-price-badge" class="hidden px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#E8F8EE] text-[#008040]">
                            Hemat Trade-In
                        </span>
                    </div>

                    <p id="pdp-installment-calc" class="text-xs sm:text-[13px] text-[#6E6E73]">
                        Atau cicilan 0% mulai dari <strong class="text-[#1D1D1F]">Rp {{ number_format(round($defaultPrice / 24), 0, ',', '.') }}/bln</strong> selama 24 bulan.
                    </p>

                    <button type="button" data-modal-target="installment-modal" class="inline-flex items-center gap-1 text-xs font-semibold text-[#0066CC] hover:underline pt-0.5">
                        <i data-lucide="calculator" class="w-3.5 h-3.5"></i>
                        <span>Lihat simulasi cicilan &amp; metode pembayaran Midtrans</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>

            <!-- Form Configurator & Buy Action -->
            <form id="pdp-buy-form" action="{{ route('cart.add') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="product_variant_id" id="selected-variant-id" value="{{ $defaultVariant->id ?? '' }}">

                <!-- STEP 1: Pilih Warna (Finish) -->
                @if($colors->isNotEmpty())
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wider">
                                1. Pilih Warna. <span class="text-[#6E6E73] font-medium normal-case">Sentuhan akhir favorit Anda.</span>
                            </label>
                        </div>
                        <p class="text-xs font-semibold text-[#1D1D1F]">
                            Warna &mdash; <span id="pdp-active-color-label" class="text-[#0066CC]">{{ $colors->first()->color_name }}</span>
                        </p>

                        <div class="flex items-center gap-3.5 py-1" id="pdp-color-picker-group">
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

                <!-- STEP 2: Pilih Kapasitas Penyimpanan (Storage 2-Column Grid) -->
                @if($storages->isNotEmpty())
                    <div class="space-y-3 pt-2 border-t border-[#E5E5EA]">
                        <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wider block">
                            2. Kapasitas Penyimpanan. <span class="text-[#6E6E73] font-medium normal-case">Berapa besar ruang yang Anda butuhkan?</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3" id="pdp-storage-group">
                            @foreach($storages as $index => $storage)
                                @php
                                    $storageVariant = $product->variants->firstWhere('storage_capacity', $storage);
                                    $storagePrice = $storageVariant->selling_price ?? $product->base_price;
                                @endphp
                                <button type="button" 
                                        class="pdp-storage-btn {{ $index === 0 ? 'active' : '' }} flex items-center justify-between gap-2"
                                        data-storage="{{ $storage }}">
                                    <div>
                                        <span class="text-base font-bold text-[#1D1D1F] block">{{ $storage }}</span>
                                        <span class="text-[10px] text-[#6E6E73] block">Siap Kirim</span>
                                    </div>
                                    <span class="text-xs font-semibold text-[#1D1D1F] text-right pdp-storage-price-hint">
                                        Rp {{ number_format($storagePrice, 0, ',', '.') }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- STEP 3: Transparency Trust Panel (Khusus Produk Second Like New) -->
                @if($isSecond)
                    <div class="p-5 rounded-[20px] bg-[#F5F5F7] border border-[#E5E5EA] space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-[#D2D2D7]">
                            <span class="text-xs font-bold text-[#008040] flex items-center gap-1.5">
                                <i data-lucide="badge-check" class="w-4 h-4"></i>
                                <span>3. Transparansi Unit Second Like New</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#E8F8EE] text-[#008040]">
                                Lulus QC 32 Titik
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3.5 text-xs">
                            <div class="p-3 rounded-xl bg-white border border-[#E5E5EA]">
                                <span class="text-[#6E6E73] block text-[11px]">Battery Health (BH)</span>
                                <span id="pdp-bh-percentage" class="text-base font-bold text-[#008040] mt-0.5 block">
                                    {{ $defaultVariant->battery_health ?? 96 }}% (Prima)
                                </span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-[#E5E5EA]">
                                <span class="text-[#6E6E73] block text-[11px]">Status IMEI & Jaringan</span>
                                <span class="text-xs font-bold text-[#1D1D1F] mt-1 flex items-center gap-1">
                                    <i data-lucide="signal" class="w-3.5 h-3.5 text-[#008040]"></i>
                                    <span>All Operator Permanen</span>
                                </span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-[#E5E5EA]">
                                <span class="text-[#6E6E73] block text-[11px]">Kondisi Fisik</span>
                                <span class="text-xs font-bold text-[#1D1D1F] mt-1 block">
                                    {{ $product->grade ?? 'Grade A+ Mulus 99%' }}
                                </span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-[#E5E5EA]">
                                <span class="text-[#6E6E73] block text-[11px]">Kelengkapan</span>
                                <span class="text-xs font-bold text-[#1D1D1F] mt-1 block">
                                    Fullset Box &amp; Kabel
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- STEP 4: Inline Trade-In Price Slasher (Kalkulator Potongan Tukar Tambah di PDP) -->
                <div class="p-5 rounded-[20px] bg-[#F5F5F7] border border-[#E5E5EA] space-y-3.5">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-[#0066CC]"></i>
                            <span class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wider">
                                {{ $isSecond ? '4.' : '3.' }} Apple Style Trade-In (Tukar Tambah)
                            </span>
                        </div>
                        <a href="{{ url('/trade-in') }}" class="text-[11px] font-semibold text-[#0066CC] hover:underline">
                            Wizard Lengkap &rarr;
                        </a>
                    </div>

                    <p class="text-xs text-[#6E6E73] leading-relaxed">
                        Punya iPhone lama? Pilih model unit lama Anda untuk melihat simulasi harga bersih setelah dipotong nilai tukar tambah:
                    </p>

                    <div>
                        <select id="pdp-tradein-select" class="glass-input !bg-white text-xs font-medium cursor-pointer">
                            @foreach($tradeInQuickModels as $tiModel)
                                <option value="{{ $tiModel['credit'] }}">{{ $tiModel['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Live Trade-In Net Payable Result Box -->
                    <div id="pdp-tradein-result-box" class="hidden p-3.5 rounded-xl bg-[#E8F8EE] border border-[#008040]/25 space-y-1 text-xs">
                        <div class="flex items-center justify-between text-[#008040]">
                            <span>Estimasi Potongan Trade-In:</span>
                            <span id="pdp-tradein-credit" class="font-bold">- Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-[#008040]/15 text-[#1D1D1F]">
                            <span class="font-semibold">Estimasi Harga Akhir (Net Payable):</span>
                            <span id="pdp-net-price" class="text-sm font-bold text-[#008040]">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 5: Ketersediaan Stok Omnichannel (Click & Collect + Kurir) & Kuantitas -->
                <div class="p-5 rounded-[20px] bg-[#F5F5F7] border border-[#E5E5EA] space-y-3.5">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-[#6E6E73] font-medium block">Ketersediaan Unit:</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-[#008040]"></span>
                                <span id="pdp-stock-status" class="text-xs font-bold text-[#008040]">
                                    Tersedia ({{ $defaultVariant->stock ?? 10 }} Unit Siap Kirim)
                                </span>
                            </div>
                        </div>

                        <!-- Quantity Stepper -->
                        <div class="flex items-center gap-1.5 bg-white px-2 py-1 rounded-full border border-[#D2D2D7]">
                            <button type="button" id="pdp-qty-dec" class="w-7 h-7 rounded-full hover:bg-[#F5F5F7] text-[#1D1D1F] font-bold flex items-center justify-center transition-colors">
                                -
                            </button>
                            <input type="number" name="quantity" id="pdp-quantity-input" value="1" min="1" max="{{ $defaultVariant->stock ?? 10 }}" class="w-10 text-center text-xs font-bold bg-transparent text-[#1D1D1F] border-0 focus:ring-0">
                            <button type="button" id="pdp-qty-inc" class="w-7 h-7 rounded-full hover:bg-[#F5F5F7] text-[#1D1D1F] font-bold flex items-center justify-center transition-colors">
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Omnichannel Fulfillment Badges -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2 border-t border-[#E5E5EA] text-xs">
                        <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-white border border-[#E5E5EA]">
                            <i data-lucide="truck" class="w-4 h-4 text-[#0066CC] shrink-0 mt-0.5"></i>
                            <div>
                                <span class="font-bold text-[#1D1D1F] block text-[11px]">Kurir Berasuransi</span>
                                <span class="text-[10px] text-[#6E6E73]">JNE / SiCepat / J&amp;T (Gratis)</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-white border border-[#E5E5EA]">
                            <i data-lucide="store" class="w-4 h-4 text-[#008040] shrink-0 mt-0.5"></i>
                            <div>
                                <span class="font-bold text-[#1D1D1F] block text-[11px]">Click &amp; Collect Toko</span>
                                <span class="text-[10px] text-[#008040] font-medium">Siap Ambil 15 Menit</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Primary & Secondary CTA Buttons -->
                <div class="space-y-2.5 pt-1" id="pdp-main-cta-group">
                    <button type="button" id="pdp-buy-now-btn" class="w-full h-[52px] rounded-[14px] bg-[#0071E3] hover:bg-[#0077ED] text-white font-semibold text-sm sm:text-base shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        <span>Beli Sekarang</span>
                    </button>

                    <button type="submit" id="pdp-add-cart-btn" class="w-full h-[50px] rounded-[14px] bg-[#FFFFFF] hover:bg-[#1D1D1F] text-[#1D1D1F] hover:text-white border border-[#1D1D1F] font-semibold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        <span>+ Tambah ke Keranjang</span>
                    </button>
                </div>

            </form>

            <!-- Guarantee Footnote Card -->
            <div class="p-4 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] space-y-1.5 text-xs text-[#6E6E73]">
                <div class="flex items-center gap-2 text-[#1D1D1F]">
                    <i data-lucide="shield-check" class="w-4 h-4 text-[#0066CC]"></i>
                    <span class="font-bold">{{ $warrantyBadgeLabel }}</span>
                </div>
                <p class="leading-relaxed">
                    Setiap pembelian dilengkapi Nota PDF Resmi Ber-QR Code dan pembaruan status real-time melalui WhatsApp (Fonnte) &amp; Email.
                </p>
            </div>

        </div>
    </div>

    <!-- =========================================================================
         3. TECHNICAL SPECIFICATIONS TABLE (Digimap Editorial Spec Card)
         ========================================================================= -->
    <section class="space-y-6 pt-10 border-t border-[#E5E5EA]">
        <div>
            <span class="text-[11px] font-bold text-[#0066CC] uppercase tracking-wider block">Spesifikasi Perangkat Keras</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] tracking-tight mt-0.5">Spesifikasi Teknis & Rincian</h2>
        </div>

        <div class="rounded-[24px] bg-[#F5F5F7] border border-[#E5E5EA] overflow-hidden">
            <div class="divide-y divide-[#E5E5EA] text-xs sm:text-sm">
                
                <div class="grid grid-cols-1 sm:grid-cols-3 p-5">
                    <span class="font-semibold text-[#6E6E73]">Layar Tampilan</span>
                    <span class="sm:col-span-2 text-[#1D1D1F] font-medium">
                        Super Retina XDR OLED, ProMotion 120Hz adaptif, Always-On Display, Dynamic Island, Kecerahan Puncak hingga 2000 nits.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-5">
                    <span class="font-semibold text-[#6E6E73]">Chipset &amp; Performa</span>
                    <span class="sm:col-span-2 text-[#1D1D1F] font-medium">
                        Chip Apple Silicon generasi terbaru dengan Neural Engine 16-core untuk Apple Intelligence dan grafis berbasis perangkat keras.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-5">
                    <span class="font-semibold text-[#6E6E73]">Sistem Kamera Pro</span>
                    <span class="sm:col-span-2 text-[#1D1D1F] font-medium">
                        Kamera Utama Fusion 48 MP, Ultra Wide, Telefoto optik, perekaman video 4K Dolby Vision.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-5">
                    <span class="font-semibold text-[#6E6E73]">Jaringan &amp; Status IMEI</span>
                    <span class="sm:col-span-2 text-[#1D1D1F] font-medium">
                        5G All Operator Indonesia (Telkomsel, Indosat, XL, Smartfren, Tri) &bull; Terdaftar Resmi Kemenperin RI.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 p-5">
                    <span class="font-semibold text-[#6E6E73]">Isi Kotak Kemasan</span>
                    <span class="sm:col-span-2 text-[#1D1D1F] font-medium">
                        Unit {{ $product->name }}, Kabel Pengisi Daya USB-C Original, Dokumentasi &amp; Kartu Garansi Resmi.
                    </span>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         4. RELATED IPHONES RECOMMENDATIONS
         ========================================================================= -->
    @if($relatedProducts->isNotEmpty())
        <section class="space-y-6 pt-6">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-[#1D1D1F] tracking-tight">Rekomendasi Seri Terkait</h2>
                    <p class="text-xs sm:text-sm text-[#6E6E73] mt-0.5">Pilihan lain dalam keluarga {{ $product->series }}</p>
                </div>
                <a href="{{ url('/katalog?series=' . urlencode($product->series)) }}" class="text-xs font-semibold text-[#0066CC] hover:underline">
                    Lihat Semua &rarr;
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

<!-- =========================================================================
     5. MODAL SIMULASI CICILAN & METODE BAYAR MIDTRANS
     ========================================================================= -->
<div id="installment-modal" class="glass-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="installment-modal-title">
    <div class="glass-modal p-6 sm:p-7 max-w-lg w-full space-y-5">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E5EA]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#E8F2FF] text-[#0066CC] flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 id="installment-modal-title" class="text-base font-bold text-[#1D1D1F]">Simulasi Cicilan 0% &amp; Midtrans</h3>
                    <p class="text-xs text-[#6E6E73]">{{ $product->name }} &bull; Bebas Biaya Admin</p>
                </div>
            </div>
            <button type="button" data-modal-close class="p-1.5 rounded-lg text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="p-4 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] flex items-center justify-between text-xs">
            <span class="text-[#6E6E73] font-medium">Harga Varian Terpilih:</span>
            <span id="sim-modal-base-price" class="text-base font-bold text-[#1D1D1F]">
                Rp {{ number_format($defaultPrice, 0, ',', '.') }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="p-3.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="text-[#6E6E73] block">Tenor 3 Bulan (0%)</span>
                <span id="sim-3m" class="text-sm font-bold text-[#1D1D1F] mt-1 block">Rp {{ number_format(round($defaultPrice / 3), 0, ',', '.') }}/bln</span>
            </div>
            <div class="p-3.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="text-[#6E6E73] block">Tenor 6 Bulan (0%)</span>
                <span id="sim-6m" class="text-sm font-bold text-[#1D1D1F] mt-1 block">Rp {{ number_format(round($defaultPrice / 6), 0, ',', '.') }}/bln</span>
            </div>
            <div class="p-3.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="text-[#6E6E73] block">Tenor 12 Bulan (0%)</span>
                <span id="sim-12m" class="text-sm font-bold text-[#0066CC] mt-1 block">Rp {{ number_format(round($defaultPrice / 12), 0, ',', '.') }}/bln</span>
            </div>
            <div class="p-3.5 rounded-xl bg-[#E8F2FF] border border-[#0066CC]/30">
                <span class="text-[#0066CC] font-semibold block">Tenor 24 Bulan (0%)</span>
                <span id="sim-24m" class="text-sm font-bold text-[#0066CC] mt-1 block">Rp {{ number_format(round($defaultPrice / 24), 0, ',', '.') }}/bln</span>
            </div>
        </div>

        <div class="text-[11px] text-[#6E6E73] leading-relaxed">
            Didukung penuh oleh <strong class="text-[#1D1D1F]">Midtrans Snap</strong>: menerima pembayaran QRIS, BCA/Mandiri/BNI/BRI Virtual Account, GoPay, ShopeePay, dan Kartu Kredit Bank Mitra.
        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" data-modal-close class="glass-btn glass-btn-dark glass-btn-sm">
                Mengerti
            </button>
        </div>
    </div>
</div>

<!-- =========================================================================
     6. STICKY BOTTOM PURCHASE BAR (Digimap Conversion Bar)
     ========================================================================= -->
<div id="pdp-sticky-buy-bar" class="fixed bottom-0 inset-x-0 z-40 bg-white/90 backdrop-blur-xl border-t border-[#D2D2D7] shadow-[0_-8px_30px_rgba(0,0,0,0.06)] py-3 px-4 transform translate-y-full transition-transform duration-300">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA] flex items-center justify-center shrink-0 hidden sm:flex">
                <i data-lucide="smartphone" class="w-5 h-5 text-[#1D1D1F]"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-bold text-[#1D1D1F] truncate">{{ $product->name }}</p>
                <p id="sticky-variant-label" class="text-[11px] text-[#6E6E73] truncate">
                    {{ ($colors->first()->color_name ?? 'Original') }} &bull; {{ ($storages->first() ?? '128GB') }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            <div class="text-right">
                <span class="text-[10px] text-[#6E6E73] block">Harga Total</span>
                <span id="sticky-live-price" class="text-sm sm:text-base font-bold text-[#1D1D1F]">
                    Rp {{ number_format($defaultPrice, 0, ',', '.') }}
                </span>
            </div>
            <button type="button" id="sticky-buy-now-btn" class="glass-btn glass-btn-primary glass-btn-md">
                <span>Beli Sekarang</span>
            </button>
        </div>
    </div>
</div>

<!-- Pass dynamic variants data into JavaScript state -->
<script>
    window.RAP_VARIANTS_DATA = {!! $variantsJson !!};
    window.RAP_CURRENT_PRODUCT_ID = {{ $product->id }};
    window.RAP_BUY_NOW_URL = "{{ route('cart.buyNow') }}";
</script>
@endsection
