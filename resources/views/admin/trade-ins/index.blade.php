@extends('layouts.admin')

@section('page_title', 'Antrean & Inspeksi Tukar Tambah (Trade-In)')

@section('content')
<div class="space-y-6">

    <!-- Top Header & Operational Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#34C759]/15 border border-[#34C759]/30 text-xs font-semibold text-[#34C759] mb-2">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span>Modul Verifikasi & Inspeksi Teknisi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Antrean Pengajuan Trade-In</h1>
            <p class="text-xs sm:text-sm text-[#8E8E93] mt-0.5">
                Kelola antrean jual & tukar tambah iPhone pelanggan, catat hasil inspeksi fisik teknisi, dan tetapkan penawaran harga final.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('trade-in.index') }}" target="_blank" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Buka Wizard Customer</span>
            </a>
        </div>
    </div>

    <!-- 4 Frosted Glass Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-[#8E8E93] uppercase tracking-wider">Menunggu Unit</p>
                    <p class="text-2xl font-black text-white mt-1">{{ $metrics['submitted'] ?? 0 }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-[#FF9F0A]/15 border border-[#FF9F0A]/30 flex items-center justify-center text-[#FF9F0A]">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-[#8E8E93] mt-3">Pengajuan baru dari form online</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-[#8E8E93] uppercase tracking-wider">Dalam Inspeksi</p>
                    <p class="text-2xl font-black text-[#5AC8FA] mt-1">{{ $metrics['inspecting'] ?? 0 }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-[#0071E3]/15 border border-[#0071E3]/30 flex items-center justify-center text-[#5AC8FA]">
                    <i data-lucide="search-check" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-[#8E8E93] mt-3">Unit diterima & dicek teknisi toko</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-[#8E8E93] uppercase tracking-wider">Disetujui / Cair</p>
                    <p class="text-2xl font-black text-[#34C759] mt-1">{{ $metrics['approved'] ?? 0 }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-[#34C759]/15 border border-[#34C759]/30 flex items-center justify-center text-[#34C759]">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-[#8E8E93] mt-3">Penawaran final disetujui / selesai</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-[#8E8E93] uppercase tracking-wider">Ditolak</p>
                    <p class="text-2xl font-black text-[#FF453A] mt-1">{{ $metrics['rejected'] ?? 0 }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-[#FF453A]/15 border border-[#FF453A]/30 flex items-center justify-center text-[#FF453A]">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-[#8E8E93] mt-3">Tidak lolos verifikasi fisik / batal</p>
        </div>
    </div>

    <!-- Filter Tabs & Search Toolbar -->
    <div class="glass-card rounded-2xl p-4 border border-white/10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        @php
            $statusTabs = [
                'all' => 'Semua (' . ($metrics['total'] ?? 0) . ')',
                'submitted' => 'Submitted',
                'unit_received' => 'Diterima Toko',
                'inspected' => 'Diinspeksi',
                'approved' => 'Approved',
                'paid_out' => 'Paid Out',
                'rejected' => 'Rejected',
            ];
        @endphp

        <div class="flex flex-wrap items-center gap-1.5">
            @foreach($statusTabs as $tabKey => $tabLabel)
                <a
                    href="{{ route('admin.trade-ins.index', ['status' => $tabKey, 'q' => $search]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $statusFilter === $tabKey ? 'bg-[#0071E3] text-white shadow-md' : 'bg-white/5 text-[#8E8E93] hover:text-white hover:bg-white/10' }}"
                >
                    {{ $tabLabel }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.trade-ins.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-80">
            @if($statusFilter !== 'all')
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <div class="relative flex-grow">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari kode TI, model, IMEI, customer..."
                    class="glass-input py-2 pl-9 pr-3 text-xs w-full"
                >
                <i data-lucide="search" class="w-3.5 h-3.5 text-[#8E8E93] absolute left-3 top-2.5"></i>
            </div>
            <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                Cari
            </button>
        </form>
    </div>

    <!-- Trade-In Queue Table -->
    <div class="glass-card rounded-3xl border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5 text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider">
                        <th class="py-3.5 px-5">Kode & Waktu</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Unit & Kondisi</th>
                        <th class="py-3.5 px-4">Metode Pencairan</th>
                        <th class="py-3.5 px-4">Estimasi vs Final</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi Teknisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 text-xs">
                    @forelse($tradeIns as $item)
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            <td class="py-4 px-5">
                                <span class="font-mono font-bold text-[#5AC8FA] block">{{ $item->trade_in_code }}</span>
                                <span class="text-[11px] text-[#8E8E93]">{{ $item->created_at->format('d M Y, H:i') }}</span>
                            </td>

                            <td class="py-4 px-4">
                                <p class="font-bold text-white">{{ $item->user->name ?? 'Customer' }}</p>
                                <p class="text-[11px] text-[#8E8E93]">{{ $item->user->email ?? '-' }}</p>
                                @if($item->user && $item->user->phone_number)
                                    <p class="text-[11px] font-mono text-[#34C759]">{{ $item->user->phone_number }}</p>
                                @endif
                            </td>

                            <td class="py-4 px-4">
                                <p class="font-bold text-white">{{ $item->phone_model }} <span class="text-[#5AC8FA]">({{ $item->storage_capacity }})</span></p>
                                <p class="text-[11px] text-[#8E8E93] mt-0.5">
                                    {{ $item->physical_condition }} &bull; Layar {{ $item->screen_condition }} &bull; <span class="text-[#34C759] font-semibold">BH {{ $item->battery_health }}%</span>
                                </p>
                                @if($item->imei)
                                    <span class="text-[10px] font-mono text-[#8E8E93]">IMEI: {{ $item->imei }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-4">
                                @if($item->isVoucherPayout())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#34C759]/15 text-[#34C759] border border-[#34C759]/30">
                                        <i data-lucide="ticket" class="w-3 h-3"></i>
                                        Kupon Belanja
                                    </span>
                                    <p class="text-[11px] font-mono text-white mt-1">{{ $item->customer_account_number }}</p>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0071E3]/15 text-[#5AC8FA] border border-[#0071E3]/30">
                                        <i data-lucide="landmark" class="w-3 h-3"></i>
                                        Transfer Bank
                                    </span>
                                    <p class="text-[11px] text-white font-medium mt-1">{{ $item->customer_bank_name }}</p>
                                    <p class="text-[10px] font-mono text-[#8E8E93]">{{ $item->customer_account_number }} ({{ $item->customer_account_holder }})</p>
                                @endif
                            </td>

                            <td class="py-4 px-4">
                                <div class="space-y-0.5">
                                    <span class="text-[10px] text-[#8E8E93] block">
                                        Est: Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                                    </span>
                                    @if($item->final_price)
                                        <span class="text-xs font-black text-[#34C759] block">
                                            Final: Rp {{ number_format($item->final_price, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-[#FF9F0A] font-semibold block">
                                            Final: Belum Ditetapkan
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4 px-4">
                                <span class="glass-badge {{ $item->status_badge_class }}">
                                    <span class="glass-badge-dot"></span>
                                    <span>{{ $item->status_label }}</span>
                                </span>
                            </td>

                            <td class="py-4 px-5 text-right">
                                <a href="{{ route('admin.trade-ins.show', $item->trade_in_code) }}" class="glass-btn glass-btn-primary glass-btn-sm text-xs">
                                    <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                    <span>Inspeksi & Keputusan</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#8E8E93]">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                <p class="text-sm font-medium text-white">Tidak ada antrean pengajuan Trade-In yang ditemukan.</p>
                                <p class="text-xs mt-1">Ubah filter status atau kata kunci pencarian di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tradeIns->hasPages())
            <div class="px-6 py-4 border-t border-white/10">
                {{ $tradeIns->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
