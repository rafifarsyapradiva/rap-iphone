@extends('layouts.admin')

@section('title', 'Pesanan & Logistik — Rap-iPhone Admin')
@section('page_title', 'Pesanan & Logistik')

@section('content')
<x-admin.page-header
    meta="Manajemen Transaksi & Pemenuhan Kurir"
    icon="truck"
    title="Pesanan &amp; Logistik"
    subtitle="Master Trade Ledger transaksi pesanan, alokasi IMEI fisik unit, nomor resi ekspedisi, serta penerbitan nota & label thermal resmi.">
</x-admin.page-header>

@if($errors->any())
    <div class="adm-alert adm-tone-red" role="alert">
        <i data-lucide="alert-triangle"></i>
        <div class="space-y-0.5">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif

{{-- Metrik Status Pesanan Statement Figures --}}
@php
    $cards = [
        ['label' => 'Menunggu bayar', 'value' => $metrics['pending'], 'icon' => 'clock', 'tone' => 'amber', 'hint' => 'Belum ada pembayaran', 'status' => 'pending'],
        ['label' => 'Perlu diproses', 'value' => $metrics['to_process'], 'icon' => 'package-check', 'tone' => 'green', 'hint' => 'Lunas & siap dikemas', 'status' => 'paid'],
        ['label' => 'Dalam pengiriman', 'value' => $metrics['shipped'], 'icon' => 'truck', 'tone' => 'purple', 'hint' => 'Sudah diserahkan kurir', 'status' => 'shipped'],
        ['label' => 'Transaksi selesai', 'value' => $metrics['completed'], 'icon' => 'badge-check', 'tone' => 'blue', 'hint' => 'Diterima pelanggan', 'status' => 'completed'],
    ];
@endphp
<div class="adm-stat-grid cols-4">
    @foreach($cards as $card)
        <x-admin.stat-card
            :label="$card['label']"
            :value="$card['value']"
            :icon="$card['icon']"
            :tone="$card['tone']"
            :hint="$card['hint']"
            :href="route('admin.orders.index', array_filter(['status' => $card['status'], 'q' => $search]))"
            :active="$statusFilter === $card['status']" />
    @endforeach
</div>

