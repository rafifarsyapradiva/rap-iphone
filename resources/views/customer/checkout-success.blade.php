@extends('layouts.customer')

@section('title', 'Pesanan ' . $order->order_number . ' Berhasil Dibuat — Rap-iPhone')
@section('meta_description', 'Rincian pesanan dan status pembayaran transaksi unit iPhone Anda di Rap-iPhone.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Success Celebration Card (Daylight Clean Card) -->
    <div class="rounded-[28px] bg-[#F5F5F7] p-8 sm:p-12 text-center border border-[#E5E5EA] relative overflow-hidden space-y-6">
        
        <div class="w-20 h-20 rounded-full bg-[#E8F8EE] border border-[#008040]/25 flex items-center justify-center mx-auto">
            <div class="w-14 h-14 rounded-full bg-[#008040] flex items-center justify-center text-white shadow-md">
                <i data-lucide="check" class="w-8 h-8 stroke-[3]"></i>
            </div>
        </div>

        <div class="space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E8F8EE] text-xs font-bold text-[#008040]">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Pesanan Berhasil Dicatat</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-[#1D1D1F] tracking-tight">Terima Kasih Atas Pesanan Anda</h1>
            <p class="text-xs sm:text-sm text-[#6E6E73] max-w-lg mx-auto leading-relaxed">
                Unit pesanan iPhone Anda sedang dipersiapkan oleh tim operasional Rap-iPhone dengan standar pengecekan kualitas ketat.
            </p>
        </div>

        <!-- Order Number & Status Pill -->
        <div class="p-4 rounded-2xl bg-white border border-[#D2D2D7] max-w-md mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="text-center sm:text-left">
                <span class="text-[#6E6E73] block text-[11px]">Nomor Nota Resmi:</span>
                <span id="order-number-text" class="text-base font-mono font-bold text-[#1D1D1F] tracking-wider">{{ $order->order_number }}</span>
            </div>

            <div class="flex items-center gap-2">
                @if($order->status === 'paid')
                    <span class="px-3 py-1 rounded-full font-bold bg-[#E8F8EE] text-[#008040] flex items-center gap-1">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> LUNAS TERVERIFIKASI
                    </span>
                @elseif($order->status === 'cancelled')
                    <span class="px-3 py-1 rounded-full font-bold bg-[#FFEBEA] text-[#D70015] flex items-center gap-1">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> DIBATALKAN
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full font-bold bg-[#FFF8E6] text-[#B25000] flex items-center gap-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> MENUNGGU PEMBAYARAN
                    </span>
                @endif

                <button 
                    type="button" 
                    onclick="navigator.clipboard.writeText('{{ $order->order_number }}'); alert('Nomor order berhasil disalin!');" 
                    class="p-1.5 rounded-lg bg-[#F5F5F7] text-[#6E6E73] hover:text-[#1D1D1F] transition-colors"
                    title="Salin Nomor Pesanan"
                >
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        <!-- If still Pending, provide Pay Now button & Sandbox Simulator -->
        @if($order->status === 'pending' && $order->payment?->snap_token)
            <div class="p-5 rounded-2xl bg-[#FFF8E6] border border-[#B25000]/25 max-w-lg mx-auto space-y-4">
                <p class="text-xs text-[#B25000] font-medium leading-relaxed">
                    Selesaikan pembayaran melalui popup Midtrans Snap untuk mengamankan unit iPhone Anda sebelum stok dialihkan.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-2.5">
                    <button 
                        type="button" 
                        id="btn-retry-pay" 
                        onclick="payWithSnap('{{ $order->payment->snap_token }}')" 
                        class="glass-btn glass-btn-primary glass-btn-md w-full flex items-center justify-center gap-2 font-bold"
                    >
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>Buka Modal Pembayaran Snap</span>
                    </button>

                    @if(!$isProduction)
                        <button 
                            type="button" 
                            data-modal-target="sandbox-simulator-modal"
                            class="glass-btn glass-btn-secondary glass-btn-md w-full sm:w-auto flex items-center justify-center gap-2 text-xs font-semibold"
                        >
                            <i data-lucide="qr-code" class="w-4 h-4 text-[#0066CC]"></i>
                            <span>Simulator Sandbox (QRIS / VA)</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <!-- Midtrans Sandbox Simulator Modal (QRIS & Virtual Account) -->
    @if(!$isProduction && $order->status === 'pending')
        <div id="sandbox-simulator-modal" class="glass-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="sandbox-modal-title">
            <div class="glass-modal p-6 sm:p-7 max-w-lg w-full space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-[#E5E5EA]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#E8F2FF] flex items-center justify-center text-[#0066CC]">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div class="text-left">
                            <h3 id="sandbox-modal-title" class="text-base font-bold text-[#1D1D1F]">Midtrans Sandbox Payment Simulator</h3>
                            <p class="text-[11px] text-[#6E6E73]">Simulasi Pembayaran QRIS &amp; Virtual Account &bull; {{ $order->order_number }}</p>
                        </div>
                    </div>
                    <button type="button" data-modal-close class="p-1.5 rounded-lg text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]" aria-label="Tutup">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-3.5 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] flex items-center justify-between text-xs">
                    <span class="text-[#6E6E73] font-medium">Nominal Tagihan Midtrans:</span>
                    <span class="text-base font-bold text-[#008040]">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>

                <form action="{{ route('checkout.simulate', $order->order_number) }}" method="POST" class="space-y-4 text-left">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wider block">Pilih Metode Pembayaran Sandbox:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[#D2D2D7] bg-[#F5F5F7] hover:bg-white cursor-pointer has-[:checked]:border-[#0066CC] has-[:checked]:bg-[#E8F2FF]/40">
                                <input type="radio" name="payment_method" value="qris" class="accent-[#0066CC]" checked>
                                <div>
                                    <span class="text-xs font-bold text-[#1D1D1F] block">QRIS Instant</span>
                                    <span class="text-[10px] text-[#6E6E73]">GoPay / ShopeePay / M-Banking</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[#D2D2D7] bg-[#F5F5F7] hover:bg-white cursor-pointer has-[:checked]:border-[#0066CC] has-[:checked]:bg-[#E8F2FF]/40">
                                <input type="radio" name="payment_method" value="bca_va" class="accent-[#0066CC]">
                                <div>
                                    <span class="text-xs font-bold text-[#1D1D1F] block">BCA Virtual Account</span>
                                    <span class="text-[10px] text-[#6E6E73]">VA 80777{{ preg_replace('/\D+/', '', $order->recipient_phone ?: '08123456') }}</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[#D2D2D7] bg-[#F5F5F7] hover:bg-white cursor-pointer has-[:checked]:border-[#0066CC] has-[:checked]:bg-[#E8F2FF]/40">
                                <input type="radio" name="payment_method" value="mandiri_va" class="accent-[#0066CC]">
                                <div>
                                    <span class="text-xs font-bold text-[#1D1D1F] block">Mandiri Virtual Account</span>
                                    <span class="text-[10px] text-[#6E6E73]">Mandiri Bill Payment</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[#D2D2D7] bg-[#F5F5F7] hover:bg-white cursor-pointer has-[:checked]:border-[#0066CC] has-[:checked]:bg-[#E8F2FF]/40">
                                <input type="radio" name="payment_method" value="bni_va" class="accent-[#0066CC]">
                                <div>
                                    <span class="text-xs font-bold text-[#1D1D1F] block">BNI Virtual Account</span>
                                    <span class="text-[10px] text-[#6E6E73]">Transfer VA Otomatis</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[#D2D2D7] bg-[#F5F5F7] hover:bg-white cursor-pointer has-[:checked]:border-[#0066CC] has-[:checked]:bg-[#E8F2FF]/40 sm:col-span-2">
                                <input type="radio" name="payment_method" value="bri_va" class="accent-[#0066CC]">
                                <div>
                                    <span class="text-xs font-bold text-[#1D1D1F] block">BRI Virtual Account (BRIVA)</span>
                                    <span class="text-[10px] text-[#6E6E73]">Verifikasi Lunas Instan via Webhook SHA-512</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <input type="hidden" name="simulation_status" value="settlement">

                    <div class="pt-2 flex items-center justify-end gap-3">
                        <button type="button" data-modal-close class="glass-btn glass-btn-ghost glass-btn-sm text-xs">
                            Batal
                        </button>
                        <button type="submit" class="glass-btn glass-btn-primary glass-btn-md text-xs font-bold">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                            <span>Konfirmasi &amp; Simulasikan Lunas Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Order Breakdown & Shipping Card -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

        <!-- Left: Purchased Units -->
        <div class="md:col-span-7 rounded-[24px] bg-[#F5F5F7] p-6 sm:p-7 border border-[#E5E5EA] space-y-5">
            <h2 class="text-base font-bold text-[#1D1D1F] flex items-center gap-2 pb-3 border-b border-[#D2D2D7]">
                <i data-lucide="package" class="w-4 h-4 text-[#0066CC]"></i>
                <span>Rincian Unit iPhone</span>
            </h2>

            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="p-3.5 rounded-2xl bg-white border border-[#E5E5EA] flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xs font-bold text-[#1D1D1F] truncate">{{ $item->product_name }}</h3>
                            <p class="text-[11px] text-[#6E6E73] mt-0.5">{{ $item->variant_details }}</p>
                            @if($item->allocated_imei)
                                <p class="text-[10px] text-[#008040] font-mono font-semibold mt-0.5">IMEI Fisik: {{ $item->allocated_imei }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[#6E6E73] text-[11px] block">{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            <span class="text-[#1D1D1F] font-bold text-xs">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-[#D2D2D7] space-y-2 text-xs">
                <div class="flex items-center justify-between text-[#6E6E73]">
                    <span>Subtotal Produk</span>
                    <span class="text-[#1D1D1F] font-medium">Rp {{ number_format($order->total_items_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-[#6E6E73]">
                    <span>Ongkos Kirim</span>
                    <span class="text-[#008040] font-bold">GRATIS</span>
                </div>
                <div class="flex items-center justify-between text-[#6E6E73]">
                    <span>Diskon</span>
                    <span class="text-[#1D1D1F] font-medium">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm font-bold text-[#1D1D1F] pt-2 border-t border-[#D2D2D7]">
                    <span>Total Tagihan</span>
                    <span class="text-base text-[#0066CC] font-bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Shipping & WhatsApp Updates -->
        <div class="md:col-span-5 space-y-6">
            
            <div class="rounded-[24px] bg-[#F5F5F7] p-6 border border-[#E5E5EA] space-y-4">
                <h2 class="text-base font-bold text-[#1D1D1F] flex items-center gap-2 pb-3 border-b border-[#D2D2D7]">
                    <i data-lucide="map-pin" class="w-4 h-4 text-[#008040]"></i>
                    <span>Tujuan Pengiriman</span>
                </h2>

                <div class="space-y-2.5 text-xs">
                    <div>
                        <span class="text-[11px] text-[#6E6E73] block">Nama Penerima:</span>
                        <span class="text-[#1D1D1F] font-semibold">{{ $order->recipient_name }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-[#6E6E73] block">Nomor WhatsApp:</span>
                        <span class="text-[#1D1D1F] font-mono font-medium">{{ $order->recipient_phone }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-[#6E6E73] block">Metode Kurir / Pengambilan:</span>
                        <span class="text-[#1D1D1F] font-semibold">{{ $order->shipping_courier }}</span>
                    </div>

                    <div>
                        <span class="text-[11px] text-[#6E6E73] block">Alamat Pengiriman:</span>
                        <p class="text-[#6E6E73] leading-relaxed mt-0.5">{{ $order->shipping_address }}</p>
                    </div>

                    @if($order->customer_notes)
                        <div>
                            <span class="text-[11px] text-[#6E6E73] block">Catatan Khusus:</span>
                            <p class="text-[#1D1D1F] italic mt-0.5">"{{ $order->customer_notes }}"</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-5 rounded-[24px] bg-[#E8F8EE] border border-[#008040]/20 space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold text-[#008040]">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Notifikasi WhatsApp Terintegrasi</span>
                </div>
                <p class="text-[11px] text-[#1D1D1F]/80 leading-relaxed">
                    Sistem Rap-iPhone mengirimkan pemberitahuan status transaksi dan nomor resi pengiriman kurir langsung ke WhatsApp <strong class="text-[#1D1D1F] font-mono">{{ $order->recipient_phone }}</strong>.
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
        const simModal = document.getElementById('sandbox-simulator-modal');
        if (simModal) {
            simModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            return;
        }
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
            onClose: function () {}
        });
    } else {
        alert('Pustaka Midtrans Snap belum selesai dimuat. Silakan muat ulang halaman.');
    }
}
</script>
@endpush
