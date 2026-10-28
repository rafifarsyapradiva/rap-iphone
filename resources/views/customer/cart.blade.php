@extends('layouts.customer')

@section('title', 'Keranjang Belanja — Rap-iPhone Authorized Style Partner')
@section('meta_description', 'Rincian unit iPhone di keranjang belanja Anda. Periksa varian warna, kapasitas, dan lanjutkan ke checkout aman dengan Midtrans.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Breadcrumbs & Header -->
    <div class="space-y-3 pb-4 border-b border-[#E5E5EA]">
        <nav class="flex items-center gap-2 text-xs text-[#6E6E73]">
            <a href="{{ url('/') }}" class="hover:text-[#1D1D1F] transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#86868B]"></i>
            <a href="{{ url('/katalog') }}" class="hover:text-[#1D1D1F] transition-colors">Katalog</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#86868B]"></i>
            <span class="text-[#1D1D1F] font-semibold">Keranjang Belanja</span>
        </nav>
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-[#1D1D1F] tracking-tight">Tas Belanja Anda</h1>
                <p class="text-xs sm:text-sm text-[#6E6E73] mt-1">Gratis pengiriman berasuransi ke seluruh Indonesia atau ambil langsung di gerai (Click &amp; Collect).</p>
            </div>

            @if(!empty($cart))
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item dari keranjang?')">
                    @csrf
                    <button type="submit" class="text-xs text-[#D70015] hover:underline flex items-center gap-1 font-semibold">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Kosongkan Keranjang</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(empty($cart))
        <!-- =========================================================================
             EMPTY CART STATE (Daylight Surface)
             ========================================================================= -->
        <div class="rounded-[28px] bg-[#F5F5F7] border border-[#E5E5EA] p-12 sm:p-20 text-center space-y-6 max-w-2xl mx-auto">
            <div class="w-20 h-20 rounded-full bg-white border border-[#D2D2D7] flex items-center justify-center mx-auto text-[#6E6E73] shadow-sm">
                <i data-lucide="shopping-bag" class="w-9 h-9"></i>
            </div>
            
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-[#1D1D1F]">Keranjang Belanja Anda Masih Kosong</h2>
                <p class="text-sm text-[#6E6E73] max-w-md mx-auto leading-relaxed">
                    Belum ada unit iPhone yang ditambahkan. Temukan unit baru garansi resmi iBox/Digimap atau second berkualitas Grade A+ sekarang.
                </p>
            </div>

            <div class="pt-2 flex flex-wrap items-center justify-center gap-3.5">
                <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-primary glass-btn-md">
                    <i data-lucide="compass" class="w-4 h-4"></i>
                    <span>Jelajahi Katalog iPhone</span>
                </a>
                <a href="{{ url('/katalog?condition=second') }}" class="glass-btn glass-btn-secondary glass-btn-md">
                    <i data-lucide="badge-check" class="w-4 h-4 text-[#008040]"></i>
                    <span>Lihat Unit Second Teruji</span>
                </a>
            </div>
        </div>
    @else
        <!-- =========================================================================
             FILLED CART (2-Column Grid: Items List + Order Summary #F5F5F7)
             ========================================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" id="cart-content-wrapper">
            
            <!-- Left: Cart Items List -->
            <div class="lg:col-span-8 space-y-4">
                @foreach($cart as $variantId => $item)
                    @php
                        $itemSubtotal = $item['price'] * $item['quantity'];
                        $isSecond = ($item['condition'] ?? 'new') === 'second';
                    @endphp

                    <div class="p-5 sm:p-6 rounded-[24px] bg-[#F5F5F7] border border-[#E5E5EA] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 relative cart-item-row" data-variant-id="{{ $variantId }}">
                        
                        <!-- Product Visual & Basic Info -->
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <!-- Studio Avatar -->
                            <div class="w-20 h-20 rounded-2xl bg-white border border-[#E5E5EA] flex items-center justify-center p-2 shrink-0 relative overflow-hidden">
                                @if(!empty($item['image']) && file_exists(public_path($item['image'])))
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" width="80" height="80" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain">
                                @else
                                    <div class="w-10 h-14 rounded-xl bg-[#1D1D1F] border border-[#9A958E] flex flex-col items-center justify-between p-1">
                                        <div class="w-4 h-1 bg-black rounded-full"></div>
                                        <i data-lucide="smartphone" class="w-5 h-5 text-white"></i>
                                        <div class="w-4 h-0.5 bg-white/40 rounded-full"></div>
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="space-y-1.5">
                                <a href="{{ url('/katalog/' . ($item['product_slug'] ?? '')) }}" class="text-base font-bold text-[#1D1D1F] hover:text-[#0066CC] transition-colors line-clamp-1">
                                    {{ $item['product_name'] }}
                                </a>

                                <!-- Variant Pills -->
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white border border-[#D2D2D7] text-[#1D1D1F] font-medium">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0 border border-black/15" style="background-color: {{ $item['color_hex'] ?? '#8E8E93' }};"></span>
                                        <span>{{ $item['color_name'] }}</span>
                                    </span>

                                    <span class="px-2.5 py-0.5 rounded-full bg-white border border-[#D2D2D7] font-bold text-[#1D1D1F]">
                                        {{ $item['storage_capacity'] }}
                                    </span>

                                    @if($isSecond)
                                        <span class="px-2.5 py-0.5 rounded-full bg-[#E8F8EE] text-[#008040] font-semibold text-[11px]">
                                            Second &bull; BH {{ $item['battery_health'] ?? 95 }}%
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-[#E8F2FF] text-[#0066CC] font-semibold text-[11px]">
                                            Baru &bull; Garansi Resmi
                                        </span>
                                    @endif
                                </div>

                                <div class="pt-0.5">
                                    <span class="text-xs text-[#6E6E73]">Harga Satuan: </span>
                                    <span class="text-xs font-semibold text-[#1D1D1F]">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Stepper & Subtotal & Remove -->
                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-[#E5E5EA]">
                            
                            <!-- Stepper -->
                            <div class="flex items-center gap-1.5 p-1 rounded-full bg-white border border-[#D2D2D7]">
                                <button type="button" 
                                        class="cart-qty-btn cart-qty-dec w-7 h-7 rounded-full hover:bg-[#F5F5F7] text-[#1D1D1F] font-bold flex items-center justify-center transition-colors disabled:opacity-40" 
                                        data-variant-id="{{ $variantId }}"
                                        data-current-qty="{{ $item['quantity'] }}">
                                    -
                                </button>
                                <span class="cart-qty-display w-8 text-center text-xs font-bold text-[#1D1D1F]">
                                    {{ $item['quantity'] }}
                                </span>
                                <button type="button" 
                                        class="cart-qty-btn cart-qty-inc w-7 h-7 rounded-full hover:bg-[#F5F5F7] text-[#1D1D1F] font-bold flex items-center justify-center transition-colors disabled:opacity-40" 
                                        data-variant-id="{{ $variantId }}"
                                        data-current-qty="{{ $item['quantity'] }}"
                                        data-max-stock="{{ $item['stock'] }}">
                                    +
                                </button>
                            </div>

                            <!-- Item Subtotal -->
                            <div class="text-right">
                                <span class="text-[10px] text-[#6E6E73] block uppercase font-semibold">Subtotal</span>
                                <span class="text-base font-bold text-[#1D1D1F] cart-item-subtotal">
                                    Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Remove Button -->
                            <form action="{{ route('cart.remove', $variantId) }}" method="POST" class="cart-remove-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-full text-[#6E6E73] hover:text-[#D70015] hover:bg-[#FFEBEA] transition-colors" title="Hapus dari Keranjang">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>

                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Right: Order Summary Card (#F5F5F7) -->
            <div class="lg:col-span-4 lg:sticky lg:top-24">
                <div class="p-6 sm:p-7 rounded-[24px] bg-[#F5F5F7] border border-[#E5E5EA] space-y-6">
                    
                    <h2 class="text-lg font-bold text-[#1D1D1F] tracking-tight pb-3 border-b border-[#D2D2D7]">
                        Ringkasan Belanja
                    </h2>

                    <!-- Calculation Rows -->
                    <div class="space-y-3 text-xs sm:text-sm">
                        <div class="flex items-center justify-between text-[#6E6E73]">
                            <span>Total Kuantitas</span>
                            <span id="cart-summary-total-qty" class="text-[#1D1D1F] font-semibold">{{ $totalQuantity }} Unit</span>
                        </div>

                        <div class="flex items-center justify-between text-[#6E6E73]">
                            <span>Subtotal Produk</span>
                            <span id="cart-summary-subtotal" class="text-[#1D1D1F] font-bold">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-[#6E6E73]">
                            <span class="flex items-center gap-1.5">
                                <span>Pengiriman &amp; Asuransi</span>
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#008040]"></i>
                            </span>
                            <span class="text-[#008040] font-bold">GRATIS</span>
                        </div>

                        <div class="flex items-center justify-between text-[#6E6E73]">
                            <span>Opsi Click &amp; Collect Toko</span>
                            <span class="text-[#0066CC] font-semibold">Tersedia</span>
                        </div>
                    </div>

                    <!-- Promo Code / Trade-In Voucher Input -->
                    <div class="pt-1">
                        <label class="text-[11px] font-bold text-[#6E6E73] uppercase tracking-wider block mb-1.5">Kode Voucher Trade-In / Promo</label>
                        <div class="flex items-center gap-2">
                            <input type="text" placeholder="Contoh: VCH-XXXXXX" class="glass-input !bg-white text-xs uppercase font-mono tracking-wider !py-2">
                            <button type="button" class="glass-btn glass-btn-secondary glass-btn-sm !py-2 shrink-0">
                                Terapkan
                            </button>
                        </div>
                    </div>

                    <!-- Grand Total -->
                    <div class="pt-4 border-t border-[#D2D2D7] space-y-1">
                        <span class="text-xs text-[#6E6E73] uppercase font-bold tracking-wider block">Total Pembayaran</span>
                        <div class="flex items-baseline justify-between">
                            <span id="cart-summary-grand-total" class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] tracking-tight">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-[11px] text-[#6E6E73] pt-0.5">
                            Cicilan 0% mulai <strong class="text-[#0066CC]">Rp {{ number_format(round($subtotal / 24), 0, ',', '.') }}/bln</strong>
                        </p>
                    </div>

                    <!-- Action CTAs -->
                    <div class="space-y-2.5 pt-2">
                        <a href="{{ url('/checkout') }}" class="glass-btn glass-btn-primary glass-btn-lg w-full flex items-center justify-center gap-2">
                            <span>Lanjut ke Checkout</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>

                        <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-secondary glass-btn-sm w-full text-center">
                            <span>Tambah Produk Lainnya</span>
                        </a>
                    </div>

                    <!-- Secure Guarantee Badges -->
                    <div class="pt-2 flex items-center justify-center gap-4 text-[11px] text-[#6E6E73] font-medium">
                        <span class="flex items-center gap-1">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                            <span>Midtrans 3DS Secure</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="award" class="w-3.5 h-3.5 text-[#008040]"></i>
                            <span>Garansi Resmi</span>
                        </span>
                    </div>

                </div>
            </div>

        </div>
    @endif

</div>
@endsection
