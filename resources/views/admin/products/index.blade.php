@extends('layouts.admin')

@section('title', 'Katalog & Varian Stok — Rap-iPhone Admin')
@section('page_title', 'Katalog & Varian Stok')

@section('content')
@php
    $filterParams = fn (array $override = []) => array_filter(array_merge([
        'condition' => $conditionFilter !== 'all' ? $conditionFilter : null,
        'category_id' => $categoryFilter,
        'warranty_type' => $warrantyFilter !== 'all' ? $warrantyFilter : null,
        'status' => $statusFilter !== 'all' ? $statusFilter : null,
        'q' => $search,
    ], $override), fn ($v) => $v !== null && $v !== '');

    $conditionTabs = [
        'all' => ['Semua Unit', $metrics['total_products']],
        'new' => ['BNIB Garansi Resmi', $metrics['new_products']],
        'second' => ['Second Like New', $metrics['second_products']],
    ];
    $isFiltered = $search !== '' || $categoryFilter || $conditionFilter !== 'all' || $warrantyFilter !== 'all' || $statusFilter !== 'all';
@endphp

<x-admin.page-header
    meta="Manajemen Inventaris & Varian"
    icon="smartphone"
    title="Katalog & Varian Stok iPhone"
    subtitle="Buku besar inventaris iPhone, varian spesifikasi (warna, kapasitas, stok, HPP, harga jual, IMEI), dan kurasi galeri studio.">
    <x-slot:actions>
        <a href="{{ route('admin.products.create') }}" class="adm-btn adm-btn-primary adm-btn-sm">
            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
            <span>Tambah Produk iPhone Baru</span>
        </a>
    </x-slot:actions>
</x-admin.page-header>

{{-- Kartu Metrik Inventaris --}}
<div class="adm-stat-grid cols-4">
    <x-admin.stat-card label="Total model iPhone" :value="$metrics['total_products']" icon="smartphone" tone="blue"
        :hint="$metrics['total_variants'] . ' varian terdaftar'" />
    <x-admin.stat-card label="Total stok fisik" :value="$metrics['total_stock']" icon="boxes" tone="green"
        hint="Unit siap jual di gudang & toko" />
    <x-admin.stat-card label="Rasio BNIB vs Second" :value="$metrics['new_products'] . ' / ' . $metrics['second_products']" icon="shield-check" tone="purple"
        hint="Model resmi baru vs kurasi bekas" />
    <x-admin.stat-card label="Stok menipis (≤ 3)" :value="$metrics['low_stock_variants']" icon="alert-triangle"
        :tone="$metrics['low_stock_variants'] > 0 ? 'amber' : 'gray'" hint="Varian perlu restock segera" />
</div>

