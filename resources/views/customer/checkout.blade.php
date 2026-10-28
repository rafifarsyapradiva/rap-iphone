@extends('layouts.customer')

@section('title', 'Checkout Terpadu — Rap-iPhone Authorized Retail')
@section('meta_description', 'Selesaikan pemesanan unit iPhone Anda secara aman dan instan dengan Midtrans Payment Gateway.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Distraction-Free Checkout Header & Step Progress Bar -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#8E8E93] hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Keranjang</span>
            </a>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#34C759]/10 border border-[#34C759]/30 text-xs text-[#34C759] font-medium">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>Enkripsi SSL 256-Bit Aman</span>
            </div>
        </div>

        <!-- 3-Step Wizard Indicator -->
        <div class="flex items-center justify-center max-w-xl mx-auto pt-2 pb-4">
            <div class="flex items-center gap-3 w-full">
                <!-- Step 1: Cart -->
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#34C759] text-white flex items-center justify-center text-xs font-bold shadow-md">
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </div>
                    <span class="text-xs text-[#8E8E93] font-medium hidden sm:inline">Keranjang</span>
                </div>
                <div class="h-0.5 flex-1 bg-[#34C759]"></div>

                <!-- Step 2: Shipping (Active) -->
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#0071E3] text-white flex items-center justify-center text-xs font-bold ring-4 ring-[#0071E3]/20 shadow-lg">
                        2
                    </div>
                    <span class="text-xs text-white font-bold">Data & Pengiriman</span>
                </div>
                <div class="h-0.5 flex-1 bg-white/10"></div>

                <!-- Step 3: Payment -->
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-white/10 text-[#8E8E93] flex items-center justify-center text-xs font-bold border border-white/20">
                        3
                    </div>
                    <span class="text-xs text-[#8E8E93] font-medium hidden sm:inline">Pembayaran Snap</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Banner (if error session exists) -->
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-[#FF453A]/15 border border-[#FF453A]/30 text-xs text-[#FF453A] flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Dynamic JS Feedback Alert Box -->
    <div id="checkout-alert" class="hidden p-4 rounded-2xl text-xs flex items-center gap-3 transition-all"></div>

    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- =========================================================================
                 LEFT COLUMN: Form Penerima, WhatsApp, Kurir & Alamat
                 ========================================================================= -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. Data Penerima & Kontak WhatsApp -->
                <div class="glass-card-elevated rounded-3xl p-6 sm:p-7 border border-white/15 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-white/10">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-[#0071E3]/20 text-[#0071E3] flex items-center justify-center">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-white">Identitas Penerima</h2>
                                <p class="text-[11px] text-[#8E8E93]">Informasi identitas pemesan untuk konfirmasi nota resmi</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-[#8E8E93]">Langkah 1/3</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Penerima -->
                        <div class="space-y-1.5">
                            <label for="recipient_name" class="text-xs font-semibold text-white/90 block">
                                Nama Lengkap Penerima <span class="text-[#FF453A]">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="recipient_name" 
                                name="recipient_name" 
                                value="{{ old('recipient_name', $user->name ?? '') }}" 
                                required 
                                placeholder="Contoh: Budi Santoso"
                                class="glass-input text-xs sm:text-sm w-full @error('recipient_name') border-[#FF453A] @enderror"
                            >
                            @error('recipient_name')
                                <p class="text-[11px] text-[#FF453A] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="recipient_phone" class="text-xs font-semibold text-white/90 block">
                                    No. WhatsApp Aktif <span class="text-[#FF453A]">*</span>
                                </label>
                                <span class="text-[10px] text-[#34C759] font-medium flex items-center gap-1">
                                    <i data-lucide="message-circle" class="w-3 h-3"></i> Notif Otomatis
                                </span>
                            </div>
                            <input 
                                type="tel" 
                                id="recipient_phone" 
                                name="recipient_phone" 
                                value="{{ old('recipient_phone', $user->phone_number ?? '') }}" 
                                required 
                                placeholder="Contoh: 081234567890"
                                class="glass-input text-xs sm:text-sm w-full font-mono @error('recipient_phone') border-[#FF453A] @enderror"
                            >
                            @error('recipient_phone')
                                <p class="text-[11px] text-[#FF453A] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email Akun Terhubung -->
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-[#8E8E93]">
                            <i data-lucide="mail" class="w-4 h-4 text-[#0071E3]"></i>
                            <span>Faktur PDF akan dikirimkan ke email akun Anda:</span>
                        </div>
                        <span class="text-white font-mono font-medium">{{ $user->email }}</span>
                    </div>
                </div>

                <!-- 2. Pilihan Opsi Kurir Pengiriman -->
                <div class="glass-card-elevated rounded-3xl p-6 sm:p-7 border border-white/15 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-white/10">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-[#34C759]/20 text-[#34C759] flex items-center justify-center">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-white">Metode Pengiriman</h2>
                                <p class="text-[11px] text-[#8E8E93]">Pilih ekspedisi terpercaya dengan garansi proteksi transit</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-[#8E8E93]">Langkah 2/3</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" role="radiogroup" aria-label="Pilihan Kurir">
                        @foreach($couriers as $index => $courier)
                            <label class="relative flex flex-col p-4 rounded-2xl border border-white/10 bg-white/5 hover:bg-white/10 hover:border-white/20 cursor-pointer transition-all has-[:checked]:border-[#0071E3] has-[:checked]:bg-[#0071E3]/10 has-[:checked]:ring-1 has-[:checked]:ring-[#0071E3]">
                                <input 
                                    type="radio" 
                                    name="shipping_courier" 
                                    value="{{ $courier['name'] }}" 
                                    class="sr-only" 
                                    {{ $index === 0 ? 'checked' : '' }}
                                >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-white tracking-tight">{{ $courier['id'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#34C759]/20 text-[#34C759] border border-[#34C759]/30">
                                        {{ $courier['badge'] }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-[#8E8E93] leading-snug">{{ $courier['name'] }}</span>
                                <div class="mt-3 pt-2 border-t border-white/10 flex items-center justify-between text-[11px]">
                                    <span class="text-[#8E8E93] flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i> {{ $courier['eta'] }}
                                    </span>
                                    <span class="font-bold text-[#34C759]">Rp 0 (GRATIS)</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Alamat Lengkap & Catatan Tambahan -->
                <div class="glass-card-elevated rounded-3xl p-6 sm:p-7 border border-white/15 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-white/10">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-[#BF5AF2]/20 text-[#BF5AF2] flex items-center justify-center">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-white">Alamat Tujuan Pengiriman</h2>
                                <p class="text-[11px] text-[#8E8E93]">Pastikan alamat detail dan nomor rumah tertulis dengan jelas</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-[#8E8E93]">Langkah 3/3</span>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="shipping_address" class="text-xs font-semibold text-white/90 block">
                                Alamat Lengkap & Kode Pos <span class="text-[#FF453A]">*</span>
                            </label>
                            <textarea 
                                id="shipping_address" 
                                name="shipping_address" 
                                rows="3" 
                                required 
                                placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota/kabupaten, dan kode pos tujuan."
                                class="glass-input text-xs sm:text-sm w-full leading-relaxed @error('shipping_address') border-[#FF453A] @enderror"
                            >{{ old('shipping_address', $user->address ?? '') }}</textarea>
                            @error('shipping_address')
                                <p class="text-[11px] text-[#FF453A] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="customer_notes" class="text-xs font-semibold text-white/90 block">
                                Catatan Khusus untuk Kurir / Toko <span class="text-[10px] text-[#8E8E93] font-normal">(Opsional)</span>
                            </label>
                            <input 
                                type="text" 
                                id="customer_notes" 
                                name="customer_notes" 
                                value="{{ old('customer_notes') }}" 
                                placeholder="Contoh: Titipkan ke satpam jika penerima tidak di tempat."
                                class="glass-input text-xs sm:text-sm w-full"
                            >
                        </div>
                    </div>
                </div>

            </div>

            <!-- =========================================================================
                 RIGHT COLUMN: Ringkasan Rincian Pesanan & Tombol Bayar
                 ========================================================================= -->
            <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">

                <div class="glass-card-elevated rounded-3xl p-6 sm:p-7 border border-white/15 space-y-6 shadow-2xl">
                    <div class="flex items-center justify-between pb-3 border-b border-white/10">
                        <h2 class="text-base font-bold text-white">Ringkasan Pesanan</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-white">
                            {{ $totalQuantity }} Unit
                        </span>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-3 max-h-[320px] overflow-y-auto pr-1">
                        @foreach($cart as $item)
                            <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-white/5 border border-white/10">
                                <!-- Thumbnail -->
                                <div class="w-14 h-14 rounded-xl bg-black/40 border border-white/10 flex items-center justify-center p-1 shrink-0 overflow-hidden">
                                    @if(!empty($item['image']))
                                        <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" width="56" height="56" loading="lazy" decoding="async" class="w-full h-full object-contain">
                                    @else
                                        <i data-lucide="smartphone" class="w-6 h-6 text-[#8E8E93]"></i>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h3 class="text-xs font-bold text-white truncate">{{ $item['product_name'] }}</h3>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] text-[#8E8E93] mt-0.5">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full border border-white/20" style="background-color: {{ $item['color_hex'] ?? '#707070' }};"></span>
                                        <span>{{ $item['color_name'] }}</span>
                                        <span>&bull;</span>
                                        <span class="text-white font-medium">{{ $item['storage_capacity'] }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-1 text-xs">
                                        <span class="text-[#8E8E93]">{{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                        <span class="text-white font-bold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Calculation Breakdown -->
                    <div class="space-y-3 text-xs border-t border-white/10 pt-4">
                        <div class="flex items-center justify-between text-[#8E8E93]">
                            <span>Subtotal Unit ({{ $totalQuantity }} unit)</span>
                            <span class="text-white font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-[#8E8E93]">
                            <span class="flex items-center gap-1">
                                <span>Ongkos Kirim & Proteksi</span>
                                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#34C759]"></i>
                            </span>
                            <span class="text-[#34C759] font-bold">GRATIS</span>
                        </div>

                        <div class="flex items-center justify-between text-[#8E8E93]">
                            <span>Biaya Layanan Midtrans</span>
                            <span class="text-[#34C759] font-bold">Rp 0 (Gratis)</span>
                        </div>

                        <div class="flex items-center justify-between text-[#8E8E93]">
                            <span>Pajak PPN (11%)</span>
                            <span class="text-white font-medium">Termasuk</span>
                        </div>
                    </div>

                    <!-- Grand Total Tagihan -->
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-white/10 via-white/5 to-[#0071E3]/15 border border-white/20 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs uppercase font-extrabold tracking-wider text-[#8E8E93]">Total Pembayaran</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#0071E3]/20 text-[#0071E3] font-bold border border-[#0071E3]/30">
                                IDR Rupiah
                            </span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black text-white tracking-tight bg-gradient-to-r from-white via-[#E5E5EA] to-[#0071E3] bg-clip-text text-transparent">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Submit Button with Loading State -->
                    <div class="space-y-3 pt-2">
                        <button 
                            type="submit" 
                            id="btn-pay-now" 
                            class="glass-btn glass-btn-primary glass-btn-lg w-full shadow-2xl flex items-center justify-center gap-2 group !py-3.5 font-bold tracking-tight text-sm"
                        >
                            <i data-lucide="lock" class="w-4 h-4 text-white"></i>
                            <span id="btn-pay-text">Bayar Sekarang dengan Midtrans</span>
                            <div id="btn-pay-spinner" class="hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </button>

                        <p class="text-[11px] text-center text-[#8E8E93] leading-relaxed">
                            Mendukung QRIS (GoPay, OVO, Dana), BCA/Mandiri/BRI Virtual Account, & Kartu Kredit berlogo resmi.
                        </p>
                    </div>

                    <!-- Apple Store Trust Badges -->
                    <div class="pt-4 border-t border-white/10 grid grid-cols-2 gap-3 text-[11px] text-[#8E8E93]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="award" class="w-4 h-4 text-[#0071E3] shrink-0"></i>
                            <span>Unit Original Bergaransi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#34C759] shrink-0"></i>
                            <span>Midtrans Snap Terverifikasi</span>
                        </div>
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
        alertBox.classList.remove('hidden', 'bg-[#FF453A]/15', 'border-[#FF453A]/30', 'text-[#FF453A]', 'bg-[#34C759]/15', 'border-[#34C759]/30', 'text-[#34C759]');
        if (type === 'error') {
            alertBox.classList.add('bg-[#FF453A]/15', 'border', 'border-[#FF453A]/30', 'text-[#FF453A]');
            alertBox.innerHTML = `<i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i><span>${message}</span>`;
        } else {
            alertBox.classList.add('bg-[#34C759]/15', 'border', 'border-[#34C759]/30', 'text-[#34C759]');
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

            // Check if mock snap token (for testing and offline local environment without Midtrans sandbox key)
            if (snapToken.startsWith('mock_snap_token_') || snapToken.startsWith('fallback_snap_token_')) {
                showAlert('Mode Dev/Mock Terdeteksi: Pesanan berhasil dibuat (' + data.order_number + '). Mengalihkan ke nota pesanan...', 'success');
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1200);
                return;
            }

            // Trigger Midtrans Snap Popup Window
            if (window.snap && typeof window.snap.pay === 'function') {
                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        showAlert('Pembayaran terverifikasi berhasil! Mengalihkan ke nota Anda...', 'success');
                        window.location.href = redirectUrl;
                    },
                    onPending: function (result) {
                        showAlert('Menunggu penyelesaian pembayaran. Mengalihkan ke nota Anda...', 'success');
                        window.location.href = redirectUrl;
                    },
                    onError: function (result) {
                        setBtnLoading(false);
                        showAlert('Pembayaran gagal diproses oleh penyedia transaksi. Silakan coba kembali.', 'error');
                    },
                    onClose: function () {
                        setBtnLoading(false);
                        showAlert('Modal pembayaran ditutup sebelum transaksi selesai. Pesanan disimpan dengan status Pending di akun Anda.', 'error');
                        // Redirect to success page or let user retry
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 2000);
                    }
                });
            } else {
                // If snap JS is blocked or unavailable
                showAlert('Pustaka Midtrans Snap tidak termuat. Mengalihkan ke halaman pesanan...', 'error');
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1500);
            }

        } catch (error) {
            setBtnLoading(false);
            showAlert('Terjadi kendala jaringan saat menghubungi server: ' + error.message, 'error');
        }
    });
});
</script>
@endpush

