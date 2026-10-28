@extends('layouts.admin')

@section('page_title', 'Pesanan & Logistik')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0071E3]/15 border border-[#0071E3]/30 text-xs font-semibold text-[#5AC8FA] mb-2">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Nota Faktur &amp; Label Pengiriman</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Pesanan &amp; Logistik</h1>
            <p class="text-xs sm:text-sm text-[#8E8E93] mt-0.5">
                Pantau pesanan masuk, cetak nota faktur resmi, dan label pengiriman thermal 100&times;150 mm secara massal.
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-[#FF453A]/15 border border-[#FF453A]/30 text-white flex items-start gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-[#FF453A] shrink-0"></i>
            <div class="text-sm font-medium space-y-0.5">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Metrik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label' => 'Menunggu Bayar', 'value' => $metrics['pending'], 'icon' => 'clock', 'color' => '#FF9F0A', 'hint' => 'Belum ada pembayaran'],
                ['label' => 'Perlu Diproses', 'value' => $metrics['to_process'], 'icon' => 'package-check', 'color' => '#5AC8FA', 'hint' => 'Lunas & siap dikemas'],
                ['label' => 'Dalam Pengiriman', 'value' => $metrics['shipped'], 'icon' => 'truck', 'color' => '#BF5AF2', 'hint' => 'Sudah diserahkan kurir'],
                ['label' => 'Selesai', 'value' => $metrics['completed'], 'icon' => 'badge-check', 'color' => '#34C759', 'hint' => 'Diterima customer'],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="glass-card rounded-2xl p-5 border border-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-[#8E8E93] uppercase tracking-wider">{{ $card['label'] }}</p>
                        <p class="text-2xl font-black mt-1" style="color: {{ $card['color'] }}">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center border" style="color: {{ $card['color'] }}; background: {{ $card['color'] }}22; border-color: {{ $card['color'] }}55;">
                        <i data-lucide="{{ $card['icon'] }}" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-[11px] text-[#8E8E93] mt-3">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filter & Pencarian --}}
    <div class="glass-card rounded-2xl p-4 border border-white/10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        @php
            $statusTabs = [
                'all' => 'Semua (' . $metrics['total'] . ')',
                'pending' => 'Pending',
                'paid' => 'Lunas',
                'processing' => 'Diproses',
                'shipped' => 'Dikirim',
                'completed' => 'Selesai',
                'cancelled' => 'Batal',
            ];
        @endphp
        <div class="flex flex-wrap items-center gap-1.5">
            @foreach($statusTabs as $tabKey => $tabLabel)
                <a href="{{ route('admin.orders.index', array_filter(['status' => $tabKey, 'q' => $search])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $statusFilter === $tabKey ? 'bg-[#0071E3] text-white shadow-md' : 'bg-white/5 text-[#8E8E93] hover:text-white hover:bg-white/10' }}">
                    {{ $tabLabel }}
                    @if($tabKey !== 'all' && ($counts[$tabKey] ?? 0) > 0)
                        <span class="ml-1 opacity-70">{{ $counts[$tabKey] }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-80">
            @if($statusFilter !== 'all')
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <div class="relative flex-grow">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari no. nota, penerima, resi, email..." class="glass-input py-2 pl-9 pr-3 text-xs w-full">
                <i data-lucide="search" class="w-3.5 h-3.5 text-[#8E8E93] absolute left-3 top-2.5"></i>
            </div>
            <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">Cari</button>
        </form>
    </div>

    {{-- Tabel Pesanan + Form Cetak Massal --}}
    <form id="bulk-print-form" method="POST" action="{{ route('admin.orders.bulk.invoices') }}" target="_blank">
        @csrf

        {{-- Toolbar Aksi Massal --}}
        <div id="bulk-toolbar" class="sticky top-20 z-20 mb-4 glass-card-elevated rounded-2xl px-4 py-3 border border-[#0071E3]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3 text-xs">
                <span class="w-8 h-8 rounded-lg bg-[#0071E3]/20 text-[#5AC8FA] flex items-center justify-center">
                    <i data-lucide="check-square" class="w-4 h-4"></i>
                </span>
                <div>
                    <p class="font-bold text-white"><span id="selected-count">0</span> pesanan dipilih</p>
                    <p class="text-[11px] text-[#8E8E93]">Maks. {{ $bulkMax }} per cetak &middot; Label hanya untuk status Lunas / Diproses / Dikirim</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" formaction="{{ route('admin.orders.bulk.invoices') }}" data-bulk-action
                        class="glass-btn glass-btn-primary glass-btn-sm text-xs flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>Cetak Invoice</span>
                </button>
                <button type="submit" formaction="{{ route('admin.orders.bulk.labels') }}" data-bulk-action
                        class="glass-btn glass-btn-secondary glass-btn-sm text-xs flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                    <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                    <span>Cetak Label Pengiriman</span>
                </button>
            </div>
        </div>

        <div class="glass-card rounded-3xl border border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 bg-white/5 text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider">
                            <th class="py-3.5 pl-5 pr-2 w-10">
                                <input type="checkbox" id="select-all" class="w-4 h-4 rounded accent-[#0071E3] cursor-pointer" aria-label="Pilih semua">
                            </th>
                            <th class="py-3.5 px-3">No. Nota &amp; Waktu</th>
                            <th class="py-3.5 px-3">Pelanggan</th>
                            <th class="py-3.5 px-3">Unit</th>
                            <th class="py-3.5 px-3">Kurir &amp; Resi</th>
                            <th class="py-3.5 px-3 text-right">Total</th>
                            <th class="py-3.5 px-3">Status</th>
                            <th class="py-3.5 px-5 text-right">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-xs">
                        @forelse($orders as $order)
                            <tr class="hover:bg-white/[0.03] transition-colors">
                                <td class="py-4 pl-5 pr-2">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" data-order-checkbox
                                           class="w-4 h-4 rounded accent-[#0071E3] cursor-pointer" aria-label="Pilih {{ $order->order_number }}">
                                </td>
                                <td class="py-4 px-3">
                                    <span class="font-mono font-bold text-[#5AC8FA] block">{{ $order->order_number }}</span>
                                    <span class="text-[11px] text-[#8E8E93]">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                </td>
                                <td class="py-4 px-3">
                                    <p class="font-bold text-white">{{ $order->recipient_name }}</p>
                                    <p class="text-[11px] font-mono text-[#8E8E93]">{{ $order->recipient_phone }}</p>
                                    <p class="text-[11px] text-[#8E8E93]">{{ $order->user->email ?? '-' }}</p>
                                </td>
                                <td class="py-4 px-3 max-w-[220px]">
                                    @foreach($order->items as $item)
                                        <p class="text-white truncate">{{ $item->quantity }}x {{ $item->product_name }}</p>
                                        <p class="text-[11px] text-[#8E8E93] truncate">{{ $item->variant_details }}</p>
                                    @endforeach
                                </td>
                                <td class="py-4 px-3">
                                    <p class="text-white">{{ $order->shipping_courier ?: '-' }}</p>
                                    @if($order->shipping_tracking_number)
                                        <p class="text-[11px] font-mono text-[#34C759]">{{ $order->shipping_tracking_number }}</p>
                                    @else
                                        <p class="text-[11px] text-[#8E8E93]">Resi belum diinput</p>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-right font-bold text-white whitespace-nowrap">
                                    Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-3">
                                    <x-order-badge :status="$order->status" />
                                </td>
                                <td class="py-4 px-5 text-right">
                                    <a href="{{ route('admin.orders.invoice', $order->order_number) }}" target="_blank"
                                       class="glass-btn glass-btn-secondary glass-btn-sm text-xs inline-flex items-center gap-1.5" title="Lihat & cetak nota">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        <span>Nota</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-[#8E8E93]">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                    <p class="text-sm font-medium text-white">Tidak ada pesanan yang ditemukan.</p>
                                    <p class="text-xs mt-1">Ubah filter status atau kata kunci pencarian di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = Array.from(document.querySelectorAll('[data-order-checkbox]'));
        const counter = document.getElementById('selected-count');
        const actions = document.querySelectorAll('[data-bulk-action]');
        const maxSelected = {{ $bulkMax }};

        function refresh() {
            const selected = checkboxes.filter(cb => cb.checked).length;
            counter.textContent = selected;
            actions.forEach(btn => btn.disabled = selected === 0 || selected > maxSelected);
            if (selectAll) {
                selectAll.checked = selected > 0 && selected === checkboxes.length;
                selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
            }
        }

        selectAll?.addEventListener('change', () => {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            refresh();
        });
        checkboxes.forEach(cb => cb.addEventListener('change', refresh));
        refresh();
    });
</script>
@endpush

