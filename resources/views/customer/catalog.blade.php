@extends('layouts.customer')

@section('title', 'Katalog Lengkap iPhone — Rap-iPhone Authorized Style Partner')
@section('meta_description', 'Jelajahi seluruh varian iPhone Baru Resmi dan Second Like New Teruji. Filter seri iPhone 16, 15, 14, 13, garansi resmi iBox/Digimap, dan kapasitas.')

@section('content')
<div class="cust-page py-6 space-y-8 pb-20">

    <!-- 1. Breadcrumbs & Header -->
    <div class="space-y-3">
        <nav class="flex items-center gap-2 text-xs text-[var(--cust-ink-muted)]">
            <a href="{{ url('/') }}" class="hover:text-[var(--cust-ink)] transition-colors">Beranda</a>
            <span class="text-[var(--cust-ink-faint)]">/</span>
            <span class="text-[var(--cust-ink)] font-medium">Katalog iPhone</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-4 border-b border-[var(--cust-rule-border)]">
            <div>
                <h1 class="font-serif text-3xl sm:text-4xl font-light text-[var(--cust-ink)] tracking-tight">
                    Katalog Lengkap iPhone
                </h1>
                <p class="text-xs sm:text-sm text-[var(--cust-ink-muted)] mt-1">
                    Tersedia <strong class="text-[var(--cust-ink)] cust-tabular">{{ $products->total() }}</strong> model resmi dan pre-owned terverifikasi.
                </p>
            </div>

            <!-- Dual-Condition Segmented Control -->
            <div class="inline-flex p-1 rounded border border-[var(--cust-rule-border)] bg-[var(--cust-paper-subtle)] self-start md:self-auto gap-1">
                <a href="{{ request()->fullUrlWithQuery(['condition' => null, 'page' => null]) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors {{ !request('condition') ? 'bg-[var(--cust-ink)] text-white shadow-sm' : 'text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]' }}">
                    Semua ({{ $products->total() }})
                </a>
                <a href="{{ request()->fullUrlWithQuery(['condition' => 'new', 'page' => null]) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors {{ request('condition') === 'new' ? 'bg-[var(--cust-tone-official-bg)] text-[var(--cust-tone-official-ink)]' : 'text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]' }}">
                    Unit Baru Resmi
                </a>
                <a href="{{ request()->fullUrlWithQuery(['condition' => 'second', 'page' => null]) }}" class="px-3 py-1 rounded text-xs font-medium transition-colors {{ request('condition') === 'second' ? 'bg-[var(--cust-tone-healthy-bg)] text-[var(--cust-tone-healthy-ink)]' : 'text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]' }}">
                    Second Like New
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Two Columns Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left: Filter Panel -->
        <aside class="lg:col-span-3">
            <form action="{{ url('/katalog') }}" method="GET" class="cust-card p-5 space-y-5">
                
                <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-rule-border)]">
                    <span class="text-xs font-semibold text-[var(--cust-ink)] flex items-center gap-1.5">
                        <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
                        <span>Filter Produk</span>
                    </span>
                    @if(request()->hasAny(['series', 'condition', 'warranty', 'min_price', 'max_price', 'q']))
                        <a href="{{ url('/katalog') }}" class="text-xs text-[var(--cust-tone-alert-ink)] hover:underline font-medium">Reset</a>
                    @endif
                </div>

                <!-- 1. Search Query -->
                <div class="space-y-1.5">
                    <label class="cust-label">Pencarian</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Seri, warna titanium..." class="cust-input pl-8 text-xs">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-[var(--cust-ink-faint)] absolute left-2.5 top-2.5"></i>
                    </div>
                </div>

                <!-- 2. Condition Filter -->
                <div class="space-y-1.5">
                    <label class="cust-label">Kondisi Unit</label>
                    <div class="space-y-1 text-xs">
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                            <input type="radio" name="condition" value="" {{ !request('condition') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="text-[var(--cust-ink)]">Semua Kondisi</span>
                        </label>
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                            <input type="radio" name="condition" value="new" {{ request('condition') === 'new' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="text-[var(--cust-tone-official-ink)] font-medium">Baru (Segel Resmi)</span>
                        </label>
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                            <input type="radio" name="condition" value="second" {{ request('condition') === 'second' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="text-[var(--cust-tone-healthy-ink)] font-medium">Second (QC 32 Titik)</span>
                        </label>
                    </div>
                </div>

                <!-- 3. Series Filter -->
                <div class="space-y-1.5">
                    <label class="cust-label">Seri iPhone</label>
                    <div class="space-y-1 text-xs">
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                            <input type="radio" name="series" value="" {{ !request('series') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="text-[var(--cust-ink)]">Semua Seri</span>
                        </label>
                        @foreach($availableSeries as $ser)
                            <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                                <input type="radio" name="series" value="{{ $ser }}" {{ request('series') === $ser ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-[var(--cust-ink)]">{{ $ser }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Warranty Filter -->
                <div class="space-y-1.5">
                    <label class="cust-label">Tipe Garansi</label>
                    <div class="space-y-1 text-xs">
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                            <input type="radio" name="warranty" value="" {{ !request('warranty') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="text-[var(--cust-ink)]">Semua Tipe Garansi</span>
                        </label>
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                            <input type="radio" name="warranty" value="resmi_ibox" {{ request('warranty') === 'resmi_ibox' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="text-[var(--cust-ink)]">Resmi iBox (TAM)</span>
                        </label>
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                            <input type="radio" name="warranty" value="resmi_digimap" {{ request('warranty') === 'resmi_digimap' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="text-[var(--cust-ink)]">Resmi Digimap</span>
                        </label>
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-[var(--cust-paper-subtle)] cursor-pointer">
                            <input type="radio" name="warranty" value="toko" {{ request('warranty') === 'toko' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="text-[var(--cust-ink)]">Garansi Toko Resmi</span>
                        </label>
                    </div>
                </div>

                <x-customer.button type="submit" variant="primary" class="w-full text-xs">
                    Terapkan Filter
                </x-customer.button>
            </form>
        </aside>

        <!-- Right: Products Grid & Toolbar -->
        <main class="lg:col-span-9 space-y-6">
            
            <!-- Toolbar -->
            <div class="cust-card p-3.5 px-4 flex flex-wrap items-center justify-between gap-3">
                <span class="text-xs text-[var(--cust-ink-muted)]">
                    Halaman <strong class="text-[var(--cust-ink)] cust-tabular">{{ $products->currentPage() }}</strong> dari <strong class="text-[var(--cust-ink)] cust-tabular">{{ $products->lastPage() }}</strong>
                </span>

                <form action="{{ url('/katalog') }}" method="GET" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <label for="sort-select" class="text-xs text-[var(--cust-ink-muted)]">Urutkan:</label>
                    <select id="sort-select" name="sort" class="cust-select text-xs py-1 px-2.5 w-auto cursor-pointer" onchange="this.form.submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Produk Terbaru</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                    </select>
                </form>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($products as $product)
                    <x-customer.product-card :product="$product" />
                @empty
                    <div class="col-span-full py-16 text-center cust-card p-8 space-y-3">
                        <div class="w-12 h-12 rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] flex items-center justify-center mx-auto text-[var(--cust-ink-muted)]">
                            <i data-lucide="search-x" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-base font-semibold text-[var(--cust-ink)]">Tidak Ada iPhone yang Sesuai</h3>
                        <p class="text-xs text-[var(--cust-ink-muted)] max-w-sm mx-auto">
                            Tidak ditemukan unit yang cocok dengan kriteria filter Anda. Silakan reset filter atau coba kata kunci lain.
                        </p>
                        <x-customer.button :href="url('/katalog')" variant="secondary" size="sm" class="inline-flex mt-2">
                            Reset Semua Filter
                        </x-customer.button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="pt-4">
                    {{ $products->links() }}
                </div>
            @endif

        </main>

    </div>

</div>
@endsection
