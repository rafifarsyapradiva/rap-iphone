@extends('layouts.admin')

@section('page_title', 'Tambah Produk iPhone Baru')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Top Navigation Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="p-2 rounded-xl bg-white/5 border border-white/10 text-[#8E8E93] hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <span class="text-[11px] font-bold text-[#5AC8FA] uppercase tracking-wider">Manajemen Katalog</span>
                <h1 class="text-2xl font-bold text-white mt-0.5">Tambah Produk iPhone Baru</h1>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-[#FF453A]/15 border border-[#FF453A]/30 text-xs text-white space-y-1">
            <p class="font-bold text-[#FF453A]">Terdapat kesalahan pada input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- 1. Informasi Produk Induk --}}
        <div class="glass-card-elevated rounded-3xl p-6 sm:p-8 border border-white/15 space-y-5">
            <div class="pb-3 border-b border-white/10">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="smartphone" class="w-4 h-4 text-[#0071E3]"></i>
                    <span>1. Informasi Produk Induk iPhone</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-0.5">Tentukan seri, kategori, kondisi unit (BNIB atau Second), dan jaminan garansi.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="space-y-1.5 lg:col-span-2">
                    <label class="text-xs font-semibold text-white block">Nama Produk iPhone <span class="text-[#FF453A]">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: iPhone 16 Pro Max"
                           class="glass-input text-xs w-full">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Kategori Katalog <span class="text-[#FF453A]">*</span></label>
                    <select name="category_id" required class="glass-input text-xs w-full bg-[#161B22]">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Seri iPhone <span class="text-[#FF453A]">*</span></label>
                    <input type="text" name="series" value="{{ old('series', 'iPhone 16 Series') }}" required
                           placeholder="Contoh: iPhone 16 Series"
                           class="glass-input text-xs w-full">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Kondisi Unit <span class="text-[#FF453A]">*</span></label>
                    <select name="condition" id="create-condition-select" required class="glass-input text-xs w-full bg-[#161B22]">
                        <option value="new" {{ old('condition', 'new') === 'new' ? 'selected' : '' }}>Baru (BNIB Segel Resmi)</option>
                        <option value="second" {{ old('condition') === 'second' ? 'selected' : '' }}>Second Berkualitas (Pre-Owned)</option>
                    </select>
                </div>

                <div class="space-y-1.5" id="create-grade-wrapper">
                    <label class="text-xs font-semibold text-white block">Grade Fisik (Khusus Second)</label>
                    <select name="grade" class="glass-input text-xs w-full bg-[#161B22]">
                        <option value="">-- Bukan Unit Second --</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade }}" {{ old('grade') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Tipe Garansi <span class="text-[#FF453A]">*</span></label>
                    <select name="warranty_type" required class="glass-input text-xs w-full bg-[#161B22]">
                        @foreach($warrantyTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('warranty_type', 'resmi_ibox') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Harga Dasar Display (Rp) <span class="text-[#FF453A]">*</span></label>
                    <input type="number" name="base_price" value="{{ old('base_price', 16499000) }}" min="0" step="1000" required
                           class="glass-input text-xs font-bold text-[#34C759] w-full">
                    <span class="text-[10px] text-[#8E8E93] block">Otomatis disinkronkan dengan harga jual varian terendah.</span>
                </div>

                <div class="space-y-1.5 flex items-center pt-5">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded accent-[#0071E3]">
                        <span class="text-xs font-semibold text-white">Tampilkan Aktif di Storefront Katalog</span>
                    </label>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-white block">Deskripsi Spesifikasi &amp; Ketentuan Garansi <span class="text-[#FF453A]">*</span></label>
                <textarea name="description" rows="4" required
                          placeholder="Tuliskan deskripsi keunggulan chipset, layar Super Retina XDR, kamera, serta ketentuan klaim garansi resmi..."
                          class="glass-input text-xs w-full">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- 2. Varian Awal (Warna, Kapasitas, Stok, HPP, Harga Jual, IMEI) --}}
        <div class="glass-card-elevated rounded-3xl p-6 sm:p-8 border border-white/15 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-white/10">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-[#34C759]"></i>
                        <span>2. Varian Awal (Warna, Kapasitas, HPP, Harga Jual, Stok &amp; IMEI)</span>
                    </h2>
                    <p class="text-xs text-[#8E8E93] mt-0.5">Tambahkan satu atau beberapa varian sekarang, atau kelola lebih lanjut setelah produk dibuat.</p>
                </div>
                <button type="button" id="btn-add-variant-row" class="glass-btn glass-btn-secondary glass-btn-sm text-xs flex items-center gap-1.5 shrink-0">
                    <i data-lucide="plus" class="w-3.5 h-3.5 text-[#34C759]"></i>
                    <span>Tambah Baris Varian</span>
                </button>
            </div>

            <div id="variants-rows-container" class="space-y-4">
                <div class="variant-row p-4 rounded-2xl bg-white/5 border border-white/10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Preset Warna Apple</label>
                        <select class="glass-input text-xs w-full bg-[#161B22] preset-color-select">
                            <option value="">-- Pilih Preset / Custom --</option>
                            @foreach($appleColors as $clr)
                                <option value="{{ $clr['name'] }}" data-hex="{{ $clr['hex'] }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $clr['name'] }} ({{ $clr['hex'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Nama Warna &amp; Hex</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="variants[0][color_name]" value="Natural Titanium" required class="glass-input text-xs w-full input-color-name">
                            <input type="color" name="variants[0][color_hex]" value="#9A958E" class="h-9 w-11 rounded-lg bg-transparent border border-white/20 cursor-pointer input-color-hex">
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Kapasitas Penyimpanan</label>
                        <select name="variants[0][storage_capacity]" class="glass-input text-xs w-full bg-[#161B22]">
                            @foreach($storageOptions as $cap)
                                <option value="{{ $cap }}" {{ $cap === '256GB' ? 'selected' : '' }}>{{ $cap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Stok Unit</label>
                        <input type="number" name="variants[0][stock]" value="10" min="0" required class="glass-input text-xs font-bold w-full">
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">HPP / Cost Price (Rp)</label>
                        <input type="number" name="variants[0][cost_price]" value="15000000" min="0" step="1000" required class="glass-input text-xs font-mono w-full">
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Harga Jual Retail (Rp)</label>
                        <input type="number" name="variants[0][selling_price]" value="16499000" min="0" step="1000" required class="glass-input text-xs font-bold text-[#34C759] w-full">
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Battery Health % (Opsional)</label>
                        <input type="number" name="variants[0][battery_health]" min="1" max="100" placeholder="Misal: 96" class="glass-input text-xs w-full">
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Nomor IMEI / SKU (Opsional)</label>
                        <input type="text" name="variants[0][imei]" placeholder="15 digit IMEI fisik" class="glass-input text-xs font-mono w-full">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Foto Produk Multi-Angle Awal --}}
        <div class="glass-card-elevated rounded-3xl p-6 sm:p-8 border border-white/15 space-y-4">
            <div class="pb-3 border-b border-white/10">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="images" class="w-4 h-4 text-[#5AC8FA]"></i>
                    <span>3. Foto Produk Multi-Angle (Opsional)</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-0.5">Unggah foto tampak depan, belakang (modul kamera), samping, dan kelengkapan box (JPG/PNG/WebP maks. 4MB per file).</p>
            </div>

            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
                   class="glass-input text-xs w-full file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0071E3] file:text-white hover:file:bg-[#0077ED]">
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.products.index') }}" class="glass-btn glass-btn-secondary glass-btn-md text-xs">Batal</a>
            <button type="submit" class="glass-btn glass-btn-primary glass-btn-md text-xs font-bold flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Produk &amp; Varian</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('variants-rows-container');
    const addBtn = document.getElementById('btn-add-variant-row');
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

    container.querySelectorAll('.variant-row').forEach(bindPresetColor);

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
    });
});
</script>
@endpush