<x-admin.card flush>
    {{-- Filter & Pencarian --}}
    <div class="adm-card-body" style="padding-bottom: 1rem;">
        @php
            $statusTabs = [
                'all' => ['Semua', $metrics['total']],
                'pending' => ['Pending', $counts['pending'] ?? 0],
                'paid' => ['Lunas', $counts['paid'] ?? 0],
                'processing' => ['Diproses', $counts['processing'] ?? 0],
                'shipped' => ['Dikirim', $counts['shipped'] ?? 0],
                'completed' => ['Selesai', $counts['completed'] ?? 0],
                'cancelled' => ['Batal', $counts['cancelled'] ?? 0],
            ];
        @endphp
        <div class="adm-toolbar">
            <div class="adm-tabs">
                @foreach($statusTabs as $tabKey => [$tabLabel, $tabCount])
                    <a href="{{ route('admin.orders.index', array_filter(['status' => $tabKey !== 'all' ? $tabKey : null, 'q' => $search])) }}"
                       class="adm-tab {{ $statusFilter === $tabKey ? 'is-active' : '' }}">
                        {{ $tabLabel }}
                        @if($tabCount > 0)
                            <span class="adm-tab-count">({{ $tabCount }})</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <form action="{{ route('admin.orders.index') }}" method="GET" class="adm-filters">
                @if($statusFilter !== 'all')
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                <div class="adm-search">
                    <i data-lucide="search"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari no. nota, penerima, resi..." class="adm-input" aria-label="Cari pesanan">
                </div>
                <button type="submit" class="adm-btn adm-btn-sm">Cari</button>
                @if($search !== '' || $statusFilter !== 'all')
                    <a href="{{ route('admin.orders.index') }}" class="adm-btn adm-btn-sm text-[var(--adm-tone-red-ink)]">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel Pesanan + Form Cetak Massal --}}
    <form id="bulk-print-form" method="POST" action="{{ route('admin.orders.bulk.invoices') }}" target="_blank">
        @csrf

        {{-- Toolbar Aksi Massal (Muncul saat baris dipilih) --}}
        <div id="bulk-toolbar" class="sticky top-20 z-20 mx-4 mb-4 p-3 bg-[var(--adm-paper-subtle)] border border-[var(--adm-rule-border)] rounded-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm transition-all" style="display:none">
            <div class="flex items-center gap-3 text-xs">
                <span class="w-7 h-7 rounded-xs bg-[var(--adm-ink)] text-[var(--adm-ink-reverse)] flex items-center justify-center font-bold">
                    <i data-lucide="check-square" class="w-3.5 h-3.5"></i>
                </span>
                <div>
                    <p class="font-medium text-[var(--adm-ink)]"><span id="selected-count">0</span> pesanan dipilih</p>
                    <p class="text-[11px] text-[var(--adm-ink-muted)]">Maks. {{ $bulkMax }} per cetak &middot; Label hanya untuk status Lunas / Diproses / Dikirim</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" formaction="{{ route('admin.orders.bulk.invoices') }}" data-bulk-action
                        class="adm-btn adm-btn-primary adm-btn-sm text-xs flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>Cetak Invoice</span>
                </button>
                <button type="submit" formaction="{{ route('admin.orders.bulk.labels') }}" data-bulk-action
                        class="adm-btn adm-btn-sm text-xs flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                    <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                    <span>Cetak Label Pengiriman</span>
                </button>
            </div>
        </div>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th scope="col" style="width:2.5rem;padding-left:1.25rem;padding-right:0.5rem">
                            <input type="checkbox" id="select-all" class="w-4 h-4 rounded-xs accent-[var(--adm-ink)] cursor-pointer" aria-label="Pilih semua pesanan">
                        </th>
                        <th scope="col">No. Nota &amp; Waktu</th>
                        <th scope="col">Pelanggan</th>
                        <th scope="col">Unit Dipesan</th>
                        <th scope="col">Kurir &amp; Resi</th>
                        <th scope="col" class="adm-num">Total Tagihan</th>
                        <th scope="col" style="text-align:center">Status</th>
                        <th scope="col" class="adm-num" style="padding-right:1.25rem">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td style="padding-left:1.25rem;padding-right:0.5rem">
                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" data-order-checkbox
                                       class="w-4 h-4 rounded-xs accent-[var(--adm-ink)] cursor-pointer" aria-label="Pilih {{ $order->order_number }}">
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->order_number) }}" class="adm-mono font-medium hover:underline block">
                                    {{ $order->order_number }}
                                </a>
                                <span class="adm-cell-sub">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td>
                                <p class="adm-cell-title">{{ $order->recipient_name }}</p>
                                <p class="adm-cell-sub adm-mono">{{ $order->recipient_phone }}</p>
                                <p class="adm-cell-sub">{{ $order->user->email ?? '-' }}</p>
                            </td>
                            <td style="max-width:240px">
                                @foreach($order->items as $item)
                                    <p class="adm-cell-title truncate">{{ $item->quantity }}x {{ $item->product_name }}</p>
                                    <p class="adm-cell-sub truncate">
                                        {{ $item->variant_details }}
                                        @if($item->allocated_imei)
                                            &middot; <span class="adm-mono text-[var(--adm-ink-muted)]">IMEI: {{ $item->allocated_imei }}</span>
                                        @endif
                                    </p>
                                @endforeach
                            </td>
                            <td>
                                <p class="adm-cell-title">{{ $order->shipping_courier ?: '-' }}</p>
                                @if($order->shipping_tracking_number)
                                    <p class="adm-mono text-[11px] font-medium text-[var(--adm-tone-green-ink)]">{{ $order->shipping_tracking_number }}</p>
                                @else
                                    <p class="adm-cell-sub text-[var(--adm-ink-faint)]">Resi belum diinput</p>
                                @endif
                            </td>
                            <td class="adm-num">
                                <span class="adm-cell-title">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span>
                            </td>
                            <td style="text-align:center">
                                <x-order-badge :status="$order->status" />
                            </td>
                            <td class="adm-num" style="padding-right:1.25rem">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($order->status !== 'cancelled')
                                        <button type="button"
                                                class="adm-btn adm-btn-primary adm-btn-sm text-xs inline-flex items-center gap-1.5 btn-open-resi-modal"
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
                                       class="adm-btn adm-btn-sm text-xs inline-flex items-center gap-1.5"
                                       title="Kelola Status, Alokasi IMEI & Detail Pesanan">
                                        <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                        <span>Kelola</span>
                                    </a>

                                    <a href="{{ route('admin.orders.invoice', $order->order_number) }}" target="_blank" rel="noopener"
                                       class="adm-btn adm-btn-sm text-xs inline-flex items-center gap-1.5" title="Lihat & cetak nota">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        <span>Nota</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-admin.empty-state icon="inbox" title="Tidak ada pesanan yang ditemukan." text="Ubah filter status atau sesuaikan kata kunci pencarian pada toolbar." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="adm-pagination">
                {{ $orders->links() }}
            </div>
        @endif
    </form>
</x-admin.card>

