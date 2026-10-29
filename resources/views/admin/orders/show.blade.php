@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number . ' — Rap-iPhone Admin')
@section('page_title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<x-admin.page-header
    :eyebrow="'Dibuat pada ' . $order->created_at->format('d M Y, H:i') . ' WIB'"
    icon="clock"
    :title="'Pesanan a.n. ' . $order->recipient_name"
    subtitle="Rincian unit pesanan, alokasi nomor IMEI fisik, kurir pengiriman, serta pembaruan status transaksi.">
    <x-slot:actions>
        <a href="{{ route('admin.orders.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i><span>Kembali ke Daftar</span>
        </a>
        <a href="{{ route('admin.orders.invoice', $order->order_number) }}" target="_blank" rel="noopener"
           class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="printer" class="w-3.5 h-3.5 text-[#0066CC]"></i>
            <span>Cetak Nota PDF</span>
        </a>
        <a href="{{ route('checkout.success', $order->order_number) }}" target="_blank" rel="noopener"
           class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
            <span>Tampilan Pelanggan</span>
        </a>
    </x-slot:actions>
</x-admin.page-header>

@if($errors->any())
    <div class="adm-alert adm-tone-red" role="alert">
        <i data-lucide="alert-octagon"></i>
        <div class="space-y-0.5">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    {{-- =====================================================================
         KOLOM KIRI (7 COLS): Rincian Unit, Alokasi IMEI, & Info Pelanggan
         ===================================================================== --}}
    <div class="lg:col-span-7 space-y-6">

        {{-- 1. Rincian Unit Dipesan & Alokasi Nomor IMEI --}}
        <x-admin.card title="Rincian Unit iPhone & Alokasi IMEI" icon="smartphone"
            description="Alokasikan nomor IMEI fisik unit sebelum paket diserahkan ke kurir ekspedisi.">
            <x-slot:actions>
                <x-order-badge :status="$order->status" />
            </x-slot:actions>

            <form action="{{ route('admin.orders.status', $order->order_number) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="{{ $order->status }}">

                <div class="divide-y divide-[#E5E5EA]">
                    @foreach($order->items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="adm-cell-title font-bold text-sm">{{ $item->product_name }}</p>
                                    <p class="adm-cell-sub">Varian: <strong class="text-[#1D1D1F]">{{ $item->variant_details }}</strong> &middot; Qty: {{ $item->quantity }}</p>
                                    @if($item->variant?->sku)
                                        <span class="inline-block mt-1 adm-mono text-[10px] text-[#0066CC] bg-blue-50 px-2 py-0.5 rounded border border-blue-200">SKU: {{ $item->variant->sku }}</span>
                                    @endif
                                </div>
                                <div class="text-right text-xs">
                                    <p class="adm-cell-title">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                                    <p class="adm-cell-sub">@ Rp {{ number_format((float) $item->price, 0, ',', '.') }}</p>
                                    <p class="adm-cell-sub text-[#008040] font-semibold">HPP: Rp {{ number_format((float) $item->cost_price, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 bg-[#FAFAFC] p-3 rounded-xl border border-[#E5E5EA]">
                                <label class="adm-label shrink-0 sm:w-44 text-xs">
                                    Nomor IMEI Unit Terkirim:
                                </label>
                                <input type="text"
                                       name="allocated_imei[{{ $item->id }}]"
                                       value="{{ old('allocated_imei.' . $item->id, $item->allocated_imei ?? $item->variant?->imei) }}"
                                       placeholder="Masukkan 15 digit IMEI fisik (*#06#)"
                                       class="adm-input adm-mono text-xs text-[#0066CC] font-bold">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-[#E5E5EA] space-y-2 text-xs">
                    <div class="flex justify-between text-[#6E6E73]">
                        <span>Subtotal Produk</span>
                        <span class="text-[#1D1D1F] font-medium">Rp {{ number_format((float) $order->total_items_price, 0, ',', '.') }}</span>
                    </div>
                    @if((float) $order->discount_amount > 0)
                        <div class="flex justify-between text-[#008040]">
                            <span>Potongan Diskon / Trade-In</span>
                            <span>-Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-[#6E6E73]">
                        <span>Ongkos Kirim &amp; Asuransi ({{ $order->shipping_courier }})</span>
                        <span class="text-[#008040] font-semibold">
                            {{ (float) $order->shipping_cost > 0 ? 'Rp ' . number_format((float) $order->shipping_cost, 0, ',', '.') : 'GRATIS (Proteksi Penuh)' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-[#1D1D1F] pt-2 border-t border-[#E5E5EA]">
                        <span>TOTAL TAGIHAN</span>
                        <span class="text-[#008040] font-extrabold">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm font-semibold flex items-center gap-1.5">
                        <i data-lucide="save" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                        <span>Simpan Alokasi IMEI</span>
                    </button>
                </div>
            </form>
        </x-admin.card>

        {{-- 2. Informasi Pelanggan & Pembayaran Midtrans --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-admin.card title="Penerima & Alamat" icon="map-pin">
                <div class="space-y-2 text-xs">
                    <p class="text-sm font-bold text-[#1D1D1F]">{{ $order->recipient_name }}</p>
                    <p class="adm-mono text-[#008040] font-bold">{{ $order->recipient_phone }}</p>
                    <p class="text-[#6E6E73]">{{ $order->user->email ?? '-' }}</p>
                    <p class="text-[#1D1D1F] leading-relaxed pt-2 border-t border-[#E5E5EA]">{{ $order->shipping_address }}</p>
                    @if($order->customer_notes)
                        <p class="text-[11px] italic text-[#B25000] pt-1">Catatan: "{{ $order->customer_notes }}"</p>
                    @endif
                </div>
            </x-admin.card>

            <x-admin.card title="Pembayaran Midtrans" icon="credit-card">
                <dl class="adm-kv text-xs">
                    <div>
                        <dt>Status Transaksi</dt>
                        <dd class="uppercase font-bold text-[#1D1D1F]">{{ $order->payment->transaction_status ?? 'pending' }}</dd>
                    </div>
                    <div>
                        <dt>Metode Bayar</dt>
                        <dd class="uppercase text-[#1D1D1F]">{{ str_replace('_', ' ', $order->payment->payment_type ?? 'Midtrans Snap') }}</dd>
                    </div>
                    <div>
                        <dt>ID Transaksi</dt>
                        <dd class="adm-mono text-[#0066CC]">{{ $order->payment->midtrans_transaction_id ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt>Waktu Lunas</dt>
                        <dd>{{ $order->payment?->payment_time ? \Carbon\Carbon::parse($order->payment->payment_time)->format('d M Y, H:i') : '-' }}</dd>
                    </div>
                </dl>
            </x-admin.card>
        </div>

        {{-- 3. Riwayat Log Notifikasi WhatsApp & Email --}}
        <x-admin.card title="Riwayat Notifikasi (WA & Email)" icon="bell-ring" description="Log pengiriman otomatis via WhatsApp Fonnte & SMTP.">
            <div class="space-y-2 text-xs">
                @forelse($notificationLogs as $log)
                    <div class="p-3 rounded-xl bg-[#FAFAFC] border border-[#E5E5EA] flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->channel === 'whatsapp' ? 'bg-emerald-50 text-[#008040] border border-emerald-200' : 'bg-blue-50 text-[#0066CC] border border-blue-200' }}">
                                {{ $log->channel }}
                            </span>
                            <div>
                                <p class="font-semibold text-[#1D1D1F]">{{ $log->event_name }} &rarr; <span class="adm-mono text-[#6E6E73]">{{ $log->recipient }}</span></p>
                                @if($log->error_message)
                                    <p class="text-[11px] text-[#D70015]">{{ $log->error_message }}</p>
                                @endif
                            </div>
                        </div>
                        <x-admin.status-badge :status="$log->status" />
                    </div>
                @empty
                    <p class="text-[#86868B] text-xs">Belum ada log notifikasi yang tercatat untuk pesanan ini.</p>
                @endforelse
            </div>
        </x-admin.card>

    </div>

    {{-- =====================================================================
         KOLOM KANAN (5 COLS): Input Resi Pengiriman & Update Status Pesanan
         ===================================================================== --}}
    <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">

        {{-- Panel 1: Input Resi & Pengiriman Kurir --}}
        <x-admin.card title="Input Resi &amp; Pengiriman Kurir" icon="truck"
            description="Simpan nomor resi (JNE / SiCepat / J&T) untuk otomatis memperbarui status ke Dikirim dan mengirim notifikasi WA + Email ke pelanggan.">

            <form action="{{ route('admin.orders.shipping', $order->order_number) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="adm-field">
                    <label for="shipping_courier" class="adm-label">
                        Pilih Kurir Ekspedisi <span style="color:#D70015">*</span>
                    </label>
                    <select id="shipping_courier" name="shipping_courier" required class="adm-select">
                        @php
                            $currentCourier = old('shipping_courier', $order->shipping_courier);
                            $courierMatched = false;
                        @endphp
                        @foreach($couriers as $courierKey => $courierLabel)
                            @php
                                $isSelected = str_contains(strtolower((string) $currentCourier), strtolower($courierKey));
                                if ($isSelected) {
                                    $courierMatched = true;
                                }
                            @endphp
                            <option value="{{ $courierKey }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $courierLabel }}
                            </option>
                        @endforeach
                        @if(!$courierMatched && $currentCourier)
                            <option value="{{ $currentCourier }}" selected>{{ $currentCourier }}</option>
                        @endif
                    </select>
                </div>

                <div class="adm-field">
                    <label for="shipping_tracking_number" class="adm-label">
                        Nomor Resi Pengiriman (AWB) <span style="color:#D70015">*</span>
                    </label>
                    <input type="text"
                           id="shipping_tracking_number"
                           name="shipping_tracking_number"
                           value="{{ old('shipping_tracking_number', $order->shipping_tracking_number) }}"
                           required
                           placeholder="Contoh: JNE-9988776655ID / SICEPAT-001122"
                           class="adm-input adm-mono font-bold text-[#008040] uppercase">
                </div>

                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-[#1D1D1F] flex items-start gap-2.5">
                    <i data-lucide="message-square" class="w-4 h-4 text-[#008040] shrink-0 mt-0.5"></i>
                    <span class="text-[11px] leading-relaxed">
                        Notifikasi <strong>WhatsApp (Fonnte)</strong> ke <strong class="adm-mono text-[#008040]">{{ $order->recipient_phone }}</strong> dan <strong>Email</strong> ke <strong class="adm-mono text-[#0066CC]">{{ $order->user->email ?? '-' }}</strong> akan terkirim otomatis.
                    </span>
                </div>

                <button type="submit" class="glass-btn glass-btn-primary glass-btn-md w-full font-bold flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Simpan Nomor Resi &amp; Kirim Notifikasi WA + Email</span>
                </button>
            </form>
        </x-admin.card>

        {{-- Panel 2: Pembaruan Status Pesanan Manual --}}
        <x-admin.card title="Ubah Status Pesanan" icon="refresh-cw" description="Pembaruan manual alur status pesanan (Order State Machine).">
            @php
                $statusLabels = [
                    'pending' => 'Pending (Menunggu Pembayaran Midtrans)',
                    'paid' => 'Paid (Pembayaran Lunas Terverifikasi)',
                    'processing' => 'Processing (Unit Sedang Disiapkan & QC Admin)',
                    'shipped' => 'Shipped (Paket Telah Dikirim Ekspedisi)',
                    'completed' => 'Completed (Paket Diterima Pelanggan / Selesai)',
                    'cancelled' => 'Cancelled (Pesanan Dibatalkan)',
                ];
            @endphp

            <form action="{{ route('admin.orders.status', $order->order_number) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="adm-field">
                    <label for="order_status_select" class="adm-label">Pilih Status Pesanan</label>
                    <select id="order_status_select" name="status" class="adm-select">
                        @foreach($statusLabels as $stKey => $stLabel)
                            <option value="{{ $stKey }}" {{ $order->status === $stKey ? 'selected' : '' }}>
                                {{ $stLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="glass-btn glass-btn-secondary glass-btn-md w-full font-bold flex items-center justify-center gap-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-[#0066CC]"></i>
                    <span>Perbarui Status Pesanan</span>
                </button>
            </form>
        </x-admin.card>

    </div>

</div>
@endsection
