@extends('layouts.customer')

@section('title', 'Detail Pengajuan Trade-In ' . $tradeIn->trade_in_code . ' — Rap-iPhone')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Top Back Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('trade-in.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[var(--cust-muted)] hover:text-[var(--cust-ink)] transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Formulir Tukar Tambah</span>
        </a>

        <div class="flex items-center gap-2">
            @if(auth()->check() && auth()->user()->hasAdminAccess())
                <a href="{{ route('admin.trade-ins.show', $tradeIn->trade_in_code) }}" class="cust-btn cust-btn-secondary cust-btn-sm text-xs">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-[var(--cust-accent)]"></i>
                    <span>Panel Inspeksi Admin</span>
                </a>
            @endif
            <a href="{{ route('catalog.index') }}" class="cust-btn cust-btn-primary cust-btn-sm text-xs">
                <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                <span>Katalog iPhone Baru</span>
            </a>
        </div>
    </div>

    <!-- Main Status & Code Header Card -->
    <div class="cust-card p-6 sm:p-8 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[var(--cust-border)]">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-[var(--cust-surface-muted)] text-[var(--cust-accent)] border border-[var(--cust-border)]">
                        {{ $tradeIn->trade_in_code }}
                    </span>
                    <span class="text-xs text-[var(--cust-muted)]">Diajukan {{ $tradeIn->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl text-[var(--cust-ink)] tracking-tight">
                    {{ $tradeIn->phone_model }} ({{ $tradeIn->storage_capacity }})
                </h1>
                <p class="text-xs text-[var(--cust-muted)] mt-1">
                    Pemohon: <strong class="text-[var(--cust-ink)]">{{ $tradeIn->user->name ?? 'Customer' }}</strong> &bull;
                    IMEI: <span class="font-mono text-[var(--cust-ink)] font-semibold">{{ $tradeIn->imei ?: 'Diverifikasi saat di store' }}</span>
                </p>
            </div>

            <div class="flex flex-col md:items-end gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $tradeIn->status_badge_class }}">
                    {{ $tradeIn->status_label }}
                </span>
                <span class="text-[11px] text-[var(--cust-muted)]">
                    Metode Pencairan: <strong class="text-[var(--cust-ink)]">{{ $tradeIn->isVoucherPayout() ? 'Kupon Potongan Belanja' : 'Transfer Rekening Bank' }}</strong>
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
                <div class="p-3.5 rounded-2xl border {{ $isRejectedNode ? 'border-rose-200 bg-rose-50/70' : ($isPassed ? 'border-emerald-200 bg-emerald-50/70' : 'border-[var(--cust-border)] bg-[var(--cust-surface-muted)] opacity-70') }} flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $isRejectedNode ? 'bg-rose-600 text-white' : ($isPassed ? 'bg-emerald-700 text-white' : 'bg-[var(--cust-surface)] border border-[var(--cust-border)] text-[var(--cust-muted)]') }}">
                        <i data-lucide="{{ $stepInfo['icon'] }}" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] text-[var(--cust-muted)] block">Tahap {{ $idx }}</span>
                        <span class="text-xs font-bold text-[var(--cust-ink)] truncate block">{{ $stepInfo['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Price Comparison & Payout Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        <!-- Left: Valuation & Final Offer Comparison -->
        <div class="md:col-span-7 cust-card p-6 space-y-5">
            <h2 class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-emerald-700"></i>
                <span>Rincian Penawaran Nilai Unit</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)]">
                    <span class="text-[11px] text-[var(--cust-muted)] block">Estimasi Awal Sistem</span>
                    <span class="font-serif text-xl text-[var(--cust-ink)] mt-1 block">
                        Rp {{ number_format($tradeIn->estimated_price, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-[var(--cust-muted)] mt-1 block">Berdasarkan isian kuesioner mandiri</span>
                </div>

                <div class="p-4 rounded-2xl {{ $tradeIn->final_price ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-[var(--cust-surface-muted)] border border-[var(--cust-border)]' }}">
                    <span class="text-[11px] {{ $tradeIn->final_price ? 'text-emerald-800 font-bold' : 'text-[var(--cust-muted)]' }} block">
                        Harga Penawaran Final Toko
                    </span>
                    @if($tradeIn->final_price)
                        <span class="font-serif text-xl text-emerald-800 mt-1 block">
                            Rp {{ number_format($tradeIn->final_price, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-emerald-700 mt-1 block">Hasil verifikasi fisik teknisi</span>
                    @else
                        <span class="text-sm font-bold text-amber-800 mt-2 block">
                            Menunggu Inspeksi Fisik
                        </span>
                        <span class="text-[10px] text-[var(--cust-muted)] mt-1 block">Ditetapkan setelah unit dicek di toko</span>
                    @endif
                </div>
            </div>

            <!-- Physical & Functional Specs Table -->
            <div class="space-y-2.5 pt-3 border-t border-[var(--cust-border)] text-xs">
                <div class="flex justify-between py-1.5 border-b border-[var(--cust-border)]">
                    <span class="text-[var(--cust-muted)]">Kondisi Fisik Bodi:</span>
                    <span class="text-[var(--cust-ink)] font-semibold">{{ $tradeIn->physical_condition }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-[var(--cust-border)]">
                    <span class="text-[var(--cust-muted)]">Kondisi Layar & Fungsi:</span>
                    <span class="text-[var(--cust-ink)] font-semibold">{{ $tradeIn->screen_condition }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-[var(--cust-border)]">
                    <span class="text-[var(--cust-muted)]">Kesehatan Baterai (BH):</span>
                    <span class="text-emerald-700 font-bold">{{ $tradeIn->battery_health }}%</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-[var(--cust-muted)]">Kelengkapan Kotak & Kabel:</span>
                    <span class="text-[var(--cust-ink)] font-semibold">{{ $tradeIn->accessories_status }}</span>
                </div>
            </div>

            @if($tradeIn->admin_notes)
                <div class="p-4 rounded-2xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] space-y-1 text-xs">
                    <span class="font-bold text-[var(--cust-ink)] flex items-center gap-1.5">
                        <i data-lucide="clipboard-check" class="w-4 h-4 text-[var(--cust-accent)]"></i>
                        Catatan Teknisi Rap-iPhone:
                    </span>
                    <p class="text-[var(--cust-muted)] leading-relaxed">{{ $tradeIn->admin_notes }}</p>
                </div>
            @endif
        </div>

        <!-- Right: Payout / Voucher Destination & Store Handover Instructions -->
        <div class="md:col-span-5 space-y-6">
            <div class="cust-card p-6 space-y-4">
                <h2 class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="{{ $tradeIn->isVoucherPayout() ? 'ticket' : 'landmark' }}" class="w-4 h-4 text-[var(--cust-accent)]"></i>
                    <span>{{ $tradeIn->isVoucherPayout() ? 'Kupon Potongan Belanja' : 'Rekening Pencairan Dana' }}</span>
                </h2>

                @if($tradeIn->isVoucherPayout())
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-center space-y-2">
                        <span class="text-[10px] uppercase tracking-widest text-emerald-800 font-bold block">Kode Voucher Trade-In</span>
                        <span class="font-mono text-xl font-bold text-[var(--cust-ink)] tracking-wider block">
                            {{ $tradeIn->customer_account_number }}
                        </span>
                        <span class="text-xs text-[var(--cust-muted)] block">
                            Senilai <strong class="text-[var(--cust-ink)]">Rp {{ number_format($tradeIn->effective_price, 0, ',', '.') }}</strong>
                        </span>
                    </div>
                    <p class="text-xs text-[var(--cust-muted)] leading-relaxed">
                        Tunjukkan kode di atas kepada kasir toko atau cantumkan pada catatan pesanan saat melakukan checkout unit iPhone baru di Rap-iPhone.
                    </p>
                @else
                    <div class="p-4 rounded-2xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Nama Bank:</span>
                            <span class="text-[var(--cust-ink)] font-bold">{{ $tradeIn->customer_bank_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Nomor Rekening:</span>
                            <span class="text-[var(--cust-accent)] font-mono font-bold">{{ $tradeIn->customer_account_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Atas Nama:</span>
                            <span class="text-[var(--cust-ink)] font-semibold">{{ $tradeIn->customer_account_holder }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Store Inspection Guide -->
            <div class="cust-card p-6 bg-[var(--cust-surface-muted)] space-y-3 text-xs">
                <h3 class="font-bold text-[var(--cust-ink)] text-sm flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-amber-700"></i>
                    <span>Instruksi Penyerahan Unit</span>
                </h3>
                <p class="text-[var(--cust-muted)] leading-relaxed">
                    Bawa unit iPhone Anda beserta kelengkapannya ke <strong class="text-[var(--cust-ink)]">Store Resmi Rap-iPhone</strong> dengan menyebutkan kode <strong class="text-[var(--cust-accent)] font-mono">{{ $tradeIn->trade_in_code }}</strong>.
                </p>
                <ul class="space-y-1.5 text-[var(--cust-muted)] list-disc list-inside">
                    <li>Cadangkan (backup) seluruh data pribadi Anda sebelum ke toko.</li>
                    <li>Nonaktifkan iCloud / Find My iPhone saat verifikasi berlangsung.</li>
                    <li>Pengecekan fisik 32 titik diselesaikan dalam waktu 15 menit.</li>
                </ul>
            </div>
        </div>

    </div>

</div>
@endsection
