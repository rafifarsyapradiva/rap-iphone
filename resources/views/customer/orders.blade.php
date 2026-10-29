@extends('layouts.customer')

@section('title', 'Pesanan & Akun Saya — Rap-iPhone')

@section('content')
<div class="cust-page py-6 space-y-8 pb-20">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-4 border-b border-[var(--cust-rule-border)]">
        <div>
            <x-customer.spec-badge variant="official" icon="user" class="mb-1.5">
                Akun Pelanggan Rap-iPhone
            </x-customer.spec-badge>
            <h1 class="font-serif text-2xl sm:text-3xl font-light text-[var(--cust-ink)] tracking-tight">Pesanan &amp; Riwayat Belanja</h1>
            <p class="text-xs sm:text-sm text-[var(--cust-ink-muted)] mt-0.5">
                Lacak status pesanan pengiriman unit iPhone dan unduh Nota PDF Resmi ber-QR Code.
            </p>
        </div>

        <x-customer.button :href="url('/katalog')" variant="primary" icon="shopping-bag" size="sm">
            Katalog iPhone
        </x-customer.button>
    </div>

    <!-- User Profile Summary Card -->
    <div class="cust-card p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded bg-[var(--cust-ink)] flex items-center justify-center font-bold text-lg text-white shadow-sm overflow-hidden shrink-0">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                @else
                    {{ substr(Auth::user()->name, 0, 1) }}
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-sm font-semibold text-[var(--cust-ink)]">{{ Auth::user()->name }}</h2>
                    <x-customer.spec-badge variant="healthy">
                        {{ strtoupper(Auth::user()->role) }}
                    </x-customer.spec-badge>
                    @if(Auth::user()->google_id)
                        <span class="text-[10px] text-[var(--cust-tone-official-ink)] bg-[var(--cust-tone-official-bg)] px-2 py-0.5 rounded font-medium">
                            Google Terverifikasi
                        </span>
                    @endif
                </div>
                <p class="text-xs text-[var(--cust-ink-muted)] mt-0.5">{{ Auth::user()->email }} &bull; {{ Auth::user()->phone_number ?? 'Nomor WhatsApp belum diisi' }}</p>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="cust-btn cust-btn-outline text-xs py-1.5 px-3 flex items-center gap-1.5 text-[var(--cust-tone-alert-ink)] hover:border-[var(--cust-tone-alert-ink)]">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Keluar Akun</span>
            </button>
        </form>
    </div>

    <!-- Orders Section (Master Ledger Style) -->
    <div class="space-y-3">
        <h2 class="text-xs font-semibold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-1.5 pb-2 border-b border-[var(--cust-rule-border)]">
            <i data-lucide="package" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
            <span>Daftar Transaksi Pembelian</span>
        </h2>

        @if(isset($orders) && count($orders) > 0)
            <div class="space-y-3">
                @foreach($orders as $order)
                    <div class="cust-card p-4 sm:p-5 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[var(--cust-rule-border)] pb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs text-[var(--cust-ink-muted)]">Nota:</span>
                                <span class="font-mono text-xs font-bold text-[var(--cust-ink)]">{{ $order->order_number }}</span>
                                <span class="text-xs text-[var(--cust-ink-faint)] cust-tabular">&bull; {{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                            <x-order-badge :status="$order->status" />
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="text-[var(--cust-ink-muted)] space-y-0.5">
                                <p class="text-[var(--cust-ink)] font-semibold">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                                <p>{{ $order->shipping_courier }} &mdash; {{ $order->shipping_address }}</p>
                                <p class="text-[11px]">
                                    Unit: <strong class="text-[var(--cust-ink)]">{{ $order->items->pluck('product_name')->implode(', ') }}</strong>
                                </p>
                            </div>

                            <div class="text-right flex flex-col sm:items-end gap-1.5 shrink-0">
                                <div>
                                    <span class="text-[10px] text-[var(--cust-ink-muted)] block">Total</span>
                                    <span class="text-sm font-bold text-[var(--cust-ink)] cust-tabular">
                                        Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center sm:justify-end gap-1.5">
                                    <a href="{{ route('checkout.success', $order->order_number) }}" class="cust-btn cust-btn-outline text-xs py-1 px-2.5">
                                        Rincian Nota
                                    </a>
                                    @if($order->status === 'paid' || $order->status === 'completed' || $order->status === 'shipped')
                                        <a href="{{ route('orders.invoice.download', $order->order_number) }}" class="cust-btn cust-btn-primary text-xs py-1 px-2.5 flex items-center gap-1">
                                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                            <span>Unduh PDF</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="cust-card p-10 text-center space-y-2">
                <i data-lucide="package-open" class="w-8 h-8 text-[var(--cust-ink-faint)] mx-auto"></i>
                <h3 class="text-sm font-semibold text-[var(--cust-ink)]">Belum Ada Transaksi Pembelian</h3>
                <p class="text-xs text-[var(--cust-ink-muted)] max-w-sm mx-auto">
                    Anda belum pernah melakukan pemesanan unit iPhone. Silakan jelajahi katalog kami.
                </p>
                <div class="pt-1">
                    <x-customer.button :href="url('/katalog')" variant="secondary" size="sm">
                        Jelajahi Katalog
                    </x-customer.button>
                </div>
            </div>
        @endif
    </div>

    <!-- Trade-In Section -->
    <div class="space-y-3 pt-6 border-t border-[var(--cust-rule-border)]">
        <div class="flex items-center justify-between pb-2 border-b border-[var(--cust-rule-border)]">
            <h2 class="text-xs font-semibold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-[var(--cust-tone-healthy-ink)]"></i>
                <span>Riwayat Pengajuan Tukar Tambah (Trade-In)</span>
            </h2>
            <a href="{{ route('trade-in.index') }}" class="text-xs text-[var(--cust-signal)] hover:underline font-medium">
                + Ajukan Trade-In Baru
            </a>
        </div>

        @if(isset($tradeIns) && count($tradeIns) > 0)
            <div class="space-y-3">
                @foreach($tradeIns as $tradeIn)
                    <div class="cust-card p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-[var(--cust-ink)]">{{ $tradeIn->trade_in_code }}</span>
                                <span class="cust-badge {{ $tradeIn->status_badge_class }}">
                                    {{ $tradeIn->status_label }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-[var(--cust-ink)]">{{ $tradeIn->phone_model }} ({{ $tradeIn->storage_capacity }})</h3>
                            <p class="text-xs text-[var(--cust-ink-muted)]">
                                Kondisi: {{ $tradeIn->physical_condition }} &bull; BH: {{ $tradeIn->battery_health }}% &bull; {{ $tradeIn->created_at->format('d M Y') }}
                            </p>
                        </div>

                        <div class="text-right flex flex-col sm:items-end gap-1.5 shrink-0">
                            <div>
                                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Taksiran Nilai</span>
                                <span class="text-sm font-bold text-[var(--cust-tone-healthy-ink)] cust-tabular">
                                    Rp {{ number_format($tradeIn->final_price ?? $tradeIn->estimated_price, 0, ',', '.') }}
                                </span>
                            </div>
                            <a href="{{ route('trade-in.show', $tradeIn->trade_in_code) }}" class="cust-btn cust-btn-outline text-xs py-1 px-2.5">
                                Detail Status
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="cust-card p-8 text-center space-y-2">
                <i data-lucide="refresh-cw" class="w-8 h-8 text-[var(--cust-ink-faint)] mx-auto"></i>
                <h3 class="text-sm font-semibold text-[var(--cust-ink)]">Belum Ada Pengajuan Trade-In</h3>
                <p class="text-xs text-[var(--cust-ink-muted)]">
                    Punya iPhone lama? Tukarkan sekarang untuk potongan harga pembelian unit baru.
                </p>
                <div class="pt-1">
                    <x-customer.button :href="route('trade-in.index')" variant="secondary" size="sm">
                        Mulai Tukar Tambah
                    </x-customer.button>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
