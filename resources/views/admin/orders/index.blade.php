@extends('layouts.admin')

@section('page_title', 'Pesanan & Logistik')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0071E3]/15 border border-[#0071E3]/30 text-xs font-semibold text-[#5AC8FA] mb-2">
                <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                <span>Manajemen Pesanan, Resi Kurir &amp; Nota Faktur</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Pesanan &amp; Logistik</h1>
            <p class="text-xs sm:text-sm text-[#8E8E93] mt-0.5">
                Filter pesanan berdasarkan status, alokasikan IMEI unit, input nomor resi kurir (otomatis notifikasi WA &amp; Email), serta cetak nota &amp; label thermal 100&times;150 mm.
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
                            <th class="py-3.5 px-5 text-right">Aksi Operasional</th>
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
                                    <a href="{{ route('admin.orders.show', $order->order_number) }}" class="font-mono font-bold text-[#5AC8FA] hover:underline block">
                                        {{ $order->order_number }}
                                    </a>
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
                                        <p class="text-[11px] text-[#8E8E93] truncate">
                                            {{ $item->variant_details }}
                                            @if($item->allocated_imei)
                                                &middot; <span class="font-mono text-[#5AC8FA]">IMEI: {{ $item->allocated_imei }}</span>
                                            @endif
                                        </p>
                                    @endforeach
                                </td>
                                <td class="py-4 px-3">
                                    <p class="text-white font-medium">{{ $order->shipping_courier ?: '-' }}</p>
                                    @if($order->shipping_tracking_number)
                                        <p class="text-[11px] font-mono font-bold text-[#34C759]">{{ $order->shipping_tracking_number }}</p>
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
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($order->status !== 'cancelled')
                                            <button type="button"
                                                    class="glass-btn glass-btn-primary glass-btn-sm text-xs inline-flex items-center gap-1.5 btn-open-resi-modal"
                                                    data-order-number="{{ $order->order_number }}"
                                                    data-action-url="{{ route('admin.orders.shipping', $order->order_number) }}"
                                                    data-recipient="{{ $order->recipient_name }}"
                                                    data-courier="{{ $order->shipping_courier }}"
                                                    data-tracking="{{ $order->shipping_tracking_number ?? '' }}"
                                                    title="Input Nomor Resi & Kirim Notifikasi WA/Email">
                                                <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                                                <span>{{ $order->shipping_tracking_number ? 'Edit Resi' : 'Input Resi' }}</span>
                                            </button>
                                        @endif

                                        <a href="{{ route('admin.orders.show', $order->order_number) }}"
                                           class="glass-btn glass-btn-secondary glass-btn-sm text-xs inline-flex items-center gap-1.5"
                                           title="Kelola Status, Alokasi IMEI & Detail Pesanan">
                                            <i data-lucide="sliders" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                                            <span>Kelola</span>
                                        </a>

                                        <a href="{{ route('admin.orders.invoice', $order->order_number) }}" target="_blank"
                                           class="glass-btn glass-btn-secondary glass-btn-sm text-xs inline-flex items-center gap-1.5" title="Lihat & cetak nota">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                            <span>Nota</span>
                                        </a>
                                    </div>
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

{{-- Modal Input Nomor Resi Pengiriman Cepat --}}
<div id="resi-quick-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-md p-4">
    <div class="glass-card-elevated rounded-3xl border border-white/15 max-w-md w-full p-6 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-white/10">
            <div>
                <span class="text-[10px] font-bold text-[#5AC8FA] uppercase tracking-wider">Logistik &amp; Otomasi Notifikasi</span>
                <h3 class="text-base font-bold text-white mt-0.5">
                    Input Resi Pengiriman <span id="modal-order-number" class="font-mono text-[#5AC8FA]"></span>
                </h3>
            </div>
            <button type="button" id="btn-close-resi-modal" class="text-[#8E8E93] hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="resi-quick-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="p-3 rounded-xl bg-white/5 border border-white/10 text-xs flex items-center justify-between">
                <span class="text-[#8E8E93]">Penerima Paket:</span>
                <span id="modal-recipient-name" class="font-bold text-white"></span>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-white block">Kurir Ekspedisi <span class="text-[#FF453A]">*</span></label>
                <select name="shipping_courier" id="modal-shipping-courier" required class="glass-input text-xs w-full bg-[#161B22]">
                    @foreach($couriers ?? [] as $courierKey => $courierLabel)
                        <option value="{{ $courierKey }}">{{ $courierLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-white block">Nomor Resi Pengiriman (AWB) <span class="text-[#FF453A]">*</span></label>
                <input type="text" name="shipping_tracking_number" id="modal-tracking-number" required
                       placeholder="Contoh: JNE-8899001122 / SICEPAT-001122"
                       class="glass-input text-xs font-mono font-bold text-[#34C759] w-full uppercase">
            </div>

            <div class="p-3 rounded-xl bg-[#34C759]/10 border border-[#34C759]/30 text-[11px] text-[#E5E5EA] flex items-start gap-2.5">
                <i data-lucide="send" class="w-4 h-4 text-[#34C759] shrink-0 mt-0.5"></i>
                <span>
                    Menyimpan nomor resi akan otomatis mengubah status pesanan menjadi <strong>DIKIRIM (Shipped)</strong> dan memicu pengiriman notifikasi <strong>WhatsApp (Fonnte)</strong> &amp; <strong>Email</strong> ke pelanggan.
                </span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" id="btn-cancel-resi-modal" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">Batal</button>
                <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm text-xs font-bold flex items-center gap-1.5">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                    <span>Simpan Resi &amp; Kirim Notifikasi</span>
                </button>
            </div>
        </form>
    </div>
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

        // Modal Input Resi Cepat
        const modal = document.getElementById('resi-quick-modal');
        const form = document.getElementById('resi-quick-form');
        const lblOrderNo = document.getElementById('modal-order-number');
        const lblRecipient = document.getElementById('modal-recipient-name');
        const selCourier = document.getElementById('modal-shipping-courier');
        const inpTracking = document.getElementById('modal-tracking-number');

        function closeResiModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.querySelectorAll('.btn-open-resi-modal').forEach(btn => {
            btn.addEventListener('click', function () {
                const orderNo = this.getAttribute('data-order-number');
                const actionUrl = this.getAttribute('data-action-url');
                const recipient = this.getAttribute('data-recipient');
                const courier = this.getAttribute('data-courier') || 'JNE Express';
                const tracking = this.getAttribute('data-tracking') || '';

                form.action = actionUrl;
                lblOrderNo.textContent = '#' + orderNo;
                lblRecipient.textContent = recipient;
                inpTracking.value = tracking;

                let matched = false;
                Array.from(selCourier.options).forEach(opt => {
                    if (courier.toLowerCase().includes(opt.value.toLowerCase())) {
                        opt.selected = true;
                        matched = true;
                    }
                });
                if (!matched && courier) {
                    const customOpt = new Option(courier, courier, true, true);
                    selCourier.add(customOpt);
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => inpTracking.focus(), 50);
            });
        });

        document.getElementById('btn-close-resi-modal')?.addEventListener('click', closeResiModal);
        document.getElementById('btn-cancel-resi-modal')?.addEventListener('click', closeResiModal);
        modal?.addEventListener('click', function (e) {
            if (e.target === modal) closeResiModal();
        });
    });
</script>
@endpush
