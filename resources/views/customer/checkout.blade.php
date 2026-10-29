@extends('layouts.customer')

@section('title', 'Checkout Terpadu — Rap-iPhone Authorized Style Partner')
@section('meta_description', 'Selesaikan pemesanan unit iPhone Anda secara aman dengan pilihan Kurir Berasuransi atau Click & Collect Store serta pembayaran Midtrans Snap.')

@section('content')
<div class="cust-page py-6 space-y-8 pb-20">

    <!-- 1. Header & Stepper -->
    <div class="space-y-4 pb-4 border-b border-[var(--cust-rule-border)]">
        <div class="flex items-center justify-between">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-1.5 text-xs text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)] transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Keranjang</span>
            </a>

            <x-customer.spec-badge variant="healthy" icon="shield-check">
                Checkout Terenkripsi Midtrans
            </x-customer.spec-badge>
        </div>

        <!-- 3-Step Progress Indicator -->
        <div class="flex items-center justify-center max-w-md mx-auto pt-1">
            <div class="flex items-center gap-2 w-full text-xs">
                <div class="flex items-center gap-1.5 text-[var(--cust-tone-healthy-ink)] font-medium">
                    <span class="w-5 h-5 rounded bg-[var(--cust-tone-healthy-bg)] flex items-center justify-center text-[10px] font-bold">1</span>
                    <span>Keranjang</span>
                </div>
                <div class="h-px flex-1 bg-[var(--cust-tone-healthy-ink)]"></div>

                <div class="flex items-center gap-1.5 text-[var(--cust-ink)] font-semibold">
                    <span class="w-5 h-5 rounded bg-[var(--cust-ink)] text-white flex items-center justify-center text-[10px] font-bold">2</span>
                    <span>Pengiriman</span>
                </div>
                <div class="h-px flex-1 bg-[var(--cust-rule-border)]"></div>

                <div class="flex items-center gap-1.5 text-[var(--cust-ink-muted)]">
                    <span class="w-5 h-5 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] flex items-center justify-center text-[10px] font-bold">3</span>
                    <span>Bayar</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="p-3.5 rounded bg-[var(--cust-tone-alert-bg)] border border-[var(--cust-tone-alert-ink)]/20 text-xs text-[var(--cust-tone-alert-ink)] font-medium flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Dynamic JS Feedback Alert Box -->
    <div id="checkout-alert" class="hidden p-3.5 rounded text-xs font-medium flex items-center gap-2 transition-all"></div>

    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Identitas, Metode Pengiriman, Alamat -->
            <div class="lg:col-span-7 space-y-5">

                <!-- 1. Identitas Penerima -->
                <div class="cust-card p-5 space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-[var(--cust-rule-border)]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-[var(--cust-signal)]"></i>
                            <h2 class="text-xs font-semibold text-[var(--cust-ink)] uppercase tracking-wider">1. Data Penerima</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1">
                            <label for="recipient_name" class="cust-label">
                                Nama Lengkap Penerima <span class="text-[var(--cust-tone-alert-ink)]">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="recipient_name" 
                                name="recipient_name" 
                                value="{{ old('recipient_name', $user->name ?? '') }}" 
                                required 
                                placeholder="Nama lengkap Anda"
                                class="cust-input text-xs"
                            >
                            @error('recipient_name')
                                <p class="text-[11px] text-[var(--cust-tone-alert-ink)] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label for="recipient_phone" class="cust-label">
                                    No. WhatsApp Aktif <span class="text-[var(--cust-tone-alert-ink)]">*</span>
                                </label>
                                <span class="text-[10px] text-[var(--cust-tone-healthy-ink)] font-medium">
                                    Fonnte Auto-WA
                                </span>
                            </div>
                            <input 
                                type="tel" 
                                id="recipient_phone" 
                                name="recipient_phone" 
                                value="{{ old('recipient_phone', $user->phone_number ?? '') }}" 
                                required 
                                placeholder="08xxxxxxxxxx"
                                class="cust-input text-xs font-mono"
                            >
                            @error('recipient_phone')
                                <p class="text-[11px] text-[var(--cust-tone-alert-ink)] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="p-2.5 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] flex items-center justify-between text-xs">
                        <span class="text-[var(--cust-ink-muted)]">Nota PDF Resmi dikirimkan ke:</span>
                        <span class="text-[var(--cust-ink)] font-mono font-medium">{{ $user->email }}</span>
                    </div>
                </div>

                <!-- 2. Pilihan Pengiriman (Kurir vs Click & Collect) -->
                <div class="cust-card p-5 space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-[var(--cust-rule-border)]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="truck" class="w-4 h-4 text-[var(--cust-signal)]"></i>
                            <h2 class="text-xs font-semibold text-[var(--cust-ink)] uppercase tracking-wider">2. Metode Pengiriman</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" role="radiogroup" aria-label="Pilihan Kurir">
                        @foreach($couriers as $index => $courier)
                            <label class="relative flex flex-col p-3 rounded border border-[var(--cust-rule-border)] bg-white hover:border-[var(--cust-ink)] cursor-pointer transition-colors has-[:checked]:border-[var(--cust-signal)] has-[:checked]:bg-[var(--cust-tone-official-bg)]">
                                <input 
                                    type="radio" 
                                    name="shipping_courier" 
                                    value="{{ $courier['name'] }}" 
                                    class="sr-only" 
                                    {{ $index === 0 ? 'checked' : '' }}
                                >
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold text-[var(--cust-ink)]">{{ $courier['id'] }}</span>
                                    <span class="text-[10px] text-[var(--cust-tone-healthy-ink)] font-bold">
                                        {{ $courier['badge'] }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-[var(--cust-ink-muted)]">{{ $courier['name'] }}</span>
                                <div class="mt-2 pt-1.5 border-t border-[var(--cust-rule-border)] flex items-center justify-between text-[11px]">
                                    <span class="text-[var(--cust-ink-muted)]">{{ $courier['eta'] }}</span>
                                    <span class="font-bold text-[var(--cust-tone-healthy-ink)]">GRATIS</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Alamat Lengkap & Catatan -->
                <div class="cust-card p-5 space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-[var(--cust-rule-border)]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-[var(--cust-signal)]"></i>
                            <h2 class="text-xs font-semibold text-[var(--cust-ink)] uppercase tracking-wider">3. Alamat Tujuan</h2>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label for="shipping_address" class="cust-label">
                                Alamat Lengkap, Kota, &amp; Kode Pos <span class="text-[var(--cust-tone-alert-ink)]">*</span>
                            </label>
                            <textarea 
                                id="shipping_address" 
                                name="shipping_address" 
                                rows="3" 
                                required 
                                placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, dan kode pos"
                                class="cust-input text-xs leading-relaxed"
                            >{{ old('shipping_address', $user->address ?? '') }}</textarea>
                            @error('shipping_address')
                                <p class="text-[11px] text-[var(--cust-tone-alert-ink)] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="customer_notes" class="cust-label">
                                Catatan Pesanan / Kode Voucher Trade-In (Opsional)
                            </label>
                            <input 
                                type="text" 
                                id="customer_notes" 
                                name="customer_notes" 
                                value="{{ old('customer_notes') }}" 
                                placeholder="Contoh: Voucher Trade-In VCH-XXXXXX atau jam pengambilan"
                                class="cust-input text-xs"
                            >
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: Order Summary & Pay -->
            <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-5">
                <div class="cust-card p-5 space-y-4">
                    
                    <div class="flex items-center justify-between pb-2 border-b border-[var(--cust-rule-border)]">
                        <h2 class="text-xs font-semibold text-[var(--cust-ink)] uppercase tracking-wider">Ringkasan Pesanan</h2>
                        <span class="text-xs text-[var(--cust-ink-muted)] cust-tabular">{{ $totalQuantity }} Unit</span>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-2.5 max-h-[280px] overflow-y-auto pr-1">
                        @foreach($cart as $item)
                            <div class="flex items-center gap-3 p-2.5 rounded border border-[var(--cust-rule-border)] bg-white">
                                <div class="w-12 h-12 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] flex items-center justify-center p-1 shrink-0">
                                    @if(!empty($item['image']))
                                        <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" width="48" height="48" class="w-full h-full object-contain">
                                    @else
                                        <i data-lucide="smartphone" class="w-5 h-5 text-[var(--cust-ink)]"></i>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-[var(--cust-ink)] truncate">{{ $item['product_name'] }}</p>
                                    <p class="text-[11px] text-[var(--cust-ink-muted)]">
                                        {{ $item['color_name'] }} &bull; {{ $item['storage_capacity'] }}
                                    </p>
                                    <div class="flex items-center justify-between mt-0.5 text-xs">
                                        <span class="text-[var(--cust-ink-muted)]">{{ $item['quantity'] }}x</span>
                                        <span class="font-bold text-[var(--cust-ink)] cust-tabular">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Calculation -->
                    <div class="space-y-2 text-xs border-t border-[var(--cust-rule-border)] pt-3">
                        <div class="flex items-center justify-between text-[var(--cust-ink-muted)]">
                            <span>Subtotal Unit</span>
                            <span class="text-[var(--cust-ink)] font-semibold cust-tabular">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--cust-ink-muted)]">
                            <span>Pengiriman &amp; Asuransi</span>
                            <span class="text-[var(--cust-tone-healthy-ink)] font-semibold">GRATIS</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--cust-ink-muted)]">
                            <span>Biaya Layanan Midtrans</span>
                            <span class="text-[var(--cust-tone-healthy-ink)] font-semibold">Rp 0 (Gratis)</span>
                        </div>
                    </div>

                    <!-- Total Tagihan -->
                    <div class="p-3.5 rounded bg-[var(--cust-paper-subtle)] border-t-2 border-[var(--cust-ink)] space-y-0.5">
                        <span class="text-[10px] uppercase font-semibold text-[var(--cust-ink-muted)]">Total Pembayaran</span>
                        <div class="text-2xl font-bold text-[var(--cust-ink)] cust-tabular">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="space-y-2.5 pt-1">
                        <button 
                            type="submit" 
                            id="btn-pay-now" 
                            class="cust-btn cust-btn-primary w-full py-3 text-xs font-semibold flex items-center justify-center gap-2"
                        >
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            <span id="btn-pay-text">Bayar Sekarang dengan Midtrans</span>
                            <div id="btn-pay-spinner" class="hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </button>

                        <p class="text-[10px] text-center text-[var(--cust-ink-muted)] leading-relaxed">
                            Mendukung QRIS, BCA/Mandiri/BNI/BRI VA, GoPay, ShopeePay, dan Kartu Kredit 0%.
                        </p>
                    </div>

                    <div class="pt-3 border-t border-[var(--cust-rule-border)] grid grid-cols-2 gap-2 text-[10px] text-[var(--cust-ink-muted)]">
                        <span class="flex items-center gap-1">
                            <i data-lucide="award" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
                            <span>Garansi Resmi &amp; Toko</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-[var(--cust-tone-healthy-ink)]"></i>
                            <span>Midtrans Snap 3DS</span>
                        </span>
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<!-- Midtrans Snap SDK JS -->
<script 
    src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
    data-client-key="{{ $clientKey }}"
