@extends('layouts.customer')

@section('title', 'Pesanan ' . $order->order_number . ' Berhasil Dibuat — Rap-iPhone')
@section('meta_description', 'Rincian pesanan dan status pembayaran transaksi unit iPhone Anda di Rap-iPhone.')

@section('content')
<div class="cust-page py-8 space-y-8 pb-20 max-w-4xl mx-auto">

    <!-- Success Celebration Card -->
    <div class="cust-card p-6 sm:p-10 text-center space-y-5 bg-[var(--cust-surface)] border border-[var(--cust-rule-border)]">
        
        <div class="w-16 h-16 rounded-full bg-[var(--cust-tone-healthy-bg)] border border-[var(--cust-tone-healthy-ink)]/20 flex items-center justify-center mx-auto text-[var(--cust-tone-healthy-ink)]">
            <i data-lucide="check" class="w-8 h-8 stroke-[2.5]"></i>
        </div>

        <div class="space-y-1.5">
            <x-customer.spec-badge variant="healthy" icon="check-circle">
                Pesanan Berhasil Dicatat
            </x-customer.spec-badge>
            <h1 class="font-serif text-2xl sm:text-3xl font-light text-[var(--cust-ink)] tracking-tight">Terima Kasih Atas Pesanan Anda</h1>
            <p class="text-xs sm:text-sm text-[var(--cust-ink-muted)] max-w-md mx-auto leading-relaxed">
                Unit pesanan iPhone Anda sedang diproses oleh tim Rap-iPhone dengan standar verifikasi kualitas ketat.
            </p>
        </div>

        <!-- Order Number & Status Pill -->
        <div class="p-3.5 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] max-w-md mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="text-center sm:text-left">
                <span class="text-[10px] text-[var(--cust-ink-muted)] block">Nomor Nota:</span>
                <span id="order-number-text" class="font-mono text-sm font-bold text-[var(--cust-ink)]">{{ $order->order_number }}</span>
            </div>

            <div class="flex items-center gap-2">
                @if($order->status === 'paid')
                    <x-customer.spec-badge variant="healthy" icon="check-circle-2">
                        Lunas Terverifikasi
                    </x-customer.spec-badge>
                @elseif($order->status === 'cancelled')
                    <span class="cust-badge cust-badge-gray text-[var(--cust-tone-alert-ink)]">
                        Dibatalkan
                    </span>
                @else
                    <x-customer.spec-badge variant="promo" icon="clock">
                        Menunggu Pembayaran
                    </x-customer.spec-badge>
                @endif

                <button 
                    type="button" 
                    onclick="navigator.clipboard.writeText('{{ $order->order_number }}'); alert('Nomor nota berhasil disalin!');" 
                    class="p-1 rounded text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]"
                    title="Salin Nomor Nota"
                >
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        <!-- Pending Banner & Snap Retry -->
        @if($order->status === 'pending' && $order->payment?->snap_token)
            <div class="p-4 rounded bg-[var(--cust-tone-promo-bg)] border border-[var(--cust-tone-promo-ink)]/20 max-w-md mx-auto space-y-3">
                <p class="text-xs text-[var(--cust-tone-promo-ink)] font-medium leading-relaxed">
                    Selesaikan pembayaran melalui modal Midtrans Snap untuk mengamankan stok unit pesanan Anda.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                    <button 
                        type="button" 
                        id="btn-retry-pay" 
                        onclick="payWithSnap('{{ $order->payment->snap_token }}')" 
                        class="cust-btn cust-btn-primary text-xs py-2 px-4 w-full sm:w-auto"
                    >
                        <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                        <span>Buka Pembayaran Snap</span>
                    </button>

                    @if(!$isProduction)
                        <button 
                            type="button" 
                            data-modal-target="sandbox-simulator-modal"
                            class="cust-btn cust-btn-outline text-xs py-2 px-3 w-full sm:w-auto"
                        >
                            <i data-lucide="qr-code" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
                            <span>Simulator Sandbox</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <!-- Items Breakdown & Shipping Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        
        <!-- Left: Items -->
        <div class="cust-card p-5 space-y-3">
            <h2 class="text-xs font-semibold text-[var(--cust-ink)] uppercase tracking-wider pb-2 border-b border-[var(--cust-rule-border)]">
                Unit Pesanan
            </h2>

            <div class="space-y-2.5">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between text-xs py-1 border-b border-[var(--cust-rule-border)] last:border-0">
                        <div>
                            <p class="font-semibold text-[var(--cust-ink)]">{{ $item->product_name }}</p>
                            <p class="text-[11px] text-[var(--cust-ink-muted)]">{{ $item->variant_details }} &bull; {{ $item->quantity }} unit</p>
                        </div>
                        <span class="font-bold text-[var(--cust-ink)] cust-tabular">
                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="pt-2 border-t-2 border-[var(--cust-ink)] flex items-center justify-between text-xs">
                <span class="font-semibold text-[var(--cust-ink)]">Total Pembayaran:</span>
                <span class="font-bold text-base text-[var(--cust-ink)] cust-tabular">
                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Right: Shipping & Actions -->
        <div class="cust-card p-5 space-y-4">
            <h2 class="text-xs font-semibold text-[var(--cust-ink)] uppercase tracking-wider pb-2 border-b border-[var(--cust-rule-border)]">
                Pengiriman &amp; Nota
            </h2>

            <div class="text-xs space-y-2 text-[var(--cust-ink-muted)]">
                <div>
                    <span class="text-[10px] uppercase font-semibold text-[var(--cust-ink)] block">Penerima</span>
                    <p>{{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                </div>

                <div>
                    <span class="text-[10px] uppercase font-semibold text-[var(--cust-ink)] block">Metode Pemenuhan</span>
                    <p>{{ $order->shipping_courier }}</p>
                    <p class="text-[11px]">{{ $order->shipping_address }}</p>
                </div>

                @if($order->shipping_tracking_number)
                    <div class="p-2.5 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)]">
                        <span class="text-[10px] text-[var(--cust-ink-muted)] block">Nomor Resi:</span>
                        <span class="font-mono text-xs font-bold text-[var(--cust-ink)]">{{ $order->shipping_tracking_number }}</span>
                    </div>
                @endif
            </div>

            <!-- Download Invoice & Back Actions -->
            <div class="pt-2 space-y-2 border-t border-[var(--cust-rule-border)]">
                @if($order->status === 'paid' || $order->status === 'completed' || $order->status === 'shipped')
                    <a href="{{ route('invoice.download', $order->order_number) }}" class="cust-btn cust-btn-primary w-full py-2.5 text-xs font-semibold flex items-center justify-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Unduh Nota Faktur Resmi (PDF)</span>
                    </a>
                @endif

                <x-customer.button :href="url('/customer/orders')" variant="outline" class="w-full text-xs py-2">
                    Riwayat Pesanan Saya
                </x-customer.button>
            </div>
        </div>

    </div>

</div>

<!-- Sandbox Simulator Modal -->
@if(!$isProduction && $order->status === 'pending')
    <div id="sandbox-simulator-modal" class="glass-modal-backdrop hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="sandbox-modal-title">
        <div class="cust-card p-6 max-w-md w-full space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-rule-border)]">
                <h3 id="sandbox-modal-title" class="text-sm font-semibold text-[var(--cust-ink)]">Simulator Webhook Sandbox Midtrans</h3>
                <button type="button" data-modal-close class="p-1 rounded text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <p class="text-xs text-[var(--cust-ink-muted)] leading-relaxed">
                Gunakan simulator ini untuk menguji callback otomatis Midtrans status <code>settlement</code> (lunas) di lingkungan sandbox.
            </p>

            <form action="{{ url('/api/midtrans/callback') }}" method="POST" target="_blank" class="space-y-3">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_number }}">
                <input type="hidden" name="status_code" value="200">
                <input type="hidden" name="transaction_status" value="settlement">
                <input type="hidden" name="gross_amount" value="{{ (int)$order->grand_total }}.00">
                <input type="hidden" name="payment_type" value="qris">
                
                <button type="submit" class="cust-btn cust-btn-primary w-full text-xs py-2">
                    Simulasikan Pembayaran Lunas (Settlement)
                </button>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>
<script>
    function payWithSnap(snapToken) {
        if (window.snap && typeof window.snap.pay === 'function') {
            window.snap.pay(snapToken, {
                onSuccess: function() { window.location.reload(); },
                onPending: function() { window.location.reload(); },
                onError: function() { alert('Pembayaran gagal diproses.'); },
                onClose: function() { window.location.reload(); }
            });
        } else {
            alert('Pustaka Midtrans Snap sedang dimuat, silakan coba sesaat lagi.');
        }
    }
</script>
@endpush
