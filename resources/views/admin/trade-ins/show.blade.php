@extends('layouts.admin')

@section('page_title', 'Inspeksi Unit Trade-In ' . $tradeIn->trade_in_code)

@section('content')
<div class="space-y-6">

    <!-- Top Back Button & Quick Status Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.trade-ins.index') }}" class="p-2 rounded-xl bg-white/5 border border-white/10 text-[#8E8E93] hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-sm font-bold text-[#5AC8FA]">{{ $tradeIn->trade_in_code }}</span>
                    <span class="glass-badge {{ $tradeIn->status_badge_class }}">
                        <span class="glass-badge-dot"></span>
                        <span>{{ $tradeIn->status_label }}</span>
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-white mt-0.5">
                    Inspeksi Teknisi: {{ $tradeIn->phone_model }} ({{ $tradeIn->storage_capacity }})
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($tradeIn->status === 'submitted')
                <form action="{{ route('admin.trade-ins.decision', $tradeIn->trade_in_code) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="decision" value="unit_received">
                    <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                        <i data-lucide="package-check" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                        <span>Tandai Unit Diterima di Toko</span>
                    </button>
                </form>
            @endif
            <a href="{{ route('trade-in.show', $tradeIn->trade_in_code) }}" target="_blank" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                <span>Lihat Tampilan Customer</span>
            </a>
        </div>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-[#FF453A]/15 border border-[#FF453A]/30 text-xs text-[#FF453A]">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- =================================================================
             LEFT COLUMN (7 COLS): Customer Info & Technician Physical Inspection Form
             ================================================================= -->
        <div class="lg:col-span-7 space-y-6">

            <!-- 1. Customer & Payout Destination Card -->
            <div class="glass-card rounded-3xl p-6 border border-white/10 space-y-4">
                <h2 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4 text-[#0071E3]"></i>
                    <span>Identitas Pelanggan & Tujuan Pencairan</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                        <span class="text-[10px] text-[#8E8E93] uppercase font-bold block">Data Pemohon</span>
                        <p class="font-bold text-white text-sm">{{ $tradeIn->user->name ?? 'Customer' }}</p>
                        <p class="text-[#8E8E93]">{{ $tradeIn->user->email ?? '-' }}</p>
                        <p class="font-mono text-[#34C759]">{{ $tradeIn->user->phone_number ?? 'No. WA belum tersedia' }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1.5">
                        <span class="text-[10px] text-[#8E8E93] uppercase font-bold block">Preferensi Pencairan Dana</span>
                        @if($tradeIn->isVoucherPayout())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#34C759]/20 text-[#34C759]">
                                Kupon Potongan Belanja iPhone Baru
                            </span>
                            <p class="font-mono font-bold text-white text-sm mt-1">{{ $tradeIn->customer_account_number }}</p>
                            <p class="text-[#8E8E93]">a.n. {{ $tradeIn->customer_account_holder }}</p>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0071E3]/20 text-[#5AC8FA]">
                                Transfer Rekening Bank Customer
                            </span>
                            <p class="font-bold text-white mt-1">{{ $tradeIn->customer_bank_name }}</p>
                            <p class="font-mono text-[#5AC8FA] font-bold">{{ $tradeIn->customer_account_number }}</p>
                            <p class="text-[#8E8E93]">a.n. {{ $tradeIn->customer_account_holder }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. Form Inspeksi Teknisi Toko Saat Unit Diterima -->
            <div class="glass-card-elevated rounded-3xl p-6 sm:p-7 border border-white/15 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-white/10">
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i data-lucide="wrench" class="w-4 h-4 text-[#FF9F0A]"></i>
                            <span>Form Inspeksi Fisik & Fungsi Teknisi Toko</span>
                        </h2>
                        <p class="text-xs text-[#8E8E93] mt-0.5">
                            Sesuaikan parameter di bawah ini berdasarkan pengecekan aktual (3uTools & fisik) saat unit diterima di toko
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.trade-ins.inspect', $tradeIn->trade_in_code) }}" method="POST" class="space-y-5" id="tech-inspection-form">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kondisi Fisik Aktual -->
                        <div class="space-y-1.5">
                            <label for="inspect_physical" class="text-xs font-semibold text-white block">
                                Kondisi Fisik Bodi Aktual
                            </label>
                            <select id="inspect_physical" name="physical_condition" class="glass-input text-xs w-full bg-[#161B22] tech-calc-input">
                                @foreach($physicalConditions as $key => $info)
                                    <option value="{{ $key }}" {{ $tradeIn->physical_condition === $key ? 'selected' : '' }}>
                                        {{ $key }} ({{ $info['badge'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kondisi Layar Aktual -->
                        <div class="space-y-1.5">
                            <label for="inspect_screen" class="text-xs font-semibold text-white block">
                                Kondisi Layar LCD, TrueTone & Face ID
                            </label>
                            <select id="inspect_screen" name="screen_condition" class="glass-input text-xs w-full bg-[#161B22] tech-calc-input">
                                @foreach($screenConditions as $key => $info)
                                    <option value="{{ $key }}" {{ $tradeIn->screen_condition === $key ? 'selected' : '' }}>
                                        {{ $key }} ({{ $info['badge'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Battery Health Aktual -->
                        <div class="space-y-1.5">
                            <label for="inspect_bh" class="text-xs font-semibold text-white block">
                                Battery Health Aktual (%)
                            </label>
                            <input
                                type="number"
                                id="inspect_bh"
                                name="battery_health"
                                min="1"
                                max="100"
                                value="{{ old('battery_health', $tradeIn->battery_health) }}"
                                required
                                class="glass-input text-xs font-mono w-full tech-calc-input"
                            >
                        </div>

                        <!-- Kelengkapan Aksesoris Aktual -->
                        <div class="space-y-1.5">
                            <label for="inspect_accessories" class="text-xs font-semibold text-white block">
                                Kelengkapan Box & Aksesoris Aktual
                            </label>
                            <select id="inspect_accessories" name="accessories_status" class="glass-input text-xs w-full bg-[#161B22] tech-calc-input">
                                @foreach($accessoriesOptions as $key => $info)
                                    <option value="{{ $key }}" {{ $tradeIn->accessories_status === $key ? 'selected' : '' }}>
                                        {{ $key }} ({{ $info['badge'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nomor IMEI Terverifikasi -->
                        <div class="space-y-1.5">
                            <label for="inspect_imei" class="text-xs font-semibold text-white block">
                                Nomor IMEI Fisik Terverifikasi (*#06#)
                            </label>
                            <input
                                type="text"
                                id="inspect_imei"
                                name="imei"
                                value="{{ old('imei', $tradeIn->imei) }}"
                                placeholder="Masukkan 15 digit nomor IMEI"
                                class="glass-input text-xs font-mono w-full"
                            >
                        </div>

                        <!-- Status Tahap Inspeksi -->
                        <div class="space-y-1.5">
                            <label for="inspect_status" class="text-xs font-semibold text-white block">
                                Tahapan Inspeksi Saat Ini
                            </label>
                            <select id="inspect_status" name="status" class="glass-input text-xs w-full bg-[#161B22]">
                                <option value="submitted" {{ $tradeIn->status === 'submitted' ? 'selected' : '' }}>Submitted (Menunggu Unit)</option>
                                <option value="unit_received" {{ $tradeIn->status === 'unit_received' ? 'selected' : '' }}>Unit Diterima di Toko</option>
                                <option value="inspected" {{ in_array($tradeIn->status, ['submitted', 'unit_received', 'inspected']) ? 'selected' : '' }}>Selesai Diinspeksi Teknisi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rekomendasi Harga Kalkulator Teknisi & Input Final Price -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="text-xs">
                            <span class="text-[#8E8E93] block">Rekomendasi Kalkulator Berdasarkan Parameter Inspeksi:</span>
                            <span id="tech-recommended-price" class="text-lg font-black text-[#34C759]">
                                Rp {{ number_format($breakdown['estimated_price'], 0, ',', '.') }}
                            </span>
                        </div>
                        <button type="button" id="btn-apply-recommended" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                            <i data-lucide="wand-2" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                            <span>Gunakan Harga Rekomendasi</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="inspect_final_price" class="text-xs font-semibold text-white block">
                                Penawaran Harga Hasil Inspeksi (Rp)
                            </label>
                            <input
                                type="number"
                                step="1000"
                                id="inspect_final_price"
                                name="final_price"
                                value="{{ old('final_price', $tradeIn->final_price ?? $tradeIn->estimated_price) }}"
                                class="glass-input text-sm font-bold text-[#34C759] w-full"
                            >
                        </div>

                        <div class="space-y-1.5">
                            <label for="inspect_admin_notes" class="text-xs font-semibold text-white block">
                                Catatan Temuan Teknisi (3uTools / Fisik)
                            </label>
                            <input
                                type="text"
                                id="inspect_admin_notes"
                                name="admin_notes"
                                value="{{ old('admin_notes', $tradeIn->admin_notes) }}"
                                placeholder="Contoh: Skor 3uTools 98% Hijau, TrueTone & FaceID Normal"
                                class="glass-input text-xs w-full"
                            >
                        </div>
                    </div>

                    <div class="pt-3 border-t border-white/10 flex justify-end">
                        <button type="submit" class="glass-btn glass-btn-primary glass-btn-md text-xs font-bold">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Hasil Inspeksi Teknisi</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- =================================================================
             RIGHT COLUMN (5 COLS): Final Price Adjustment & Approve / Reject Panel
             ================================================================= -->
        <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">

            <div class="glass-card-elevated rounded-3xl p-6 sm:p-7 border border-white/15 space-y-6 shadow-2xl">
                <div class="pb-3 border-b border-white/10">
                    <span class="text-[10px] font-bold text-[#34C759] uppercase tracking-wider">Keputusan Manajemen Toko</span>
                    <h2 class="text-lg font-bold text-white mt-0.5">Penawaran Final & Persetujuan</h2>
                </div>

                <!-- Price Summary Box -->
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10">
                        <span class="text-[10px] text-[#8E8E93] block">Estimasi Awal Sistem</span>
                        <span class="text-base font-bold text-white mt-1 block">
                            Rp {{ number_format($tradeIn->estimated_price, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-[#34C759]/10 border border-[#34C759]/30">
                        <span class="text-[10px] text-[#34C759] font-bold block">Penawaran Final Saat Ini</span>
                        <span class="text-base font-black text-[#34C759] mt-1 block">
                            Rp {{ number_format($tradeIn->effective_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Decision Form (Approve / Reject / Paid Out) -->
                <form action="{{ route('admin.trade-ins.decision', $tradeIn->trade_in_code) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-1.5">
                        <label for="decision_final_price" class="text-xs font-bold text-white block">
                            Penyesuaian Harga Deal Final (Rp) <span class="text-[#FF453A]">*</span>
                        </label>
                        <input
                            type="number"
                            step="1000"
                            id="decision_final_price"
                            name="final_price"
                            value="{{ old('final_price', $tradeIn->final_price ?? $tradeIn->estimated_price) }}"
                            required
                            class="glass-input text-base font-black text-white w-full"
                        >
                        <p class="text-[11px] text-[#8E8E93]">
                            Harga ini yang akan dicairkan ke rekening customer atau menjadi nilai kupon potongan belanja.
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="decision_notes" class="text-xs font-bold text-white block">
                            Catatan Keputusan / Alasan Penyesuaian / Ref Transfer
                        </label>
                        <textarea
                            id="decision_notes"
                            name="admin_notes"
                            rows="3"
                            placeholder="Tuliskan rincian kesepakatan harga dengan customer, alasan penolakan, atau nomor referensi bukti transfer..."
                            class="glass-input text-xs w-full"
                        >{{ old('admin_notes', $tradeIn->admin_notes) }}</textarea>
                    </div>

                    <!-- Action Buttons: Approve & Reject -->
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <button
                            type="submit"
                            name="decision"
                            value="approved"
                            class="glass-btn glass-btn-primary glass-btn-md text-xs font-bold flex items-center justify-center gap-1.5 !bg-gradient-to-r !from-[#34C759] !to-[#28a745] !shadow-[#34C759]/30"
                        >
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span>Approve (Setujui)</span>
                        </button>

                        <button
                            type="submit"
                            name="decision"
                            value="rejected"
                            class="glass-btn glass-btn-danger glass-btn-md text-xs font-bold flex items-center justify-center gap-1.5"
                        >
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            <span>Reject (Tolak)</span>
                        </button>
                    </div>

                    <!-- Mark as Paid Out / Voucher Issued Button -->
                    @if(in_array($tradeIn->status, ['approved', 'inspected']))
                        <div class="pt-3 border-t border-white/10">
                            <button
                                type="submit"
                                name="decision"
                                value="paid_out"
                                class="glass-btn glass-btn-secondary glass-btn-md w-full text-xs font-bold border-[#5AC8FA]/40 text-[#5AC8FA] hover:bg-[#5AC8FA]/15"
                            >
                                <i data-lucide="wallet" class="w-4 h-4"></i>
                                <span>
                                    {{ $tradeIn->isVoucherPayout() ? 'Tandai Kupon Potongan Telah Aktif (Paid Out)' : 'Tandai Dana Sudah Ditransfer ke Rekening (Paid Out)' }}
                                </span>
                            </button>
                        </div>
                    @endif
                </form>

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const basePrice = {{ (float) $breakdown['base_price'] }};
    const physicalConditions = @json($physicalConditions);
    const screenConditions = @json($screenConditions);
    const accessoriesOptions = @json($accessoriesOptions);

    const selPhysical = document.getElementById('inspect_physical');
    const selScreen = document.getElementById('inspect_screen');
    const inpBh = document.getElementById('inspect_bh');
    const selAccessories = document.getElementById('inspect_accessories');
    const recLabel = document.getElementById('tech-recommended-price');
    const btnApply = document.getElementById('btn-apply-recommended');
    const inpFinalInspect = document.getElementById('inspect_final_price');
    const inpFinalDecision = document.getElementById('decision_final_price');

    let currentRecommendation = {{ (float) $breakdown['estimated_price'] }};

    function computeTechRecommendation() {
        const physicalRate = physicalConditions[selPhysical.value]?.deduction_rate ?? 0;
        const screenRate = screenConditions[selScreen.value]?.deduction_rate ?? 0;
        const bh = parseInt(inpBh.value || '85', 10);

        let bhRate = 0;
        if (bh >= 90) bhRate = 0;
        else if (bh >= 85) bhRate = 0.04;
        else if (bh >= 80) bhRate = 0.08;
        else bhRate = 0.15;

        const physicalDed = Math.round((basePrice * physicalRate) / 1000) * 1000;
        const screenDed = Math.round((basePrice * screenRate) / 1000) * 1000;
        const bhDed = Math.round((basePrice * bhRate) / 1000) * 1000;
        const accDed = accessoriesOptions[selAccessories.value]?.deduction_amount ?? 0;

        const minFloor = Math.max(1000000, Math.round((basePrice * 0.25) / 1000) * 1000);
        currentRecommendation = Math.max(minFloor, basePrice - (physicalDed + screenDed + bhDed + accDed));

        recLabel.textContent = 'Rp ' + Math.round(currentRecommendation).toLocaleString('id-ID');
    }

    document.querySelectorAll('.tech-calc-input').forEach((el) => {
        el.addEventListener('change', computeTechRecommendation);
        el.addEventListener('input', computeTechRecommendation);
    });

    btnApply.addEventListener('click', function () {
        computeTechRecommendation();
        inpFinalInspect.value = currentRecommendation;
        inpFinalDecision.value = currentRecommendation;
    });
});
</script>
@endpush