></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkoutForm = document.getElementById('checkout-form');
    const btnPay = document.getElementById('btn-pay-now');
    const btnPayText = document.getElementById('btn-pay-text');
    const btnPaySpinner = document.getElementById('btn-pay-spinner');
    const alertBox = document.getElementById('checkout-alert');

    function showAlert(message, type = 'error') {
        alertBox.classList.remove('hidden', 'bg-[var(--cust-tone-alert-bg)]', 'text-[var(--cust-tone-alert-ink)]', 'border-[var(--cust-tone-alert-ink)]/20', 'bg-[var(--cust-tone-healthy-bg)]', 'text-[var(--cust-tone-healthy-ink)]', 'border-[var(--cust-tone-healthy-ink)]/20');
        if (type === 'error') {
            alertBox.classList.add('bg-[var(--cust-tone-alert-bg)]', 'border', 'border-[var(--cust-tone-alert-ink)]/20', 'text-[var(--cust-tone-alert-ink)]');
            alertBox.innerHTML = `<i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i><span>${message}</span>`;
        } else {
            alertBox.classList.add('bg-[var(--cust-tone-healthy-bg)]', 'border', 'border-[var(--cust-tone-healthy-ink)]/20', 'text-[var(--cust-tone-healthy-ink)]');
            alertBox.innerHTML = `<i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i><span>${message}</span>`;
        }
        if (window.lucide) window.lucide.createIcons();
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function setBtnLoading(isLoading) {
        btnPay.disabled = isLoading;
        if (isLoading) {
            btnPayText.textContent = 'Menyiapkan Pembayaran Snap...';
            btnPaySpinner.classList.remove('hidden');
        } else {
            btnPayText.textContent = 'Bayar Sekarang dengan Midtrans';
            btnPaySpinner.classList.add('hidden');
        }
    }

    checkoutForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        setBtnLoading(true);

        const formData = new FormData(checkoutForm);

        try {
            const response = await fetch("{{ route('checkout.process') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                setBtnLoading(false);
                showAlert(data.message || 'Gagal memproses pesanan. Periksa kembali isian formulir Anda.', 'error');
                return;
            }

            const snapToken = data.snap_token;
            const redirectUrl = data.redirect_url;

            if (snapToken.startsWith('mock_snap_token_') || snapToken.startsWith('fallback_snap_token_')) {
                showAlert('Mode Sandbox: Pesanan berhasil dibuat (' + data.order_number + '). Mengalihkan...', 'success');
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1200);
                return;
            }

            if (window.snap && typeof window.snap.pay === 'function') {
                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        showAlert('Pembayaran terverifikasi! Mengalihkan ke nota Anda...', 'success');
                        window.location.href = redirectUrl;
                    },
                    onPending: function (result) {
                        showAlert('Menunggu penyelesaian pembayaran. Mengalihkan ke nota...', 'success');
                        window.location.href = redirectUrl;
                    },
                    onError: function (result) {
                        setBtnLoading(false);
                        showAlert('Pembayaran gagal diproses oleh penyedia transaksi.', 'error');
                    },
                    onClose: function () {
                        setBtnLoading(false);
                        showAlert('Modal pembayaran ditutup. Pesanan disimpan dengan status Pending.', 'error');
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 2000);
                    }
                });
            } else {
                showAlert('Pustaka Midtrans Snap tidak termuat. Mengalihkan...', 'error');
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1500);
            }

        } catch (error) {
            setBtnLoading(false);
            showAlert('Kendala jaringan: ' + error.message, 'error');
        }
    });
});
</script>
@endpush