<x-admin.card flush>
    {{-- Toolbar: Tab Kondisi + Filter --}}
    <div class="adm-card-body" style="padding-bottom: 1rem;">
        <div class="adm-toolbar">
            <div class="adm-tabs">
                @foreach($conditionTabs as $condKey => [$condLabel, $condCount])
                    <a href="{{ route('admin.products.index', $filterParams(['condition' => $condKey !== 'all' ? $condKey : null])) }}"
                       class="adm-tab {{ $conditionFilter === $condKey ? 'is-active' : '' }}">
                        {{ $condLabel }}
                        <span class="adm-tab-count">({{ $condCount }})</span>
                    </a>
                @endforeach
            </div>

            <form action="{{ route('admin.products.index') }}" method="GET" class="adm-filters">
                @if($conditionFilter !== 'all')
                    <input type="hidden" name="condition" value="{{ $conditionFilter }}">
                @endif

                <select name="category_id" class="adm-select" style="width: 11rem;" onchange="this.form.submit()" aria-label="Filter kategori seri">
                    <option value="">Semua Kategori Seri</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) $categoryFilter === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <div class="adm-search">
                    <i data-lucide="search"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari iPhone, SKU, IMEI, warna..." class="adm-input" aria-label="Cari produk">
                </div>

                <button type="submit" class="adm-btn adm-btn-sm">Filter</button>
                @if($isFiltered)
                    <a href="{{ route('admin.products.index') }}" class="adm-btn adm-btn-sm text-[var(--adm-tone-red-ink)]">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel Inventaris Produk (Master Trade Ledger) --}}
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th scope="col">Produk &amp; Seri</th>
                    <th scope="col">Kondisi &amp; Garansi</th>
                    <th scope="col">Varian Koleksi</th>
                    <th scope="col" class="adm-num">HPP (Modal)</th>
                    <th scope="col" class="adm-num">Harga Jual</th>
                    <th scope="col" style="text-align:center">Stok</th>
                    <th scope="col" style="text-align:center">Status</th>
                    <th scope="col" class="adm-num">Aksi</th>
                </tr>
            </thead>
            <tbody>
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
                    <tr>
                        {{-- Produk & Seri --}}
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="adm-thumb" style="width:2.75rem;height:2.75rem;">
                                    @if($primaryImg && file_exists(public_path($primaryImg)))
                                        <img src="{{ asset($primaryImg) }}" alt="{{ $product->name }}" width="44" height="44" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain">
                                    @else
                                        <i data-lucide="smartphone" class="w-4 h-4 text-[var(--adm-ink-muted)]"></i>
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="adm-cell-title hover:underline block">{{ $product->name }}</a>
                                    <span class="adm-cell-sub">{{ $product->series }} &middot; {{ $product->images->count() }} Foto</span>
                                </div>
                            </div>
                        </td>

                        {{-- Kondisi & Garansi --}}
                        <td>
                            @if($product->condition === 'second')
                                <x-admin.status-badge tone="green" :label="'Second · ' . ($product->grade ?? 'Grade A+')" :dot="false" />
                            @else
                                <x-admin.status-badge tone="blue" label="BNIB Baru" :dot="false" />
                            @endif
                            <span class="adm-cell-sub block">{{ $warrantyTypes[$product->warranty_type] ?? $product->warranty_type }}</span>
                        </td>

                        {{-- Varian --}}
                        <td>
                            <div class="flex items-center gap-1.5 mb-1.5">
                                @forelse($colors as $clr)
                                    <span class="inline-block rounded-xs" style="width:.75rem;height:.75rem;background-color: {{ $clr->color_hex }};border:1px solid var(--adm-rule-border)" title="{{ $clr->color_name }}"></span>
                                @empty
                                    <span class="adm-cell-sub">Belum ada varian</span>
                                @endforelse
                                @if($variants->isNotEmpty())
                                    <span class="adm-cell-sub" style="margin:0 0 0 .25rem">({{ $variants->count() }} SKU)</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($storages as $cap)
                                    <span class="adm-badge adm-tone-gray" style="padding:.0625rem .375rem">{{ $cap }}</span>
                                @endforeach
                            </div>
                        </td>

                        {{-- HPP --}}
                        <td class="adm-num adm-muted">
                            @if($variants->isNotEmpty())
                                <span class="adm-mono text-[var(--adm-ink)]">Rp {{ number_format((float) $minCost, 0, ',', '.') }}</span>
                                @if($maxCost > $minCost)
                                    <span class="adm-cell-sub block">s/d Rp {{ number_format((float) $maxCost, 0, ',', '.') }}</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>

                        {{-- Harga Jual --}}
                        <td class="adm-num">
                            <span class="adm-cell-title">Rp {{ number_format((float) $minSell, 0, ',', '.') }}</span>
                            @if($maxSell > $minSell)
                                <span class="adm-cell-sub block">s/d Rp {{ number_format((float) $maxSell, 0, ',', '.') }}</span>
                            @endif
                        </td>

                        {{-- Total Stok --}}
                        <td style="text-align:center">
                            @if($totalStock <= 0)
                                <x-admin.status-badge tone="red" label="Habis (0)" :dot="false" />
                            @elseif($totalStock <= 5)
                                <x-admin.status-badge tone="amber" :label="$totalStock . ' Unit'" :dot="false" />
                            @else
                                <x-admin.status-badge tone="green" :label="$totalStock . ' Unit'" :dot="false" />
                            @endif
                        </td>

                        {{-- Status Aktif --}}
                        <td style="text-align:center">
                            @if($product->is_active)
                                <x-admin.status-badge tone="green" label="Aktif" />
                            @else
                                <x-admin.status-badge tone="gray" label="Nonaktif" />
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="adm-num">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.products.edit', $product) }}" class="adm-btn adm-btn-sm" title="Kelola Produk, Varian & Foto">
                                    <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                    <span>Kelola Varian &amp; Foto</span>
                                </a>

                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                      onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }} beserta seluruh variannya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="adm-icon-btn is-danger" title="Hapus Produk" aria-label="Hapus produk {{ $product->name }}">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-admin.empty-state icon="smartphone" title="Belum ada produk iPhone yang sesuai dengan filter."
                                text="Tambahkan produk baru atau sesuaikan kata kunci pencarian pada toolbar." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="adm-pagination">
            {{ $products->links() }}
        </div>
    @endif
</x-admin.card>
@endsection
