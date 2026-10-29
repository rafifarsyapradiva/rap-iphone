@extends('layouts.admin')

@section('title', 'Kelola Produk: ' . $product->name . ' — Rap-iPhone Admin')
@section('page_title', 'Kelola Produk: ' . $product->name)

@section('content')
<x-admin.page-header
    :eyebrow="$product->series"
    icon="smartphone"
    :title="$product->name"
    subtitle="Kelola informasi produk induk, rincian varian (warna, kapasitas, stok, HPP, harga jual, IMEI), serta galeri foto produk.">
    <x-slot:actions>
        <a href="{{ route('admin.products.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i><span>Kembali</span>
        </a>
        <a href="{{ route('catalog.show', $product->slug) }}" target="_blank" rel="noopener" class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="external-link" class="w-3.5 h-3.5 text-[#0066CC]"></i><span>Lihat di Toko</span>
        </a>
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
              onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }} beserta seluruh variannya?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="glass-btn glass-btn-danger glass-btn-sm">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i><span>Hapus Produk</span>
            </button>
        </form>
    </x-slot:actions>
</x-admin.page-header>

@if($errors->any())
    <div class="adm-alert adm-tone-red" role="alert">
        <i data-lucide="alert-octagon"></i>
        <div>
            <p class="font-bold">Terdapat kesalahan pada input:</p>
            <ul class="list-disc list-inside mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- =====================================================================
     BAGIAN 1: FORM EDIT PRODUK INDUK
     ===================================================================== --}}
