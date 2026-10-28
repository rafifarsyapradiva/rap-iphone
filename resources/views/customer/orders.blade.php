@extends('layouts.customer')

@section('title', 'Pesanan Saya - Rap-iPhone')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs text-[#E5E5EA] mb-2">
                <i data-lucide="user" class="w-3.5 h-3.5 text-[#0071E3]"></i>
                <span>Customer Portal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Pesanan & Akun Saya</h1>
            <p class="text-xs sm:text-sm text-[#8E8E93] mt-1">
                Lacak status pengiriman paket unit iPhone dan unduh faktur resmi bergaransi Anda.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="glass-btn glass-btn-primary glass-btn-sm flex items-center gap-2">
                <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                <span>Belanja iPhone Baru</span>
            </a>
        </div>
    </div>

    <!-- User Profile Summary Pill -->
    <div class="glass-card-elevated rounded-2xl p-5 mb-8 border border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#0071E3] to-[#34C759] flex items-center justify-center font-bold text-xl text-white shadow-lg overflow-hidden shrink-0">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                @else
                    {{ substr(Auth::user()->name, 0, 1) }}
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-white">{{ Auth::user()->name }}</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#34C759]/20 text-[#34C759] border border-[#34C759]/30">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>
                    @if(Auth::user()->google_id)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white/10 text-white/80 border border-white/20 flex items-center gap-1">
                            <i data-lucide="check" class="w-3 h-3 text-[#5AC8FA]"></i>
                            Google Connected
                        </span>
                    @endif
                </div>
                <p class="text-xs text-[#8E8E93] mt-0.5">{{ Auth::user()->email }} &bull; {{ Auth::user()->phone_number ?? 'Nomor WhatsApp belum diisi' }}</p>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm text-[#FF453A] border-[#FF453A]/30 hover:bg-[#FF453A]/10 flex items-center gap-1.5">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Keluar Akun</span>
            </button>
        </form>
    </div>

    <!-- Orders Section -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="package" class="w-4 h-4 text-[#0071E3]"></i>
            <span>Daftar Transaksi Pembelian</span>
        </h2>

        @if(isset($orders) && count($orders) > 0)
            <div class="grid grid-cols-1 gap-4">
                @foreach($orders as $order)
                    <div class="glass-card rounded-2xl p-5 border border-white/10 hover:border-white/20 transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                            <div>
                                <span class="text-xs text-[#8E8E93]">No. Nota:</span>
                                <span class="font-mono text-sm font-bold text-white ml-1">{{ $order->order_number }}</span>
                                <span class="text-xs text-[#8E8E93] ml-3">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div>
                                @if($order->status === 'paid')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#34C759]/20 text-[#34C759] border border-[#34C759]/30">
                                        LUNAS (PAID)
                                    </span>
                                @elseif($order->status === 'pending')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FF9F0A]/20 text-[#FF9F0A] border border-[#FF9F0A]/30">
                                        MENUNGGU PEMBAYARAN
                                    </span>
                                @elseif($order->status === 'cancelled')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FF453A]/20 text-[#FF453A] border border-[#FF453A]/30">
                                        DIBATALKAN
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#0071E3]/20 text-[#0071E3] border border-[#0071E3]/30">
                                        {{ strtoupper($order->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="text-xs text-[#8E8E93] space-y-1">
                                <p class="text-white font-medium">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                                <p>{{ $order->shipping_courier }} - {{ $order->shipping_address }}</p>
                                <p class="text-[11px] text-[#8E8E93]">
                                    Unit: {{ $order->items->pluck('product_name')->implode(', ') }}
                                </p>
                            </div>
                            <div class="text-right flex flex-col sm:items-end gap-2">
                                <div>
                                    <span class="text-xs text-[#8E8E93] block">Total Pembayaran</span>
                                    <span class="text-base font-bold text-white">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('checkout.success', $order->order_number) }}" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                                        Lihat Rincian
                                    </a>
                                    @if($order->status === 'pending')
                                        <a href="{{ route('checkout.success', $order->order_number) }}" class="glass-btn glass-btn-primary glass-btn-sm text-xs font-bold">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="glass-card rounded-3xl p-12 text-center border border-white/10">
                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-4 text-[#8E8E93]">
                    <i data-lucide="shopping-bag" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Belum Ada Riwayat Pesanan</h3>
                <p class="text-xs text-[#8E8E93] mt-1 max-w-md mx-auto">
                    Anda belum melakukan pemesanan unit iPhone. Jelajahi katalog resmi kami untuk menemukan iPhone impian Anda dengan garansi terpercaya.
                </p>
                <div class="mt-6">
                    <a href="{{ url('/') }}" class="glass-btn glass-btn-primary glass-btn-sm">
                        <span>Mulai Belanja</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Trade-In Submissions Section -->
    <div class="space-y-4 mt-10 pt-8 border-t border-white/10">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4 text-[#34C759]"></i>
                <span>Riwayat Pengajuan Tukar Tambah (Trade-In)</span>
            </h2>
            <a href="{{ route('trade-in.index') }}" class="text-xs font-semibold text-[#5AC8FA] hover:text-white flex items-center gap-1">
                <span>+ Ajukan Trade-In Baru</span>
            </a>
        </div>

        @if(isset($tradeIns) && count($tradeIns) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($tradeIns as $ti)
                    <div class="glass-card rounded-2xl p-5 border border-white/10 hover:border-white/20 transition-all flex flex-col justify-between gap-3">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <div>
                                <span class="font-mono text-xs font-bold text-[#5AC8FA]">{{ $ti->trade_in_code }}</span>
                                <span class="text-[11px] text-[#8E8E93] ml-2">{{ $ti->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <span class="glass-badge {{ $ti->status_badge_class }}">
                                <span class="glass-badge-dot"></span>
                                <span>{{ $ti->status_label }}</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs space-y-1">
                                <p class="font-bold text-white">{{ $ti->phone_model }} ({{ $ti->storage_capacity }})</p>
                                <p class="text-[#8E8E93]">{{ $ti->physical_condition }} &bull; BH {{ $ti->battery_health }}%</p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-[#8E8E93] block">{{ $ti->final_price ? 'Harga Final' : 'Estimasi' }}</span>
                                <span class="text-sm font-black text-[#34C759] block">Rp {{ number_format($ti->effective_price, 0, ',', '.') }}</span>
                                <a href="{{ route('trade-in.show', $ti->trade_in_code) }}" class="text-[11px] text-[#5AC8FA] hover:underline font-semibold">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="glass-card rounded-2xl p-6 text-center border border-white/10 text-xs text-[#8E8E93]">
                Belum ada pengajuan tukar tambah iPhone. <a href="{{ route('trade-in.index') }}" class="text-[#5AC8FA] font-semibold hover:underline">Cek estimasi nilai iPhone lama Anda di sini</a>.
            </div>
        @endif
    </div>

</div>
@endsection
