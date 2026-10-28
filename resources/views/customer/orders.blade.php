@extends('layouts.customer')

@section('title', 'Pesanan & Akun Saya — Rap-iPhone')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-5 border-b border-[#E5E5EA]">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] text-xs font-semibold text-[#0066CC] mb-2">
                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                <span>Akun Pelanggan Rap-iPhone</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] tracking-tight">Pesanan &amp; Riwayat Trade-In Saya</h1>
            <p class="text-xs sm:text-sm text-[#6E6E73] mt-1">
                Lacak status pengiriman paket unit iPhone dan unduh Nota PDF Resmi ber-QR Code Anda.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-primary glass-btn-sm flex items-center gap-2">
                <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                <span>Belanja iPhone Baru</span>
            </a>
        </div>
    </div>

    <!-- User Profile Summary Pill -->
    <div class="rounded-[24px] bg-[#F5F5F7] p-5 mb-8 border border-[#E5E5EA] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#1D1D1F] flex items-center justify-center font-bold text-xl text-white shadow-sm overflow-hidden shrink-0">
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                @else
                    {{ substr(Auth::user()->name, 0, 1) }}
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base font-bold text-[#1D1D1F]">{{ Auth::user()->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#E8F8EE] text-[#008040]">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>
                    @if(Auth::user()->google_id)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-white text-[#1D1D1F] border border-[#D2D2D7] flex items-center gap-1">
                            <i data-lucide="check" class="w-3 h-3 text-[#0066CC]"></i>
                            <span>Google Verified</span>
                        </span>
                    @endif
                </div>
                <p class="text-xs text-[#6E6E73] mt-0.5">{{ Auth::user()->email }} &bull; {{ Auth::user()->phone_number ?? 'Nomor WhatsApp belum diisi' }}</p>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="glass-btn glass-btn-danger glass-btn-sm flex items-center gap-1.5">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Keluar Akun</span>
            </button>
        </form>
    </div>

    <!-- Orders Section -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-[#1D1D1F] uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="package" class="w-4 h-4 text-[#0066CC]"></i>
            <span>Daftar Transaksi Pembelian</span>
        </h2>

        @if(isset($orders) && count($orders) > 0)
            <div class="grid grid-cols-1 gap-4">
                @foreach($orders as $order)
                    <div class="rounded-[20px] bg-[#F5F5F7] p-5 border border-[#E5E5EA] hover:border-[#D2D2D7] transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#D2D2D7] pb-4">
                            <div>
                                <span class="text-xs text-[#6E6E73]">No. Nota:</span>
                                <span class="font-mono text-sm font-bold text-[#1D1D1F] ml-1">{{ $order->order_number }}</span>
                                <span class="text-xs text-[#6E6E73] ml-3">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div>
                                <x-order-badge :status="$order->status" />
                            </div>
                        </div>
                        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="text-xs text-[#6E6E73] space-y-1">
                                <p class="text-[#1D1D1F] font-semibold">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                                <p>{{ $order->shipping_courier }} &mdash; {{ $order->shipping_address }}</p>
                                <p class="text-[11px] text-[#6E6E73]">
                                    Unit: <strong class="text-[#1D1D1F]">{{ $order->items->pluck('product_name')->implode(', ') }}</strong>
                                </p>
                            </div>
                            <div class="text-right flex flex-col sm:items-end gap-2">
                                <div>
                                    <span class="text-xs text-[#6E6E73] block">Total Pembayaran</span>
                                    <span class="text-base font-bold text-[#1D1D1F]">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex flex-wrap items-center sm:justify-end gap-2">
                                    <a href="{{ route('checkout.success', $order->order_number) }}" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                                        Lihat Rincian
                                    </a>
                                    @if($order->status === 'pending')
                                        <a href="{{ route('checkout.success', $order->order_number) }}" class="glass-btn glass-btn-primary glass-btn-sm text-xs font-bold">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                    @can('downloadInvoice', $order)
                                        <a href="{{ route('orders.invoice.preview', $order->order_number) }}" target="_blank" class="glass-btn glass-btn-secondary glass-btn-sm text-xs inline-flex items-center gap-1.5" title="Buka nota untuk dicetak">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                            <span>Cetak</span>
                                        </a>
                                        <a href="{{ route('orders.invoice.download', $order->order_number) }}" class="glass-btn glass-btn-primary glass-btn-sm text-xs font-bold inline-flex items-center gap-1.5" data-testid="download-invoice">
                                            <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                                            <span>Unduh Nota PDF</span>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-[24px] bg-[#F5F5F7] p-12 text-center border border-[#E5E5EA]">
                <div class="w-14 h-14 rounded-2xl bg-white border border-[#D2D2D7] flex items-center justify-center mx-auto mb-4 text-[#6E6E73]">
                    <i data-lucide="shopping-bag" class="w-7 h-7"></i>
                </div>
                <h3 class="text-lg font-bold text-[#1D1D1F]">Belum Ada Riwayat Pesanan</h3>
                <p class="text-xs text-[#6E6E73] mt-1 max-w-md mx-auto">
                    Anda belum melakukan pemesanan unit iPhone. Jelajahi katalog resmi kami untuk menemukan iPhone impian Anda.
                </p>
                <div class="mt-5">
                    <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-primary glass-btn-sm">
                        <span>Mulai Belanja</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Trade-In Submissions Section -->
    <div class="space-y-4 mt-10 pt-8 border-t border-[#E5E5EA]">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#1D1D1F] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4 text-[#008040]"></i>
                <span>Riwayat Pengajuan Tukar Tambah (Trade-In)</span>
            </h2>
            <a href="{{ route('trade-in.index') }}" class="text-xs font-semibold text-[#0066CC] hover:underline flex items-center gap-1">
                <span>+ Ajukan Trade-In Baru</span>
            </a>
        </div>

        @if(isset($tradeIns) && count($tradeIns) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($tradeIns as $ti)
                    <div class="rounded-[20px] bg-[#F5F5F7] p-5 border border-[#E5E5EA] flex flex-col justify-between gap-3">
                        <div class="flex items-center justify-between border-b border-[#D2D2D7] pb-3">
                            <div>
                                <span class="font-mono text-xs font-bold text-[#0066CC]">{{ $ti->trade_in_code }}</span>
                                <span class="text-[11px] text-[#6E6E73] ml-2">{{ $ti->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <span class="glass-badge {{ $ti->status_badge_class }}">
                                <span class="glass-badge-dot"></span>
                                <span>{{ $ti->status_label }}</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs space-y-1">
                                <p class="font-bold text-[#1D1D1F]">{{ $ti->phone_model }} ({{ $ti->storage_capacity }})</p>
                                <p class="text-[#6E6E73]">{{ $ti->physical_condition }} &bull; BH {{ $ti->battery_health }}%</p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-[#6E6E73] block">{{ $ti->final_price ? 'Harga Final' : 'Estimasi' }}</span>
                                <span class="text-sm font-bold text-[#008040] block">Rp {{ number_format($ti->effective_price, 0, ',', '.') }}</span>
                                <a href="{{ route('trade-in.show', $ti->trade_in_code) }}" class="text-[11px] text-[#0066CC] hover:underline font-semibold">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-[20px] bg-[#F5F5F7] p-6 text-center border border-[#E5E5EA] text-xs text-[#6E6E73]">
                Belum ada pengajuan tukar tambah iPhone. <a href="{{ route('trade-in.index') }}" class="text-[#0066CC] font-semibold hover:underline">Cek estimasi nilai iPhone lama Anda di sini</a>.
            </div>
        @endif
    </div>

</div>
@endsection
