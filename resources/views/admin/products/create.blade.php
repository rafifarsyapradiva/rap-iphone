@extends('layouts.admin')

@section('title', 'Tambah Produk iPhone Baru — Rap-iPhone Admin')
@section('page_title', 'Tambah Produk iPhone Baru')

@section('content')
<x-admin.page-header
    meta="Manajemen Katalog & Inventaris"
    icon="smartphone"
    title="Tambah Produk iPhone Baru"
    subtitle="Lengkapi lembar spesifikasi produk induk, varian awal, dan foto studio. Varian dan galeri dapat dikelola lagi setelah produk dibuat.">
    <x-slot:actions>
        <a href="{{ route('admin.products.index') }}" class="adm-btn adm-btn-sm">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i><span>Kembali ke Katalog</span>
        </a>
    </x-slot:actions>
</x-admin.page-header>

@if($errors->any())
    <div class="adm-alert adm-tone-red" role="alert">
        <i data-lucide="alert-octagon"></i>
        <div>
            <p class="font-medium">Terdapat kesalahan pada formulir:</p>
            <ul class="list-disc list-inside mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="adm-page" style="max-width: 1100px; margin-inline: 0;">
    @csrf

    {{-- 1. Informasi Produk Induk --}}
    <x-admin.card title="1. Informasi Produk Induk iPhone" icon="smartphone"
        description="Tentukan seri, kategori katalog, kondisi unit (BNIB atau Second), dan jenis garansi resmi.">
        <div class="adm-grid-form cols-3">
            <div class="adm-field" style="grid-column: span 2 / span 2;">
                <label class="adm-label" for="f-name">Nama Produk iPhone <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
                <input id="f-name" type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: iPhone 16 Pro Max" class="adm-input @error('name') is-invalid @enderror">
                @error('name')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="f-category">Kategori Katalog <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
                <select id="f-category" name="category_id" required class="adm-select @error('category_id') is-invalid @enderror">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="f-series">Seri iPhone <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
                <input id="f-series" type="text" name="series" value="{{ old('series', 'iPhone 16 Series') }}" required
                       placeholder="Contoh: iPhone 16 Series" class="adm-input @error('series') is-invalid @enderror">
                @error('series')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="create-condition-select">Kondisi Unit <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
                <select name="condition" id="create-condition-select" required class="adm-select @error('condition') is-invalid @enderror">
                    <option value="new" {{ old('condition', 'new') === 'new' ? 'selected' : '' }}>Baru (BNIB Segel Resmi)</option>
                    <option value="second" {{ old('condition') === 'second' ? 'selected' : '' }}>Second Berkualitas (Pre-Owned)</option>
                </select>
                @error('condition')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field" id="create-grade-wrapper">
                <label class="adm-label" for="f-grade">Grade Fisik (Khusus Second)</label>
                <select id="f-grade" name="grade" class="adm-select @error('grade') is-invalid @enderror">
                    <option value="">-- Bukan Unit Second --</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}" {{ old('grade') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                    @endforeach
                </select>
                @error('grade')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="f-warranty">Tipe Garansi <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
                <select id="f-warranty" name="warranty_type" required class="adm-select @error('warranty_type') is-invalid @enderror">
                    @foreach($warrantyTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('warranty_type', 'resmi_ibox') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('warranty_type')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="f-base">Harga Dasar Display (Rp) <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
                <input id="f-base" type="number" name="base_price" value="{{ old('base_price', 16499000) }}" min="0" step="1000" required
                       class="adm-input adm-num @error('base_price') is-invalid @enderror" style="text-align:left !important; font-weight:600;">
                <span class="adm-help">Disinkronkan dengan harga jual varian terendah.</span>
                @error('base_price')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <label class="inline-flex items-center gap-2.5 cursor-pointer self-end pb-2.5">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded-xs accent-[var(--adm-ink)]">
                <span class="adm-label">Tampilkan aktif di etalase toko</span>
            </label>
        </div>

        <div class="adm-field" style="margin-top: 1.25rem;">
            <label class="adm-label" for="f-desc">Deskripsi Spesifikasi &amp; Ketentuan Garansi <span class="text-[var(--adm-tone-red-ink)]">*</span></label>
            <textarea id="f-desc" name="description" rows="4" required
                      placeholder="Tuliskan spesifikasi detail keunggulan chipset, layar Super Retina XDR, kamera, serta ketentuan klaim garansi resmi..."
                      class="adm-textarea @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
            @error('description')<span class="adm-error">{{ $message }}</span>@enderror
        </div>
    </x-admin.card>

    {{-- 2. Varian Awal --}}
    <x-admin.card title="2. Varian Awal (Warna, Kapasitas, HPP, Harga Jual, Stok & IMEI)" icon="layers"
        description="Tambahkan satu atau beberapa varian awal. Konfigurasi lebih lanjut dapat dikelola setelah produk tersimpan.">
        <x-slot:actions>
            <button type="button" id="btn-add-variant-row" class="adm-btn adm-btn-sm">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i><span>Tambah Baris Varian</span>
            </button>
        </x-slot:actions>

        <div id="variants-rows-container" class="space-y-4">
            <div class="variant-row rounded-xs border border-[var(--adm-rule-border)] bg-[var(--adm-paper-subtle)] p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="variant-row-title adm-label font-serif text-base">Varian #1</span>
                    <button type="button" class="btn-remove-variant adm-icon-btn is-danger" title="Hapus baris varian" aria-label="Hapus baris varian" style="display:none">
                        <i data-lucide="x"></i>
                    </button>
                </div>
                <div class="adm-grid-form cols-4">
                    <div class="adm-field">
                        <label class="adm-label">Preset Warna Apple</label>
                        <select class="adm-select preset-color-select">
                            <option value="">-- Pilih Preset / Custom --</option>
                            @foreach($appleColors as $clr)
                                <option value="{{ $clr['name'] }}" data-hex="{{ $clr['hex'] }}" {{ $loop->first ? 'selected' : '' }}>{{ $clr['name'] }} ({{ $clr['hex'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Nama Warna &amp; Hex</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="variants[0][color_name]" value="Natural Titanium" required class="adm-input input-color-name">
                            <input type="color" name="variants[0][color_hex]" value="#9A958E" class="input-color-hex" style="height:2.375rem;width:2.5rem;border-radius:2px;border:1px solid var(--adm-rule-border);background:var(--adm-surface);cursor:pointer;padding:2px">
                        </div>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Kapasitas Penyimpanan</label>
                        <select name="variants[0][storage_capacity]" class="adm-select">
                            @foreach($storageOptions as $cap)
                                <option value="{{ $cap }}" {{ $cap === '256GB' ? 'selected' : '' }}>{{ $cap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Stok Unit Fisik</label>
                        <input type="number" name="variants[0][stock]" value="10" min="0" required class="adm-input font-medium">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">HPP / Cost Price (Rp)</label>
                        <input type="number" name="variants[0][cost_price]" value="15000000" min="0" step="1000" required class="adm-input adm-mono">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Harga Jual Retail (Rp)</label>
                        <input type="number" name="variants[0][selling_price]" value="16499000" min="0" step="1000" required class="adm-input font-medium">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Battery Health % (Opsional)</label>
                        <input type="number" name="variants[0][battery_health]" min="1" max="100" placeholder="Misal: 96" class="adm-input">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Nomor IMEI / SKU (Opsional)</label>
                        <input type="text" name="variants[0][imei]" placeholder="15 digit IMEI fisik" class="adm-input adm-mono">
                    </div>
                </div>
            </div>
        </div>
    </x-admin.card>

    {{-- 3. Foto Produk --}}
    <x-admin.card title="3. Foto Produk Multi-Angle Studio (Opsional)" icon="images"
        description="Unggah foto tampak depan, belakang, samping, dan kelengkapan box (JPG/PNG/WebP maks. 4MB per file).">
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
               class="adm-input file:mr-4 file:py-1 file:px-3 file:rounded-xs file:border file:border-[var(--adm-ink)] file:text-xs file:font-medium file:bg-[var(--adm-ink)] file:text-[var(--adm-ink-reverse)] hover:file:bg-[var(--adm-accent)]"
               style="padding-top:.375rem">
    </x-admin.card>

    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.products.index') }}" class="adm-btn">Batal</a>
        <button type="submit" class="adm-btn adm-btn-primary">
            <i data-lucide="save" class="w-4 h-4"></i>
            <span>Simpan Produk &amp; Varian</span>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('variants-rows-container');
    const addBtn = document.getElementById('btn-add-variant-row');
    const conditionSelect = document.getElementById('create-condition-select');
    const gradeWrapper = document.getElementById('create-grade-wrapper');
    let rowIndex = 1;

    function bindPresetColor(row) {
        const select = row.querySelector('.preset-color-select');
        const nameInput = row.querySelector('.input-color-name');
        const hexInput = row.querySelector('.input-color-hex');

        select?.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const hex = opt?.getAttribute('data-hex');
            if (this.value && hex) {
                nameInput.value = this.value;
                hexInput.value = hex;
            }
        });
    }

    function refreshRows() {
        const rows = container.querySelectorAll('.variant-row');
        rows.forEach(function (row, i) {
            const title = row.querySelector('.variant-row-title');
            if (title) title.textContent = 'Varian #' + (i + 1);
            const rm = row.querySelector('.btn-remove-variant');
            if (rm) rm.style.display = rows.length > 1 ? '' : 'none';
        });
    }

    function syncGrade() {
        if (!conditionSelect || !gradeWrapper) return;
        gradeWrapper.style.display = conditionSelect.value === 'second' ? '' : 'none';
    }

    container.querySelectorAll('.variant-row').forEach(bindPresetColor);
    conditionSelect?.addEventListener('change', syncGrade);
    syncGrade();

    addBtn?.addEventListener('click', function () {
        const firstRow = container.querySelector('.variant-row');
        if (!firstRow) return;

        const clone = firstRow.cloneNode(true);
        clone.querySelectorAll('input, select').forEach(el => {
            if (el.name) {
                el.name = el.name.replace(/variants\[\d+\]/, `variants[${rowIndex}]`);
            }
        });

        container.appendChild(clone);
        bindPresetColor(clone);
        rowIndex++;
        refreshRows();
        if (window.lucide) window.lucide.createIcons();
    });

    container.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-remove-variant');
        if (!btn) return;
        if (container.querySelectorAll('.variant-row').length > 1) {
            btn.closest('.variant-row').remove();
            refreshRows();
        }
    });

    refreshRows();
});
</script>
@endpush
