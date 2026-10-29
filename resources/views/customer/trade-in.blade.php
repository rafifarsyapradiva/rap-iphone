@extends('layouts.customer')

@section('title', 'Program Jual & Tukar Tambah iPhone (Trade-In 15 Menit) — Rap-iPhone')
@section('meta_description', 'Taksir nilai jual atau tukar tambah iPhone lama Anda secara instan dalam 4 langkah. Cair tunai ke rekening bank atau jadikan voucher potongan pembelian iPhone baru.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10">

    <!-- =========================================================================
         1. EDITORIAL HEADER & PROGRESS INDICATOR
         ========================================================================= -->
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-5 border-b border-[var(--cust-border)] pb-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] text-xs font-semibold text-[var(--cust-ink)]">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-[var(--cust-accent)]"></i>
                    <span>Apple Style Trade-In & Buyback</span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl text-[var(--cust-ink)] tracking-tight leading-tight">
                    Tukar Tambah <span class="italic font-normal text-[var(--cust-muted)]">iPhone Lama Anda.</span>
                </h1>
                <p class="text-sm text-[var(--cust-muted)] max-w-2xl leading-relaxed">
                    Ketahui estimasi nilai unit Anda secara transparan dalam 4 tahap. Pilih pencairan dana langsung ke rekening bank atau gunakan voucher potongan belanja untuk iPhone baru.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="bg-[var(--cust-surface)] px-4 py-3 rounded-2xl border border-[var(--cust-border)] flex items-center gap-3 text-xs shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200 shrink-0">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="text-[10px] text-[var(--cust-muted)] uppercase tracking-wider block font-semibold">Inspeksi di Store</span>
                        <span class="text-[var(--cust-ink)] font-bold">15 Menit Langsung Cair</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive 4-Step Wizard Bar -->
        <div class="bg-[var(--cust-surface-muted)] p-2 sm:p-2.5 rounded-3xl border border-[var(--cust-border)]">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <button type="button" data-step-nav="1" class="wizard-step-tab flex items-center gap-3 p-3.5 rounded-2xl border-2 border-[var(--cust-ink)] bg-[var(--cust-surface)] text-left transition-[border-color,background-color,box-shadow] shadow-sm">
                    <div class="step-badge w-8 h-8 rounded-xl bg-[var(--cust-ink)] text-[var(--cust-canvas)] font-bold text-xs flex items-center justify-center shrink-0">
                        1
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-[var(--cust-muted)] block">Tahap 1</span>
                        <span class="text-xs font-bold text-[var(--cust-ink)] truncate block">Pilih Model</span>
                    </div>
                </button>

                <button type="button" data-step-nav="2" class="wizard-step-tab flex items-center gap-3 p-3.5 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] text-left transition-[border-color,background-color,box-shadow]">
                    <div class="step-badge w-8 h-8 rounded-xl bg-[var(--cust-surface-muted)] text-[var(--cust-muted)] font-bold text-xs flex items-center justify-center shrink-0 border border-[var(--cust-border)]">
                        2
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-[var(--cust-muted)] block">Tahap 2</span>
                        <span class="text-xs font-bold text-[var(--cust-ink)] truncate block">Kapasitas Storage</span>
                    </div>
                </button>

                <button type="button" data-step-nav="3" class="wizard-step-tab flex items-center gap-3 p-3.5 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] text-left transition-[border-color,background-color,box-shadow]">
                    <div class="step-badge w-8 h-8 rounded-xl bg-[var(--cust-surface-muted)] text-[var(--cust-muted)] font-bold text-xs flex items-center justify-center shrink-0 border border-[var(--cust-border)]">
                        3
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-[var(--cust-muted)] block">Tahap 3</span>
                        <span class="text-xs font-bold text-[var(--cust-ink)] truncate block">Kondisi Fisik & BH</span>
                    </div>
                </button>

                <button type="button" data-step-nav="4" class="wizard-step-tab flex items-center gap-3 p-3.5 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] text-left transition-[border-color,background-color,box-shadow]">
                    <div class="step-badge w-8 h-8 rounded-xl bg-[var(--cust-surface-muted)] text-[var(--cust-muted)] font-bold text-xs flex items-center justify-center shrink-0 border border-[var(--cust-border)]">
                        4
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-[var(--cust-muted)] block">Tahap 4</span>
                        <span class="text-xs font-bold text-[var(--cust-ink)] truncate block">Estimasi & Pencairan</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Validation Errors Banner -->
    @if($errors->any())
        <div class="bg-rose-50 p-4 rounded-2xl border border-rose-200 text-xs text-rose-800 space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                <span>Mohon periksa kembali isian formulir pengajuan Anda:</span>
            </div>
            <ul class="list-disc list-inside pl-5 text-[var(--cust-ink)]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- =========================================================================
         2. MAIN WIZARD FORM & LIVE VALUATION SIDEBAR
         ========================================================================= -->
    <form id="trade-in-wizard-form" action="{{ route('trade-in.store') }}" method="POST">
        @csrf

        <!-- Hidden Synchronized Inputs -->
        <input type="hidden" name="phone_model" id="input-phone-model" value="{{ old('phone_model', 'iPhone 15 Pro Max') }}">
        <input type="hidden" name="storage_capacity" id="input-storage-capacity" value="{{ old('storage_capacity', '256GB') }}">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- =================================================================
                 LEFT COLUMN: 4-Step Interactive Wizard Panels
                 ================================================================= -->
            <div class="lg:col-span-8 space-y-6">

                <!-- -------------------------------------------------------------
                     STEP 1: PILIH MODEL IPHONE LAMA
                     ------------------------------------------------------------- -->
                <div id="wizard-panel-1" class="wizard-panel cust-card p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[var(--cust-border)]">
                        <div>
                            <span class="text-[11px] font-bold text-[var(--cust-accent)] uppercase tracking-wider">Tahap 1 dari 4</span>
                            <h2 class="font-serif text-xl sm:text-2xl text-[var(--cust-ink)] mt-0.5">Pilih Model iPhone Anda</h2>
                            <p class="text-xs text-[var(--cust-muted)]">Pilih seri dan model iPhone yang ingin Anda jual atau tukar tambah</p>
                        </div>

                        <!-- Series Quick Filter -->
                        <div class="flex flex-wrap gap-1.5" id="series-filter-bar">
                            <button type="button" data-series-filter="all" class="series-filter-btn px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[var(--cust-ink)] text-[var(--cust-canvas)] transition-[background-color,color]">
                                Semua
                            </button>
                            @foreach($seriesGroups as $seriesName)
                                <button type="button" data-series-filter="{{ $seriesName }}" class="series-filter-btn px-3.5 py-1.5 rounded-full text-xs font-medium bg-[var(--cust-surface-muted)] text-[var(--cust-muted)] hover:text-[var(--cust-ink)] border border-[var(--cust-border)] transition-[background-color,color,border-color]">
                                    {{ str_replace(' Series', '', $seriesName) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Model Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5" id="model-selection-grid">
                        @foreach($modelCatalog as $modelName => $modelInfo)
                            <button
                                type="button"
                                data-model-name="{{ $modelName }}"
                                data-model-series="{{ $modelInfo['series'] }}"
                                class="model-option-card group relative flex flex-col justify-between p-4 rounded-2xl border text-left transition-[border-color,background-color,box-shadow] cursor-pointer {{ old('phone_model', 'iPhone 15 Pro Max') === $modelName ? 'border-2 border-[var(--cust-ink)] bg-[var(--cust-surface)] ring-2 ring-[var(--cust-ink)]/10 shadow-sm' : 'border-[var(--cust-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-border-hover)]' }}"
                            >
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="w-9 h-9 rounded-xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] flex items-center justify-center shrink-0">
                                        <i data-lucide="smartphone" class="w-4 h-4 text-[var(--cust-ink)]"></i>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] text-[var(--cust-muted)]">
                                        {{ implode(' / ', array_keys($modelInfo['capacities'])) }}
                                    </span>
                                </div>

                                <div>
                                    <h3 class="text-sm font-bold text-[var(--cust-ink)] group-hover:text-[var(--cust-accent)] transition-colors">{{ $modelName }}</h3>
                                    <p class="text-[11px] text-[var(--cust-muted)] mt-1">
                                        Taksiran s/d <span class="text-emerald-700 font-bold">Rp {{ number_format($modelInfo['max_price'], 0, ',', '.') }}</span>
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <!-- Step 1 Footer Navigation -->
                    <div class="pt-4 border-t border-[var(--cust-border)] flex items-center justify-between">
                        <div class="text-xs text-[var(--cust-muted)]">
                            Model terpilih: <span id="step1-selected-label" class="text-[var(--cust-ink)] font-bold">iPhone 15 Pro Max</span>
                        </div>
                        <button type="button" data-next-step="2" class="cust-btn cust-btn-primary cust-btn-md">
                            <span>Lanjut Pilih Kapasitas</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- -------------------------------------------------------------
                     STEP 2: PILIH KAPASITAS PENYIMPANAN
                     ------------------------------------------------------------- -->
                <div id="wizard-panel-2" class="wizard-panel hidden cust-card p-6 sm:p-8 space-y-6">
                    <div class="pb-4 border-b border-[var(--cust-border)]">
                        <span class="text-[11px] font-bold text-[var(--cust-accent)] uppercase tracking-wider">Tahap 2 dari 4</span>
                        <h2 class="font-serif text-xl sm:text-2xl text-[var(--cust-ink)] mt-0.5">Pilih Kapasitas Memori (Storage)</h2>
                        <p class="text-xs text-[var(--cust-muted)]">Periksa kapasitas memori internal unit pada menu <span class="text-[var(--cust-ink)] font-mono font-semibold">Pengaturan > Umum > Mengenai</span></p>
                    </div>

                    <!-- Dynamic Storage Capacities Grid -->
                    <div id="capacity-selection-grid" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <!-- Populated dynamically by JS based on selected model -->
                    </div>

                    <div class="p-4 rounded-2xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] flex items-center gap-3 text-xs text-[var(--cust-muted)]">
                        <i data-lucide="info" class="w-4 h-4 text-[var(--cust-accent)] shrink-0"></i>
                        <span>Harga basis di atas adalah estimasi maksimal untuk unit kondisi Grade A+ Fullset Original sebelum penyesuaian fisik & baterai.</span>
                    </div>

                    <!-- Step 2 Footer Navigation -->
                    <div class="pt-4 border-t border-[var(--cust-border)] flex items-center justify-between">
                        <button type="button" data-prev-step="1" class="cust-btn cust-btn-secondary cust-btn-md">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span>Kembali</span>
                        </button>
                        <button type="button" data-next-step="3" class="cust-btn cust-btn-primary cust-btn-md">
                            <span>Lanjut Kondisi Fisik & Fungsi</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- -------------------------------------------------------------
                     STEP 3: KONDISI FISIK, LAYAR, BATERAI & KELENGKAPAN
                     ------------------------------------------------------------- -->
                <div id="wizard-panel-3" class="wizard-panel hidden cust-card p-6 sm:p-8 space-y-7">
                    <div class="pb-4 border-b border-[var(--cust-border)]">
                        <span class="text-[11px] font-bold text-[var(--cust-accent)] uppercase tracking-wider">Tahap 3 dari 4</span>
                        <h2 class="font-serif text-xl sm:text-2xl text-[var(--cust-ink)] mt-0.5">Kondisi Fisik & Fungsi Unit</h2>
                        <p class="text-xs text-[var(--cust-muted)]">Jawab secara akurat agar estimasi nilai sistem mendekati hasil inspeksi 15 menit teknisi di store</p>
                    </div>

                    <!-- 3A. Kondisi Fisik Bodi -->
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="shield" class="w-4 h-4 text-[var(--cust-accent)]"></i>
                            <span>1. Kondisi Fisik Bodi & Bezel</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($physicalConditions as $key => $item)
                                <label class="relative flex flex-col justify-between p-4 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-border-hover)] cursor-pointer transition-[border-color,background-color,box-shadow] has-[:checked]:border-2 has-[:checked]:border-[var(--cust-ink)] has-[:checked]:ring-2 has-[:checked]:ring-[var(--cust-ink)]/10">
                                    <input
                                        type="radio"
                                        name="physical_condition"
                                        value="{{ $key }}"
                                        class="sr-only calc-trigger"
                                        {{ old('physical_condition', 'Mulus (Grade A+)') === $key ? 'checked' : '' }}
                                    >
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <span class="text-xs font-bold text-[var(--cust-ink)]">{{ $item['label'] }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item['deduction_rate'] == 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                            {{ $item['badge'] }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-[var(--cust-muted)] leading-relaxed">{{ $item['description'] }}</p>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3B. Kondisi Layar & Fungsi Utama -->
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="monitor-smartphone" class="w-4 h-4 text-emerald-700"></i>
                            <span>2. Kondisi Layar LCD, TrueTone & Face ID</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach($screenConditions as $key => $item)
                                <label class="relative flex flex-col justify-between p-4 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-border-hover)] cursor-pointer transition-[border-color,background-color,box-shadow] has-[:checked]:border-2 has-[:checked]:border-[var(--cust-ink)] has-[:checked]:ring-2 has-[:checked]:ring-[var(--cust-ink)]/10">
                                    <input
                                        type="radio"
                                        name="screen_condition"
                                        value="{{ $key }}"
                                        class="sr-only calc-trigger"
                                        {{ old('screen_condition', 'Normal') === $key ? 'checked' : '' }}
                                    >
                                    <div>
                                        <div class="flex items-center justify-between gap-1 mb-1.5">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item['deduction_rate'] == 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                                {{ $item['badge'] }}
                                            </span>
                                        </div>
                                        <span class="text-xs font-bold text-[var(--cust-ink)] block mb-1">{{ $item['label'] }}</span>
                                        <p class="text-[11px] text-[var(--cust-muted)] leading-relaxed">{{ $item['description'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3C. Persentase Kesehatan Baterai (Battery Health) -->
                    <div class="space-y-3 p-5 rounded-2xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)]">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label for="battery_health_range" class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-2">
                                <i data-lucide="battery-charging" class="w-4 h-4 text-emerald-700"></i>
                                <span>3. Persentase Kesehatan Baterai (Battery Health)</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <span id="bh-live-badge" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ old('battery_health', 92) }}% — Optimal
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <span class="text-xs font-mono text-[var(--cust-muted)]">50%</span>
                            <input
                                type="range"
                                id="battery_health_range"
                                name="battery_health"
                                min="50"
                                max="100"
                                value="{{ old('battery_health', 92) }}"
                                class="w-full accent-[var(--cust-ink)] cursor-pointer calc-trigger"
                            >
                            <span class="text-xs font-mono text-emerald-700 font-bold">100%</span>
                        </div>
                        <p class="text-[11px] text-[var(--cust-muted)]">
                            Battery Health <strong class="text-[var(--cust-ink)]">90% - 100%</strong> mendapatkan harga penuh tanpa potongan nilai baterai.
                        </p>
                    </div>

                    <!-- 3D. Kelengkapan Aksesoris & Nomor IMEI -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                        <div class="md:col-span-8 space-y-3">
                            <label class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-2">
                                <i data-lucide="package" class="w-4 h-4 text-amber-700"></i>
                                <span>4. Kelengkapan Kotak & Aksesoris</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach($accessoriesOptions as $key => $item)
                                    <label class="relative flex flex-col justify-between p-3.5 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-border-hover)] cursor-pointer transition-[border-color,background-color,box-shadow] has-[:checked]:border-2 has-[:checked]:border-[var(--cust-ink)] has-[:checked]:ring-2 has-[:checked]:ring-[var(--cust-ink)]/10">
                                        <input
                                            type="radio"
                                            name="accessories_status"
                                            value="{{ $key }}"
                                            class="sr-only calc-trigger"
                                            {{ old('accessories_status', 'Fullset Box Original') === $key ? 'checked' : '' }}
                                        >
                                        <div>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item['deduction_amount'] == 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] text-[var(--cust-muted)]' }} inline-block mb-1.5">
                                                {{ $item['badge'] }}
                                            </span>
                                            <span class="text-xs font-bold text-[var(--cust-ink)] block">{{ $item['label'] }}</span>
                                            <p class="text-[10px] text-[var(--cust-muted)] mt-1 leading-snug">{{ $item['description'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="md:col-span-4 space-y-2">
                            <label for="imei" class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider flex items-center gap-2">
                                <i data-lucide="hash" class="w-4 h-4 text-[var(--cust-accent)]"></i>
                                <span>5. Nomor IMEI (Opsional)</span>
                            </label>
                            <input
                                type="text"
                                id="imei"
                                name="imei"
                                maxlength="20"
                                value="{{ old('imei') }}"
                                placeholder="Contoh: 356789012345678"
                                class="cust-input text-xs font-mono w-full"
                            >
                            <p class="text-[11px] text-[var(--cust-muted)] leading-relaxed">
                                Tekan <code class="text-[var(--cust-ink)] font-semibold bg-[var(--cust-surface-muted)] px-1 rounded">*#06#</code> di keypad panggilan untuk melihat nomor seri IMEI.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 Footer Navigation -->
                    <div class="pt-4 border-t border-[var(--cust-border)] flex items-center justify-between">
                        <button type="button" data-prev-step="2" class="cust-btn cust-btn-secondary cust-btn-md">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span>Kembali</span>
                        </button>
                        <button type="button" data-next-step="4" class="cust-btn cust-btn-primary cust-btn-md">
                            <span>Lihat Hasil Estimasi Nilai</span>
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- -------------------------------------------------------------
                     STEP 4: ESTIMASI NILAI OTOMATIS & PILIHAN PENCAIRAN DANA
                     ------------------------------------------------------------- -->
                <div id="wizard-panel-4" class="wizard-panel hidden cust-card p-6 sm:p-8 space-y-7">
                    <div class="pb-4 border-b border-[var(--cust-border)]">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Tahap 4 dari 4 — Tahap Akhir</span>
                        <h2 class="font-serif text-xl sm:text-2xl text-[var(--cust-ink)] mt-0.5">Estimasi Taksiran & Metode Pencairan</h2>
                        <p class="text-xs text-[var(--cust-muted)]">Pilih mekanisme penyaluran nilai tukar tambah yang paling menguntungkan bagi Anda</p>
                    </div>

                    <!-- Instant Valuation Hero Box -->
                    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-7 bg-[var(--cust-surface-muted)] border border-[var(--cust-border)]">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                            <div class="space-y-1.5">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 uppercase tracking-wider">
                                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                    Estimasi Penawaran Sistem
                                </span>
                                <div id="step4-final-estimate" class="font-serif text-3xl sm:text-4xl text-[var(--cust-ink)] tracking-tight">
                                    Rp 14.500.000
                                </div>
                                <p class="text-xs text-[var(--cust-muted)]">
                                    Untuk unit <span id="step4-unit-summary" class="text-[var(--cust-ink)] font-semibold">iPhone 15 Pro Max · 256GB</span>
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-[var(--cust-surface)] border border-[var(--cust-border)] text-xs space-y-1.5 min-w-[240px] shadow-sm">
                                <div class="flex justify-between text-[var(--cust-muted)]">
                                    <span>Harga Dasar Maks:</span>
                                    <span id="breakdown-base" class="text-[var(--cust-ink)] font-semibold">Rp 14.500.000</span>
                                </div>
                                <div class="flex justify-between text-[var(--cust-muted)]">
                                    <span>Penyesuaian Fisik/Layar:</span>
                                    <span id="breakdown-condition" class="text-amber-700 font-semibold">- Rp 0</span>
                                </div>
                                <div class="flex justify-between text-[var(--cust-muted)]">
                                    <span>Penyesuaian BH & Box:</span>
                                    <span id="breakdown-extras" class="text-amber-700 font-semibold">- Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Pencairan Dana: Voucher Potongan Belanja vs Transfer Bank -->
                    <div class="space-y-4">
                        <label class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider block">
                            Pilih Metode Pencairan Nilai Trade-In <span class="text-rose-600">*</span>
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Opsi A: Transfer Bank Rekening Customer -->
                            <label class="payout-method-card relative flex flex-col justify-between p-5 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-border-hover)] cursor-pointer transition-[border-color,background-color,box-shadow] has-[:checked]:border-2 has-[:checked]:border-[var(--cust-ink)] has-[:checked]:ring-2 has-[:checked]:ring-[var(--cust-ink)]/10">
                                <input
                                    type="radio"
                                    name="payout_method"
                                    value="bank_transfer"
                                    class="sr-only payout-radio"
                                    {{ old('payout_method', 'bank_transfer') === 'bank_transfer' ? 'checked' : '' }}
                                >
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-200">
                                            <i data-lucide="landmark" class="w-5 h-5"></i>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Cair Tunai Langsung
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-bold text-[var(--cust-ink)]">Transfer ke Rekening Bank Pribadi</h3>
                                    <p class="text-xs text-[var(--cust-muted)] mt-1.5 leading-relaxed">
                                        Dana hasil taksiran iPhone ditransfer langsung ke rekening bank Anda setelah inspeksi 15 menit diverifikasi teknisi.
                                    </p>
                                </div>
                            </label>

                            <!-- Opsi B: Kupon Potongan Belanja iPhone Baru -->
                            <label class="payout-method-card relative flex flex-col justify-between p-5 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-border-hover)] cursor-pointer transition-[border-color,background-color,box-shadow] has-[:checked]:border-2 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40 has-[:checked]:ring-2 has-[:checked]:ring-emerald-600/10">
                                <input
                                    type="radio"
                                    name="payout_method"
                                    value="voucher"
                                    class="sr-only payout-radio"
                                    {{ old('payout_method') === 'voucher' ? 'checked' : '' }}
                                >
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200">
                                            <i data-lucide="ticket" class="w-5 h-5"></i>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Prioritas Upgrade iPhone
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-bold text-[var(--cust-ink)]">Kupon Potongan Belanja iPhone Baru</h3>
                                    <p class="text-xs text-[var(--cust-muted)] mt-1.5 leading-relaxed">
                                        Jadikan nilai unit lama Anda sebagai kupon potongan instan untuk pembelian unit iPhone Baru atau Second Resmi di Rap-iPhone.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Conditional Panel A: Bank Account Details -->
                    <div id="bank-transfer-fields" class="p-5 rounded-2xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] space-y-4">
                        <div class="flex items-center gap-2 text-xs font-bold text-[var(--cust-ink)]">
                            <i data-lucide="credit-card" class="w-4 h-4 text-[var(--cust-accent)]"></i>
                            <span>Rincian Rekening Pencairan Dana</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <label for="customer_bank_name" class="text-xs font-semibold text-[var(--cust-ink)] block">
                                    Nama Bank Tujuan <span class="text-rose-600">*</span>
                                </label>
                                <select id="customer_bank_name" name="customer_bank_name" class="cust-input text-xs w-full">
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($supportedBanks as $bank)
                                        <option value="{{ $bank }}" {{ old('customer_bank_name', 'BCA (Bank Central Asia)') === $bank ? 'selected' : '' }}>
                                            {{ $bank }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label for="customer_account_number" class="text-xs font-semibold text-[var(--cust-ink)] block">
                                    Nomor Rekening <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="customer_account_number"
                                    name="customer_account_number"
                                    value="{{ old('customer_account_number') }}"
                                    placeholder="Contoh: 8720192831"
                                    class="cust-input text-xs font-mono w-full"
                                >
                            </div>

                            <div class="space-y-1.5">
                                <label for="customer_account_holder" class="text-xs font-semibold text-[var(--cust-ink)] block">
                                    Nama Pemilik Rekening <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="customer_account_holder"
                                    name="customer_account_holder"
                                    value="{{ old('customer_account_holder', auth()->user()->name ?? '') }}"
                                    placeholder="Nama sesuai buku rekening"
                                    class="cust-input text-xs w-full"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Conditional Panel B: Store Discount Voucher Info -->
                    <div id="voucher-payout-fields" class="hidden p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-emerald-800">
                            <i data-lucide="sparkles" class="w-4 h-4 text-emerald-600"></i>
                            <span>Keuntungan Kupon Potongan Belanja Rap-iPhone</span>
                        </div>
                        <p class="text-xs text-[var(--cust-ink)] leading-relaxed">
                            Kode Voucher Tukar Tambah (<span class="font-mono font-bold text-emerald-800">VCH-XXXXXX</span>) akan diterbitkan otomatis pada akun Anda. Begitu unit diverifikasi di toko, kode dapat langsung diaplikasikan pada transaksi checkout unit iPhone impian.
                        </p>
                    </div>

                    <!-- Step 4 Footer & Submit Button -->
                    <div class="pt-4 border-t border-[var(--cust-border)] flex flex-col sm:flex-row items-center justify-between gap-4">
                        <button type="button" data-prev-step="3" class="cust-btn cust-btn-secondary cust-btn-md w-full sm:w-auto">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span>Ubah Kondisi Unit</span>
                        </button>

                        @auth
                            <button type="submit" class="cust-btn cust-btn-primary cust-btn-lg w-full sm:w-auto font-bold">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                <span>Ajukan Tukar Tambah Sekarang</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="cust-btn cust-btn-primary cust-btn-lg w-full sm:w-auto font-bold">
                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                <span>Masuk Akun untuk Mengajukan</span>
                            </a>
                        @endauth
                    </div>
                </div>

            </div>

            <!-- =================================================================
                 RIGHT COLUMN: Sticky Live Summary & Valuation Calculator Card
                 ================================================================= -->
            <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
                <div class="cust-card p-6 space-y-5 bg-[var(--cust-surface)] shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-border)]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="calculator" class="w-4 h-4 text-[var(--cust-accent)]"></i>
                            <h3 class="text-xs font-bold text-[var(--cust-ink)] uppercase tracking-wider">Kalkulator Taksiran</h3>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Real-Time
                        </span>
                    </div>

                    <!-- Selected Device Preview -->
                    <div class="p-4 rounded-2xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] space-y-2.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Model Unit:</span>
                            <span id="summary-model" class="font-bold text-[var(--cust-ink)]">iPhone 15 Pro Max</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Kapasitas:</span>
                            <span id="summary-capacity" class="font-bold text-[var(--cust-accent)]">256GB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Kondisi Fisik:</span>
                            <span id="summary-physical" class="font-medium text-[var(--cust-ink)]">Mulus (Grade A+)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Kondisi Layar:</span>
                            <span id="summary-screen" class="font-medium text-[var(--cust-ink)]">Normal</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Battery Health:</span>
                            <span id="summary-bh" class="font-bold text-emerald-700">92%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--cust-muted)]">Kelengkapan:</span>
                            <span id="summary-accessories" class="font-medium text-[var(--cust-ink)]">Fullset Box Original</span>
                        </div>
                    </div>

                    <!-- Estimated Price Display -->
                    <div class="p-5 rounded-2xl bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] space-y-1 text-center">
                        <span class="text-[10px] uppercase tracking-widest text-[var(--cust-muted)] font-bold block">Estimasi Nilai Unit</span>
                        <div id="summary-estimated-price" class="font-serif text-2xl sm:text-3xl text-[var(--cust-ink)] tracking-tight">
                            Rp 14.500.000
                        </div>
                        <span class="text-[10px] text-emerald-700 font-semibold block">Transparansi Standar Apple Retail</span>
                    </div>

                    <!-- 3 Step Store Process Info -->
                    <div class="space-y-2.5 pt-2 border-t border-[var(--cust-border)] text-[11px] text-[var(--cust-muted)]">
                        <p class="font-bold text-[var(--cust-ink)] text-xs">Alur Penyerahan di Store:</p>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] text-[var(--cust-ink)] text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <span>Dapatkan kode booking <strong class="text-[var(--cust-ink)]">TI-YYYYMMDD-XXX</strong> saat pengajuan submit.</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] text-[var(--cust-ink)] text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <span>Bawa unit ke Store Rap-iPhone untuk verifikasi fisik 32 titik selama 15 menit.</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[var(--cust-surface-muted)] border border-[var(--cust-border)] text-[var(--cust-ink)] text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <span>Setujui nilai penawaran final dan dana tunai / voucher dicairkan seketika.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <!-- =========================================================================
         3. RIWAYAT PENGAJUAN TRADE-IN SAYA (Jika Customer Login)
         ========================================================================= -->
    @auth
        <div class="space-y-4 pt-6 border-t border-[var(--cust-border)]">
            <div class="flex items-center justify-between">
                <h2 class="font-serif text-xl text-[var(--cust-ink)] flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-[var(--cust-accent)]"></i>
                    <span>Riwayat Pengajuan Trade-In Anda</span>
                </h2>
                <span class="text-xs text-[var(--cust-muted)] font-mono">{{ count($myTradeIns) }} Pengajuan</span>
            </div>

            @if(count($myTradeIns) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($myTradeIns as $item)
                        <div class="cust-card p-5 hover:border-[var(--cust-border-hover)] transition-[border-color,box-shadow] flex flex-col justify-between gap-4">
                            <div class="flex items-center justify-between border-b border-[var(--cust-border)] pb-3">
                                <div>
                                    <span class="font-mono text-xs font-bold text-[var(--cust-accent)]">{{ $item->trade_in_code }}</span>
                                    <span class="text-[11px] text-[var(--cust-muted)] ml-2">{{ $item->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->status_badge_class }}">
                                    {{ $item->status_label }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <div class="space-y-1 text-xs">
                                    <h3 class="text-sm font-bold text-[var(--cust-ink)]">{{ $item->phone_model }} ({{ $item->storage_capacity }})</h3>
                                    <p class="text-[var(--cust-muted)]">{{ $item->physical_condition }} &bull; Layar {{ $item->screen_condition }} &bull; BH {{ $item->battery_health }}%</p>
                                    <p class="text-[11px] text-[var(--cust-muted)]">
                                        Pencairan:
                                        <span class="text-[var(--cust-ink)] font-medium">
                                            {{ $item->isVoucherPayout() ? 'Kupon Potongan Belanja (' . $item->customer_account_number . ')' : $item->customer_bank_name . ' - ' . $item->customer_account_number }}
                                        </span>
                                    </p>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <div>
                                        <span class="text-[10px] text-[var(--cust-muted)] block">
                                            {{ $item->final_price ? 'Nilai Disetujui' : 'Estimasi Sistem' }}
                                        </span>
                                        <span class="text-base font-bold {{ $item->final_price ? 'text-emerald-700 font-serif' : 'text-[var(--cust-ink)] font-serif' }}">
                                            Rp {{ number_format($item->effective_price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('trade-in.show', $item->trade_in_code) }}" class="cust-btn cust-btn-secondary cust-btn-sm text-xs">
                                        <span>Rincian</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cust-card p-8 text-center text-xs text-[var(--cust-muted)] bg-[var(--cust-surface-muted)]">
                    Belum ada riwayat pengajuan tukar tambah pada akun Anda. Mulai dengan memilih model iPhone di formulir 4 langkah di atas.
                </div>
            @endif
        </div>
    @endauth

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modelCatalog = @json($modelCatalog);
    const physicalConditions = @json($physicalConditions);
    const screenConditions = @json($screenConditions);
    const accessoriesOptions = @json($accessoriesOptions);

    const inputModel = document.getElementById('input-phone-model');
    const inputCapacity = document.getElementById('input-storage-capacity');
    const capacityGrid = document.getElementById('capacity-selection-grid');
    const bhRange = document.getElementById('battery_health_range');
    const bhBadge = document.getElementById('bh-live-badge');

    let currentStep = 1;

    function formatIDR(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function showStep(step) {
        currentStep = step;
        document.querySelectorAll('.wizard-panel').forEach((panel, idx) => {
            panel.classList.toggle('hidden', (idx + 1) !== step);
        });

        document.querySelectorAll('.wizard-step-tab').forEach((tab) => {
            const tabStep = parseInt(tab.getAttribute('data-step-nav'), 10);
            const badge = tab.querySelector('.step-badge');
            if (tabStep === step) {
                tab.className = 'wizard-step-tab flex items-center gap-3 p-3.5 rounded-2xl border-2 border-[var(--cust-ink)] bg-[var(--cust-surface)] text-left transition-[border-color,background-color,box-shadow] shadow-sm';
                if (badge) badge.className = 'step-badge w-8 h-8 rounded-xl bg-[var(--cust-ink)] text-[var(--cust-canvas)] font-bold text-xs flex items-center justify-center shrink-0';
            } else if (tabStep < step) {
                tab.className = 'wizard-step-tab flex items-center gap-3 p-3.5 rounded-2xl border border-emerald-200 bg-emerald-50/70 text-left transition-[border-color,background-color,box-shadow]';
                if (badge) badge.className = 'step-badge w-8 h-8 rounded-xl bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shrink-0';
            } else {
                tab.className = 'wizard-step-tab flex items-center gap-3 p-3.5 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] text-left transition-[border-color,background-color,box-shadow]';
                if (badge) badge.className = 'step-badge w-8 h-8 rounded-xl bg-[var(--cust-surface-muted)] text-[var(--cust-muted)] font-bold text-xs flex items-center justify-center shrink-0 border border-[var(--cust-border)]';
            }
        });
    }

    function renderCapacitiesForModel(modelName) {
        const modelData = modelCatalog[modelName];
        if (!modelData) return;

        const capacities = modelData.capacities;
        const availableKeys = Object.keys(capacities);
        if (!availableKeys.includes(inputCapacity.value)) {
            inputCapacity.value = availableKeys[0];
        }

        capacityGrid.innerHTML = '';
        availableKeys.forEach((cap) => {
            const baseVal = capacities[cap];
            const isSelected = inputCapacity.value === cap;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = isSelected
                ? 'p-4 rounded-2xl border-2 border-[var(--cust-ink)] bg-[var(--cust-surface)] ring-2 ring-[var(--cust-ink)]/10 text-center transition-[border-color,background-color,box-shadow] cursor-pointer shadow-sm'
                : 'p-4 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-border-hover)] text-center transition-[border-color,background-color,box-shadow] cursor-pointer';
            btn.innerHTML = `
                <span class="text-xl font-bold text-[var(--cust-ink)] block">${cap}</span>
                <span class="text-[11px] text-[var(--cust-muted)] block mt-1">Estimasi Basis</span>
                <span class="text-xs font-bold text-emerald-700 block mt-0.5">${formatIDR(baseVal)}</span>
            `;
            btn.addEventListener('click', () => {
                inputCapacity.value = cap;
                renderCapacitiesForModel(modelName);
                recalculateEstimate();
            });
            capacityGrid.appendChild(btn);
        });
    }

    function recalculateEstimate() {
        const modelName = inputModel.value;
        const capacity = inputCapacity.value;
        const bh = parseInt(bhRange.value || '92', 10);
        const physical = document.querySelector('input[name="physical_condition"]:checked')?.value || 'Mulus (Grade A+)';
        const screen = document.querySelector('input[name="screen_condition"]:checked')?.value || 'Normal';
        const accessories = document.querySelector('input[name="accessories_status"]:checked')?.value || 'Fullset Box Original';

        const modelData = modelCatalog[modelName];
        let basePrice = 5000000;
        if (modelData && modelData.capacities) {
            basePrice = modelData.capacities[capacity] || Object.values(modelData.capacities)[0];
        }

        const physicalRate = physicalConditions[physical]?.deduction_rate ?? 0;
        const screenRate = screenConditions[screen]?.deduction_rate ?? 0;

        let bhRate = 0;
        if (bh >= 90) bhRate = 0;
        else if (bh >= 85) bhRate = 0.04;
        else if (bh >= 80) bhRate = 0.08;
        else bhRate = 0.15;

        const physicalDeduction = Math.round((basePrice * physicalRate) / 1000) * 1000;
        const screenDeduction = Math.round((basePrice * screenRate) / 1000) * 1000;
        const bhDeduction = Math.round((basePrice * bhRate) / 1000) * 1000;
        const accessoriesDeduction = accessoriesOptions[accessories]?.deduction_amount ?? 0;

        const totalDeduction = physicalDeduction + screenDeduction + bhDeduction + accessoriesDeduction;
        const minFloor = Math.max(1000000, Math.round((basePrice * 0.25) / 1000) * 1000);
        const estimatedPrice = Math.max(minFloor, basePrice - totalDeduction);

        // Update BH badge
        let bhStatus = 'Optimal (0%)';
        if (bh < 80) bhStatus = 'Service (-15%)';
        else if (bh < 85) bhStatus = 'Standar (-8%)';
        else if (bh < 90) bhStatus = 'Normal (-4%)';
        bhBadge.textContent = `${bh}% — ${bhStatus}`;

        // Update Sidebar & Step 4 Summaries
        document.getElementById('step1-selected-label').textContent = modelName;
        document.getElementById('summary-model').textContent = modelName;
        document.getElementById('summary-capacity').textContent = capacity;
        document.getElementById('summary-physical').textContent = physical;
        document.getElementById('summary-screen').textContent = screen;
        document.getElementById('summary-bh').textContent = `${bh}%`;
        document.getElementById('summary-accessories').textContent = accessories;
        document.getElementById('summary-estimated-price').textContent = formatIDR(estimatedPrice);

        document.getElementById('step4-final-estimate').textContent = formatIDR(estimatedPrice);
        document.getElementById('step4-unit-summary').textContent = `${modelName} · ${capacity} · ${physical}`;
        document.getElementById('breakdown-base').textContent = formatIDR(basePrice);
        document.getElementById('breakdown-condition').textContent = '- ' + formatIDR(physicalDeduction + screenDeduction);
        document.getElementById('breakdown-extras').textContent = '- ' + formatIDR(bhDeduction + accessoriesDeduction);
    }

    // Model Selection Click
    document.querySelectorAll('.model-option-card').forEach((card) => {
        card.addEventListener('click', function () {
            const selectedModel = this.getAttribute('data-model-name');
            inputModel.value = selectedModel;

            document.querySelectorAll('.model-option-card').forEach((c) => {
                c.className = 'model-option-card group relative flex flex-col justify-between p-4 rounded-2xl border border-[var(--cust-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-border-hover)] text-left transition-[border-color,background-color,box-shadow] cursor-pointer';
            });
            this.className = 'model-option-card group relative flex flex-col justify-between p-4 rounded-2xl border-2 border-[var(--cust-ink)] bg-[var(--cust-surface)] ring-2 ring-[var(--cust-ink)]/10 text-left transition-[border-color,background-color,box-shadow] cursor-pointer shadow-sm';

            renderCapacitiesForModel(selectedModel);
            recalculateEstimate();
        });
    });

    // Series Filter Click
    document.querySelectorAll('.series-filter-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            const filter = this.getAttribute('data-series-filter');
            document.querySelectorAll('.series-filter-btn').forEach((b) => {
                b.className = 'series-filter-btn px-3.5 py-1.5 rounded-full text-xs font-medium bg-[var(--cust-surface-muted)] text-[var(--cust-muted)] hover:text-[var(--cust-ink)] border border-[var(--cust-border)] transition-[background-color,color,border-color]';
            });
            this.className = 'series-filter-btn px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[var(--cust-ink)] text-[var(--cust-canvas)] transition-[background-color,color]';

            document.querySelectorAll('.model-option-card').forEach((card) => {
                const cardSeries = card.getAttribute('data-model-series');
                card.classList.toggle('hidden', filter !== 'all' && cardSeries !== filter);
            });
        });
    });

    // Step Nav Buttons
    document.querySelectorAll('[data-next-step]').forEach((btn) => {
        btn.addEventListener('click', () => showStep(parseInt(btn.getAttribute('data-next-step'), 10)));
    });
    document.querySelectorAll('[data-prev-step]').forEach((btn) => {
        btn.addEventListener('click', () => showStep(parseInt(btn.getAttribute('data-prev-step'), 10)));
    });
    document.querySelectorAll('[data-step-nav]').forEach((btn) => {
        btn.addEventListener('click', () => showStep(parseInt(btn.getAttribute('data-step-nav'), 10)));
    });

    // Condition & Battery Triggers
    document.querySelectorAll('.calc-trigger').forEach((input) => {
        input.addEventListener('input', recalculateEstimate);
        input.addEventListener('change', recalculateEstimate);
    });

    // Payout Method Toggle
    const bankFields = document.getElementById('bank-transfer-fields');
    const voucherFields = document.getElementById('voucher-payout-fields');
    function syncPayoutFields() {
        const selectedPayout = document.querySelector('input[name="payout_method"]:checked')?.value || 'bank_transfer';
        if (selectedPayout === 'voucher') {
            bankFields.classList.add('hidden');
            voucherFields.classList.remove('hidden');
        } else {
            bankFields.classList.remove('hidden');
            voucherFields.classList.add('hidden');
        }
    }
    document.querySelectorAll('.payout-radio').forEach((r) => r.addEventListener('change', syncPayoutFields));

    // Initial Render
    renderCapacitiesForModel(inputModel.value);
    recalculateEstimate();
    syncPayoutFields();
});
</script>
@endpush
