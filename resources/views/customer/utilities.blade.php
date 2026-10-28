@extends('layouts.customer')

@section('title', match($activeTab) {
    'warranty-check' => 'Cek Status IMEI & Garansi Resmi — Rap-iPhone',
    'track-order' => 'Lacak Status Pesanan & Resi Kurir — Rap-iPhone',
    default => 'Syarat & Ketentuan Garansi — Rap-iPhone Authorized Retail',
})

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Navigation Pills -->
    <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
        <a href="{{ route('warranty.check') }}" class="glass-btn {{ $activeTab === 'warranty-check' ? 'glass-btn-primary' : 'glass-btn-secondary' }} glass-btn-sm text-xs">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>Cek IMEI & Garansi</span>
        </a>
        <a href="{{ route('orders.track') }}" class="glass-btn {{ $activeTab === 'track-order' ? 'glass-btn-primary' : 'glass-btn-secondary' }} glass-btn-sm text-xs">
            <i data-lucide="truck" class="w-4 h-4"></i>
            <span>Lacak Pesanan</span>
        </a>
        <a href="{{ route('warranty.terms') }}" class="glass-btn {{ $activeTab === 'warranty-terms' ? 'glass-btn-primary' : 'glass-btn-secondary' }} glass-btn-sm text-xs">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            <span>Syarat & Ketentuan Garansi</span>
        </a>
    </div>

    @if($activeTab === 'warranty-check')
        <!-- =========================================================================
             TAB 1: CEK STATUS IMEI & GARANSI UNIT
             ========================================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#E5E5EA] shadow-sm space-y-6">
            <div class="text-center space-y-2 max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-xs font-bold text-[#0066CC]">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    <span>Portal Verifikasi Unit & IMEI</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-[#1D1D1F] tracking-tight">Cek Status Garansi & IMEI</h1>
                <p class="text-xs sm:text-sm text-[#6E6E73] leading-relaxed">
                    Masukkan 15 digit nomor IMEI fisik atau kode SKU unit iPhone Anda untuk memeriksa status registrasi dan cakupan perlindungan garansi di sistem Rap-iPhone.
                </p>
            </div>

            <form action="{{ route('warranty.check') }}" method="GET" class="max-w-xl mx-auto flex flex-col sm:flex-row gap-3">
                <input
                    type="text"
                    name="imei"
                    value="{{ $imeiQuery }}"
                    placeholder="Contoh IMEI: 358792110293812 atau SKU: SEC-IP15P-NT-128GB"
                    required
                    class="glass-input text-xs sm:text-sm font-mono flex-1"
                >
                <button type="submit" class="glass-btn glass-btn-primary glass-btn-md text-xs font-bold shrink-0">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Verifikasi Sekarang</span>
                </button>
            </form>

            @if($imeiQuery !== '')
                @if($variantResult || $orderItemResult)
                    @php
                        $productName = $orderItemResult->product_name ?? ($variantResult->product->name ?? 'iPhone Official Unit');
                        $variantDetails = $orderItemResult->variant_details ?? (($variantResult->color_name ?? '') . ' / ' . ($variantResult->storage_capacity ?? ''));
                        $warrantyType = $variantResult->product->warranty_type ?? 'resmi_ibox';
                        $warrantyLabel = match($warrantyType) {
                            'resmi_ibox' => 'Garansi Resmi Apple Indonesia (iBox / TAM 1 Tahun)',
                            'resmi_digimap' => 'Garansi Resmi Digimap Indonesia (1 Tahun)',
                            'inter_all_operator' => 'Garansi Sinyal All Operator Permanen + Toko 30 Hari',
                            default => 'Garansi Toko Resmi Rap-iPhone (30 Hari Ganti Unit)',
                        };
                    @endphp
                    <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-4 max-w-2xl mx-auto">
                        <div class="flex items-center justify-between border-b border-emerald-200 pb-3">
                            <div class="flex items-center gap-2.5 text-[#008009] font-bold text-sm">
                                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                                <span>Unit Terdaftar & Terverifikasi Resmi</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white border border-emerald-200 text-[#008009]">
                                IMEI AKTIF
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-[#6E6E73] block">Model Perangkat:</span>
                                <span class="text-[#1D1D1F] font-bold text-sm">{{ $productName }}</span>
                            </div>
                            <div>
                                <span class="text-[#6E6E73] block">Varian & Kapasitas:</span>
                                <span class="text-[#1D1D1F] font-semibold">{{ $variantDetails }}</span>
                            </div>
                            <div>
                                <span class="text-[#6E6E73] block">Nomor IMEI / SKU:</span>
                                <span class="text-[#0066CC] font-mono font-bold">{{ $imeiQuery }}</span>
                            </div>
                            <div>
                                <span class="text-[#6E6E73] block">Cakupan Garansi:</span>
                                <span class="text-[#008009] font-semibold">{{ $warrantyLabel }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-2 max-w-xl mx-auto">
                        <p class="text-sm font-bold text-[#BF4800]">Data IMEI "{{ $imeiQuery }}" Belum Ditemukan</p>
                        <p class="text-xs text-[#6E6E73]">
                            Pastikan 15 digit nomor IMEI sesuai dengan yang tertera pada Nota PDF resmi Rap-iPhone atau hubungi layanan pelanggan WhatsApp kami.
                        </p>
                    </div>
                @endif
            @endif
        </div>

    @elseif($activeTab === 'track-order')
        <!-- =========================================================================
             TAB 2: LACAK STATUS PESANAN & RESI KURIR
             ========================================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#E5E5EA] shadow-sm space-y-6">
            <div class="text-center space-y-2 max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-bold text-[#008009]">
                    <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                    <span>Live Order & Logistics Tracker</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-[#1D1D1F] tracking-tight">Lacak Status Pesanan Anda</h1>
                <p class="text-xs sm:text-sm text-[#6E6E73] leading-relaxed">
                    Masukkan Nomor Nota Pesanan (contoh: <code class="text-[#1D1D1F] font-mono font-semibold">RAP-20261028-0001</code>) untuk memantau status verifikasi pembayaran dan nomor resi pengiriman kurir.
                </p>
            </div>

            <form action="{{ route('orders.track') }}" method="GET" class="max-w-xl mx-auto flex flex-col sm:flex-row gap-3">
                <input
                    type="text"
                    name="order_number"
                    value="{{ $orderQuery }}"
                    placeholder="Masukkan Nomor Nota (contoh: RAP-20261028-0001)"
                    required
                    class="glass-input text-xs sm:text-sm font-mono uppercase flex-1"
                >
                <button type="submit" class="glass-btn glass-btn-primary glass-btn-md text-xs font-bold shrink-0">
                    <i data-lucide="compass" class="w-4 h-4"></i>
                    <span>Lacak Pesanan</span>
                </button>
            </form>

            @if($orderQuery !== '')
                @if($trackedOrder)
                    <div class="p-6 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] space-y-5 max-w-2xl mx-auto">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-[#E5E5EA]">
                            <div>
                                <span class="text-[11px] text-[#6E6E73] block">Nomor Nota Resmi:</span>
                                <span class="font-mono text-base font-black text-[#1D1D1F]">{{ $trackedOrder->order_number }}</span>
                            </div>
                            <x-order-badge :status="$trackedOrder->status" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-[#6E6E73] block">Penerima:</span>
                                <span class="text-[#1D1D1F] font-bold">{{ Str::mask($trackedOrder->recipient_name, '*', 3) }}</span>
                            </div>
                            <div>
                                <span class="text-[#6E6E73] block">Ekspedisi Kurir:</span>
                                <span class="text-[#1D1D1F] font-semibold">{{ $trackedOrder->shipping_courier }}</span>
                            </div>
                            <div>
                                <span class="text-[#6E6E73] block">Nomor Resi Pengiriman:</span>
                                <span class="font-mono font-bold text-[#008009]">{{ $trackedOrder->shipping_tracking_number ?: 'Menunggu Input Resi' }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                            <span class="text-[#6E6E73]">Total Nilai Transaksi:</span>
                            <span class="text-sm font-black text-[#1D1D1F]">Rp {{ number_format($trackedOrder->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-6 rounded-2xl bg-red-50 border border-red-200 text-center space-y-2 max-w-xl mx-auto">
                        <p class="text-sm font-bold text-[#E30000]">Pesanan "{{ $orderQuery }}" Tidak Ditemukan</p>
                        <p class="text-xs text-[#6E6E73]">Periksa kembali penulisan nomor nota pesanan pada pesan WhatsApp atau email konfirmasi Anda.</p>
                    </div>
                @endif
            @endif
        </div>

    @else
        <!-- =========================================================================
             TAB 3: SYARAT & KETENTUAN GARANSI RESMI & TOKO
             ========================================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-[#E5E5EA] shadow-sm space-y-6">
            <div class="space-y-2">
                <span class="text-xs font-bold text-[#0066CC] uppercase tracking-wider">Standar Perlindungan Konsumen</span>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1D1D1F] tracking-tight">Syarat & Ketentuan Garansi Rap-iPhone</h1>
                <p class="text-xs sm:text-sm text-[#6E6E73]">Transparansi jaminan purna jual untuk Unit Baru (BNIB) dan Unit Second Like New Teruji.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs leading-relaxed">
                <div class="bg-[#F5F5F7] p-5 rounded-2xl border border-[#E5E5EA] space-y-3">
                    <h2 class="text-sm font-bold text-[#1D1D1F] flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[#0071E3]"></i>
                        <span>1. Garansi Resmi Apple Indonesia (BNIB)</span>
                    </h2>
                    <ul class="space-y-2 text-[#6E6E73] list-disc list-inside">
                        <li>Berlaku 12 bulan sejak perangkat diaktifkan pertama kali.</li>
                        <li>Klaim dapat dilakukan di seluruh jaringan servis resmi Apple di Indonesia (iBox, Digimap, Mitra Care).</li>
                        <li>IMEI terdaftar resmi di database Kemenperin RI (bebas blokir sinyal seumur hidup).</li>
                    </ul>
                </div>

                <div class="bg-[#F5F5F7] p-5 rounded-2xl border border-[#E5E5EA] space-y-3">
                    <h2 class="text-sm font-bold text-[#1D1D1F] flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-[#008009]"></i>
                        <span>2. Garansi Toko Unit Second Like New (30 Hari)</span>
                    </h2>
                    <ul class="space-y-2 text-[#6E6E73] list-disc list-inside">
                        <li>Jaminan tukar unit selama 30 hari penuh apabila ditemukan cacat fungsi pabrik (bukan human error).</li>
                        <li>Wajib menyertakan video unboxing tanpa jeda dan segel pengaman toko dalam kondisi utuh.</li>
                        <li>Garansi tidak mencakup kerusakan akibat cairan (water damage), jatuh/benturan, atau modifikasi iCloud.</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
