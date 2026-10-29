@extends('layouts.admin')

@section('title', 'Inspeksi Unit Trade-In ' . $tradeIn->trade_in_code . ' — Rap-iPhone Admin')
@section('page_title', 'Inspeksi Unit Trade-In')

@section('content')
<x-admin.page-header
    :eyebrow="$tradeIn->trade_in_code"
    icon="refresh-cw"
    :title="'Inspeksi Teknisi: ' . $tradeIn->phone_model . ' (' . $tradeIn->storage_capacity . ')'"
    subtitle="Verifikasi kondisi fisik aktual, kesehatan baterai, kelengkapan, dan tetapkan harga penawaran deal final.">
    <x-slot:actions>
        <a href="{{ route('admin.trade-ins.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i><span>Kembali ke Antrean</span>
        </a>
        @if($tradeIn->status === 'submitted')
            <form action="{{ route('admin.trade-ins.decision', $tradeIn->trade_in_code) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="decision" value="unit_received">
                <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm">
                    <i data-lucide="package-check" class="w-3.5 h-3.5"></i>
                    <span>Tandai Unit Diterima di Toko</span>
                </button>
            </form>
        @endif
        <a href="{{ route('trade-in.show', $tradeIn->trade_in_code) }}" target="_blank" rel="noopener" class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
            <span>Tampilan Pelanggan</span>
        </a>
    </x-slot:actions>
</x-admin.page-header>

