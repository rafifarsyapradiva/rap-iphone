@extends('layouts.customer')

@section('title', 'Verifikasi Keaslian Nota ' . $orderNumber . ' - Rap-iPhone')
@section('meta_description', 'Validasi keaslian nota faktur resmi Rap-iPhone melalui QR Code.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-12">

    @if($isValid)
        {{-- ============ NOTA ASLI ============ --}}
        <div class="glass-card-elevated rounded-3xl border border-[#34C759]/30 overflow-hidden">
            <div class="relative p-8 text-center border-b border-white/10">
                <div class="absolute inset-x-0 -top-16 mx-auto w-56 h-56 bg-[#34C759]/20 rounded-full blur-[80px] pointer-events-none"></div>
                <div class="relative">
                    <div class="w-16 h-16 rounded-2xl bg-[#34C759]/15 border border-[#34C759]/40 flex items-center justify-center mx-auto mb-4 text-[#34C759]">
                        <i data-lucide="shield-check" class="w-8 h-8"></i>
                    </div>
                    <p class="text-[11px] font-bold tracking-[0.2em] text-[#34C759] uppercase">Nota Asli Terverifikasi</p>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">Nota Terdaftar di Sistem {{ $store['name'] }}</h1>
                    <p class="font-mono text-sm text-[#5AC8FA] font-bold mt-2">#{{ $order->order_number }}</p>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                        <span class="text-[#8E8E93] block mb-1">Tanggal Transaksi</span>
                        <span class="font-semibold text-white">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                        <span class="text-[#8E8E93] block mb-1">Status Pembayaran</span>
                        <span class="font-bold {{ $status['is_paid'] ? 'text-[#34C759]' : 'text-[#FF9F0A]' }}">{{ $status['label'] }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                        <span class="text-[#8E8E93] block mb-1">Nama Pembeli</span>
                        <span class="font-semibold text-white font-mono">{{ $maskedName }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                        <span class="text-[#8E8E93] block mb-1">Total Transaksi</span>
                        <span class="font-bold text-white">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    <h2 class="text-xs font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i data-lucide="smartphone" class="w-4 h-4 text-[#0071E3]"></i>
                        <span>Unit &amp; Status Garansi</span>
                    </h2>
                    <div class="space-y-3">
                        @foreach($items as $item)
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div>
                                    <p class="font-bold text-white">{{ $item['quantity'] }}x {{ $item['name'] }}</p>
                                    <p class="text-[#8E8E93] mt-0.5">{{ $item['variant'] }}</p>
                                    <p class="font-mono text-[11px] text-[#5AC8FA] mt-1">IMEI: {{ $item['imei'] }}</p>
                                </div>
                                <div class="sm:text-right">
                                    <p class="font-semibold text-white">{{ $item['warranty']['label'] }}</p>
                                    @if($item['warranty']['ends_at'])
                                        <p class="mt-0.5 {{ $item['warranty']['is_active'] ? 'text-[#34C759]' : 'text-[#FF453A]' }}">
                                            {{ $item['warranty']['is_active'] ? 'Aktif' : 'Berakhir' }} s/d {{ $item['warranty']['ends_at']->format('d M Y') }}
                                        </p>
                                    @else
                                        <p class="mt-0.5 text-[#FF9F0A]">Aktif setelah pelunasan</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <p class="text-[11px] text-[#8E8E93] leading-relaxed border-t border-white/10 pt-4">
                    Data pribadi pembeli disamarkan demi privasi. Pastikan nomor nota dan IMEI pada fisik nota cocok dengan informasi di atas.
                    Butuh bantuan? Hubungi WhatsApp resmi {{ $store['whatsapp'] }}.
                </p>
            </div>
        </div>
    @else
        {{-- ============ NOTA TIDAK VALID ============ --}}
        <div class="glass-card-elevated rounded-3xl border border-[#FF453A]/30 p-8 sm:p-10 text-center relative overflow-hidden">
            <div class="absolute inset-x-0 -top-16 mx-auto w-56 h-56 bg-[#FF453A]/20 rounded-full blur-[80px] pointer-events-none"></div>
            <div class="relative">
                <div class="w-16 h-16 rounded-2xl bg-[#FF453A]/15 border border-[#FF453A]/40 flex items-center justify-center mx-auto mb-4 text-[#FF453A]">
                    <i data-lucide="shield-alert" class="w-8 h-8"></i>
                </div>
                <p class="text-[11px] font-bold tracking-[0.2em] text-[#FF453A] uppercase">Nota Tidak Valid</p>
                <h1 class="text-2xl font-black text-white tracking-tight mt-1">Nota Tidak Terdaftar</h1>
                <p class="text-xs text-[#8E8E93] mt-3 max-w-md mx-auto leading-relaxed">
                    Kode verifikasi untuk nota <span class="font-mono text-white">#{{ $orderNumber }}</span> tidak cocok dengan data di sistem Rap-iPhone.
                    Nota mungkin telah dimodifikasi atau bukan diterbitkan oleh toko kami.
                </p>
                <a href="{{ route('home') }}" class="glass-btn glass-btn-secondary glass-btn-sm mt-6 inline-flex items-center gap-2">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    @endif

</div>
@endsection

