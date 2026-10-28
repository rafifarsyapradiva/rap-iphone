@extends('layouts.customer')

@section('title', 'Katalog Lengkap iPhone — Rap-iPhone Authorized Style Store')
@section('meta_description', 'Jelajahi seluruh varian iPhone Baru Resmi dan Second Like New Teruji. Filter seri iPhone 16, 15, 14, 13, garansi resmi iBox/Digimap, dan kapasitas.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Breadcrumbs & Page Header -->
    <div class="space-y-2">
        <nav class="flex items-center gap-2 text-xs text-[#8E8E93]">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-white font-medium">Katalog iPhone</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Katalog Lengkap iPhone</h1>
                <p class="text-xs text-[#8E8E93] mt-1">Daftar unit resmi dan pre-owned terverifikasi siap kirim</p>
            </div>
            
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-[#E5E5EA]">
                Ditemukan <strong class="text-[#5AC8FA]">{{ $products->total() }}</strong> Unit
            </span>
        </div>
    </div>

    <!-- Layout 2 Columns: Filter Sidebar + Products Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- =========================================================================
             FILTER SIDEBAR (Desktop)
             ========================================================================= -->
        <aside class="lg:col-span-3">
            <form action="{{ url('/katalog') }}" method="GET" class="glass-card p-6 rounded-3xl border border-white/10 space-y-6">
                
                <div class="flex items-center justify-between pb-3 border-b border-white/10">
                    <span class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-[#0071E3]"></i>
                        Filter Produk
                    </span>
                    @if(request()->hasAny(['series', 'condition', 'warranty', 'min_price', 'max_price', 'q']))
                        <a href="{{ url('/katalog') }}" class="text-[11px] text-[#FF453A] hover:underline font-medium">Reset</a>
                    @endif
                </div>

                <!-- 1. Keyword Search -->
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider block">Pencarian</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari model..." class="glass-input pl-9 pr-3 py-2 text-xs w-full">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-[#8E8E93] absolute left-3 top-3"></i>
                    </div>
                </div>

                <!-- 2. Kondisi Unit -->
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider block">Kondisi Unit</label>
                    <div class="space-y-1.5 text-xs">
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                            <input type="radio" name="condition" value="" {{ !request('condition') ? 'checked' : '' }} class="accent-[#0071E3]" onchange="this.form.submit()">
                            <span class="text-[#E5E5EA]">Semua Kondisi</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                            <input type="radio" name="condition" value="new" {{ request('condition') === 'new' ? 'checked' : '' }} class="accent-[#0071E3]" onchange="this.form.submit()">
                            <span class="text-[#5AC8FA] font-medium">Baru (BNIB Segel)</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                            <input type="radio" name="condition" value="second" {{ request('condition') === 'second' ? 'checked' : '' }} class="accent-[#34C759]" onchange="this.form.submit()">
                            <span class="text-[#34C759] font-medium">Second Like New</span>
                        </label>
                    </div>
                </div>

                <!-- 3. Seri iPhone -->
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider block">Seri iPhone</label>
                    <div class="space-y-1 text-xs">
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                            <input type="radio" name="series" value="" {{ !request('series') ? 'checked' : '' }} class="accent-[#0071E3]" onchange="this.form.submit()">
                            <span class="text-[#E5E5EA]">Semua Seri</span>
                        </label>
                        @foreach($availableSeries as $ser)
                            <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                                <input type="radio" name="series" value="{{ $ser }}" {{ request('series') === $ser ? 'checked' : '' }} class="accent-[#0071E3]" onchange="this.form.submit()">
                                <span class="text-[#E5E5EA]">{{ $ser }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Tipe Garansi -->
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider block">Tipe Garansi</label>
                    <div class="space-y-1 text-xs">
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                            <input type="radio" name="warranty" value="" {{ !request('warranty') ? 'checked' : '' }} class="accent-[#0071E3]" onchange="this.form.submit()">
                            <span class="text-[#E5E5EA]">Semua Garansi</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                            <input type="radio" name="warranty" value="resmi_ibox" {{ request('warranty') === 'resmi_ibox' ? 'checked' : '' }} class="accent-[#0071E3]" onchange="this.form.submit()">
                            <span class="text-[#E5E5EA]">Resmi iBox (TAM)</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                            <input type="radio" name="warranty" value="resmi_digimap" {{ request('warranty') === 'resmi_digimap' ? 'checked' : '' }} class="accent-[#0071E3]" onchange="this.form.submit()">
                            <span class="text-[#E5E5EA]">Resmi Digimap</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                            <input type="radio" name="warranty" value="toko" {{ request('warranty') === 'toko' ? 'checked' : '' }} class="accent-[#0071E3]" onchange="this.form.submit()">
                            <span class="text-[#E5E5EA]">Garansi Toko Resmi</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm w-full">
                    Terapkan Filter
                </button>
            </form>
        </aside>

        <!-- =========================================================================
             PRODUCTS DISPLAY GRID
             ========================================================================= -->
        <main class="lg:col-span-9 space-y-6">
            
            <!-- Sorting Controls Bar -->
            <div class="glass-card px-5 py-3 rounded-2xl border border-white/10 flex flex-wrap items-center justify-between gap-4">
                <span class="text-xs text-[#8E8E93]">
                    Menampilkan halaman <strong class="text-white">{{ $products->currentPage() }}</strong> dari {{ $products->lastPage() }}
                </span>

                <form action="{{ url('/katalog') }}" method="GET" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <label for="sort-select" class="text-xs text-[#8E8E93]">Urutkan:</label>
                    <select id="sort-select" name="sort" class="glass-input text-xs !py-1 !px-2.5 !w-auto cursor-pointer" onchange="this.form.submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }} class="bg-[#161B22] text-white">Produk Terbaru</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }} class="bg-[#161B22] text-white">Harga Terendah</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }} class="bg-[#161B22] text-white">Harga Tertinggi</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }} class="bg-[#161B22] text-white">Nama (A - Z)</option>
                    </select>
                </form>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full py-20 text-center glass-card rounded-3xl border border-white/10 p-8 space-y-4">
                        <div class="w-16 h-16 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mx-auto text-[#8E8E93]">
                            <i data-lucide="search-x" class="w-8 h-8"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-lg font-bold text-white">Tidak Ada iPhone yang Sesuai</h3>
                            <p class="text-xs text-[#8E8E93] max-w-md mx-auto">
                                Kami tidak menemukan unit yang cocok dengan kriteria filter Anda. Cobalah untuk mereset filter atau cari kata kunci lain.
                            </p>
                        </div>
                        <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-secondary glass-btn-sm inline-flex">
                            Reset Semua Filter
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if($products->hasPages())
                <div class="pt-6">
                    {{ $products->links() }}
                </div>
            @endif

        </main>
    </div>

</div>
@endsection