<!-- Validation Errors -->
@if($errors->any())
    <div class="adm-alert adm-tone-red" role="alert">
        <i data-lucide="alert-octagon"></i>
        <ul class="list-disc list-inside space-y-0.5">
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
        <x-admin.card title="Identitas Pelanggan &amp; Tujuan Pencairan" icon="user-check">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3.5 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] space-y-1.5">
                    <span class="text-[10px] text-[#6E6E73] uppercase font-bold block">Data Pemohon</span>
                    <p class="font-bold text-[#1D1D1F] text-sm">{{ $tradeIn->user->name ?? 'Customer' }}</p>
                    <p class="text-[#6E6E73]">{{ $tradeIn->user->email ?? '-' }}</p>
                    <p class="adm-mono text-[#008040] font-bold">{{ $tradeIn->user->phone_number ?? 'No. WA belum tersedia' }}</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] space-y-1.5">
                    <span class="text-[10px] text-[#6E6E73] uppercase font-bold block">Preferensi Pencairan Dana</span>
                    @if($tradeIn->isVoucherPayout())
                        <x-admin.status-badge tone="green" label="Kupon Potongan Belanja iPhone Baru" :dot="false" />
                        <p class="adm-mono font-bold text-[#1D1D1F] text-sm mt-1">{{ $tradeIn->customer_account_number }}</p>
                        <p class="text-[#6E6E73]">a.n. {{ $tradeIn->customer_account_holder }}</p>
                    @else
                        <x-admin.status-badge tone="blue" label="Transfer Rekening Bank Customer" :dot="false" />
                        <p class="font-bold text-[#1D1D1F] mt-1">{{ $tradeIn->customer_bank_name }}</p>
                        <p class="adm-mono text-[#0066CC] font-bold">{{ $tradeIn->customer_account_number }}</p>
                        <p class="text-[#6E6E73]">a.n. {{ $tradeIn->customer_account_holder }}</p>
                    @endif
                </div>
            </div>
        </x-admin.card>

        <!-- 2. Form Inspeksi Teknisi Toko Saat Unit Diterima -->
        <x-admin.card title="Form Inspeksi Fisik &amp; Fungsi Teknisi Toko" icon="wrench"
            description="Sesuaikan parameter di bawah ini berdasarkan pengecekan aktual (3uTools & fisik) saat unit diterima di toko.">

            <form action="{{ route('admin.trade-ins.inspect', $tradeIn->trade_in_code) }}" method="POST" class="space-y-4" id="tech-inspection-form">
                @csrf
                @method('PUT')

                <div class="adm-grid-form cols-2">
                    <!-- Kondisi Fisik Aktual -->
                    <div class="adm-field">
                        <label for="inspect_physical" class="adm-label">Kondisi Fisik Bodi Aktual</label>
                        <select id="inspect_physical" name="physical_condition" class="adm-select tech-calc-input">
                            @foreach($physicalConditions as $key => $info)
                                <option value="{{ $key }}" {{ $tradeIn->physical_condition === $key ? 'selected' : '' }}>
                                    {{ $key }} ({{ $info['badge'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kondisi Layar Aktual -->
                    <div class="adm-field">
                        <label for="inspect_screen" class="adm-label">Kondisi Layar LCD, TrueTone &amp; Face ID</label>
                        <select id="inspect_screen" name="screen_condition" class="adm-select tech-calc-input">
                            @foreach($screenConditions as $key => $info)
                                <option value="{{ $key }}" {{ $tradeIn->screen_condition === $key ? 'selected' : '' }}>
                                    {{ $key }} ({{ $info['badge'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Battery Health Aktual -->
                    <div class="adm-field">
                        <label for="inspect_bh" class="adm-label">Battery Health Aktual (%)</label>
                        <input type="number" id="inspect_bh" name="battery_health" min="1" max="100"
                               value="{{ old('battery_health', $tradeIn->battery_health) }}" required class="adm-input adm-mono tech-calc-input">
                    </div>

                    <!-- Kelengkapan Aksesoris Aktual -->
                    <div class="adm-field">
                        <label for="inspect_accessories" class="adm-label">Kelengkapan Box &amp; Aksesoris Aktual</label>
                        <select id="inspect_accessories" name="accessories_status" class="adm-select tech-calc-input">
                            @foreach($accessoriesOptions as $key => $info)
                                <option value="{{ $key }}" {{ $tradeIn->accessories_status === $key ? 'selected' : '' }}>
                                    {{ $key }} ({{ $info['badge'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nomor IMEI Terverifikasi -->
                    <div class="adm-field">
                        <label for="inspect_imei" class="adm-label">Nomor IMEI Fisik Terverifikasi (*#06#)</label>
                        <input type="text" id="inspect_imei" name="imei" value="{{ old('imei', $tradeIn->imei) }}"
                               placeholder="Masukkan 15 digit nomor IMEI" class="adm-input adm-mono">
                    </div>

                    <!-- Status Tahap Inspeksi -->
                    <div class="adm-field">
                        <label for="inspect_status" class="adm-label">Tahapan Inspeksi Saat Ini</label>
                        <select id="inspect_status" name="status" class="adm-select">
                            <option value="submitted" {{ $tradeIn->status === 'submitted' ? 'selected' : '' }}>Submitted (Menunggu Unit)</option>
                            <option value="unit_received" {{ $tradeIn->status === 'unit_received' ? 'selected' : '' }}>Unit Diterima di Toko</option>
                            <option value="inspected" {{ in_array($tradeIn->status, ['submitted', 'unit_received', 'inspected']) ? 'selected' : '' }}>Selesai Diinspeksi Teknisi</option>
                        </select>
                    </div>
                </div>

                <!-- Rekomendasi Harga Kalkulator Teknisi & Input Final Price -->
                <div class="p-4 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-xs">
                        <span class="text-[#6E6E73] block">Rekomendasi Kalkulator Berdasarkan Parameter Inspeksi:</span>
                        <span id="tech-recommended-price" class="text-xl font-extrabold text-[#008040] block mt-0.5">
                            Rp {{ number_format($breakdown['estimated_price'], 0, ',', '.') }}
                        </span>
                    </div>
                    <button type="button" id="btn-apply-recommended" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">
                        <i data-lucide="wand-2" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                        <span>Gunakan Harga Rekomendasi</span>
                    </button>
                </div>

                <div class="adm-grid-form cols-2">
                    <div class="adm-field">
                        <label for="inspect_final_price" class="adm-label">Penawaran Harga Hasil Inspeksi (Rp)</label>
                        <input type="number" step="1000" id="inspect_final_price" name="final_price"
                               value="{{ old('final_price', $tradeIn->final_price ?? $tradeIn->estimated_price) }}"
                               class="adm-input font-bold text-[#008040]">
                    </div>

                    <div class="adm-field">
                        <label for="inspect_admin_notes" class="adm-label">Catatan Temuan Teknisi (3uTools / Fisik)</label>
                        <input type="text" id="inspect_admin_notes" name="admin_notes"
                               value="{{ old('admin_notes', $tradeIn->admin_notes) }}"
                               placeholder="Contoh: Skor 3uTools 98% Hijau, TrueTone &amp; FaceID Normal"
                               class="adm-input">
                    </div>
                </div>

                <div class="pt-3 border-t border-[#E5E5EA] flex justify-end">
                    <button type="submit" class="glass-btn glass-btn-primary glass-btn-md text-xs font-bold">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Hasil Inspeksi Teknisi</span>
                    </button>
                </div>
            </form>
        </x-admin.card>

    </div>

    <!-- =================================================================
         RIGHT COLUMN (5 COLS): Final Price Adjustment & Approve / Reject Panel
         ================================================================= -->
    <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-6">

        <x-admin.card title="Penawaran Final &amp; Persetujuan" icon="check-circle"
            description="Keputusan penetapan harga deal dan persetujuan transfer dana / penerbitan kupon.">

            <!-- Price Summary Box -->
            <div class="grid grid-cols-2 gap-3 text-xs mb-4">
                <div class="p-3.5 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA]">
                    <span class="text-[10px] text-[#6E6E73] block uppercase font-semibold">Estimasi Awal</span>
                    <span class="text-base font-bold text-[#1D1D1F] mt-1 block">
                        Rp {{ number_format($tradeIn->estimated_price, 0, ',', '.') }}
                    </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <span class="text-[10px] text-[#008040] font-bold block uppercase">Penawaran Final</span>
                    <span class="text-base font-extrabold text-[#008040] mt-1 block">
                        Rp {{ number_format($tradeIn->effective_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Decision Form (Approve / Reject / Paid Out) -->
            <form action="{{ route('admin.trade-ins.decision', $tradeIn->trade_in_code) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="adm-field">
                    <label for="decision_final_price" class="adm-label">
                        Penyesuaian Harga Deal Final (Rp) <span style="color:#D70015">*</span>
                    </label>
                    <input type="number" step="1000" id="decision_final_price" name="final_price"
                           value="{{ old('final_price', $tradeIn->final_price ?? $tradeIn->estimated_price) }}" required
                           class="adm-input text-base font-extrabold text-[#1D1D1F]">
                    <p class="adm-help">
                        Harga ini yang akan dicairkan ke rekening customer atau menjadi nilai kupon potongan belanja.
                    </p>
                </div>

                <div class="adm-field">
                    <label for="decision_notes" class="adm-label">Catatan Keputusan / Alasan Penyesuaian / Ref Transfer</label>
                    <textarea id="decision_notes" name="admin_notes" rows="3"
                              placeholder="Tuliskan rincian kesepakatan harga dengan customer, alasan penolakan, atau nomor referensi transfer..."
                              class="adm-textarea">{{ old('admin_notes', $tradeIn->admin_notes) }}</textarea>
                </div>

                <!-- Action Buttons: Approve & Reject -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="submit" name="decision" value="approved"
                            class="glass-btn glass-btn-primary glass-btn-md font-bold flex items-center justify-center gap-1.5"
                            style="background:#008040;border-color:#008040">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        <span>Approve (Setujui)</span>
                    </button>

                    <button type="submit" name="decision" value="rejected"
                            class="glass-btn glass-btn-danger glass-btn-md font-bold flex items-center justify-center gap-1.5">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        <span>Reject (Tolak)</span>
                    </button>
                </div>

                <!-- Mark as Paid Out / Voucher Issued Button -->
                @if(in_array($tradeIn->status, ['approved', 'inspected']))
                    <div class="pt-3 border-t border-[#E5E5EA]">
                        <button type="submit" name="decision" value="paid_out"
                                class="glass-btn glass-btn-secondary glass-btn-md w-full font-bold flex items-center justify-center gap-2"
                                style="color:#0066CC;border-color:#0066CC">
                            <i data-lucide="wallet" class="w-4 h-4"></i>
                            <span>
                                {{ $tradeIn->isVoucherPayout() ? 'Tandai Kupon Potongan Telah Aktif (Paid Out)' : 'Tandai Dana Sudah Ditransfer ke Rekening (Paid Out)' }}
                            </span>
                        </button>
                    </div>
                @endif
            </form>
        </x-admin.card>

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

    btnApply?.addEventListener('click', function () {
        computeTechRecommendation();
        inpFinalInspect.value = currentRecommendation;
        inpFinalDecision.value = currentRecommendation;
    });
});
</script>
@endpush
