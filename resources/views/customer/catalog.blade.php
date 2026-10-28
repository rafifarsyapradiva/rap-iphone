@extends('layouts.customer')

@section('title', 'Katalog Lengkap iPhone — Rap-iPhone Authorized Style Partner')
@section('meta_description', 'Jelajahi seluruh varian iPhone Baru Resmi dan Second Like New Teruji. Filter seri iPhone 16, 15, 14, 13, garansi resmi iBox/Digimap, dan kapasitas.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- 1. Breadcrumbs & Editorial Header -->
    <div class="space-y-4">
        <nav class="flex items-center gap-2 text-xs text-[#6E6E73]">
            <a href="{{ url('/') }}" class="hover:text-[#1D1D1F] transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#86868B]"></i>
            <span class="text-[#1D1D1F] font-semibold">Katalog iPhone</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-5 border-b border-[#E5E5EA]">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-[#1D1D1F] tracking-tight">Beli iPhone</h1>
                <p class="text-xs sm:text-sm text-[#6E6E73] mt-1">
                    Tersedia <strong class="text-[#1D1D1F]">{{ $products->total() }}</strong> model resmi &amp; pre-owned terverifikasi. Gratis pengiriman berasuransi atau ambil di gerai.
                </p>
            </div>

            <!-- Dual-Condition Segmented Control -->
            <div class="inline-flex p-1 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] self-start md:self-auto">
                <a href="{{ request()->fullUrlWithQuery(['condition' => null, 'page' => null]) }}" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ !request('condition') ? 'bg-[#1D1D1F] text-white shadow-sm' : 'text-[#6E6E73] hover:text-[#1D1D1F]' }}">
                    Semua ({{ $products->total() }})
                </a>
                <a href="{{ request()->fullUrlWithQuery(['condition' => 'new', 'page' => null]) }}" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ request('condition') === 'new' ? 'bg-[#0066CC] text-white shadow-sm' : 'text-[#6E6E73] hover:text-[#0066CC]' }}">
                    Unit Baru Resmi
                </a>
                <a href="{{ request()->fullUrlWithQuery(['condition' => 'second', 'page' => null]) }}" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ request('condition') === 'second' ? 'bg-[#008040] text-white shadow-sm' : 'text-[#6E6E73] hover:text-[#008040]' }}">
                    Second Like New
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Layout 2 Columns: Filter Sidebar + Products Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- =========================================================================
             FILTER SIDEBAR (Clean Daylight Panel)
             ========================================================================= -->
        <aside class="lg:col-span-3">
            <form action="{{ url('/katalog') }}" method="GET" class="bg-[#F5F5F7] p-6 rounded-[20px] border border-[#E5E5EA] space-y-6">
                
                <div class="flex items-center justify-between pb-3 border-b border-[#D2D2D7]">
                    <span class="text-sm font-bold text-[#1D1D1F] flex items-center gap-2">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-[#0066CC]"></i>
                        <span>Filter Produk</span>
                    </span>
                    @if(request()->hasAny(['series', 'condition', 'warranty', 'min_price', 'max_price', 'q']))
                        <a href="{{ url('/katalog') }}" class="text-xs text-[#D70015] hover:underline font-semibold">Reset</a>
                    @endif
                </div>

                <!-- 1. Keyword Search -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#6E6E73] uppercase tracking-wider block">Pencarian Model</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik seri / warna..." class="glass-input !bg-white pl-9 pr-3 py-2 text-xs w-full">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-[#6E6E73] absolute left-3 top-2.5"></i>
                    </div>
                </div>

                <!-- 2. Kondisi Unit -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#6E6E73] uppercase tracking-wider block">Kondisi Unit</label>
                    <div class="space-y-1 text-xs">
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                            <input type="radio" name="condition" value="" {{ !request('condition') ? 'checked' : '' }} class="accent-[#0066CC]" onchange="this.form.submit()">
                            <span class="text-[#1D1D1F] font-medium">Semua Kondisi</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                            <input type="radio" name="condition" value="new" {{ request('condition') === 'new' ? 'checked' : '' }} class="accent-[#0066CC]" onchange="this.form.submit()">
                            <span class="text-[#0066CC] font-semibold">Baru (BNIB Segel Resmi)</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                            <input type="radio" name="condition" value="second" {{ request('condition') === 'second' ? 'checked' : '' }} class="accent-[#008040]" onchange="this.form.submit()">
                            <span class="text-[#008040] font-semibold">Second Like New (QC 32 Titik)</span>
                        </label>
                    </div>
                </div>

                <!-- 3. Seri iPhone -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#6E6E73] uppercase tracking-wider block">Seri iPhone</label>
                    <div class="space-y-1 text-xs">
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                            <input type="radio" name="series" value="" {{ !request('series') ? 'checked' : '' }} class="accent-[#0066CC]" onchange="this.form.submit()">
                            <span class="text-[#1D1D1F] font-medium">Semua Seri</span>
                        </label>
                        @foreach($availableSeries as $ser)
                            <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                                <input type="radio" name="series" value="{{ $ser }}" {{ request('series') === $ser ? 'checked' : '' }} class="accent-[#0066CC]" onchange="this.form.submit()">
                                <span class="text-[#1D1D1F] font-medium">{{ $ser }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Tipe Garansi -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#6E6E73] uppercase tracking-wider block">Tipe Garansi</label>
                    <div class="space-y-1 text-xs">
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                            <input type="radio" name="warranty" value="" {{ !request('warranty') ? 'checked' : '' }} class="accent-[#0066CC]" onchange="this.form.submit()">
                            <span class="text-[#1D1D1F] font-medium">Semua Garansi</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                            <input type="radio" name="warranty" value="resmi_ibox" {{ request('warranty') === 'resmi_ibox' ? 'checked' : '' }} class="accent-[#0066CC]" onchange="this.form.submit()">
                            <span class="text-[#1D1D1F] font-medium">Resmi iBox (TAM)</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                            <input type="radio" name="warranty" value="resmi_digimap" {{ request('warranty') === 'resmi_digimap' ? 'checked' : '' }} class="accent-[#0066CC]" onchange="this.form.submit()">
                            <span class="text-[#1D1D1F] font-medium">Resmi Digimap</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors">
                            <input type="radio" name="warranty" value="toko" {{ request('warranty') === 'toko' ? 'checked' : '' }} class="accent-[#0066CC]" onchange="this.form.submit()">
                            <span class="text-[#1D1D1F] font-medium">Garansi Toko Resmi</span>
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
            <div class="bg-[#F5F5F7] px-5 py-3.5 rounded-2xl border border-[#E5E5EA] flex flex-wrap items-center justify-between gap-4">
                <span class="text-xs text-[#6E6E73]">
                    Menampilkan halaman <strong class="text-[#1D1D1F]">{{ $products->currentPage() }}</strong> dari <strong class="text-[#1D1D1F]">{{ $products->lastPage() }}</strong>
                </span>

                <form action="{{ url('/katalog') }}" method="GET" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <label for="sort-select" class="text-xs font-medium text-[#6E6E73]">Urutkan:</label>
                    <select id="sort-select" name="sort" class="glass-input !bg-white text-xs !py-1.5 !px-3 !w-auto cursor-pointer font-medium" onchange="this.form.submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Produk Terbaru</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                    </select>
                </form>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full py-16 text-center bg-[#F5F5F7] rounded-[24px] border border-[#E5E5EA] p-8 space-y-4">
                        <div class="w-14 h-14 rounded-full bg-white border border-[#D2D2D7] flex items-center justify-center mx-auto text-[#6E6E73]">
                            <i data-lucide="search-x" class="w-7 h-7"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-lg font-bold text-[#1D1D1F]">Tidak Ada iPhone yang Sesuai</h3>
                            <p class="text-xs text-[#6E6E73] max-w-md mx-auto">
                                Kami tidak menemukan unit yang cocok dengan kriteria filter Anda. Silakan reset filter atau gunakan kata kunci lain.
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
