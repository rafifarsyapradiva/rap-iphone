@extends('layouts.admin')

@section('page_title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="space-y-6">

    {{-- Top Navigation & Quick Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="p-2 rounded-xl bg-white/5 border border-white/10 text-[#8E8E93] hover:text-white transition-colors" title="Kembali ke Daftar Pesanan">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="font-mono text-sm font-bold text-[#5AC8FA]">{{ $order->order_number }}</span>
                    <x-order-badge :status="$order->status" />
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-white mt-0.5">
                    Pesanan a.n. {{ $order->recipient_name }}
                </h1>
                <p class="text-xs text-[#8E8E93]">
                    Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} WIB
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.orders.invoice', $order->order_number) }}" target="_blank"
               class="glass-btn glass-btn-secondary glass-btn-sm text-xs flex items-center gap-1.5">
                <i data-lucide="printer" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                <span>Cetak Nota Faktur PDF</span>
            </a>
            <a href="{{ route('checkout.success', $order->order_number) }}" target="_blank"
               class="glass-btn glass-btn-secondary glass-btn-sm text-xs flex items-center gap-1.5">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                <span>Tampilan Pelanggan</span>
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-[#FF453A]/15 border border-[#FF453A]/30 text-xs text-white space-y-1">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- =====================================================================
             KOLOM KIRI (7 COLS): Rincian Unit, Alokasi IMEI, & Info Pelanggan
             ===================================================================== --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- 1. Rincian Unit Dipesan & Alokasi Nomor IMEI --}}
            <div class="glass-card-elevated rounded-3xl p-6 border border-white/15 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-white/10">
                    <div>
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="smartphone" class="w-4 h-4 text-[#0071E3]"></i>
                            <span>Rincian Unit iPhone &amp; Alokasi IMEI</span>
                        </h2>
                        <p class="text-xs text-[#8E8E93] mt-0.5">Alokasikan nomor IMEI fisik unit sebelum paket diserahkan ke kurir ekspedisi.</p>
                    </div>
                </div>

                <form action="{{ route('admin.orders.status', $order->order_number) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $order->status }}">

                    <div class="divide-y divide-white/10">
                        @foreach($order->items as $item)
                            <div class="py-4 first:pt-0 last:pb-0 space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-bold text-white">{{ $item->product_name }}</p>
                                        <p class="text-xs text-[#8E8E93]">Varian: <span class="text-[#E5E5EA] font-medium">{{ $item->variant_details }}</span> &middot; Qty: {{ $item->quantity }}</p>
                                        @if($item->variant?->sku)
                                            <span class="inline-block mt-1 font-mono text-[10px] text-[#5AC8FA] bg-[#0071E3]/10 px-2 py-0.5 rounded">SKU: {{ $item->variant->sku }}</span>
                                        @endif
                                    </div>
                                    <div class="text-right text-xs">
                                        <p class="font-bold text-white">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                                        <p class="text-[11px] text-[#8E8E93]">@ Rp {{ number_format((float) $item->price, 0, ',', '.') }}</p>
                                        <p class="text-[10px] text-[#34C759]">HPP: Rp {{ number_format((float) $item->cost_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 bg-white/5 p-3 rounded-xl border border-white/10">
                                    <label class="text-xs font-semibold text-[#8E8E93] shrink-0 sm:w-44">
                                        Nomor IMEI Unit Terkirim:
                                    </label>
                                    <input type="text"
                                           name="allocated_imei[{{ $item->id }}]"
                                           value="{{ old('allocated_imei.' . $item->id, $item->allocated_imei ?? $item->variant?->imei) }}"
                                           placeholder="Masukkan 15 digit IMEI fisik (*#06#)"
                                           class="glass-input py-1.5 px-3 text-xs font-mono text-[#5AC8FA] w-full">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-white/10 space-y-1.5 text-xs">
                        <div class="flex justify-between text-[#8E8E93]">
                            <span>Subtotal Produk</span>
                            <span class="text-white font-medium">Rp {{ number_format((float) $order->total_items_price, 0, ',', '.') }}</span>
                        </div>
                        @if((float) $order->discount_amount > 0)
                            <div class="flex justify-between text-[#34C759]">
                                <span>Potongan Diskon / Trade-In</span>
                                <span>-Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-[#8E8E93]">
                            <span>Ongkos Kirim &amp; Asuransi ({{ $order->shipping_courier }})</span>
                            <span class="text-[#34C759] font-semibold">
                                {{ (float) $order->shipping_cost > 0 ? 'Rp ' . number_format((float) $order->shipping_cost, 0, ',', '.') : 'GRATIS (Proteksi Penuh)' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm font-black text-white pt-2 border-t border-white/10">
                            <span>TOTAL TAGIHAN</span>
                            <span class="text-[#34C759]">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm text-xs font-semibold flex items-center gap-1.5">
                            <i data-lucide="save" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                            <span>Simpan Alokasi IMEI</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. Informasi Pelanggan, Alamat Pengiriman & Pembayaran Midtrans --}}
            <div class="glass-card rounded-3xl p-6 border border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                    <span class="text-[10px] font-bold text-[#5AC8FA] uppercase tracking-wider block">Penerima &amp; Alamat Pengiriman</span>
                    <p class="text-sm font-bold text-white">{{ $order->recipient_name }}</p>
                    <p class="font-mono text-[#34C759]">{{ $order->recipient_phone }}</p>
                    <p class="text-[#8E8E93]">{{ $order->user->email ?? '-' }}</p>
                    <p class="text-[#E5E5EA] leading-relaxed pt-1 border-t border-white/10">{{ $order->shipping_address }}</p>
                    @if($order->customer_notes)
                        <p class="text-[11px] italic text-[#FF9F0A] pt-1">Catatan: "{{ $order->customer_notes }}"</p>
                    @endif
                </div>

                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                    <span class="text-[10px] font-bold text-[#34C759] uppercase tracking-wider block">Informasi Pembayaran Midtrans</span>
                    <div class="space-y-1">
                        <div class="flex justify-between">
                            <span class="text-[#8E8E93]">Status Transaksi:</span>
                            <span class="font-bold uppercase text-white">{{ $order->payment->transaction_status ?? 'pending' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#8E8E93]">Metode Bayar:</span>
                            <span class="font-semibold text-white uppercase">{{ str_replace('_', ' ', $order->payment->payment_type ?? 'Midtrans Snap') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#8E8E93]">ID Transaksi:</span>
                            <span class="font-mono text-[11px] text-[#5AC8FA]">{{ $order->payment->midtrans_transaction_id ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#8E8E93]">Waktu Lunas:</span>
                            <span class="text-white">{{ $order->payment?->payment_time ? \Carbon\Carbon::parse($order->payment->payment_time)->format('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Riwayat Log Notifikasi WhatsApp & Email --}}
            <div class="glass-card rounded-3xl p-6 border border-white/10 space-y-4">
                <h2 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="bell-ring" class="w-4 h-4 text-[#34C759]"></i>
                    <span>Riwayat Pengiriman Notifikasi (WhatsApp Fonnte &amp; Email SMTP)</span>
                </h2>

                <div class="space-y-2 text-xs">
                    @forelse($notificationLogs as $log)
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->channel === 'whatsapp' ? 'bg-[#34C759]/20 text-[#34C759]' : 'bg-[#0071E3]/20 text-[#5AC8FA]' }}">
                                    {{ $log->channel }}
                                </span>
                                <div>
                                    <p class="font-semibold text-white">{{ $log->event_name }} &rarr; <span class="font-mono text-[#E5E5EA]">{{ $log->recipient }}</span></p>
                                    @if($log->error_message)
                                        <p class="text-[11px] text-[#FF453A]">{{ $log->error_message }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $log->status === 'sent' ? 'bg-[#34C759]/15 text-[#34C759]' : 'bg-[#FF453A]/15 text-[#FF453A]' }}">
                                {{ strtoupper($log->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-[#8E8E93] text-xs">Belum ada log notifikasi yang tercatat untuk pesanan ini.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- =====================================================================
             KOLOM KANAN (5 COLS): Input Resi Pengiriman & Update Status Pesanan
             ===================================================================== --}}
        <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">

            {{-- Panel 1: Input Nomor Resi Pengiriman Kurir (Otomatis WA & Email) --}}
            <div class="glass-card-elevated rounded-3xl p-6 sm:p-7 border border-[#0071E3]/30 space-y-5 shadow-2xl">
                <div class="pb-3 border-b border-white/10">
                    <span class="text-[10px] font-bold text-[#5AC8FA] uppercase tracking-wider">Fase 10.2 &mdash; Logistik Ekspedisi</span>
                    <h2 class="text-lg font-bold text-white mt-0.5 flex items-center gap-2">
                        <i data-lucide="truck" class="w-5 h-5 text-[#5AC8FA]"></i>
                        <span>Input Resi &amp; Pengiriman Kurir</span>
                    </h2>
                    <p class="text-xs text-[#8E8E93] mt-1">
                        Simpan nomor resi (JNE / SiCepat / J&amp;T) untuk otomatis memperbarui status ke <strong>Dikirim</strong> dan mengirim notifikasi WA + Email ke pelanggan.
                    </p>
                </div>

                <form action="{{ route('admin.orders.shipping', $order->order_number) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-1.5">
                        <label for="shipping_courier" class="text-xs font-bold text-white block">
                            Pilih Kurir Ekspedisi <span class="text-[#FF453A]">*</span>
                        </label>
                        <select id="shipping_courier" name="shipping_courier" required class="glass-input text-xs w-full bg-[#161B22]">
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

                    <div class="space-y-1.5">
                        <label for="shipping_tracking_number" class="text-xs font-bold text-white block">
                            Nomor Resi Pengiriman (AWB) <span class="text-[#FF453A]">*</span>
                        </label>
                        <input type="text"
                               id="shipping_tracking_number"
                               name="shipping_tracking_number"
                               value="{{ old('shipping_tracking_number', $order->shipping_tracking_number) }}"
                               required
                               placeholder="Contoh: JNE-9988776655ID / SICEPAT-001122"
                               class="glass-input text-sm font-mono font-bold text-[#34C759] w-full uppercase">
                    </div>

                    <div class="p-3.5 rounded-2xl bg-[#34C759]/10 border border-[#34C759]/30 text-xs text-[#E5E5EA] flex items-start gap-2.5">
                        <i data-lucide="message-square" class="w-4 h-4 text-[#34C759] shrink-0 mt-0.5"></i>
                        <span>
                            Notifikasi <strong>WhatsApp (Fonnte)</strong> ke <code class="text-[#34C759]">{{ $order->recipient_phone }}</code> dan <strong>Email</strong> ke <code class="text-[#5AC8FA]">{{ $order->user->email ?? '-' }}</code> akan terkirim otomatis.
                        </span>
                    </div>

                    <button type="submit" class="glass-btn glass-btn-primary glass-btn-md w-full text-xs font-bold flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Simpan Nomor Resi &amp; Kirim Notifikasi WA + Email</span>
                    </button>
                </form>
            </div>

            {{-- Panel 2: Pembaruan Status Pesanan Manual (Order State Machine) --}}
            <div class="glass-card rounded-3xl p-6 border border-white/10 space-y-4">
                <div class="pb-3 border-b border-white/10">
                    <span class="text-[10px] font-bold text-[#8E8E93] uppercase tracking-wider">Order State Machine</span>
                    <h2 class="text-base font-bold text-white mt-0.5">Ubah Status Pesanan</h2>
                </div>

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

                    <div class="space-y-1.5">
                        <label for="order_status_select" class="text-xs font-semibold text-white block">Pilih Status Pesanan</label>
                        <select id="order_status_select" name="status" class="glass-input text-xs w-full bg-[#161B22]">
                            @foreach($statusLabels as $stKey => $stLabel)
                                <option value="{{ $stKey }}" {{ $order->status === $stKey ? 'selected' : '' }}>
                                    {{ $stLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="glass-btn glass-btn-secondary glass-btn-md w-full text-xs font-bold flex items-center justify-center gap-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-[#5AC8FA]"></i>
                        <span>Perbarui Status Pesanan</span>
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection

