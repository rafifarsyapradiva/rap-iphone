@extends('layouts.admin')

@section('page_title', 'Kelola Produk: ' . $product->name)

@section('content')
<div class="space-y-6">

    {{-- Top Navigation & Quick Storefront Preview --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="p-2 rounded-xl bg-white/5 border border-white/10 text-[#8E8E93] hover:text-white transition-colors" title="Kembali ke Inventaris">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-[#5AC8FA] uppercase tracking-wider">{{ $product->series }}</span>
                    @if($product->is_active)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#34C759]/15 text-[#34C759] border border-[#34C759]/30">Aktif di Katalog</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-[#8E8E93]">Nonaktif</span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mt-0.5">{{ $product->name }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('catalog.show', $product->slug) }}" target="_blank" class="glass-btn glass-btn-secondary glass-btn-sm text-xs flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                <span>Lihat di Storefront</span>
            </a>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }} beserta seluruh variannya?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="glass-btn glass-btn-danger glass-btn-sm text-xs flex items-center gap-1.5">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span>Hapus Produk</span>
                </button>
            </form>
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

    {{-- =====================================================================
         BAGIAN 1: FORM EDIT PRODUK INDUK
         ===================================================================== --}}
    <div class="glass-card-elevated rounded-3xl p-6 sm:p-8 border border-white/15 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-white/10">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="smartphone" class="w-4 h-4 text-[#0071E3]"></i>
                    <span>Informasi Produk Induk</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-0.5">Perbarui nama produk, kategori seri, kondisi unit, tipe garansi, dan deskripsi spesifikasi.</p>
            </div>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="space-y-1.5 lg:col-span-2">
                    <label class="text-xs font-semibold text-white block">Nama Produk <span class="text-[#FF453A]">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="glass-input text-xs w-full">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Slug URL</label>
                    <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="glass-input text-xs font-mono w-full">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Kategori <span class="text-[#FF453A]">*</span></label>
                    <select name="category_id" required class="glass-input text-xs w-full bg-[#161B22]">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (int) old('category_id', $product->category_id) === $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Seri iPhone <span class="text-[#FF453A]">*</span></label>
                    <input type="text" name="series" value="{{ old('series', $product->series) }}" required class="glass-input text-xs w-full">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Kondisi Unit <span class="text-[#FF453A]">*</span></label>
                    <select name="condition" class="glass-input text-xs w-full bg-[#161B22]">
                        <option value="new" {{ old('condition', $product->condition) === 'new' ? 'selected' : '' }}>Baru (BNIB Segel Resmi)</option>
                        <option value="second" {{ old('condition', $product->condition) === 'second' ? 'selected' : '' }}>Second Berkualitas (Pre-Owned)</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Grade Fisik (Khusus Second)</label>
                    <select name="grade" class="glass-input text-xs w-full bg-[#161B22]">
                        <option value="">-- Bukan Unit Second --</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade }}" {{ old('grade', $product->grade) === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Tipe Garansi <span class="text-[#FF453A]">*</span></label>
                    <select name="warranty_type" required class="glass-input text-xs w-full bg-[#161B22]">
                        @foreach($warrantyTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('warranty_type', $product->warranty_type) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-white block">Harga Dasar Display (Rp) <span class="text-[#FF453A]">*</span></label>
                    <input type="number" name="base_price" value="{{ old('base_price', (float) $product->base_price) }}" min="0" step="1000" required class="glass-input text-xs font-bold text-[#34C759] w-full">
                </div>

                <div class="space-y-1.5 flex items-center pt-5 lg:col-span-2">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded accent-[#0071E3]">
                        <span class="text-xs font-semibold text-white">Produk Aktif Ditampilkan di Storefront Pelanggan</span>
                    </label>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-white block">Deskripsi Produk &amp; Ketentuan Garansi <span class="text-[#FF453A]">*</span></label>
                <textarea name="description" rows="3" required class="glass-input text-xs w-full">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm text-xs font-bold flex items-center gap-1.5">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Simpan Perubahan Produk Induk</span>
                </button>
            </div>
        </form>
    </div>

    {{-- =====================================================================
         BAGIAN 2: MANAJEMEN VARIAN (WARNA, KAPASITAS, STOK, HPP, HARGA JUAL, IMEI)
         ===================================================================== --}}
    <div class="glass-card-elevated rounded-3xl p-6 sm:p-8 border border-white/15 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-white/10">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4 text-[#34C759]"></i>
                    <span>Manajemen Varian &amp; Stok ({{ $product->variants->count() }} Varian Terdaftar)</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-0.5">
                    Kelola kombinasi Warna, Kapasitas, Stok, HPP (Cost Price), Harga Jual, Battery Health, dan Nomor IMEI per varian.
                </p>
            </div>
            <div class="text-xs text-right">
                <span class="text-[#8E8E93]">Total Stok Semua Varian:</span>
                <span class="font-black text-[#34C759] ml-1">{{ (int) $product->variants->sum('stock') }} Unit</span>
            </div>
        </div>

        {{-- Form Tambah Varian Baru --}}
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-4">
            @csrf
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#5AC8FA] uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    <span>Tambah Varian Baru</span>
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Preset Warna Apple</label>
                    <select id="new-variant-preset-color" class="glass-input text-xs w-full bg-[#161B22]">
                        <option value="">-- Pilih Preset / Custom --</option>
                        @foreach($appleColors as $clr)
                            <option value="{{ $clr['name'] }}" data-hex="{{ $clr['hex'] }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $clr['name'] }} ({{ $clr['hex'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Nama Warna &amp; Hex <span class="text-[#FF453A]">*</span></label>
                    <div class="flex items-center gap-1.5">
                        <input type="text" name="color_name" id="new-variant-color-name" value="Natural Titanium" required class="glass-input text-xs w-full">
                        <input type="color" name="color_hex" id="new-variant-color-hex" value="#9A958E" class="h-9 w-11 rounded-lg bg-transparent border border-white/20 cursor-pointer">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Kapasitas <span class="text-[#FF453A]">*</span></label>
                    <select name="storage_capacity" required class="glass-input text-xs w-full bg-[#161B22]">
                        @foreach($storageOptions as $cap)
                            <option value="{{ $cap }}" {{ $cap === '256GB' ? 'selected' : '' }}>{{ $cap }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">HPP / Cost Price (Rp) <span class="text-[#FF453A]">*</span></label>
                    <input type="number" name="cost_price" value="{{ (float) $product->base_price * 0.9 }}" min="0" step="1000" required class="glass-input text-xs font-mono w-full">
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Harga Jual (Rp) <span class="text-[#FF453A]">*</span></label>
                    <input type="number" name="selling_price" value="{{ (float) $product->base_price }}" min="0" step="1000" required class="glass-input text-xs font-bold text-[#34C759] w-full">
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Stok Unit <span class="text-[#FF453A]">*</span></label>
                    <input type="number" name="stock" value="5" min="0" required class="glass-input text-xs font-bold w-full">
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Kode SKU (Kosong = Otomatis)</label>
                    <input type="text" name="sku" placeholder="Otomatis bila kosong" class="glass-input text-xs font-mono w-full">
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Battery Health % (Opsional)</label>
                    <input type="number" name="battery_health" min="1" max="100" placeholder="Misal: 96" class="glass-input text-xs w-full">
                </div>

                <div>
                    <label class="text-[11px] font-semibold text-[#8E8E93] block mb-1">Nomor IMEI Fisik (Opsional)</label>
                    <input type="text" name="imei" placeholder="Contoh: 358792110293812" class="glass-input text-xs font-mono w-full">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm w-full text-xs font-bold flex items-center justify-center gap-1.5 h-9">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Varian</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Daftar Varian yang Dapat Diedit Langsung --}}
        <div class="space-y-3">
            @forelse($product->variants as $variant)
                @php
                    $margin = (float) $variant->selling_price - (float) $variant->cost_price;
                @endphp
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 hover:border-white/20 transition-all">
                    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-white/10 text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-4 h-4 rounded-full border border-white/30 inline-block" style="background-color: {{ $variant->color_hex }}"></span>
                                <span class="font-bold text-white">{{ $variant->color_name }} &mdash; {{ $variant->storage_capacity }}</span>
                                <span class="font-mono text-[11px] text-[#5AC8FA] bg-[#0071E3]/10 px-2 py-0.5 rounded">{{ $variant->sku }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-[11px]">
                                <span class="text-[#8E8E93]">Estimasi Margin Laba/Unit:</span>
                                <span class="font-bold {{ $margin >= 0 ? 'text-[#34C759]' : 'text-[#FF453A]' }}">
                                    Rp {{ number_format($margin, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-9 gap-2.5 items-end">
                            <div class="lg:col-span-2">
                                <label class="text-[10px] font-semibold text-[#8E8E93] block mb-1">Warna &amp; Hex</label>
                                <div class="flex items-center gap-1.5">
                                    <input type="text" name="color_name" value="{{ $variant->color_name }}" required class="glass-input py-1.5 px-2.5 text-xs w-full">
                                    <input type="color" name="color_hex" value="{{ $variant->color_hex }}" class="h-8 w-10 rounded bg-transparent border border-white/20 cursor-pointer">
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-semibold text-[#8E8E93] block mb-1">Kapasitas</label>
                                <input type="text" name="storage_capacity" value="{{ $variant->storage_capacity }}" required class="glass-input py-1.5 px-2.5 text-xs w-full">
                            </div>

                            <div>
                                <label class="text-[10px] font-semibold text-[#8E8E93] block mb-1">HPP (Rp)</label>
                                <input type="number" name="cost_price" value="{{ (float) $variant->cost_price }}" min="0" step="1000" required class="glass-input py-1.5 px-2.5 text-xs font-mono w-full">
                            </div>

                            <div>
                                <label class="text-[10px] font-semibold text-[#8E8E93] block mb-1">Harga Jual (Rp)</label>
                                <input type="number" name="selling_price" value="{{ (float) $variant->selling_price }}" min="0" step="1000" required class="glass-input py-1.5 px-2.5 text-xs font-bold text-[#34C759] w-full">
                            </div>

                            <div>
                                <label class="text-[10px] font-semibold text-[#8E8E93] block mb-1">Stok</label>
                                <input type="number" name="stock" value="{{ $variant->stock }}" min="0" required class="glass-input py-1.5 px-2.5 text-xs font-bold w-full">
                            </div>

                            <div>
                                <label class="text-[10px] font-semibold text-[#8E8E93] block mb-1">SKU</label>
                                <input type="text" name="sku" value="{{ $variant->sku }}" required class="glass-input py-1.5 px-2.5 text-xs font-mono w-full">
                            </div>

                            <div>
                                <label class="text-[10px] font-semibold text-[#8E8E93] block mb-1">BH (%) &amp; IMEI</label>
                                <div class="flex items-center gap-1">
                                    <input type="number" name="battery_health" value="{{ $variant->battery_health }}" min="1" max="100" placeholder="BH%" class="glass-input py-1.5 px-2 text-xs w-16" title="Battery Health %">
                                    <input type="text" name="imei" value="{{ $variant->imei }}" placeholder="IMEI Fisik" class="glass-input py-1.5 px-2 text-xs font-mono w-full" title="Nomor IMEI">
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-1.5">
                                <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm text-xs py-1.5 px-3" title="Simpan Varian">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Update</span>
                                </button>
                                <button type="submit" form="delete-variant-{{ $variant->id }}" class="p-2 rounded-xl bg-white/5 hover:bg-[#FF453A]/20 text-[#8E8E93] hover:text-[#FF453A] border border-white/10 transition-colors" title="Hapus Varian">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <form id="delete-variant-{{ $variant->id }}" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST"
                          class="hidden" onsubmit="return confirm('Hapus varian {{ $variant->color_name }} ({{ $variant->storage_capacity }})?');">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @empty
                <div class="p-8 rounded-2xl bg-white/5 border border-white/10 text-center text-xs text-[#8E8E93]">
                    Belum ada varian untuk produk ini. Silakan tambahkan varian menggunakan form di atas.
                </div>
            @endforelse
        </div>
    </div>

    {{-- =====================================================================
         BAGIAN 3: MANAJEMEN FOTO PRODUK MULTI-ANGLE
         ===================================================================== --}}
    <div class="glass-card-elevated rounded-3xl p-6 sm:p-8 border border-white/15 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-white/10">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="images" class="w-4 h-4 text-[#5AC8FA]"></i>
                    <span>Manajemen Foto Produk Multi-Angle ({{ $product->images->count() }} Foto)</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-0.5">
                    Unggah foto unit dari berbagai sudut (Tampak Depan, Modul Kamera Belakang, Rangka Samping, Kelengkapan Box), atur urutan tampil, dan pilih foto utama.
                </p>
            </div>
        </div>

        {{-- Form Upload Foto Multi-Angle --}}
        <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data"
              class="p-5 rounded-2xl bg-white/5 border border-white/10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            @csrf

            <div class="lg:col-span-5 space-y-1.5">
                <label class="text-xs font-semibold text-white block">Pilih File Foto Multi-Angle (Bisa Lebih dari 1 File) <span class="text-[#FF453A]">*</span></label>
                <input type="file" name="images[]" multiple required accept="image/jpeg,image/png,image/webp"
                       class="glass-input text-xs w-full file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#0071E3] file:text-white">
            </div>

            <div class="lg:col-span-3 space-y-1.5">
                <label class="text-xs font-semibold text-white block">Kaitkan ke Varian Warna (Opsional)</label>
                <select name="variant_id" class="glass-input text-xs w-full bg-[#161B22]">
                    <option value="">Semua Varian (Galeri Umum)</option>
                    @foreach($product->variants as $variant)
                        <option value="{{ $variant->id }}">{{ $variant->color_name }} ({{ $variant->storage_capacity }})</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2 space-y-1.5">
                <label class="text-xs font-semibold text-white block">Urutan Awal (Angle)</label>
                <input type="number" name="order_index" value="{{ (int) $product->images->max('order_index') + 1 }}" min="0" class="glass-input text-xs w-full">
            </div>

            <div class="lg:col-span-2 flex flex-col gap-2">
                <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-white">
                    <input type="checkbox" name="is_primary" value="1" class="w-4 h-4 rounded accent-[#0071E3]">
                    <span>Jadikan Utama</span>
                </label>
                <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm text-xs font-bold flex items-center justify-center gap-1.5">
                    <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                    <span>Unggah Foto</span>
                </button>
            </div>
        </form>

        {{-- Galeri Foto Produk --}}
        @php
            $angleLabels = [
                1 => 'Sudut 1: Tampak Depan',
                2 => 'Sudut 2: Modul Kamera Belakang',
                3 => 'Sudut 3: Rangka Titanium Samping',
                4 => 'Sudut 4: Kelengkapan Box & Aksesoris',
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($product->images as $img)
                <div class="glass-card rounded-2xl p-4 border {{ $img->is_primary ? 'border-[#0071E3] bg-[#0071E3]/5' : 'border-white/10' }} flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            @if($img->is_primary)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0071E3] text-white">
                                    ★ Foto Utama
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-white/10 text-[#8E8E93]">
                                    {{ $angleLabels[$img->order_index] ?? ('Sudut #' . $img->order_index) }}
                                </span>
                            @endif

                            @if($img->variant)
                                <span class="text-[10px] text-[#E5E5EA] truncate">{{ $img->variant->color_name }}</span>
                            @endif
                        </div>

                        <div class="w-full h-40 rounded-xl bg-black/30 border border-white/10 flex items-center justify-center p-3 overflow-hidden">
                            @if(file_exists(public_path($img->image_path)))
                                <img src="{{ asset($img->image_path) }}" alt="Foto {{ $product->name }}" class="max-h-full max-w-full object-contain">
                            @else
                                <div class="text-center text-[#8E8E93] space-y-1">
                                    <i data-lucide="image" class="w-8 h-8 mx-auto opacity-50"></i>
                                    <span class="text-[10px] font-mono block truncate max-w-[180px]">{{ $img->image_path }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-white/10">
                        {{-- Form Update Urutan Angle & Varian --}}
                        <form action="{{ route('admin.products.images.order', [$product, $img]) }}" method="POST" class="flex items-center gap-1.5">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="order_index" value="{{ $img->order_index }}" min="0" class="glass-input py-1 px-2 text-xs w-16" title="Urutan Sudut">
                            <select name="variant_id" class="glass-input py-1 px-2 text-[11px] bg-[#161B22] flex-grow">
                                <option value="">Semua Varian</option>
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}" {{ (int) $img->variant_id === $variant->id ? 'selected' : '' }}>
                                        {{ $variant->color_name }} ({{ $variant->storage_capacity }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs" title="Simpan Urutan">
                                <i data-lucide="save" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>

                        {{-- Tombol Set Utama & Hapus --}}
                        <div class="flex items-center justify-between gap-2">
                            @if(!$img->is_primary)
                                <form action="{{ route('admin.products.images.primary', [$product, $img]) }}" method="POST" class="flex-grow">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm w-full text-[11px] py-1">
                                        Jadikan Utama
                                    </button>
                                </form>
                            @else
                                <span class="text-[11px] text-[#5AC8FA] font-semibold">Aktif sebagai cover</span>
                            @endif

                            <form action="{{ route('admin.products.images.destroy', [$product, $img]) }}" method="POST"
                                  onsubmit="return confirm('Hapus foto sudut ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-white/5 hover:bg-[#FF453A]/20 text-[#8E8E93] hover:text-[#FF453A] transition-colors" title="Hapus Foto">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="sm:col-span-2 lg:col-span-4 p-8 rounded-2xl bg-white/5 border border-white/10 text-center text-xs text-[#8E8E93]">
                    Belum ada foto multi-angle yang diunggah untuk produk ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const presetSelect = document.getElementById('new-variant-preset-color');
    const nameInput = document.getElementById('new-variant-color-name');
    const hexInput = document.getElementById('new-variant-color-hex');

    presetSelect?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const hex = opt?.getAttribute('data-hex');
        if (this.value && hex) {
            nameInput.value = this.value;
            hexInput.value = hex;
        }
    });
});
</script>
@endpush

