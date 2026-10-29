@extends('layouts.customer')

@php
    $defaultVariant = $product->variants->first();
    $isSecond = $product->condition === 'second';
    $defaultPrice = $defaultVariant->selling_price ?? $product->base_price;
    
    $warrantyBadgeLabel = match($product->warranty_type ?? 'resmi_ibox') {
        'resmi_ibox' => 'Garansi Resmi Apple Indonesia (iBox / TAM)',
        'resmi_digimap' => 'Garansi Resmi Digimap Indonesia',
        'inter_all_operator' => 'Inter All Operator (Sinyal Permanen)',
        default => 'Garansi Toko Resmi Rap-iPhone',
    };

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
@section('meta_description', 'Beli ' . $product->name . ' dengan garansi resmi terpercaya, simulasi cicilan 0%, kalkulator tukar tambah instan, dan layanan Click & Collect.')

@section('content')
<div class="cust-page py-6 pb-24 space-y-16">

    <!-- 1. Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-[var(--cust-ink-muted)]">
        <a href="{{ url('/') }}" class="hover:text-[var(--cust-ink)] transition-colors">Beranda</a>
        <span class="text-[var(--cust-ink-faint)]">/</span>
        <a href="{{ url('/katalog') }}" class="hover:text-[var(--cust-ink)] transition-colors">Katalog iPhone</a>
        <span class="text-[var(--cust-ink-faint)]">/</span>
        <a href="{{ url('/katalog?series=' . urlencode($product->series)) }}" class="hover:text-[var(--cust-ink)] transition-colors">{{ $product->series }}</a>
        <span class="text-[var(--cust-ink-faint)]">/</span>
        <span class="text-[var(--cust-ink)] font-medium truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    <!-- 2. Split Studio PDP Layout (Macrostructure 15: Split Studio) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start" id="pdp-interactive-container">
        
        <!-- Left: Sticky Studio Gallery (7 Cols) -->
        <div class="lg:col-span-7 space-y-4 lg:sticky lg:top-24">
            
            <!-- Main Studio Showcase Canvas -->
            <div class="cust-card p-6 sm:p-10 relative flex items-center justify-center overflow-hidden min-h-[380px] sm:min-h-[480px] bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)]">
                
                <!-- Ambient Subtle Aura -->
                <div id="pdp-glow-aura" class="absolute w-64 h-64 rounded-full blur-3xl opacity-10 pointer-events-none transition-all duration-500" style="background-color: {{ $colors->first()->color_hex ?? '#0071E3' }};"></div>

                <!-- Condition Badge Top-Left -->
                <div class="absolute top-4 left-4 z-20">
                    @if($isSecond)
                        <x-customer.spec-badge variant="healthy" icon="badge-check">
                            Second · {{ $product->grade ?? 'Grade A+ Teruji' }}
                        </x-customer.spec-badge>
                    @else
                        <x-customer.spec-badge variant="official" icon="shield-check">
                            Baru · Segel Resmi
                        </x-customer.spec-badge>
                    @endif
                </div>

                <!-- Main Display Image / Device Viewport -->
                <div id="pdp-main-image-wrapper" class="relative z-10 w-full flex items-center justify-center">
                    @php
                        $primaryImg = $product->primaryImage->image_path ?? ($product->images->first()->image_path ?? null);
                    @endphp

                    @if($primaryImg && file_exists(public_path($primaryImg)))
                        <img id="pdp-active-image" 
                             src="{{ asset($primaryImg) }}" 
                             alt="{{ $product->name }}" 
                             width="440" 
                             height="440" 
                             fetchpriority="high" 
                             decoding="async" 
                             class="max-h-[360px] w-auto object-contain transition-transform duration-300 hover:scale-105">
                    @else
                        <div id="pdp-vector-device" class="w-52 h-76 sm:w-60 sm:h-88 rounded-[36px] bg-[var(--cust-ink)] border-2 border-[var(--cust-accent)] shadow-lg flex flex-col items-center justify-between p-3 transition-transform duration-300 hover:scale-105">
                            <div class="w-16 h-3 bg-black rounded-full flex items-center justify-end px-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-[var(--cust-signal)]"></div>
                            </div>
                            <div class="w-full flex-grow my-2 rounded-[26px] bg-[var(--cust-paper)] flex flex-col items-center justify-center text-center p-3">
                                <i data-lucide="smartphone" class="w-10 h-10 text-[var(--cust-ink)] mb-2"></i>
                                <span class="text-xs font-semibold text-[var(--cust-ink)]">{{ $product->name }}</span>
                                <span id="pdp-vector-color-name" class="text-[11px] text-[var(--cust-ink-muted)] mt-0.5">{{ $colors->first()->color_name ?? 'Titanium' }}</span>
                                <span class="mt-2 text-[10px] text-[var(--cust-tone-healthy-ink)] font-medium">
                                    {{ $isSecond ? 'Certified Pre-Owned' : 'Garansi Resmi Apple' }}
                                </span>
                            </div>
                            <div class="w-12 h-1 bg-white/40 rounded-full"></div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Thumbnails Row -->
            @php
                $existingImages = $product->images->filter(fn ($img) => !empty($img->image_path) && file_exists(public_path($img->image_path)))->unique('image_path')->values();
            @endphp
            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                @if($existingImages->isNotEmpty())
                    @foreach($existingImages as $idx => $img)
                        <button type="button"
                                class="pdp-thumb-btn {{ $idx === 0 ? 'active' : '' }} p-1 rounded border border-[var(--cust-rule-border)] shrink-0 w-16 h-16 flex items-center justify-center bg-white hover:border-[var(--cust-ink)] transition-colors"
                                data-image-src="{{ asset($img->image_path) }}"
                                data-angle="angle-{{ $img->order_index }}"
                                title="Sudut #{{ $img->order_index }}">
                            <img src="{{ asset($img->image_path) }}" alt="{{ $product->name }}" width="56" height="56" loading="lazy" class="max-h-full max-w-full object-contain">
                        </button>
                    @endforeach
                @else
                    <button type="button" class="pdp-thumb-btn active p-1.5 rounded border border-[var(--cust-signal)] shrink-0 w-16 h-16 flex flex-col items-center justify-center gap-0.5 bg-white text-[10px]" data-angle="front" title="Tampak Depan">
                        <i data-lucide="smartphone" class="w-4 h-4 text-[var(--cust-ink)]"></i>
                        <span class="text-[9px] text-[var(--cust-ink-muted)]">Depan</span>
                    </button>
                    <button type="button" class="pdp-thumb-btn p-1.5 rounded border border-[var(--cust-rule-border)] shrink-0 w-16 h-16 flex flex-col items-center justify-center gap-0.5 bg-white text-[10px] hover:border-[var(--cust-ink)]" data-angle="back" title="Modul Kamera">
                        <i data-lucide="camera" class="w-4 h-4 text-[var(--cust-ink-muted)]"></i>
                        <span class="text-[9px] text-[var(--cust-ink-muted)]">Kamera</span>
                    </button>
                    <button type="button" class="pdp-thumb-btn p-1.5 rounded border border-[var(--cust-rule-border)] shrink-0 w-16 h-16 flex flex-col items-center justify-center gap-0.5 bg-white text-[10px] hover:border-[var(--cust-ink)]" data-angle="side" title="Rangka Bodi">
                        <i data-lucide="layers" class="w-4 h-4 text-[var(--cust-ink-muted)]"></i>
                        <span class="text-[9px] text-[var(--cust-ink-muted)]">Rangka</span>
                    </button>
                @endif
            </div>

            <!-- Service Pillars Below Gallery -->
            <div class="grid grid-cols-3 gap-2.5 pt-2">
                <div class="cust-card p-3 text-center space-y-1">
                    <i data-lucide="truck" class="w-4 h-4 text-[var(--cust-signal)] mx-auto"></i>
                    <p class="text-[11px] font-semibold text-[var(--cust-ink)]">Gratis Ongkir</p>
                    <p class="text-[10px] text-[var(--cust-ink-muted)]">Asuransi Penuh</p>
                </div>
                <div class="cust-card p-3 text-center space-y-1">
                    <i data-lucide="shield-check" class="w-4 h-4 text-[var(--cust-tone-healthy-ink)] mx-auto"></i>
                    <p class="text-[11px] font-semibold text-[var(--cust-ink)]">Garansi Teruji</p>
                    <p class="text-[10px] text-[var(--cust-ink-muted)]">QC 32 Titik</p>
                </div>
                <div class="cust-card p-3 text-center space-y-1">
                    <i data-lucide="store" class="w-4 h-4 text-[var(--cust-ink)] mx-auto"></i>
                    <p class="text-[11px] font-semibold text-[var(--cust-ink)]">Click &amp; Collect</p>
                    <p class="text-[10px] text-[var(--cust-ink-muted)]">Gerai Toko</p>
                </div>
            </div>

        </div>

        <!-- Right: Step-by-Step Configurator (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Title & Dynamic Price -->
            <div class="space-y-2.5 pb-4 border-b border-[var(--cust-rule-border)]">
                <p class="text-xs font-medium text-[var(--cust-ink-muted)]">
                    {{ $product->series }} &bull; {{ $isSecond ? 'Certified Pre-Owned' : 'Authorized Retail Unit' }}
                </p>

                <h1 class="font-serif text-2xl sm:text-3xl font-light text-[var(--cust-ink)] tracking-tight">
                    Beli {{ $product->name }}
                </h1>

                <!-- Price Row -->
                <div class="pt-1 space-y-1">
                    <div class="flex items-baseline gap-2.5 flex-wrap">
                        <span id="pdp-live-price" class="text-2xl sm:text-3xl font-bold text-[var(--cust-ink)] cust-tabular">
                            Rp {{ number_format($defaultPrice, 0, ',', '.') }}
                        </span>
                        <span id="pdp-net-price-badge" class="hidden cust-badge cust-badge-healthy">
                            Hemat Trade-In
                        </span>
                    </div>

                    <p id="pdp-installment-calc" class="text-xs text-[var(--cust-ink-muted)]">
                        Atau cicilan 0% mulai <strong class="text-[var(--cust-ink)] cust-tabular">Rp {{ number_format(round($defaultPrice / 24), 0, ',', '.') }}/bln</strong> (24 bulan).
                    </p>

                    <button type="button" data-modal-target="installment-modal" class="inline-flex items-center gap-1 text-xs text-[var(--cust-signal)] hover:underline pt-0.5">
                        <i data-lucide="calculator" class="w-3.5 h-3.5"></i>
                        <span>Simulasi cicilan &amp; metode bayar Midtrans</span>
                    </button>
                </div>
            </div>

            <!-- Form Configurator -->
            <form id="pdp-buy-form" action="{{ route('cart.add') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="product_variant_id" id="selected-variant-id" value="{{ $defaultVariant->id ?? '' }}">

                <!-- STEP 1: Warna / Finish -->
                @if($colors->isNotEmpty())
                    <div class="space-y-2.5">
                        <label class="cust-label">
                            1. Varian Warna &mdash; <span id="pdp-active-color-label" class="text-[var(--cust-ink)] font-semibold">{{ $colors->first()->color_name }}</span>
                        </label>
                        <div class="flex items-center gap-3 py-1" id="pdp-color-picker-group">
                            @foreach($colors as $index => $color)
                                <button type="button" 
                                        class="pdp-color-dot cust-swatch {{ $index === 0 ? 'active' : '' }}" 
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

                <!-- STEP 2: Kapasitas Penyimpanan -->
                @if($storages->isNotEmpty())
                    <div class="space-y-2.5 pt-3 border-t border-[var(--cust-rule-border)]">
                        <label class="cust-label">
                            2. Kapasitas Penyimpanan
                        </label>
                        <div class="grid grid-cols-2 gap-2" id="pdp-storage-group">
                            @foreach($storages as $index => $storage)
                                @php
                                    $storageVariant = $product->variants->firstWhere('storage_capacity', $storage);
                                    $storagePrice = $storageVariant->selling_price ?? $product->base_price;
                                @endphp
                                <button type="button" 
                                        class="pdp-storage-btn cust-pill {{ $index === 0 ? 'active' : '' }} flex items-center justify-between text-left p-3"
                                        data-storage="{{ $storage }}">
                                    <div>
                                        <span class="text-sm font-bold block">{{ $storage }}</span>
                                        <span class="text-[10px] text-[var(--cust-ink-muted)] block">Siap Kirim</span>
                                    </div>
                                    <span class="text-xs font-semibold cust-tabular pdp-storage-price-hint">
                                        Rp {{ number_format($storagePrice, 0, ',', '.') }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- STEP 3: Transparansi Unit Second Like New -->
                @if($isSecond)
                    <div class="cust-card p-4 space-y-3 bg-[var(--cust-paper-subtle)]">
                        <div class="flex items-center justify-between pb-2 border-b border-[var(--cust-rule-border)]">
                            <span class="text-xs font-semibold text-[var(--cust-tone-healthy-ink)] flex items-center gap-1.5">
                                <i data-lucide="badge-check" class="w-3.5 h-3.5"></i>
                                <span>Transparansi Unit Second Like New</span>
                            </span>
                            <x-customer.spec-badge variant="healthy">
                                Lulus QC 32 Titik
                            </x-customer.spec-badge>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 text-xs">
                            <div class="p-2.5 rounded bg-white border border-[var(--cust-rule-border)]">
                                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Battery Health (BH)</span>
                                <span id="pdp-bh-percentage" class="text-sm font-bold text-[var(--cust-tone-healthy-ink)] block cust-tabular">
                                    {{ $defaultVariant->battery_health ?? 96 }}% (Prima)
                                </span>
                            </div>
                            <div class="p-2.5 rounded bg-white border border-[var(--cust-rule-border)]">
                                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Status IMEI & Jaringan</span>
                                <span class="text-xs font-semibold text-[var(--cust-ink)] block">All Operator Permanen</span>
                            </div>
                            <div class="p-2.5 rounded bg-white border border-[var(--cust-rule-border)]">
                                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Kondisi Fisik</span>
                                <span class="text-xs font-semibold text-[var(--cust-ink)] block">{{ $product->grade ?? 'Grade A+ Mulus' }}</span>
                            </div>
                            <div class="p-2.5 rounded bg-white border border-[var(--cust-rule-border)]">
                                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Kelengkapan</span>
                                <span class="text-xs font-semibold text-[var(--cust-ink)] block">Fullset Box &amp; Kabel</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- STEP 4: Inline Trade-In Price Slasher -->
                <div class="cust-card p-4 space-y-2.5 bg-[var(--cust-paper-subtle)]">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[var(--cust-ink)] flex items-center gap-1.5">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
                            <span>Simulasi Tukar Tambah (Trade-In)</span>
                        </span>
                        <a href="{{ url('/trade-in') }}" class="text-[11px] text-[var(--cust-signal)] hover:underline">
                            Wizard &rarr;
                        </a>
                    </div>

                    <p class="text-xs text-[var(--cust-ink-muted)] leading-relaxed">
                        Pilih model iPhone lama untuk melihat potongan harga langsung:
                    </p>

                    <select id="pdp-tradein-select" class="cust-select text-xs">
                        @foreach($tradeInQuickModels as $tiModel)
                            <option value="{{ $tiModel['credit'] }}">{{ $tiModel['label'] }}</option>
                        @endforeach
                    </select>

                    <div id="pdp-tradein-result-box" class="hidden p-3 rounded bg-[var(--cust-tone-healthy-bg)] border border-[var(--cust-tone-healthy-ink)]/20 text-xs space-y-1">
                        <div class="flex items-center justify-between text-[var(--cust-tone-healthy-ink)]">
                            <span>Potongan Trade-In:</span>
                            <span id="pdp-tradein-credit" class="font-bold cust-tabular">- Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-[var(--cust-tone-healthy-ink)]/15 text-[var(--cust-ink)]">
                            <span class="font-semibold">Harga Akhir Bersih:</span>
                            <span id="pdp-net-price" class="text-sm font-bold text-[var(--cust-tone-healthy-ink)] cust-tabular">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 5: Ketersediaan Stok & Kuantitas -->
                <div class="cust-card p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-[var(--cust-ink-muted)] block">Ketersediaan Unit</span>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[var(--cust-tone-healthy-ink)]"></span>
                                <span id="pdp-stock-status" class="text-xs font-semibold text-[var(--cust-tone-healthy-ink)] cust-tabular">
                                    Tersedia ({{ $defaultVariant->stock ?? 10 }} Unit Siap Kirim)
                                </span>
                            </div>
                        </div>

                        <!-- Stepper -->
                        <div class="flex items-center border border-[var(--cust-rule-border)] rounded">
                            <button type="button" id="pdp-qty-dec" class="px-2.5 py-1 text-xs hover:bg-[var(--cust-paper-subtle)] text-[var(--cust-ink)] font-bold">
                                -
                            </button>
                            <input type="number" name="quantity" id="pdp-quantity-input" value="1" min="1" max="{{ $defaultVariant->stock ?? 10 }}" class="w-10 text-center text-xs font-bold bg-transparent text-[var(--cust-ink)] border-0 focus:ring-0 cust-tabular">
                            <button type="button" id="pdp-qty-inc" class="px-2.5 py-1 text-xs hover:bg-[var(--cust-paper-subtle)] text-[var(--cust-ink)] font-bold">
                                +
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CTA Actions -->
                <div class="space-y-2 pt-1" id="pdp-main-cta-group">
                    <button type="button" id="pdp-buy-now-btn" class="cust-btn cust-btn-primary w-full py-3 text-sm">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        <span>Beli Sekarang</span>
                    </button>

                    <button type="submit" id="pdp-add-cart-btn" class="cust-btn cust-btn-outline w-full py-2.5 text-xs">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        <span>+ Tambah ke Keranjang</span>
                    </button>
                </div>

            </form>

            <!-- Guarantee Note -->
            <div class="cust-card p-3.5 space-y-1 text-xs text-[var(--cust-ink-muted)]">
                <div class="flex items-center gap-1.5 text-[var(--cust-ink)] font-medium">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
                    <span>{{ $warrantyBadgeLabel }}</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Setiap transaksi menyertakan Nota PDF Resmi Ber-QR Code serta notifikasi otomatis WhatsApp Fonnte &amp; Email SMTP.
                </p>
            </div>

        </div>

    </div>

    <!-- 3. Technical Specifications Table -->
    <section class="space-y-4 pt-10 border-t border-[var(--cust-rule-border)]">
        <div>
            <span class="text-[10px] font-semibold text-[var(--cust-ink-muted)] uppercase">Rincian Perangkat Keras</span>
            <h2 class="font-serif text-2xl font-light text-[var(--cust-ink)] tracking-tight">
                Spesifikasi Teknis & Rincian
            </h2>
        </div>

        <div class="cust-card overflow-hidden">
            <div class="divide-y divide-[var(--cust-rule-border)] text-xs sm:text-sm">
                <div class="grid grid-cols-1 sm:grid-cols-3 p-4">
                    <span class="text-[var(--cust-ink-muted)] font-medium">Layar Tampilan</span>
                    <span class="sm:col-span-2 text-[var(--cust-ink)]">
                        Super Retina XDR OLED, ProMotion 120Hz adaptif, Always-On Display, Dynamic Island, kecerahan hingga 2000 nits.
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 p-4">
                    <span class="text-[var(--cust-ink-muted)] font-medium">Chipset &amp; Kinerja</span>
                    <span class="sm:col-span-2 text-[var(--cust-ink)]">
                        Chip Apple Silicon A-Series dengan Neural Engine untuk pemrosesan on-device Apple Intelligence.
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 p-4">
                    <span class="text-[var(--cust-ink-muted)] font-medium">Sistem Kamera</span>
                    <span class="sm:col-span-2 text-[var(--cust-ink)]">
                        Kamera Fusion 48 MP, Ultra Wide, Telefoto optik, perekaman 4K Dolby Vision.
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 p-4">
                    <span class="text-[var(--cust-ink-muted)] font-medium">Jaringan &amp; IMEI</span>
                    <span class="sm:col-span-2 text-[var(--cust-ink)]">
                        5G All Operator Indonesia &bull; Terdaftar Resmi Kemenperin RI &bull; Garansi Sinyal Seumur Hidup.
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 p-4">
                    <span class="text-[var(--cust-ink-muted)] font-medium">Isi Kemasan</span>
                    <span class="sm:col-span-2 text-[var(--cust-ink)]">
                        Unit {{ $product->name }}, Kabel Pengisi Daya USB-C, Dokumentasi Resmi.
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Related Products -->
    @if($relatedProducts->isNotEmpty())
        <section class="space-y-4 pt-6">
            <x-customer.section-header 
                title="Rekomendasi Seri Terkait"
                subtitle="Pilihan lain dalam keluarga {{ $product->series }}"
                actionHref="{{ url('/katalog?series=' . urlencode($product->series)) }}"
                actionLabel="Lihat Semua" />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($relatedProducts as $rel)
                    <x-customer.product-card :product="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</div>

<!-- 5. Modal Simulasi Cicilan & Midtrans -->
<div id="installment-modal" class="glass-modal-backdrop hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="installment-modal-title">
    <div class="cust-card p-6 max-w-md w-full space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-rule-border)]">
            <div>
                <h3 id="installment-modal-title" class="font-serif text-base font-light text-[var(--cust-ink)]">Simulasi Cicilan 0% &amp; Midtrans</h3>
                <p class="text-xs text-[var(--cust-ink-muted)]">{{ $product->name }}</p>
            </div>
            <button type="button" data-modal-close class="p-1 rounded text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-3 rounded bg-[var(--cust-paper-subtle)] flex items-center justify-between text-xs">
            <span class="text-[var(--cust-ink-muted)]">Harga Terpilih:</span>
            <span id="sim-modal-base-price" class="text-sm font-bold text-[var(--cust-ink)] cust-tabular">
                Rp {{ number_format($defaultPrice, 0, ',', '.') }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="p-2.5 rounded border border-[var(--cust-rule-border)]">
                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Tenor 3 Bulan (0%)</span>
                <span id="sim-3m" class="text-xs font-bold text-[var(--cust-ink)] cust-tabular mt-0.5 block">Rp {{ number_format(round($defaultPrice / 3), 0, ',', '.') }}/bln</span>
            </div>
            <div class="p-2.5 rounded border border-[var(--cust-rule-border)]">
                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Tenor 6 Bulan (0%)</span>
                <span id="sim-6m" class="text-xs font-bold text-[var(--cust-ink)] cust-tabular mt-0.5 block">Rp {{ number_format(round($defaultPrice / 6), 0, ',', '.') }}/bln</span>
            </div>
            <div class="p-2.5 rounded border border-[var(--cust-rule-border)]">
                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Tenor 12 Bulan (0%)</span>
                <span id="sim-12m" class="text-xs font-bold text-[var(--cust-signal)] cust-tabular mt-0.5 block">Rp {{ number_format(round($defaultPrice / 12), 0, ',', '.') }}/bln</span>
            </div>
            <div class="p-2.5 rounded border border-[var(--cust-signal)]/30 bg-[var(--cust-tone-official-bg)]">
                <span class="text-[10px] text-[var(--cust-signal)] block font-medium">Tenor 24 Bulan (0%)</span>
                <span id="sim-24m" class="text-xs font-bold text-[var(--cust-signal)] cust-tabular mt-0.5 block">Rp {{ number_format(round($defaultPrice / 24), 0, ',', '.') }}/bln</span>
            </div>
        </div>

        <p class="text-[11px] text-[var(--cust-ink-muted)] leading-relaxed">
            Metode pembayaran Midtrans Snap: QRIS, BCA/Mandiri/BNI/BRI VA, GoPay, ShopeePay, dan Kartu Kredit Bank Mitra.
        </p>

        <div class="pt-2 flex justify-end">
            <button type="button" data-modal-close class="cust-btn cust-btn-secondary text-xs py-1.5 px-3">
                Mengerti
            </button>
        </div>
    </div>
</div>

<!-- 6. Sticky Bottom Purchase Bar -->
<div id="pdp-sticky-buy-bar" class="cust-buybar transform translate-y-full transition-transform duration-200">
    <div class="cust-page flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] flex items-center justify-center shrink-0 hidden sm:flex">
                <i data-lucide="smartphone" class="w-4 h-4 text-[var(--cust-ink)]"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-[var(--cust-ink)] truncate">{{ $product->name }}</p>
                <p id="sticky-variant-label" class="text-[11px] text-[var(--cust-ink-muted)] truncate">
                    {{ ($colors->first()->color_name ?? 'Finish') }} &bull; {{ ($storages->first() ?? '128GB') }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="text-right">
                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Harga</span>
                <span id="sticky-live-price" class="text-xs sm:text-sm font-bold text-[var(--cust-ink)] cust-tabular">
                    Rp {{ number_format($defaultPrice, 0, ',', '.') }}
                </span>
            </div>
            <button type="button" id="sticky-buy-now-btn" class="cust-btn cust-btn-primary text-xs py-1.5 px-3.5">
                <span>Beli</span>
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
