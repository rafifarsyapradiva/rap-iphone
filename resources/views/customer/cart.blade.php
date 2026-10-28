@extends('layouts.customer')

@section('title', 'Keranjang Belanja — Rap-iPhone Authorized Retail')
@section('meta_description', 'Rincian unit iPhone di keranjang belanja Anda. Periksa varian warna, kapasitas, dan lanjutkan ke checkout aman dengan Midtrans.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Breadcrumbs & Header -->
    <div class="space-y-2">
        <nav class="flex items-center gap-2 text-xs text-[#8E8E93]">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <a href="{{ url('/katalog') }}" class="hover:text-white transition-colors">Katalog</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-white font-medium">Keranjang Belanja</span>
        </nav>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Keranjang Belanja</h1>
                <p class="text-xs text-[#8E8E93] mt-1">Periksa kembali varian warna dan kapasitas sebelum melanjutkan ke pembayaran</p>
            </div>

            @if(!empty($cart))
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item dari keranjang?')">
                    @csrf
                    <button type="submit" class="text-xs text-[#FF453A] hover:underline flex items-center gap-1 font-medium">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Kosongkan Keranjang</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(empty($cart))
        <!-- =========================================================================
             EMPTY CART STATE
             ========================================================================= -->
        <div class="glass-card-elevated rounded-3xl p-12 sm:p-20 text-center space-y-6 border border-white/10 max-w-2xl mx-auto">
            <div class="w-20 h-20 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mx-auto text-[#8E8E93] shadow-inner">
                <i data-lucide="shopping-bag" class="w-10 h-10 text-[#8E8E93]/80"></i>
            </div>
            
            <div class="space-y-2">
                <h2 class="text-2xl font-black text-white">Keranjang Belanja Anda Masih Kosong</h2>
                <p class="text-sm text-[#8E8E93] max-w-md mx-auto leading-relaxed">
                    Belum ada unit iPhone yang ditambahkan. Temukan unit baru garansi resmi iBox atau second berkualitas Grade A+ sekarang.
                </p>
            </div>

            <div class="pt-2 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-primary glass-btn-md">
                    <i data-lucide="compass" class="w-4 h-4"></i>
                    <span>Jelajahi Katalog iPhone</span>
                </a>
                <a href="{{ url('/katalog?condition=second') }}" class="glass-btn glass-btn-secondary glass-btn-md">
                    <i data-lucide="sparkles" class="w-4 h-4 text-[#34C759]"></i>
                    <span>Lihat Unit Second Teruji</span>
                </a>
            </div>
        </div>
    @else
        <!-- =========================================================================
             FILLED CART (2-Column Grid: Items List + Order Summary)
             ========================================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" id="cart-content-wrapper">
            
            <!-- Left: Cart Items List -->
            <div class="lg:col-span-8 space-y-4">
                @foreach($cart as $variantId => $item)
                    @php
                        $itemSubtotal = $item['price'] * $item['quantity'];
                        $isSecond = ($item['condition'] ?? 'new') === 'second';
                    @endphp

                    <div class="glass-card p-5 sm:p-6 rounded-3xl border border-white/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 relative cart-item-row" data-variant-id="{{ $variantId }}">
                        
                        <!-- Product Visual & Basic Info -->
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <!-- Visual Avatar / Image -->
                            <div class="w-20 h-20 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center p-2 shrink-0 relative overflow-hidden">
                                @if(!empty($item['image']) && file_exists(public_path($item['image'])))
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" width="80" height="80" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="w-10 h-14 rounded-xl bg-gradient-to-tr from-[#161B22] to-[#21262D] border border-white/10 flex flex-col items-center justify-between p-1">
                                        <div class="w-4 h-1 bg-black rounded-full"></div>
                                        <i data-lucide="smartphone" class="w-5 h-5 text-[#8E8E93]"></i>
                                        <div class="w-4 h-0.5 bg-white/20 rounded-full"></div>
                                    </div>
                                @endif

                                @if($isSecond)
                                    <span class="absolute bottom-1 right-1 w-2.5 h-2.5 rounded-full bg-[#34C759] border-2 border-[#0B0C10]" title="Unit Second"></span>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <a href="{{ url('/katalog/' . ($item['product_slug'] ?? '')) }}" class="text-base font-bold text-white hover:text-[#5AC8FA] transition-colors line-clamp-1">
                                        {{ $item['product_name'] }}
                                    </a>
                                </div>

                                <!-- Variant Pills -->
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-white/5 border border-white/10 text-[#E5E5EA]">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $item['color_hex'] ?? '#8E8E93' }};"></span>
                                        <span>{{ $item['color_name'] }}</span>
                                    </span>

                                    <span class="px-2 py-0.5 rounded-md bg-white/5 border border-white/10 font-bold text-white">
                                        {{ $item['storage_capacity'] }}
                                    </span>

                                    @if($isSecond)
                                        <span class="px-2 py-0.5 rounded-md bg-[#34C759]/15 border border-[#34C759]/30 text-[#34C759] font-bold text-[11px]">
                                            BH {{ $item['battery_health'] ?? 95 }}%
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-[#0071E3]/15 border border-[#0071E3]/30 text-[#5AC8FA] font-medium text-[11px]">
                                            Garansi Resmi
                                        </span>
                                    @endif
                                </div>

                                <div class="pt-1">
                                    <span class="text-xs text-[#8E8E93]">Harga Satuan: </span>
                                    <span class="text-xs font-semibold text-white">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Stepper & Subtotal & Remove -->
                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-white/10">
                            
                            <!-- Stepper -->
                            <div class="flex items-center gap-2 p-1 rounded-xl bg-white/5 border border-white/10">
                                <button type="button" 
                                        class="cart-qty-btn cart-qty-dec w-7 h-7 rounded-lg bg-white/5 hover:bg-white/15 text-white flex items-center justify-center transition-colors disabled:opacity-40" 
                                        data-variant-id="{{ $variantId }}"
                                        data-current-qty="{{ $item['quantity'] }}">
                                    -
                                </button>
                                <span class="cart-qty-display w-8 text-center text-xs font-bold text-white">
                                    {{ $item['quantity'] }}
                                </span>
                                <button type="button" 
                                        class="cart-qty-btn cart-qty-inc w-7 h-7 rounded-lg bg-white/5 hover:bg-white/15 text-white flex items-center justify-center transition-colors disabled:opacity-40" 
                                        data-variant-id="{{ $variantId }}"
                                        data-current-qty="{{ $item['quantity'] }}"
                                        data-max-stock="{{ $item['stock'] }}">
                                    +
                                </button>
                            </div>

                            <!-- Item Subtotal -->
                            <div class="text-right">
                                <span class="text-[10px] text-[#8E8E93] block uppercase font-medium">Subtotal</span>
                                <span class="text-base font-black text-white cart-item-subtotal">
                                    Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Remove Button -->
                            <form action="{{ route('cart.remove', $variantId) }}" method="POST" class="cart-remove-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-[#8E8E93] hover:text-[#FF453A] hover:bg-[#FF453A]/10 transition-colors" title="Hapus dari Keranjang">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>

                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Right: Order Summary Card -->
            <div class="lg:col-span-4 lg:sticky lg:top-28">
                <div class="glass-card-elevated p-6 sm:p-7 rounded-3xl border border-white/15 space-y-6">
                    
                    <h2 class="text-lg font-black text-white tracking-tight pb-3 border-b border-white/10">
                        Ringkasan Belanja
                    </h2>

                    <!-- Calculation Rows -->
                    <div class="space-y-3 text-xs sm:text-sm">
                        <div class="flex items-center justify-between text-[#8E8E93]">
                            <span>Total Kuantitas</span>
                            <span id="cart-summary-total-qty" class="text-white font-medium">{{ $totalQuantity }} Unit</span>
                        </div>

                        <div class="flex items-center justify-between text-[#8E8E93]">
                            <span>Subtotal Produk</span>
                            <span id="cart-summary-subtotal" class="text-white font-bold">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-[#8E8E93]">
                            <span class="flex items-center gap-1.5">
                                <span>Ongkos Kirim & Asuransi</span>
                                <i data-lucide="info" class="w-3.5 h-3.5 text-[#34C759]" title="Promo Gratis Ongkir Seluruh Indonesia"></i>
                            </span>
                            <span class="text-[#34C759] font-bold">GRATIS</span>
                        </div>

                        <div class="flex items-center justify-between text-[#8E8E93]">
                            <span>Estimasi Pajak PPN (11%)</span>
                            <span class="text-white font-medium">Termasuk</span>
                        </div>
                    </div>

                    <!-- Promo Code Voucher Input -->
                    <div class="pt-2">
                        <label class="text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider block mb-1.5">Kupon Promo / Diskon</label>
                        <div class="flex items-center gap-2">
                            <input type="text" placeholder="Masukkan kode kupon" class="glass-input text-xs uppercase font-mono tracking-wider !py-2">
                            <button type="button" class="glass-btn glass-btn-secondary glass-btn-sm !py-2 shrink-0">
                                Pasang
                            </button>
                        </div>
                    </div>

                    <!-- Grand Total -->
                    <div class="pt-4 border-t border-white/10 space-y-1">
                        <span class="text-xs text-[#8E8E93] uppercase font-bold tracking-wider block">Total Tagihan</span>
                        <div class="flex items-baseline justify-between">
                            <span id="cart-summary-grand-total" class="text-2xl sm:text-3xl font-black text-white tracking-tight bg-gradient-to-r from-white via-[#E5E5EA] to-[#0071E3] bg-clip-text text-transparent">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Action CTAs -->
                    <div class="space-y-2.5 pt-2">
                        <a href="{{ url('/checkout') }}" class="glass-btn glass-btn-primary glass-btn-lg w-full shadow-xl flex items-center justify-center gap-2 group">
                            <span>Lanjut ke Pembayaran</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                        </a>

                        <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-ghost glass-btn-sm w-full text-center">
                            <span>Lanjut Belanja iPhone Lainnya</span>
                        </a>
                    </div>

                    <!-- Secure Guarantee Badges -->
                    <div class="pt-2 flex items-center justify-center gap-4 text-[11px] text-[#8E8E93]">
                        <span class="flex items-center gap-1">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#0071E3]"></i>
                            Midtrans 3DS Secure
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="award" class="w-3.5 h-3.5 text-[#34C759]"></i>
                            Garansi Resmi
                        </span>
                    </div>

                </div>
            </div>

        </div>
    @endif

</div>
@endsection