<x-admin.card title="Informasi Produk Induk" icon="smartphone"
    description="Perbarui nama produk, kategori seri, kondisi unit, tipe garansi, dan deskripsi spesifikasi.">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="adm-grid-form cols-4">
            <div class="adm-field lg:col-span-2">
                <label class="adm-label" for="edit-name">Nama Produk <span style="color:#D70015">*</span></label>
                <input id="edit-name" type="text" name="name" value="{{ old('name', $product->name) }}" required class="adm-input @error('name') is-invalid @enderror">
                @error('name')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="edit-slug">Slug URL</label>
                <input id="edit-slug" type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="adm-input adm-mono @error('slug') is-invalid @enderror">
                @error('slug')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="edit-category">Kategori <span style="color:#D70015">*</span></label>
                <select id="edit-category" name="category_id" required class="adm-select @error('category_id') is-invalid @enderror">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (int) old('category_id', $product->category_id) === $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="edit-series">Seri iPhone <span style="color:#D70015">*</span></label>
                <input id="edit-series" type="text" name="series" value="{{ old('series', $product->series) }}" required class="adm-input @error('series') is-invalid @enderror">
                @error('series')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="edit-condition">Kondisi Unit <span style="color:#D70015">*</span></label>
                <select id="edit-condition" name="condition" class="adm-select @error('condition') is-invalid @enderror">
                    <option value="new" {{ old('condition', $product->condition) === 'new' ? 'selected' : '' }}>Baru (BNIB Segel Resmi)</option>
                    <option value="second" {{ old('condition', $product->condition) === 'second' ? 'selected' : '' }}>Second Berkualitas (Pre-Owned)</option>
                </select>
                @error('condition')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field" id="edit-grade-wrapper">
                <label class="adm-label" for="edit-grade">Grade Fisik (Khusus Second)</label>
                <select id="edit-grade" name="grade" class="adm-select @error('grade') is-invalid @enderror">
                    <option value="">-- Bukan Unit Second --</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}" {{ old('grade', $product->grade) === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                    @endforeach
                </select>
                @error('grade')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="edit-warranty">Tipe Garansi <span style="color:#D70015">*</span></label>
                <select id="edit-warranty" name="warranty_type" required class="adm-select @error('warranty_type') is-invalid @enderror">
                    @foreach($warrantyTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('warranty_type', $product->warranty_type) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('warranty_type')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field">
                <label class="adm-label" for="edit-base-price">Harga Dasar Display (Rp) <span style="color:#D70015">*</span></label>
                <input id="edit-base-price" type="number" name="base_price" value="{{ old('base_price', (float) $product->base_price) }}" min="0" step="1000" required class="adm-input adm-num @error('base_price') is-invalid @enderror" style="text-align:left !important; font-weight:700">
                @error('base_price')<span class="adm-error">{{ $message }}</span>@enderror
            </div>

            <div class="adm-field lg:col-span-2 flex items-center pt-5">
                <label class="inline-flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded accent-[#0071E3]">
                    <span class="adm-label">Produk Aktif Ditampilkan di Storefront Pelanggan</span>
                </label>
            </div>
        </div>

        <div class="adm-field">
            <label class="adm-label" for="edit-description">Deskripsi Produk &amp; Ketentuan Garansi <span style="color:#D70015">*</span></label>
            <textarea id="edit-description" name="description" rows="3" required class="adm-textarea @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
            @error('description')<span class="adm-error">{{ $message }}</span>@enderror
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm font-bold flex items-center gap-1.5">
                <i data-lucide="save" class="w-3.5 h-3.5"></i>
                <span>Simpan Perubahan Produk Induk</span>
            </button>
        </div>
    </form>
</x-admin.card>

{{-- =====================================================================
     BAGIAN 2: MANAJEMEN VARIAN (WARNA, KAPASITAS, STOK, HPP, HARGA JUAL, IMEI)
     ===================================================================== --}}
<x-admin.card
    :title="'Manajemen Varian & Stok (' . $product->variants->count() . ' Varian Terdaftar)'"
    icon="layers"
    description="Kelola kombinasi Warna, Kapasitas, Stok, HPP (Cost Price), Harga Jual, Battery Health, dan Nomor IMEI per varian.">
    <x-slot:actions>
        <span class="text-xs text-[#6E6E73]">
            Total Stok: <strong class="text-[#008040]">{{ (int) $product->variants->sum('stock') }} Unit</strong>
        </span>
    </x-slot:actions>

    {{-- Form Tambah Varian Baru --}}
    <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" class="p-4 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] space-y-4 mb-6">
        @csrf
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-[#0066CC] uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                <span>Tambah Varian Baru</span>
            </span>
        </div>

        <div class="adm-grid-form cols-5">
            <div class="adm-field">
                <label class="adm-label">Preset Warna</label>
                <select id="new-variant-preset-color" class="adm-select">
                    <option value="">-- Preset / Custom --</option>
                    @foreach($appleColors as $clr)
                        <option value="{{ $clr['name'] }}" data-hex="{{ $clr['hex'] }}" {{ $loop->first ? 'selected' : '' }}>
                            {{ $clr['name'] }} ({{ $clr['hex'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field">
                <label class="adm-label">Nama Warna &amp; Hex <span style="color:#D70015">*</span></label>
                <div class="flex items-center gap-1.5">
                    <input type="text" name="color_name" id="new-variant-color-name" value="Natural Titanium" required class="adm-input">
                    <input type="color" name="color_hex" id="new-variant-color-hex" value="#9A958E" style="height:2.5rem;width:2.5rem;border-radius:10px;border:1px solid #D2D2D7;background:#fff;cursor:pointer;padding:2px">
                </div>
            </div>

            <div class="adm-field">
                <label class="adm-label">Kapasitas <span style="color:#D70015">*</span></label>
                <select name="storage_capacity" required class="adm-select">
                    @foreach($storageOptions as $cap)
                        <option value="{{ $cap }}" {{ $cap === '256GB' ? 'selected' : '' }}>{{ $cap }}</option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field">
                <label class="adm-label">HPP / Cost (Rp) <span style="color:#D70015">*</span></label>
                <input type="number" name="cost_price" value="{{ (float) $product->base_price * 0.9 }}" min="0" step="1000" required class="adm-input adm-mono">
            </div>

            <div class="adm-field">
                <label class="adm-label">Harga Jual (Rp) <span style="color:#D70015">*</span></label>
                <input type="number" name="selling_price" value="{{ (float) $product->base_price }}" min="0" step="1000" required class="adm-input" style="font-weight:700">
            </div>

            <div class="adm-field">
                <label class="adm-label">Stok <span style="color:#D70015">*</span></label>
                <input type="number" name="stock" value="5" min="0" required class="adm-input" style="font-weight:700">
            </div>

            <div class="adm-field">
                <label class="adm-label">SKU (Opsional)</label>
                <input type="text" name="sku" placeholder="Otomatis jika kosong" class="adm-input adm-mono">
            </div>

            <div class="adm-field">
                <label class="adm-label">Battery Health %</label>
                <input type="number" name="battery_health" min="1" max="100" placeholder="Misal: 96" class="adm-input">
            </div>

            <div class="adm-field">
                <label class="adm-label">IMEI Fisik (Opsional)</label>
                <input type="text" name="imei" placeholder="15 digit IMEI" class="adm-input adm-mono">
            </div>

            <div class="adm-field flex items-end">
                <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm w-full font-bold flex items-center justify-center gap-1.5" style="height:2.5rem">
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
            <div class="p-4 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] hover:border-[#D2D2D7] transition-all">
                <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-[#E5E5EA] text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3.5 h-3.5 rounded-full inline-block border border-black/20" style="background-color: {{ $variant->color_hex }}"></span>
                            <span class="font-bold text-[#1D1D1F]">{{ $variant->color_name }} &mdash; {{ $variant->storage_capacity }}</span>
                            <span class="adm-mono text-[11px] text-[#0066CC] bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $variant->sku }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px]">
                            <span class="text-[#6E6E73]">Estimasi Margin/Unit:</span>
                            <span class="font-bold {{ $margin >= 0 ? 'text-[#008040]' : 'text-[#D70015]' }}">
                                Rp {{ number_format($margin, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="adm-grid-form cols-4 lg:grid-cols-9 gap-2.5 items-end">
                        <div class="lg:col-span-2 adm-field">
                            <label class="adm-label" style="font-size:0.6875rem">Warna &amp; Hex</label>
                            <div class="flex items-center gap-1.5">
                                <input type="text" name="color_name" value="{{ $variant->color_name }}" required class="adm-input" style="min-height:2.25rem;padding:0.35rem 0.65rem">
                                <input type="color" name="color_hex" value="{{ $variant->color_hex }}" style="height:2.25rem;width:2.25rem;border-radius:8px;border:1px solid #D2D2D7;background:#fff;cursor:pointer;padding:2px">
                            </div>
                        </div>

                        <div class="adm-field">
                            <label class="adm-label" style="font-size:0.6875rem">Kapasitas</label>
                            <input type="text" name="storage_capacity" value="{{ $variant->storage_capacity }}" required class="adm-input" style="min-height:2.25rem;padding:0.35rem 0.65rem">
                        </div>

                        <div class="adm-field">
                            <label class="adm-label" style="font-size:0.6875rem">HPP (Rp)</label>
                            <input type="number" name="cost_price" value="{{ (float) $variant->cost_price }}" min="0" step="1000" required class="adm-input adm-mono" style="min-height:2.25rem;padding:0.35rem 0.65rem">
                        </div>

                        <div class="adm-field">
                            <label class="adm-label" style="font-size:0.6875rem">Harga Jual (Rp)</label>
                            <input type="number" name="selling_price" value="{{ (float) $variant->selling_price }}" min="0" step="1000" required class="adm-input font-bold" style="min-height:2.25rem;padding:0.35rem 0.65rem">
                        </div>

                        <div class="adm-field">
                            <label class="adm-label" style="font-size:0.6875rem">Stok</label>
                            <input type="number" name="stock" value="{{ $variant->stock }}" min="0" required class="adm-input font-bold" style="min-height:2.25rem;padding:0.35rem 0.65rem">
                        </div>

                        <div class="adm-field">
                            <label class="adm-label" style="font-size:0.6875rem">SKU</label>
                            <input type="text" name="sku" value="{{ $variant->sku }}" required class="adm-input adm-mono" style="min-height:2.25rem;padding:0.35rem 0.65rem">
                        </div>

                        <div class="adm-field">
                            <label class="adm-label" style="font-size:0.6875rem">BH(%) &amp; IMEI</label>
                            <div class="flex items-center gap-1">
                                <input type="number" name="battery_health" value="{{ $variant->battery_health }}" min="1" max="100" placeholder="BH%" class="adm-input" style="min-height:2.25rem;padding:0.35rem 0.4rem;width:3.5rem" title="Battery Health %">
                                <input type="text" name="imei" value="{{ $variant->imei }}" placeholder="IMEI" class="adm-input adm-mono" style="min-height:2.25rem;padding:0.35rem 0.4rem" title="Nomor IMEI">
                            </div>
                        </div>

                        <div class="adm-field flex items-center justify-end gap-1.5">
                            <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm" style="min-height:2.25rem;padding:0.35rem 0.75rem" title="Simpan Varian">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                <span>Update</span>
                            </button>
                            <button type="submit" form="delete-variant-{{ $variant->id }}" class="adm-icon-btn is-danger" style="min-height:2.25rem;width:2.25rem" title="Hapus Varian">
                                <i data-lucide="trash-2"></i>
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
            <x-admin.empty-state icon="layers" title="Belum ada varian" text="Silakan tambahkan varian menggunakan form di atas." />
        @endforelse
    </div>
</x-admin.card>

{{-- =====================================================================
     BAGIAN 3: MANAJEMEN FOTO PRODUK MULTI-ANGLE
     ===================================================================== --}}
<x-admin.card
    :title="'Manajemen Foto Produk Multi-Angle (' . $product->images->count() . ' Foto)'"
    icon="images"
    description="Unggah foto unit dari berbagai sudut (Tampak Depan, Modul Kamera Belakang, Rangka Samping, Kelengkapan Box), atur urutan tampil, dan pilih foto utama.">

    {{-- Form Upload Foto Multi-Angle --}}
    <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data"
          class="p-4 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] adm-grid-form cols-4 lg:grid-cols-12 gap-3 items-end mb-6">
        @csrf

        <div class="lg:col-span-5 adm-field">
            <label class="adm-label">Pilih File Foto Multi-Angle <span style="color:#D70015">*</span></label>
            <input type="file" name="images[]" multiple required accept="image/jpeg,image/png,image/webp"
                   class="adm-input file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#0071E3] file:text-white" style="padding-top:0.35rem">
        </div>

        <div class="lg:col-span-3 adm-field">
            <label class="adm-label">Kaitkan ke Varian (Opsional)</label>
            <select name="variant_id" class="adm-select">
                <option value="">Semua Varian (Galeri Umum)</option>
                @foreach($product->variants as $variant)
                    <option value="{{ $variant->id }}">{{ $variant->color_name }} ({{ $variant->storage_capacity }})</option>
                @endforeach
            </select>
        </div>

        <div class="lg:col-span-2 adm-field">
            <label class="adm-label">Urutan Angle</label>
            <input type="number" name="order_index" value="{{ (int) $product->images->max('order_index') + 1 }}" min="0" class="adm-input">
        </div>

        <div class="lg:col-span-2 adm-field flex flex-col gap-1.5">
            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-[#1D1D1F]">
                <input type="checkbox" name="is_primary" value="1" class="w-4 h-4 rounded accent-[#0071E3]">
                <span class="adm-label" style="font-size:0.75rem">Jadikan Utama</span>
            </label>
            <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm w-full font-bold flex items-center justify-center gap-1.5" style="height:2.5rem">
                <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                <span>Unggah Foto</span>
            </button>
        </div>
    </form>

    {{-- Galeri Foto Produk --}}
    @php
        $angleLabels = [
            1 => 'Sudut 1: Depan',
            2 => 'Sudut 2: Kamera Belakang',
            3 => 'Sudut 3: Rangka Samping',
            4 => 'Sudut 4: Box & Aksesoris',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($product->images as $img)
            <div class="adm-card p-4 flex flex-col justify-between space-y-3 {{ $img->is_primary ? 'border-[#0071E3] ring-2 ring-[#0071E3]/20' : '' }}">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        @if($img->is_primary)
                            <x-admin.status-badge tone="blue" label="★ Foto Utama" :dot="false" />
                        @else
                            <x-admin.status-badge tone="gray" :label="$angleLabels[$img->order_index] ?? ('Sudut #' . $img->order_index)" :dot="false" />
                        @endif

                        @if($img->variant)
                            <span class="text-[11px] text-[#6E6E73] truncate font-medium">{{ $img->variant->color_name }}</span>
                        @endif
                    </div>

                    <div class="w-full h-44 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA] flex items-center justify-center p-3 overflow-hidden">
                        @if(file_exists(public_path($img->image_path)))
                            <img src="{{ asset($img->image_path) }}" alt="Foto {{ $product->name }}" class="max-h-full max-w-full object-contain">
                        @else
                            <div class="text-center text-[#86868B] space-y-1">
                                <i data-lucide="image" class="w-8 h-8 mx-auto opacity-50"></i>
                                <span class="text-[10px] font-mono block truncate max-w-[180px]">{{ $img->image_path }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-[#E5E5EA]">
                    {{-- Form Update Urutan Angle & Varian --}}
                    <form action="{{ route('admin.products.images.order', [$product, $img]) }}" method="POST" class="flex items-center gap-1.5">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="order_index" value="{{ $img->order_index }}" min="0" class="adm-input" style="width:4rem;padding:0.25rem 0.5rem;min-height:2rem;font-size:0.75rem" title="Urutan Sudut">
                        <select name="variant_id" class="adm-select flex-grow" style="padding:0.25rem 0.5rem;min-height:2rem;font-size:0.75rem">
                            <option value="">Semua Varian</option>
                            @foreach($product->variants as $variant)
                                <option value="{{ $variant->id }}" {{ (int) $img->variant_id === $variant->id ? 'selected' : '' }}>
                                    {{ $variant->color_name }} ({{ $variant->storage_capacity }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="adm-icon-btn" style="width:2rem;height:2rem" title="Simpan Urutan">
                            <i data-lucide="save"></i>
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
                            <span class="text-[11px] text-[#0066CC] font-semibold">Aktif sebagai cover</span>
                        @endif

                        <form action="{{ route('admin.products.images.destroy', [$product, $img]) }}" method="POST"
                              onsubmit="return confirm('Hapus foto sudut ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="adm-icon-btn is-danger" style="width:2rem;height:2rem" title="Hapus Foto">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-4">
                <x-admin.empty-state icon="images" title="Belum ada foto produk" text="Silakan unggah foto multi-angle untuk produk ini." />
            </div>
        @endforelse
    </div>
</x-admin.card>
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

    const editCondition = document.getElementById('edit-condition');
    const editGradeWrapper = document.getElementById('edit-grade-wrapper');
    function syncEditGrade() {
        if (!editCondition || !editGradeWrapper) return;
        editGradeWrapper.style.display = editCondition.value === 'second' ? '' : 'none';
    }
    editCondition?.addEventListener('change', syncEditGrade);
    syncEditGrade();
});
</script>
@endpush
