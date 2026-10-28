@extends('layouts.customer')

@section('title', 'Detail Pengajuan Trade-In ' . $tradeIn->trade_in_code . ' — Rap-iPhone')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Top Back Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('trade-in.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#6E6E73] hover:text-[#1D1D1F] transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Halaman Tukar Tambah</span>
        </a>

        <div class="flex items-center gap-2">
            @if(auth()->check() && auth()->user()->hasAdminAccess())
                <a href="{{ route('admin.trade-ins.show', $tradeIn->trade_in_code) }}" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                    <span>Buka di Panel Inspeksi Admin</span>
                </a>
            @endif
            <a href="{{ route('catalog.index') }}" class="glass-btn glass-btn-primary glass-btn-sm text-xs">
                <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                <span>Lihat Katalog iPhone Baru</span>
            </a>
        </div>
    </div>

    <!-- Main Status & Code Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#E5E5EA] shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#E5E5EA]">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-blue-50 text-[#0066CC] border border-blue-200">
                        {{ $tradeIn->trade_in_code }}
                    </span>
                    <span class="text-xs text-[#6E6E73]">Diajukan {{ $tradeIn->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1D1D1F] tracking-tight">
                    {{ $tradeIn->phone_model }} ({{ $tradeIn->storage_capacity }})
                </h1>
                <p class="text-xs text-[#6E6E73] mt-1">
                    Pemohon: <strong class="text-[#1D1D1F]">{{ $tradeIn->user->name ?? 'Customer' }}</strong> &bull;
                    IMEI: <span class="font-mono text-[#1D1D1F] font-semibold">{{ $tradeIn->imei ?: 'Dicek saat di toko' }}</span>
                </p>
            </div>

            <div class="flex flex-col md:items-end gap-2">
                <span class="glass-badge {{ $tradeIn->status_badge_class }}">
                    <span class="glass-badge-dot"></span>
                    <span>{{ $tradeIn->status_label }}</span>
                </span>
                <span class="text-[11px] text-[#6E6E73]">
                    Metode Pencairan: <strong class="text-[#1D1D1F]">{{ $tradeIn->isVoucherPayout() ? 'Kupon Potongan Belanja' : 'Transfer Rekening Bank' }}</strong>
                </span>
            </div>
        </div>

        <!-- 5-Step Status Timeline -->
        @php
            $steps = [
                'submitted' => ['label' => 'Pengajuan Dibuat', 'icon' => 'file-text'],
                'unit_received' => ['label' => 'Unit Diterima Toko', 'icon' => 'package-check'],
                'inspected' => ['label' => 'Inspeksi Teknisi', 'icon' => 'search-check'],
                'decision' => [
                    'label' => $tradeIn->status === 'rejected' ? 'Pengajuan Ditolak' : 'Penawaran Disetujui',
                    'icon' => $tradeIn->status === 'rejected' ? 'x-circle' : 'check-circle-2',
                ],
                'paid_out' => [
                    'label' => $tradeIn->isVoucherPayout() ? 'Voucher Aktif' : 'Dana Ditransfer',
                    'icon' => 'wallet',
                ],
            ];

            $statusRank = match ($tradeIn->status) {
                'submitted' => 1,
                'unit_received' => 2,
                'inspected' => 3,
                'approved', 'rejected' => 4,
                'paid_out' => 5,
                default => 1,
            };
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @php $idx = 0; @endphp
            @foreach($steps as $key => $stepInfo)
                @php
                    $idx++;
                    $isPassed = $idx <= $statusRank;
                    $isRejectedNode = ($idx === 4 && $tradeIn->status === 'rejected');
                @endphp
                <div class="p-3.5 rounded-2xl border {{ $isRejectedNode ? 'border-red-200 bg-red-50' : ($isPassed ? 'border-emerald-200 bg-emerald-50' : 'border-[#E5E5EA] bg-[#F5F5F7] opacity-70') }} flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $isRejectedNode ? 'bg-[#E30000] text-white' : ($isPassed ? 'bg-[#008009] text-white' : 'bg-white border border-[#E5E5EA] text-[#6E6E73]') }}">
                        <i data-lucide="{{ $stepInfo['icon'] }}" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] text-[#6E6E73] block">Tahap {{ $idx }}</span>
                        <span class="text-xs font-bold text-[#1D1D1F] truncate block">{{ $stepInfo['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Price Comparison & Payout Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        <!-- Left: Valuation & Final Offer Comparison -->
        <div class="md:col-span-7 bg-white rounded-3xl p-6 border border-[#E5E5EA] shadow-sm space-y-5">
            <h2 class="text-sm font-bold text-[#1D1D1F] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-[#008009]"></i>
                <span>Rincian Penawaran Nilai Unit</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA]">
                    <span class="text-[11px] text-[#6E6E73] block">Estimasi Awal Sistem</span>
                    <span class="text-xl font-black text-[#1D1D1F] mt-1 block">
                        Rp {{ number_format($tradeIn->estimated_price, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-[#6E6E73] mt-1 block">Berdasarkan form kuesioner mandiri</span>
                </div>

                <div class="p-4 rounded-2xl {{ $tradeIn->final_price ? 'bg-emerald-50 border border-emerald-200' : 'bg-[#F5F5F7] border border-[#E5E5EA]' }}">
                    <span class="text-[11px] {{ $tradeIn->final_price ? 'text-[#008009] font-bold' : 'text-[#6E6E73]' }} block">
                        Harga Penawaran Final Toko
                    </span>
                    @if($tradeIn->final_price)
                        <span class="text-xl font-black text-[#008009] mt-1 block">
                            Rp {{ number_format($tradeIn->final_price, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-[#6E6E73] mt-1 block">Hasil verifikasi fisik teknisi</span>
                    @else
                        <span class="text-sm font-bold text-[#BF4800] mt-2 block">
                            Menunggu Inspeksi Fisik
                        </span>
                        <span class="text-[10px] text-[#6E6E73] mt-1 block">Ditetapkan setelah unit dicek teknisi</span>
                    @endif
                </div>
            </div>

            <!-- Physical & Functional Specs Table -->
            <div class="space-y-2.5 pt-3 border-t border-[#E5E5EA] text-xs">
                <div class="flex justify-between py-1.5 border-b border-[#E5E5EA]/70">
                    <span class="text-[#6E6E73]">Kondisi Fisik Bodi:</span>
                    <span class="text-[#1D1D1F] font-semibold">{{ $tradeIn->physical_condition }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-[#E5E5EA]/70">
                    <span class="text-[#6E6E73]">Kondisi Layar & Fungsi:</span>
                    <span class="text-[#1D1D1F] font-semibold">{{ $tradeIn->screen_condition }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-[#E5E5EA]/70">
                    <span class="text-[#6E6E73]">Kesehatan Baterai (BH):</span>
                    <span class="text-[#008009] font-bold">{{ $tradeIn->battery_health }}%</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-[#6E6E73]">Kelengkapan Kotak & Kabel:</span>
                    <span class="text-[#1D1D1F] font-semibold">{{ $tradeIn->accessories_status }}</span>
                </div>
            </div>

            @if($tradeIn->admin_notes)
                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 space-y-1 text-xs">
                    <span class="font-bold text-[#0066CC] flex items-center gap-1.5">
                        <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                        Catatan Teknisi / Admin Rap-iPhone:
                    </span>
                    <p class="text-[#1D1D1F] leading-relaxed">{{ $tradeIn->admin_notes }}</p>
                </div>
            @endif
        </div>

        <!-- Right: Payout / Voucher Destination & Store Handover Instructions -->
        <div class="md:col-span-5 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-[#E5E5EA] shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-[#1D1D1F] uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="{{ $tradeIn->isVoucherPayout() ? 'ticket' : 'landmark' }}" class="w-4 h-4 text-[#0071E3]"></i>
                    <span>{{ $tradeIn->isVoucherPayout() ? 'Kupon Potongan Belanja' : 'Rekening Pencairan Dana' }}</span>
                </h2>

                @if($tradeIn->isVoucherPayout())
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-2">
                        <span class="text-[10px] uppercase tracking-widest text-[#008009] font-bold block">Kode Voucher Trade-In</span>
                        <span class="font-mono text-xl font-black text-[#1D1D1F] tracking-wider block">
                            {{ $tradeIn->customer_account_number }}
                        </span>
                        <span class="text-xs text-[#6E6E73] block">
                            Senilai <strong class="text-[#1D1D1F]">Rp {{ number_format($tradeIn->effective_price, 0, ',', '.') }}</strong>
                        </span>
                    </div>
                    <p class="text-xs text-[#6E6E73] leading-relaxed">
                        Tunjukkan kode di atas kepada kasir toko atau cantumkan pada catatan pesanan saat melakukan checkout unit iPhone baru di Rap-iPhone.
                    </p>
                @else
                    <div class="p-4 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-[#6E6E73]">Nama Bank:</span>
                            <span class="text-[#1D1D1F] font-bold">{{ $tradeIn->customer_bank_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#6E6E73]">Nomor Rekening:</span>
                            <span class="text-[#0066CC] font-mono font-bold">{{ $tradeIn->customer_account_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#6E6E73]">Atas Nama:</span>
                            <span class="text-[#1D1D1F] font-semibold">{{ $tradeIn->customer_account_holder }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Store Inspection Guide -->
            <div class="bg-[#F5F5F7] rounded-3xl p-6 border border-[#E5E5EA] space-y-3 text-xs">
                <h3 class="font-bold text-[#1D1D1F] text-sm flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-[#BF4800]"></i>
                    <span>Instruksi Penyerahan Unit</span>
                </h3>
                <p class="text-[#6E6E73] leading-relaxed">
                    Silakan bawa unit iPhone Anda beserta kelengkapannya ke <strong class="text-[#1D1D1F]">Store Resmi Rap-iPhone</strong> dengan menyebutkan kode pengajuan <strong class="text-[#0066CC] font-mono">{{ $tradeIn->trade_in_code }}</strong>.
                </p>
                <ul class="space-y-1.5 text-[#6E6E73] list-disc list-inside">
                    <li>Pastikan Anda telah mencadangkan (backup) data penting.</li>
                    <li>Lepaskan akun iCloud / Find My iPhone saat inspeksi dilakukan.</li>
                    <li>Proses pengecekan fisik 32 titik hanya membutuhkan waktu 15 menit.</li>
                </ul>
            </div>
        </div>

    </div>

</div>
@endsection