{{-- Modal Input Nomor Resi Pengiriman Cepat --}}
<div id="resi-quick-modal" class="glass-modal-backdrop" aria-hidden="true" style="display:none">
    <div class="glass-modal max-w-md p-6 space-y-5 bg-[var(--adm-surface)] border border-[var(--adm-rule-border)] rounded-xs">
        <div class="flex items-center justify-between pb-3 border-b border-[var(--adm-rule-border)]">
            <div>
                <p class="text-xs text-[var(--adm-ink-muted)]">Logistik &amp; Otomasi Notifikasi</p>
                <h3 class="text-base font-serif font-medium text-[var(--adm-ink)] mt-0.5">
                    Input Resi Pengiriman <span id="modal-order-number" class="adm-mono text-[var(--adm-ink)]"></span>
                </h3>
            </div>
            <button type="button" id="btn-close-resi-modal" class="adm-icon-btn" aria-label="Tutup modal">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form id="resi-quick-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="p-3 rounded-xs bg-[var(--adm-paper-subtle)] border border-[var(--adm-rule-border)] text-xs flex items-center justify-between">
                <span class="text-[var(--adm-ink-muted)]">Penerima Paket:</span>
                <span id="modal-recipient-name" class="font-medium text-[var(--adm-ink)]"></span>
            </div>

            <div class="adm-field">
                <label class="adm-label" for="modal-shipping-courier">Kurir Ekspedisi <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
                <select name="shipping_courier" id="modal-shipping-courier" required class="adm-select">
                    @foreach($couriers ?? [] as $courierKey => $courierLabel)
                        <option value="{{ $courierKey }}">{{ $courierLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field">
                <label class="adm-label" for="modal-tracking-number">Nomor Resi Pengiriman (AWB) <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
                <input type="text" name="shipping_tracking_number" id="modal-tracking-number" required
                       placeholder="Contoh: JNE-8899001122 / SICEPAT-001122"
                       class="adm-input adm-mono font-medium uppercase">
            </div>

            <div class="p-3 rounded-xs bg-[var(--adm-paper-subtle)] border border-[var(--adm-rule-border)] text-xs text-[var(--adm-ink)] flex items-start gap-2.5">
                <i data-lucide="send" class="w-4 h-4 text-[var(--adm-ink-muted)] shrink-0 mt-0.5"></i>
                <span class="text-[11px] leading-relaxed text-[var(--adm-ink-muted)]">
                    Menyimpan nomor resi akan otomatis mengubah status pesanan menjadi <strong>DIKIRIM (Shipped)</strong> dan memicu pengiriman notifikasi <strong>WhatsApp (Fonnte)</strong> &amp; <strong>Email</strong> ke pelanggan.
                </span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" id="btn-cancel-resi-modal" class="adm-btn adm-btn-sm text-xs">Batal</button>
                <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm text-xs flex items-center gap-1.5">
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
        const bulkToolbar = document.getElementById('bulk-toolbar');
        const maxSelected = {{ $bulkMax }};

        function refresh() {
            const selected = checkboxes.filter(cb => cb.checked).length;
            if (counter) counter.textContent = selected;
            actions.forEach(btn => btn.disabled = selected === 0 || selected > maxSelected);
            if (bulkToolbar) {
                bulkToolbar.style.display = selected > 0 ? 'flex' : 'none';
            }
            if (selectAll) {
                selectAll.checked = checkboxes.length > 0 && selected === checkboxes.length;
                selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
            }
        }

        selectAll?.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            refresh();
        });

        checkboxes.forEach(cb => cb.addEventListener('change', refresh));
        refresh();

        // Resi quick modal
        const modal = document.getElementById('resi-quick-modal');
        const form = document.getElementById('resi-quick-form');
        const modalOrderNum = document.getElementById('modal-order-number');
        const modalRecipient = document.getElementById('modal-recipient-name');
        const modalCourier = document.getElementById('modal-shipping-courier');
        const modalTracking = document.getElementById('modal-tracking-number');
        const btnClose = document.getElementById('btn-close-resi-modal');
        const btnCancel = document.getElementById('btn-cancel-resi-modal');

        document.querySelectorAll('.btn-open-resi-modal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const orderNum = this.getAttribute('data-order-number');
                const actionUrl = this.getAttribute('data-action-url');
                const recipient = this.getAttribute('data-recipient');
                const courier = this.getAttribute('data-courier');
                const tracking = this.getAttribute('data-tracking');

                if (modalOrderNum) modalOrderNum.textContent = '#' + orderNum;
                if (modalRecipient) modalRecipient.textContent = recipient;
                if (modalCourier) modalCourier.value = courier || modalCourier.options[0]?.value || '';
                if (modalTracking) modalTracking.value = tracking || '';
                if (form) form.action = actionUrl;

                if (modal) {
                    modal.style.display = 'flex';
                    modal.setAttribute('aria-hidden', 'false');
                }
            });
        });

        function closeModal() {
            if (modal) {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
            }
        }

        btnClose?.addEventListener('click', closeModal);
        btnCancel?.addEventListener('click', closeModal);
        modal?.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
    });
</script>
@endpush
