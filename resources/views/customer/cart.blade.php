@extends('layouts.customer')

@section('title', 'Keranjang Belanja — Rap-iPhone Authorized Style Partner')
@section('meta_description', 'Rincian unit iPhone di keranjang belanja Anda. Periksa varian warna, kapasitas, dan lanjutkan ke checkout aman dengan Midtrans.')

@section('content')
<div class="cust-page py-6 space-y-8 pb-20">

    <!-- Breadcrumbs & Header -->
    <div class="space-y-3 pb-4 border-b border-[var(--cust-rule-border)]">
        <nav class="flex items-center gap-2 text-xs text-[var(--cust-ink-muted)]">
            <a href="{{ url('/') }}" class="hover:text-[var(--cust-ink)] transition-colors">Beranda</a>
            <span class="text-[var(--cust-ink-faint)]">/</span>
            <a href="{{ url('/katalog') }}" class="hover:text-[var(--cust-ink)] transition-colors">Katalog</a>
            <span class="text-[var(--cust-ink-faint)]">/</span>
            <span class="text-[var(--cust-ink)] font-medium">Keranjang Belanja</span>
        </nav>
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="font-serif text-3xl font-light text-[var(--cust-ink)] tracking-tight">Tas Belanja</h1>
                <p class="text-xs sm:text-sm text-[var(--cust-ink-muted)] mt-1">Gratis pengiriman berasuransi atau ambil di gerai (Click &amp; Collect).</p>
            </div>

            @if(!empty($cart))
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item dari keranjang?')">
                    @csrf
                    <button type="submit" class="text-xs text-[var(--cust-tone-alert-ink)] hover:underline flex items-center gap-1 font-medium">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Kosongkan Keranjang</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(empty($cart))
        <!-- Empty Cart -->
        <div class="cust-card p-12 sm:p-16 text-center space-y-4 max-w-xl mx-auto">
            <div class="w-16 h-16 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] flex items-center justify-center mx-auto text-[var(--cust-ink-muted)]">
                <i data-lucide="shopping-bag" class="w-7 h-7"></i>
            </div>
            
            <div class="space-y-1">
                <h2 class="font-serif text-xl font-light text-[var(--cust-ink)]">Keranjang Belanja Anda Masih Kosong</h2>
                <p class="text-xs text-[var(--cust-ink-muted)] max-w-sm mx-auto leading-relaxed">
                    Belum ada unit iPhone yang dipilih. Temukan unit baru garansi resmi atau second teruji sekarang.
                </p>
            </div>

            <div class="pt-2 flex flex-wrap items-center justify-center gap-2.5">
                <x-customer.button :href="url('/katalog')" variant="primary" icon="compass">
                    Jelajahi Katalog iPhone
                </x-customer.button>
                <x-customer.button :href="url('/katalog?condition=second')" variant="outline" icon="badge-check">
                    Unit Second Teruji
                </x-customer.button>
            </div>
        </div>
    @else
        <!-- Filled Cart (Spec Sheet & Master Ledger Layout) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" id="cart-content-wrapper">
            
            <!-- Left: Cart Items List -->
            <div class="lg:col-span-8 space-y-3">
                @foreach($cart as $variantId => $item)
                    @php
                        $itemSubtotal = $item['price'] * $item['quantity'];
                        $isSecond = ($item['condition'] ?? 'new') === 'second';
                    @endphp

                    <div class="cust-card p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 cart-item-row" data-variant-id="{{ $variantId }}">
                        
                        <!-- Visual & Details -->
                        <div class="flex items-center gap-3.5 w-full sm:w-auto">
                            <div class="w-16 h-16 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] flex items-center justify-center p-1.5 shrink-0 overflow-hidden">
                                @if(!empty($item['image']) && file_exists(public_path($item['image'])))
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" width="64" height="64" class="max-h-full max-w-full object-contain">
                                @else
                                    <i data-lucide="smartphone" class="w-6 h-6 text-[var(--cust-ink)]"></i>
                                @endif
                            </div>

                            <div class="space-y-1">
                                <a href="{{ url('/katalog/' . ($item['product_slug'] ?? '')) }}" class="text-sm font-semibold text-[var(--cust-ink)] hover:text-[var(--cust-signal)] transition-colors line-clamp-1">
                                    {{ $item['product_name'] }}
                                </a>

                                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border border-[var(--cust-rule-border)] text-[var(--cust-ink)] font-medium">
                                        <span class="w-2 h-2 rounded-full border border-black/15" style="background-color: {{ $item['color_hex'] ?? '#8E8E93' }};"></span>
                                        <span>{{ $item['color_name'] }}</span>
                                    </span>

                                    <span class="px-2 py-0.5 rounded border border-[var(--cust-rule-border)] font-bold text-[var(--cust-ink)]">
                                        {{ $item['storage_capacity'] }}
                                    </span>

                                    @if($isSecond)
                                        <x-customer.spec-badge variant="healthy">
                                            Second · BH {{ $item['battery_health'] ?? 95 }}%
                                        </x-customer.spec-badge>
                                    @else
                                        <x-customer.spec-badge variant="official">
                                            Baru Resmi
                                        </x-customer.spec-badge>
                                    @endif
                                </div>

                                <p class="text-xs text-[var(--cust-ink-muted)] cust-tabular">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }} / unit
                                </p>
                            </div>
                        </div>

                        <!-- Stepper & Subtotal -->
                        <div class="flex items-center justify-between sm:justify-end gap-5 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-[var(--cust-rule-border)]">
                            
                            <!-- Stepper -->
                            <div class="flex items-center border border-[var(--cust-rule-border)] rounded">
                                <button type="button" 
                                        class="cart-qty-btn cart-qty-dec px-2.5 py-1 text-xs hover:bg-[var(--cust-paper-subtle)] font-bold text-[var(--cust-ink)] disabled:opacity-30" 
                                        data-variant-id="{{ $variantId }}"
                                        data-current-qty="{{ $item['quantity'] }}">
                                    -
                                </button>
                                <span class="cart-qty-display w-7 text-center text-xs font-bold text-[var(--cust-ink)] cust-tabular">
                                    {{ $item['quantity'] }}
                                </span>
                                <button type="button" 
                                        class="cart-qty-btn cart-qty-inc px-2.5 py-1 text-xs hover:bg-[var(--cust-paper-subtle)] font-bold text-[var(--cust-ink)] disabled:opacity-30" 
                                        data-variant-id="{{ $variantId }}"
                                        data-current-qty="{{ $item['quantity'] }}"
                                        data-max-stock="{{ $item['stock'] }}">
                                    +
                                </button>
                            </div>

                            <!-- Subtotal -->
                            <div class="text-right">
                                <span class="text-[10px] text-[var(--cust-ink-muted)] block uppercase">Subtotal</span>
                                <span class="text-sm font-bold text-[var(--cust-ink)] cust-tabular cart-item-subtotal">
                                    Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Remove Form -->
                            <form action="{{ route('cart.remove', $variantId) }}" method="POST" class="cart-remove-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded text-[var(--cust-ink-muted)] hover:text-[var(--cust-tone-alert-ink)] hover:bg-[var(--cust-paper-subtle)]" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>

                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Right: Order Summary Ledger -->
            <div class="lg:col-span-4 lg:sticky lg:top-24">
                <div class="cust-card p-5 sm:p-6 space-y-4">
                    
                    <h2 class="text-sm font-semibold text-[var(--cust-ink)] pb-2 border-b border-[var(--cust-rule-border)]">
                        Ringkasan Belanja
                    </h2>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-[var(--cust-ink-muted)]">
                            <span>Total Unit</span>
                            <span id="cart-summary-total-qty" class="text-[var(--cust-ink)] font-semibold cust-tabular">{{ $totalQuantity }} Unit</span>
                        </div>

                        <div class="flex items-center justify-between text-[var(--cust-ink-muted)]">
                            <span>Subtotal Produk</span>
                            <span id="cart-summary-subtotal" class="text-[var(--cust-ink)] font-bold cust-tabular">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-[var(--cust-ink-muted)]">
                            <span>Pengiriman &amp; Asuransi</span>
                            <span class="text-[var(--cust-tone-healthy-ink)] font-semibold">GRATIS</span>
                        </div>

                        <div class="flex items-center justify-between text-[var(--cust-ink-muted)]">
                            <span>Click &amp; Collect Gerai</span>
                            <span class="text-[var(--cust-signal)] font-medium">Tersedia</span>
                        </div>
                    </div>

                    <!-- Voucher -->
                    <div class="pt-2">
                        <label class="cust-label">Kode Voucher Trade-In / Promo</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" placeholder="VCH-XXXXXX" class="cust-input text-xs py-1.5 uppercase font-mono">
                            <x-customer.button variant="secondary" size="sm" class="shrink-0">
                                Terapkan
                            </x-customer.button>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="pt-3 border-t border-[var(--cust-rule-border)] space-y-1">
                        <span class="text-[10px] text-[var(--cust-ink-muted)] uppercase font-semibold">Total Pembayaran</span>
                        <div class="text-xl sm:text-2xl font-bold text-[var(--cust-ink)] cust-tabular" id="cart-summary-grand-total">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-[var(--cust-ink-muted)]">
                            Cicilan 0% mulai <strong class="text-[var(--cust-signal)] cust-tabular">Rp {{ number_format(round($subtotal / 24), 0, ',', '.') }}/bln</strong>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2 pt-1">
                        <x-customer.button :href="url('/checkout')" variant="primary" class="w-full py-2.5 text-xs font-semibold" icon="arrow-right">
                            Lanjut ke Checkout
                        </x-customer.button>

                        <x-customer.button :href="url('/katalog')" variant="outline" class="w-full py-2 text-xs">
                            Tambah Produk Lainnya
                        </x-customer.button>
                    </div>

                    <div class="pt-2 flex items-center justify-center gap-4 text-[10px] text-[var(--cust-ink-muted)]">
                        <span class="flex items-center gap-1">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
                            <span>Midtrans 3DS</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="award" class="w-3.5 h-3.5 text-[var(--cust-tone-healthy-ink)]"></i>
                            <span>Garansi Toko &amp; Resmi</span>
                        </span>
                    </div>

                </div>
            </div>

        </div>
    @endif

</div>
@endsection
