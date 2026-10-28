@extends('layouts.admin')

@section('page_title', 'Katalog & Varian Stok')

@section('content')
<div class="space-y-6">

    {{-- Header & Action --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0071E3]/15 border border-[#0071E3]/30 text-xs font-semibold text-[#5AC8FA] mb-2">
                <i data-lucide="smartphone" class="w-3.5 h-3.5"></i>
                <span>Manajemen Inventaris &amp; Varian</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Katalog &amp; Varian Stok iPhone</h1>
            <p class="text-xs sm:text-sm text-[#8E8E93] mt-0.5">
                Kelola produk iPhone induk, kombinasi varian (Warna, Kapasitas, Stok, HPP, Harga Jual, IMEI), dan foto multi-angle.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.products.create') }}" class="glass-btn glass-btn-primary glass-btn-md text-xs font-bold flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Produk iPhone Baru</span>
            </a>
        </div>
    </div>

    {{-- Kartu Metrik Inventaris --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $statCards = [
                [
                    'label' => 'Total Model iPhone',
                    'value' => $metrics['total_products'],
                    'sub' => $metrics['total_variants'] . ' Varian Terdaftar',
                    'icon' => 'smartphone',
                    'color' => '#0071E3',
                ],
                [
                    'label' => 'Total Stok Fisik',
                    'value' => $metrics['total_stock'],
                    'sub' => 'Unit siap jual di gudang/toko',
                    'icon' => 'boxes',
                    'color' => '#34C759',
                ],
                [
                    'label' => 'BNIB vs Second',
                    'value' => $metrics['new_products'] . ' / ' . $metrics['second_products'],
                    'sub' => 'Model Baru Resmi vs Pre-Owned',
                    'icon' => 'shield-check',
                    'color' => '#5AC8FA',
                ],
                [
                    'label' => 'Stok Menipis (≤ 3)',
                    'value' => $metrics['low_stock_variants'],
                    'sub' => 'Varian perlu restock segera',
                    'icon' => 'alert-triangle',
                    'color' => '#FF9F0A',
                ],
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="glass-card rounded-2xl p-5 border border-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-[#8E8E93] uppercase tracking-wider">{{ $card['label'] }}</p>
                        <p class="text-2xl font-black mt-1" style="color: {{ $card['color'] }}">{{ $card['value'] }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center border"
                         style="color: {{ $card['color'] }}; background: {{ $card['color'] }}22; border-color: {{ $card['color'] }}55;">
                        <i data-lucide="{{ $card['icon'] }}" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-[11px] text-[#8E8E93] mt-3">{{ $card['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filter & Pencarian --}}
    <div class="glass-card rounded-2xl p-4 border border-white/10 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Tab Kondisi --}}
            @php
                $conditionTabs = [
                    'all' => 'Semua Unit (' . $metrics['total_products'] . ')',
                    'new' => 'BNIB Garansi Resmi (' . $metrics['new_products'] . ')',
                    'second' => 'Second Like New (' . $metrics['second_products'] . ')',
                ];
            @endphp
            <div class="flex flex-wrap items-center gap-1.5">
                @foreach($conditionTabs as $condKey => $condLabel)
                    <a href="{{ route('admin.products.index', array_filter([
                        'condition' => $condKey !== 'all' ? $condKey : null,
                        'category_id' => $categoryFilter,
                        'warranty_type' => $warrantyFilter !== 'all' ? $warrantyFilter : null,
                        'status' => $statusFilter !== 'all' ? $statusFilter : null,
                        'q' => $search,
                    ])) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $conditionFilter === $condKey ? 'bg-[#0071E3] text-white shadow-md' : 'bg-white/5 text-[#8E8E93] hover:text-white hover:bg-white/10' }}">
                        {{ $condLabel }}
                    </a>
                @endforeach
            </div>

            {{-- Form Filter & Search --}}
            <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                @if($conditionFilter !== 'all')
                    <input type="hidden" name="condition" value="{{ $conditionFilter }}">
                @endif

                <select name="category_id" class="glass-input py-2 px-3 text-xs bg-[#161B22] w-44" onchange="this.form.submit()">
                    <option value="">Semua Kategori Seri</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) $categoryFilter === (string) $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <div class="relative w-full sm:w-64">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari iPhone, SKU, IMEI, warna..." class="glass-input py-2 pl-9 pr-3 text-xs w-full">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-[#8E8E93] absolute left-3 top-2.5"></i>
                </div>

                <button type="submit" class="glass-btn glass-btn-secondary glass-btn-sm text-xs">Filter</button>
                @if($search !== '' || $categoryFilter || $conditionFilter !== 'all')
                    <a href="{{ route('admin.products.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm text-xs text-[#FF453A]">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel Inventaris Produk --}}
    <div class="glass-card rounded-3xl border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5 text-[11px] font-bold text-[#8E8E93] uppercase tracking-wider">
                        <th class="py-3.5 pl-6 pr-3">Produk &amp; Seri</th>
                        <th class="py-3.5 px-3">Kondisi &amp; Garansi</th>
                        <th class="py-3.5 px-3">Varian Warna &amp; Kapasitas</th>
                        <th class="py-3.5 px-3 text-right">Rentang HPP</th>
                        <th class="py-3.5 px-3 text-right">Harga Jual</th>
                        <th class="py-3.5 px-3 text-center">Total Stok</th>
                        <th class="py-3.5 px-3 text-center">Status</th>
                        <th class="py-3.5 pr-6 pl-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 text-xs">
                    @forelse($products as $product)
                        @php
                            $variants = $product->variants;
                            $colors = $variants->unique('color_name');
                            $storages = $variants->pluck('storage_capacity')->unique();
                            $totalStock = (int) $variants->sum('stock');
                            $minCost = $variants->min('cost_price') ?? 0;
                            $maxCost = $variants->max('cost_price') ?? 0;
                            $minSell = $variants->min('selling_price') ?? $product->base_price;
                            $maxSell = $variants->max('selling_price') ?? $product->base_price;
                            $primaryImg = $product->primaryImage->image_path ?? ($product->images->first()->image_path ?? null);
                        @endphp
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            {{-- Produk & Seri --}}
                            <td class="py-4 pl-6 pr-3">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 overflow-hidden p-1.5">
                                        @if($primaryImg && file_exists(public_path($primaryImg)))
                                            <img src="{{ asset($primaryImg) }}" alt="{{ $product->name }}" width="48" height="48" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain">
                                        @else
                                            <i data-lucide="smartphone" class="w-5 h-5 text-[#5AC8FA]"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="font-bold text-white hover:text-[#5AC8FA] transition-colors text-sm block">
                                            {{ $product->name }}
                                        </a>
                                        <span class="text-[11px] text-[#8E8E93]">{{ $product->series }} &middot; {{ $product->images->count() }} Foto</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Kondisi & Garansi --}}
                            <td class="py-4 px-3">
                                @if($product->condition === 'second')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#34C759]/15 text-[#34C759] border border-[#34C759]/30">
                                        Second &middot; {{ $product->grade ?? 'Grade A+' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0071E3]/15 text-[#5AC8FA] border border-[#0071E3]/30">
                                        BNIB Baru
                                    </span>
                                @endif
                                <span class="block text-[11px] text-[#8E8E93] mt-1">
                                    {{ $warrantyTypes[$product->warranty_type] ?? $product->warranty_type }}
                                </span>
                            </td>

                            {{-- Varian Warna & Kapasitas --}}
                            <td class="py-4 px-3">
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    @forelse($colors as $clr)
                                        <span class="w-3.5 h-3.5 rounded-full border border-white/30 inline-block"
                                              style="background-color: {{ $clr->color_hex }};"
                                              title="{{ $clr->color_name }}"></span>
                                    @empty
                                        <span class="text-[11px] text-[#8E8E93]">Belum ada varian</span>
                                    @endforelse
                                    @if($variants->isNotEmpty())
                                        <span class="text-[11px] text-[#8E8E93] ml-1">({{ $variants->count() }} SKU)</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($storages as $cap)
                                        <span class="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-[10px] font-semibold text-[#E5E5EA]">
                                            {{ $cap }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- HPP --}}
                            <td class="py-4 px-3 text-right whitespace-nowrap text-[#8E8E93]">
                                @if($variants->isNotEmpty())
                                    <span class="font-mono text-white">Rp {{ number_format((float) $minCost, 0, ',', '.') }}</span>
                                    @if($maxCost > $minCost)
                                        <span class="block text-[10px] text-[#8E8E93]">s/d Rp {{ number_format((float) $maxCost, 0, ',', '.') }}</span>
                                    @endif
                                @else
                                    <span>-</span>
                                @endif
                            </td>

                            {{-- Harga Jual --}}
                            <td class="py-4 px-3 text-right whitespace-nowrap">
                                <span class="font-bold text-[#34C759]">Rp {{ number_format((float) $minSell, 0, ',', '.') }}</span>
                                @if($maxSell > $minSell)
                                    <span class="block text-[10px] text-[#8E8E93]">s/d Rp {{ number_format((float) $maxSell, 0, ',', '.') }}</span>
                                @endif
                            </td>

                            {{-- Total Stok --}}
                            <td class="py-4 px-3 text-center">
                                @if($totalStock <= 0)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FF453A]/15 text-[#FF453A] border border-[#FF453A]/30">
                                        Habis (0)
                                    </span>
                                @elseif($totalStock <= 5)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FF9F0A]/15 text-[#FF9F0A] border border-[#FF9F0A]/30">
                                        {{ $totalStock }} Unit
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#34C759]/15 text-[#34C759] border border-[#34C759]/30">
                                        {{ $totalStock }} Unit
                                    </span>
                                @endif
                            </td>

                            {{-- Status Aktif --}}
                            <td class="py-4 px-3 text-center">
                                @if($product->is_active)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#34C759]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#34C759]"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#8E8E93]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8E8E93]"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 pr-6 pl-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="glass-btn glass-btn-secondary glass-btn-sm text-xs inline-flex items-center gap-1.5"
                                       title="Kelola Produk, Varian & Foto">
                                        <i data-lucide="sliders" class="w-3.5 h-3.5 text-[#5AC8FA]"></i>
                                        <span>Kelola Varian &amp; Foto</span>
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }} beserta seluruh variannya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-white/5 hover:bg-[#FF453A]/20 text-[#8E8E93] hover:text-[#FF453A] border border-white/10 transition-colors" title="Hapus Produk">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-[#8E8E93]">
                                <i data-lucide="smartphone" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                <p class="text-sm font-medium text-white">Belum ada produk iPhone yang sesuai dengan filter.</p>
                                <p class="text-xs mt-1">Tambahkan produk baru atau ubah kata kunci pencarian di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-white/10">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

