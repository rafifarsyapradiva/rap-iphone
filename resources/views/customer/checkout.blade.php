@extends('layouts.customer')

@section('title', 'Checkout Terpadu — Rap-iPhone Authorized Style Partner')
@section('meta_description', 'Selesaikan pemesanan unit iPhone Anda secara aman dengan pilihan Kurir Berasuransi atau Click & Collect Store serta pembayaran Midtrans Snap.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- 1. Checkout Header & Stepper -->
    <div class="space-y-4 pb-4 border-b border-[#E5E5EA]">
        <div class="flex items-center justify-between">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#6E6E73] hover:text-[#1D1D1F] transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Keranjang</span>
            </a>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E8F8EE] text-xs text-[#008040] font-semibold">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>Checkout Terenkripsi Midtrans</span>
            </div>
        </div>

        <!-- 3-Step Digimap Progress Indicator -->
        <div class="flex items-center justify-center max-w-xl mx-auto pt-1">
            <div class="flex items-center gap-3 w-full">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#008040] text-white flex items-center justify-center text-xs font-bold">
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </div>
                    <span class="text-xs text-[#1D1D1F] font-semibold hidden sm:inline">Tas Belanja</span>
                </div>
                <div class="h-0.5 flex-1 bg-[#008040]"></div>

                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#0066CC] text-white flex items-center justify-center text-xs font-bold ring-4 ring-[#E8F2FF]">
                        2
                    </div>
                    <span class="text-xs text-[#1D1D1F] font-bold">Pengiriman / Click &amp; Collect</span>
                </div>
                <div class="h-0.5 flex-1 bg-[#D2D2D7]"></div>

                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#F5F5F7] text-[#6E6E73] flex items-center justify-center text-xs font-bold border border-[#D2D2D7]">
                        3
                    </div>
                    <span class="text-xs text-[#6E6E73] font-medium hidden sm:inline">Pembayaran Snap</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-[#FFEBEA] border border-[#D70015]/30 text-xs text-[#D70015] font-semibold flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Dynamic JS Feedback Alert Box -->
    <div id="checkout-alert" class="hidden p-4 rounded-2xl text-xs font-semibold flex items-center gap-3 transition-all"></div>

    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- =========================================================================
                 LEFT COLUMN: Form Penerima, Metode Pengiriman / Click & Collect, Alamat
                 ========================================================================= -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. Data Penerima & Kontak WhatsApp -->
                <div class="rounded-[24px] bg-[#F5F5F7] p-6 sm:p-7 border border-[#E5E5EA] space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-[#D2D2D7]">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-[#E8F2FF] text-[#0066CC] flex items-center justify-center">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#1D1D1F]">1. Identitas Penerima</h2>
                                <p class="text-[11px] text-[#6E6E73]">Digunakan untuk pencetakan Nota PDF Resmi &amp; notifikasi WhatsApp</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="recipient_name" class="text-xs font-semibold text-[#1D1D1F] block">
                                Nama Lengkap Penerima <span class="text-[#D70015]">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="recipient_name" 
                                name="recipient_name" 
                                value="{{ old('recipient_name', $user->name ?? '') }}" 
                                required 
                                placeholder="Contoh: Budi Santoso"
                                class="glass-input !bg-white text-xs sm:text-sm w-full"
                            >
                            @error('recipient_name')
                                <p class="text-[11px] text-[#D70015] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="recipient_phone" class="text-xs font-semibold text-[#1D1D1F] block">
                                    No. WhatsApp Aktif <span class="text-[#D70015]">*</span>
                                </label>
                                <span class="text-[10px] text-[#008040] font-semibold flex items-center gap-1">
                                    <i data-lucide="message-circle" class="w-3 h-3"></i> Fonnte Auto-Notify
                                </span>
                            </div>
                            <input 
                                type="tel" 
                                id="recipient_phone" 
                                name="recipient_phone" 
                                value="{{ old('recipient_phone', $user->phone_number ?? '') }}" 
                                required 
                                placeholder="Contoh: 081234567890"
                                class="glass-input !bg-white text-xs sm:text-sm w-full font-mono"
                            >
                            @error('recipient_phone')
                                <p class="text-[11px] text-[#D70015] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-white border border-[#E5E5EA] flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-[#6E6E73]">
                            <i data-lucide="mail" class="w-4 h-4 text-[#0066CC]"></i>
                            <span>Nota PDF Resmi juga dikirimkan ke email:</span>
                        </div>
                        <span class="text-[#1D1D1F] font-mono font-semibold">{{ $user->email }}</span>
                    </div>
                </div>

                <!-- 2. Pilihan Metode Pengiriman (Kurir Ekspedisi vs Click & Collect) -->
                <div class="rounded-[24px] bg-[#F5F5F7] p-6 sm:p-7 border border-[#E5E5EA] space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-[#D2D2D7]">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-[#E8F8EE] text-[#008040] flex items-center justify-center">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#1D1D1F]">2. Metode Pengiriman &amp; Click &amp; Collect</h2>
                                <p class="text-[11px] text-[#6E6E73]">Pilih pengiriman kurir berasuransi atau ambil langsung di gerai</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" role="radiogroup" aria-label="Pilihan Kurir">
                        @foreach($couriers as $index => $courier)
                            <label class="relative flex flex-col p-4 rounded-2xl border border-[#D2D2D7] bg-white hover:border-[#86868B] cursor-pointer transition-all has-[:checked]:border-[#0066CC] has-[:checked]:border-2 has-[:checked]:bg-[#E8F2FF]/30">
                                <input 
                                    type="radio" 
                                    name="shipping_courier" 
                                    value="{{ $courier['name'] }}" 
                                    class="sr-only" 
                                    {{ $index === 0 ? 'checked' : '' }}
                                >
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-bold text-[#1D1D1F] tracking-tight">{{ $courier['id'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E8F8EE] text-[#008040]">
                                        {{ $courier['badge'] }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-[#6E6E73] leading-snug">{{ $courier['name'] }}</span>
                                <div class="mt-3 pt-2 border-t border-[#E5E5EA] flex items-center justify-between text-[11px]">
                                    <span class="text-[#6E6E73] flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i> {{ $courier['eta'] }}
                                    </span>
                                    <span class="font-bold text-[#008040]">GRATIS</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Alamat Lengkap & Catatan Tambahan -->
                <div class="rounded-[24px] bg-[#F5F5F7] p-6 sm:p-7 border border-[#E5E5EA] space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-[#D2D2D7]">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-[#FFF2EB] text-[#BF4800] flex items-center justify-center">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#1D1D1F]">3. Alamat Pengiriman / Lokasi Pengambilan</h2>
                                <p class="text-[11px] text-[#6E6E73]">Pastikan alamat lengkap tertulis jelas untuk kelancaran pengiriman</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="shipping_address" class="text-xs font-semibold text-[#1D1D1F] block">
                                Alamat Lengkap &amp; Kode Pos <span class="text-[#D70015]">*</span>
                            </label>
                            <textarea 
                                id="shipping_address" 
                                name="shipping_address" 
                                rows="3" 
                                required 
                                placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota/kabupaten, dan kode pos tujuan."
                                class="glass-input !bg-white text-xs sm:text-sm w-full leading-relaxed"
                            >{{ old('shipping_address', $user->address ?? '') }}</textarea>
                            @error('shipping_address')
                                <p class="text-[11px] text-[#D70015] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="customer_notes" class="text-xs font-semibold text-[#1D1D1F] block">
                                Catatan Pesanan / Kode Voucher Trade-In <span class="text-[10px] text-[#6E6E73] font-normal">(Opsional)</span>
                            </label>
                            <input 
                                type="text" 
                                id="customer_notes" 
                                name="customer_notes" 
                                value="{{ old('customer_notes') }}" 
                                placeholder="Contoh: Gunakan kode voucher Trade-In VCH-XXXXXX atau jadwal ambil di toko."
                                class="glass-input !bg-white text-xs sm:text-sm w-full"
                            >
                        </div>
                    </div>
                </div>

            </div>

            <!-- =========================================================================
                 RIGHT COLUMN: Ringkasan Rincian Pesanan & Tombol Bayar Midtrans
                 ========================================================================= -->
            <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">

                <div class="rounded-[24px] bg-[#F5F5F7] p-6 sm:p-7 border border-[#E5E5EA] space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-[#D2D2D7]">
                        <h2 class="text-base font-bold text-[#1D1D1F]">Ringkasan Pesanan</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white border border-[#D2D2D7] text-[#1D1D1F]">
                            {{ $totalQuantity }} Unit
                        </span>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-3 max-h-[320px] overflow-y-auto pr-1">
                        @foreach($cart as $item)
                            <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-white border border-[#E5E5EA]">
                                <div class="w-14 h-14 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA] flex items-center justify-center p-1 shrink-0 overflow-hidden">
                                    @if(!empty($item['image']))
                                        <img src="{{ asset($item['image']) }}" alt="{{ $item['product_name'] }}" width="56" height="56" loading="lazy" decoding="async" class="w-full h-full object-contain">
                                    @else
                                        <i data-lucide="smartphone" class="w-6 h-6 text-[#1D1D1F]"></i>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xs font-bold text-[#1D1D1F] truncate">{{ $item['product_name'] }}</h3>
                                    <div class="flex items-center gap-2 text-[11px] text-[#6E6E73] mt-0.5">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full border border-black/15" style="background-color: {{ $item['color_hex'] ?? '#707070' }};"></span>
                                        <span>{{ $item['color_name'] }}</span>
                                        <span>&bull;</span>
                                        <span class="text-[#1D1D1F] font-semibold">{{ $item['storage_capacity'] }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-1 text-xs">
                                        <span class="text-[#6E6E73]">{{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                        <span class="text-[#1D1D1F] font-bold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Calculation Breakdown -->
                    <div class="space-y-2.5 text-xs border-t border-[#D2D2D7] pt-4">
                        <div class="flex items-center justify-between text-[#6E6E73]">
                            <span>Subtotal Unit ({{ $totalQuantity }} unit)</span>
                            <span class="text-[#1D1D1F] font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-[#6E6E73]">
                            <span>Pengiriman &amp; Proteksi Asuransi</span>
                            <span class="text-[#008040] font-bold">GRATIS</span>
                        </div>

                        <div class="flex items-center justify-between text-[#6E6E73]">
                            <span>Biaya Layanan Midtrans</span>
                            <span class="text-[#008040] font-bold">Rp 0 (Gratis)</span>
                        </div>

                        <div class="flex items-center justify-between text-[#6E6E73]">
                            <span>Pajak PPN (11%)</span>
                            <span class="text-[#1D1D1F] font-medium">Termasuk</span>
                        </div>
                    </div>

                    <!-- Grand Total Tagihan -->
                    <div class="p-4 rounded-2xl bg-white border border-[#D2D2D7] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs uppercase font-bold tracking-wider text-[#6E6E73]">Total Pembayaran</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#E8F2FF] text-[#0066CC] font-bold">
                                IDR Resmi
                            </span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] tracking-tight">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Submit Button with Loading State -->
                    <div class="space-y-3 pt-1">
                        <button 
                            type="submit" 
                            id="btn-pay-now" 
                            class="glass-btn glass-btn-primary glass-btn-lg w-full flex items-center justify-center gap-2 !py-3.5 font-bold text-sm"
                        >
                            <i data-lucide="lock" class="w-4 h-4 text-white"></i>
                            <span id="btn-pay-text">Bayar Sekarang dengan Midtrans</span>
                            <div id="btn-pay-spinner" class="hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </button>

                        <p class="text-[11px] text-center text-[#6E6E73] leading-relaxed">
                            Mendukung QRIS (GoPay, ShopeePay, M-Banking), BCA/Mandiri/BNI/BRI Virtual Account, &amp; Kartu Kredit Cicilan 0%.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#D2D2D7] grid grid-cols-2 gap-3 text-[11px] text-[#6E6E73] font-medium">
                        <div class="flex items-center gap-2">
                            <i data-lucide="award" class="w-4 h-4 text-[#0066CC] shrink-0"></i>
                            <span>Garansi Resmi &amp; Toko</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#008040] shrink-0"></i>
                            <span>Midtrans Snap Verified</span>
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
        alertBox.classList.remove('hidden', 'bg-[#FFEBEA]', 'border-[#D70015]/30', 'text-[#D70015]', 'bg-[#E8F8EE]', 'border-[#008040]/30', 'text-[#008040]');
        if (type === 'error') {
            alertBox.classList.add('bg-[#FFEBEA]', 'border', 'border-[#D70015]/30', 'text-[#D70015]');
            alertBox.innerHTML = `<i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i><span>${message}</span>`;
        } else {
            alertBox.classList.add('bg-[#E8F8EE]', 'border', 'border-[#008040]/30', 'text-[#008040]');
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
                showAlert('Mode Dev/Mock Terdeteksi: Pesanan berhasil dibuat (' + data.order_number + '). Mengalihkan ke nota pesanan...', 'success');
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1200);
                return;
            }

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
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 2000);
                    }
                });
            } else {
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
