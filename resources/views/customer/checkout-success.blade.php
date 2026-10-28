@extends('layouts.customer')

@section('title', 'Pesanan ' . $order->order_number . ' Berhasil Dibuat — Rap-iPhone')
@section('meta_description', 'Rincian pesanan dan status pembayaran transaksi unit iPhone Anda di Rap-iPhone.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Success Celebration Card -->
    <div class="glass-card-elevated rounded-3xl p-8 sm:p-12 text-center border border-white/15 relative overflow-hidden space-y-6 shadow-2xl">
        
        <!-- Ambient Glow behind icon -->
        <div class="w-24 h-24 rounded-full bg-[#34C759]/20 border border-[#34C759]/40 flex items-center justify-center mx-auto shadow-[0_0_50px_rgba(52,199,89,0.3)] animate-pulse">
            <div class="w-16 h-16 rounded-full bg-[#34C759] flex items-center justify-center text-white shadow-lg">
                <i data-lucide="check" class="w-10 h-10 stroke-[3]"></i>
            </div>
        </div>

        <div class="space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#34C759]/15 border border-[#34C759]/30 text-xs font-bold text-[#34C759]">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Pesanan Berhasil Dicatat</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Terima Kasih Atas Pesanan Anda</h1>
            <p class="text-xs sm:text-sm text-[#8E8E93] max-w-lg mx-auto leading-relaxed">
                Unit pesanan iPhone Anda sedang dipersiapkan oleh tim operasional Rap-iPhone dengan standar pengecekan ketat.
            </p>
        </div>

        <!-- Order Number & Status Pill -->
        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 max-w-md mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div>
                <span class="text-[#8E8E93] block text-[11px]">Nomor Nota Resmi:</span>
                <span id="order-number-text" class="text-base font-mono font-bold text-white tracking-wider">{{ $order->order_number }}</span>
            </div>

            <div class="flex items-center gap-2">
                @if($order->status === 'paid')
                    <span class="px-3 py-1 rounded-full font-bold bg-[#34C759]/20 text-[#34C759] border border-[#34C759]/30 flex items-center gap-1">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> LUNAS TERVERIFIKASI
                    </span>
                @elseif($order->status === 'cancelled')
                    <span class="px-3 py-1 rounded-full font-bold bg-[#FF453A]/20 text-[#FF453A] border border-[#FF453A]/30 flex items-center gap-1">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> DIBATALKAN
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full font-bold bg-[#FF9F0A]/20 text-[#FF9F0A] border border-[#FF9F0A]/30 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> MENUNGGU PEMBAYARAN
                    </span>
                @endif

                <button 
                    type="button" 
                    onclick="navigator.clipboard.writeText('{{ $order->order_number }}'); alert('Nomor order berhasil disalin!');" 
                    class="p-1.5 rounded-lg bg-white/10 text-[#8E8E93] hover:text-white transition-colors"
                    title="Salin Nomor Pesanan"
                >
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        <!-- If still Pending, provide Pay Now button -->
        @if($order->status === 'pending' && $order->payment?->snap_token)
            <div class="p-4 rounded-2xl bg-[#FF9F0A]/10 border border-[#FF9F0A]/20 max-w-md mx-auto space-y-3">
                <p class="text-xs text-[#FF9F0A] font-medium leading-relaxed">
                    Selesaikan pembayaran melalui popup Midtrans Snap untuk mengamankan unit iPhone Anda sebelum stok dialihkan.
                </p>
                <button 
                    type="button" 
                    id="btn-retry-pay" 
                    onclick="payWithSnap('{{ $order->payment->snap_token }}')" 
                    class="glass-btn glass-btn-primary glass-btn-md w-full flex items-center justify-center gap-2 font-bold shadow-lg"
                >
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    <span>Buka Modal Pembayaran Snap</span>
                </button>
            </div>
        @endif

    </div>

    <!-- Order Breakdown & Shipping Card -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

        <!-- Left: Purchased Units -->
        <div class="md:col-span-7 glass-card-elevated rounded-3xl p-6 sm:p-7 border border-white/15 space-y-5">
            <h2 class="text-base font-bold text-white flex items-center gap-2 pb-3 border-b border-white/10">
                <i data-lucide="package" class="w-4 h-4 text-[#0071E3]"></i>
                <span>Rincian Unit iPhone</span>
            </h2>

            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xs font-bold text-white truncate">{{ $item->product_name }}</h3>
                            <p class="text-[11px] text-[#8E8E93] mt-0.5">{{ $item->variant_details }}</p>
                            @if($item->allocated_imei)
                                <p class="text-[10px] text-[#34C759] font-mono mt-0.5">IMEI Fisik: {{ $item->allocated_imei }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[#8E8E93] text-[11px] block">{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            <span class="text-white font-bold text-xs">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-white/10 space-y-2 text-xs">
                <div class="flex items-center justify-between text-[#8E8E93]">
                    <span>Subtotal Produk</span>
                    <span class="text-white font-medium">Rp {{ number_format($order->total_items_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-[#8E8E93]">
                    <span>Ongkos Kirim</span>
                    <span class="text-[#34C759] font-bold">GRATIS</span>
                </div>
                <div class="flex items-center justify-between text-[#8E8E93]">
                    <span>Diskon</span>
                    <span class="text-white font-medium">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm font-bold text-white pt-2 border-t border-white/10">
                    <span>Total Tagihan</span>
                    <span class="text-base text-[#0071E3] font-black">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Shipping & WhatsApp Updates -->
        <div class="md:col-span-5 space-y-6">
            
            <!-- Shipping Info Card -->
            <div class="glass-card-elevated rounded-3xl p-6 border border-white/15 space-y-4">
                <h2 class="text-base font-bold text-white flex items-center gap-2 pb-3 border-b border-white/10">
                    <i data-lucide="map-pin" class="w-4 h-4 text-[#34C759]"></i>
                    <span>Tujuan Pengiriman</span>
                </h2>

                <div class="space-y-2.5 text-xs">
                    <div>
                        <span class="text-[11px] text-[#8E8E93] block">Nama Penerima:</span>
                        <span class="text-white font-semibold">{{ $order->recipient_name }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-[#8E8E93] block">Nomor WhatsApp:</span>
                        <span class="text-white font-mono font-medium">{{ $order->recipient_phone }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-[#8E8E93] block">Metode Kurir:</span>
                        <span class="text-white font-medium">{{ $order->shipping_courier }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-[#8E8E93] block">Alamat Pengiriman:</span>
                        <p class="text-[#8E8E93] leading-relaxed mt-0.5">{{ $order->shipping_address }}</p>
                    </div>

                    @if($order->customer_notes)
                        <div>
                            <span class="text-[11px] text-[#8E8E93] block">Catatan Khusus:</span>
                            <p class="text-white/80 italic mt-0.5">"{{ $order->customer_notes }}"</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- WhatsApp & Email Notification Notice -->
            <div class="p-5 rounded-3xl bg-[#34C759]/10 border border-[#34C759]/20 space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-[#34C759]">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Notifikasi WhatsApp Terintegrasi</span>
                </div>
                <p class="text-[11px] text-[#8E8E93] leading-relaxed">
                    Sistem Rap-iPhone akan mengirimkan pemberitahuan status transaksi dan nomor resi pengiriman kurir langsung ke nomor WhatsApp <strong class="text-white font-mono">{{ $order->recipient_phone }}</strong>.
                </p>
            </div>

            <!-- Action CTAs -->
            <div class="space-y-2.5">
                @can('downloadInvoice', $order)
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('orders.invoice.download', $order->order_number) }}" class="glass-btn glass-btn-primary glass-btn-md w-full flex items-center justify-center gap-2 text-xs font-bold" data-testid="download-invoice">
                            <i data-lucide="file-down" class="w-4 h-4"></i>
                            <span>Unduh Nota PDF</span>
                        </a>
                        <a href="{{ route('orders.invoice.preview', $order->order_number) }}" target="_blank" class="glass-btn glass-btn-secondary glass-btn-md w-full flex items-center justify-center gap-2 text-xs font-bold">
                            <i data-lucide="printer" class="w-4 h-4"></i>
                            <span>Cetak Nota</span>
                        </a>
                    </div>
                @endcan

                <a href="{{ route('customer.orders') }}" class="glass-btn glass-btn-secondary glass-btn-md w-full flex items-center justify-center gap-2 text-xs font-bold">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Lihat Riwayat Pesanan Saya</span>
                </a>

                <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-ghost glass-btn-sm w-full text-center text-xs">
                    <span>Lanjut Belanja iPhone Lainnya</span>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script 
    src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
    data-client-key="{{ $clientKey }}"
></script>

<script>
function payWithSnap(snapToken) {
    if (snapToken.startsWith('mock_snap_token_') || snapToken.startsWith('fallback_snap_token_')) {
        alert('Mode Dev/Mock Snap Aktif untuk pengujian lokal.');
        return;
    }

    if (window.snap && typeof window.snap.pay === 'function') {
        window.snap.pay(snapToken, {
            onSuccess: function (result) {
                location.reload();
            },
            onPending: function (result) {
                location.reload();
            },
            onError: function (result) {
                alert('Gagal menyelesaikan pembayaran: ' + (result.status_message || 'Terjadi kesalahan'));
            },
            onClose: function () {
                // do nothing
            }
        });
    } else {
        alert('Pustaka Midtrans Snap belum selesai dimuat. Silakan muat ulang halaman.');
    }
}
</script>
@endpush

