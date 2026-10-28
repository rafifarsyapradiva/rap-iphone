<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductManagementController extends Controller
{
    /**
     * Preset warna resmi Apple sesuai spesifikasi Design System.
     *
     * @var array<int, array{name: string, hex: string}>
     */
    public const APPLE_COLORS = [
        ['name' => 'Natural Titanium', 'hex' => '#9A958E'],
        ['name' => 'Desert Titanium', 'hex' => '#C5B49F'],
        ['name' => 'White Titanium', 'hex' => '#F2F1EC'],
        ['name' => 'Black Titanium', 'hex' => '#2D2F33'],
        ['name' => 'Ultramarine', 'hex' => '#496495'],
        ['name' => 'Teal', 'hex' => '#799896'],
        ['name' => 'Pink', 'hex' => '#E8C4C6'],
        ['name' => 'Midnight', 'hex' => '#2D2F33'],
        ['name' => 'Starlight', 'hex' => '#F2F1EC'],
        ['name' => 'Blue Titanium', 'hex' => '#3B4454'],
    ];

    /**
     * Pilihan kapasitas penyimpanan standar iPhone.
     *
     * @var array<int, string>
     */
    public const STORAGE_OPTIONS = ['128GB', '256GB', '512GB', '1TB'];

    /**
     * Pilihan tipe garansi resmi & toko.
     *
     * @var array<string, string>
     */
    public const WARRANTY_TYPES = [
        'resmi_ibox' => 'Garansi Resmi Apple Indonesia (iBox / TAM)',
        'resmi_digimap' => 'Garansi Resmi Digimap',
        'inter_all_operator' => 'Inter All Operator (Bea Cukai / Kemenperin)',
        'toko' => 'Garansi Toko Rap-iPhone (30 Hari)',
    ];

    /**
     * Pilihan grade fisik untuk unit second.
     *
     * @var array<int, string>
     */
    public const GRADES = ['Grade A+', 'Grade A', 'Grade B'];

    /**
     * Tampilkan daftar inventaris produk beserta metrik stok dan filter pencarian.
     */
    public function index(Request $request): View
    {
        $categoryFilter = $request->query('category_id');
        $conditionFilter = (string) $request->query('condition', 'all');
        $warrantyFilter = (string) $request->query('warranty_type', 'all');
        $statusFilter = (string) $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));

        $query = Product::with(['category', 'variants', 'images', 'primaryImage'])->latest();

        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }

        if (in_array($conditionFilter, ['new', 'second'], true)) {
            $query->where('condition', $conditionFilter);
        } else {
            $conditionFilter = 'all';
        }

        if (array_key_exists($warrantyFilter, self::WARRANTY_TYPES)) {
            $query->where('warranty_type', $warrantyFilter);
        } else {
            $warrantyFilter = 'all';
        }

        if ($statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', false);
        } else {
            $statusFilter = 'all';
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('series', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('variants', function ($vq) use ($search) {
                        $vq->where('sku', 'like', "%{$search}%")
                            ->orWhere('imei', 'like', "%{$search}%")
                            ->orWhere('color_name', 'like', "%{$search}%");
                    });
            });
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $metrics = [
            'total_products' => Product::count(),
            'total_variants' => ProductVariant::count(),
            'total_stock' => (int) ProductVariant::sum('stock'),
            'new_products' => Product::where('condition', 'new')->count(),
            'second_products' => Product::where('condition', 'second')->count(),
            'low_stock_variants' => ProductVariant::where('stock', '<=', 3)->count(),
        ];

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'metrics' => $metrics,
            'categoryFilter' => $categoryFilter,
            'conditionFilter' => $conditionFilter,
            'warrantyFilter' => $warrantyFilter,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'warrantyTypes' => self::WARRANTY_TYPES,
        ]);
    }

    /**
     * Tampilkan form tambah produk induk baru beserta varian awal & foto multi-angle.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', [
            'categories' => $categories,
            'appleColors' => self::APPLE_COLORS,
            'storageOptions' => self::STORAGE_OPTIONS,
            'warrantyTypes' => self::WARRANTY_TYPES,
            'grades' => self::GRADES,
        ]);
    }

    /**
     * Simpan produk induk baru (beserta varian awal dan foto multi-angle bila disertakan).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'series' => ['required', 'string', 'max:50'],
            'condition' => ['required', Rule::in(['new', 'second'])],
            'grade' => ['nullable', 'string', 'max:20'],
            'warranty_type' => ['required', Rule::in(array_keys(self::WARRANTY_TYPES))],
            'base_price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],

            // Varian awal opsional saat create
            'variants' => ['nullable', 'array'],
            'variants.*.color_name' => ['required_with:variants', 'string', 'max:50'],
            'variants.*.color_hex' => ['required_with:variants', 'string', 'max:10'],
            'variants.*.storage_capacity' => ['required_with:variants', 'string', 'max:20'],
            'variants.*.cost_price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.selling_price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:100', 'distinct'],
            'variants.*.battery_health' => ['nullable', 'integer', 'min:1', 'max:100'],
            'variants.*.imei' => ['nullable', 'string', 'max:30'],

            // Foto multi-angle awal opsional
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $slug = $this->generateUniqueSlug($validated['slug'] ?? $validated['name']);

        $product = DB::transaction(function () use ($validated, $request, $slug) {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'series' => $validated['series'],
                'condition' => $validated['condition'],
                'grade' => $validated['condition'] === 'second' ? ($validated['grade'] ?? 'Grade A+') : null,
                'warranty_type' => $validated['warranty_type'],
                'base_price' => $validated['base_price'],
                'description' => $validated['description'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            if (!empty($validated['variants']) && is_array($validated['variants'])) {
                foreach ($validated['variants'] as $varData) {
                    if (empty($varData['color_name']) || empty($varData['storage_capacity'])) {
                        continue;
                    }

                    $sku = !empty($varData['sku'])
                        ? strtoupper(trim($varData['sku']))
                        : $this->generateUniqueSku($product->name, $varData['color_name'], $varData['storage_capacity'], $product->condition);

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'color_name' => $varData['color_name'],
                        'color_hex' => $varData['color_hex'] ?? '#9A958E',
                        'storage_capacity' => $varData['storage_capacity'],
                        'cost_price' => $varData['cost_price'],
                        'selling_price' => $varData['selling_price'],
                        'stock' => (int) $varData['stock'],
                        'sku' => $sku,
                        'battery_health' => !empty($varData['battery_health']) ? (int) $varData['battery_health'] : null,
                        'imei' => !empty($varData['imei']) ? trim($varData['imei']) : null,
                    ]);
                }

                $this->syncBasePrice($product);
            }

            if ($request->hasFile('images')) {
                $this->saveUploadedImages($product, $request->file('images'), null, true);
            }

            return $product;
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', "Produk \"{$product->name}\" berhasil ditambahkan. Anda dapat mengelola varian dan foto multi-angle di bawah ini.");
    }

    /**
     * Tampilkan halaman kelola produk induk, varian stok/HPP/IMEI, dan foto multi-angle.
     */
    public function edit(Product $product): View
    {
        $product->load([
            'category',
            'variants' => fn ($q) => $q->orderBy('storage_capacity')->orderBy('color_name'),
            'images' => fn ($q) => $q->with('variant')->orderByDesc('is_primary')->orderBy('order_index'),
        ]);

        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'appleColors' => self::APPLE_COLORS,
            'storageOptions' => self::STORAGE_OPTIONS,
            'warrantyTypes' => self::WARRANTY_TYPES,
            'grades' => self::GRADES,
        ]);
    }

    /**
     * Perbarui data produk induk.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
            'series' => ['required', 'string', 'max:50'],
            'condition' => ['required', Rule::in(['new', 'second'])],
            'grade' => ['nullable', 'string', 'max:20'],
            'warranty_type' => ['required', Rule::in(array_keys(self::WARRANTY_TYPES))],
            'base_price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : $this->generateUniqueSlug($validated['name'], $product->id);

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'series' => $validated['series'],
            'condition' => $validated['condition'],
            'grade' => $validated['condition'] === 'second' ? ($validated['grade'] ?? 'Grade A+') : null,
            'warranty_type' => $validated['warranty_type'],
            'base_price' => $validated['base_price'],
            'description' => $validated['description'],
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', "Informasi produk \"{$product->name}\" berhasil diperbarui.");
    }

    /**
     * Hapus produk beserta seluruh varian dan file foto terkait.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $productName = $product->name;

        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                $this->deletePhysicalImageIfUploaded($image->image_path);
            }
            $product->delete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Produk \"{$productName}\" beserta seluruh varian berhasil dihapus dari inventaris.");
    }

    /**
     * Tambah varian baru (Warna, Kapasitas, Stok, HPP, Harga Jual, BH, IMEI) pada produk.
     */
    public function storeVariant(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'color_name' => ['required', 'string', 'max:50'],
            'color_hex' => ['required', 'string', 'max:10', 'regex:/^#[0-9A-Fa-f]{3,8}$/'],
            'storage_capacity' => ['required', 'string', 'max:20'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'battery_health' => ['nullable', 'integer', 'min:1', 'max:100'],
            'imei' => ['nullable', 'string', 'max:30'],
        ]);

        $sku = !empty($validated['sku'])
            ? strtoupper(trim($validated['sku']))
            : $this->generateUniqueSku($product->name, $validated['color_name'], $validated['storage_capacity'], $product->condition);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'color_name' => $validated['color_name'],
            'color_hex' => $validated['color_hex'],
            'storage_capacity' => $validated['storage_capacity'],
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
            'stock' => (int) $validated['stock'],
            'sku' => $sku,
            'battery_health' => $validated['battery_health'] ?? null,
            'imei' => !empty($validated['imei']) ? trim($validated['imei']) : null,
        ]);

        $this->syncBasePrice($product);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', "Varian {$variant->color_name} ({$variant->storage_capacity}) [SKU: {$variant->sku}] berhasil ditambahkan.");
    }

    /**
     * Perbarui data varian (Warna, Kapasitas, Stok, HPP, Harga Jual, BH, IMEI).
     */
    public function updateVariant(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $validated = $request->validate([
            'color_name' => ['required', 'string', 'max:50'],
            'color_hex' => ['required', 'string', 'max:10', 'regex:/^#[0-9A-Fa-f]{3,8}$/'],
            'storage_capacity' => ['required', 'string', 'max:20'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('product_variants', 'sku')->ignore($variant->id)],
            'battery_health' => ['nullable', 'integer', 'min:1', 'max:100'],
            'imei' => ['nullable', 'string', 'max:30'],
        ]);

        $variant->update([
            'color_name' => $validated['color_name'],
            'color_hex' => $validated['color_hex'],
            'storage_capacity' => $validated['storage_capacity'],
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
            'stock' => (int) $validated['stock'],
            'sku' => strtoupper(trim($validated['sku'])),
            'battery_health' => $validated['battery_health'] ?? null,
            'imei' => !empty($validated['imei']) ? trim($validated['imei']) : null,
        ]);

        $this->syncBasePrice($product);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', "Varian {$variant->color_name} ({$variant->storage_capacity}) berhasil diperbarui.");
    }

    /**
     * Hapus varian dari produk.
     */
    public function destroyVariant(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $label = "{$variant->color_name} ({$variant->storage_capacity})";
        $variant->delete();

        $this->syncBasePrice($product);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', "Varian {$label} berhasil dihapus.");
    }

    /**
     * Unggah dan simpan foto produk multi-angle (mendukung multi-upload).
     */
    public function storeImages(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:8'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'variant_id' => [
                'nullable',
                Rule::exists('product_variants', 'id')->where('product_id', $product->id),
            ],
            'is_primary' => ['nullable', 'boolean'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $files = $request->file('images', []);
        $variantId = !empty($validated['variant_id']) ? (int) $validated['variant_id'] : null;
        $makeFirstPrimary = $request->boolean('is_primary', false);
        $startingOrder = isset($validated['order_index'])
            ? (int) $validated['order_index']
            : ((int) $product->images()->max('order_index') + 1);

        $this->saveUploadedImages($product, $files, $variantId, $makeFirstPrimary, $startingOrder);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', count($files) . ' foto produk multi-angle berhasil diunggah.');
    }

    /**
     * Jadikan salah satu foto sebagai gambar utama (primary) produk.
     */
    public function setPrimaryImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        DB::transaction(function () use ($product, $image) {
            $product->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Foto utama produk berhasil diperbarui.');
    }

    /**
     * Perbarui urutan tampilan (order_index) dan asosiasi varian pada foto multi-angle.
     */
    public function updateImageOrder(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $validated = $request->validate([
            'order_index' => ['required', 'integer', 'min:0', 'max:999'],
            'variant_id' => [
                'nullable',
                Rule::exists('product_variants', 'id')->where('product_id', $product->id),
            ],
        ]);

        $image->update([
            'order_index' => (int) $validated['order_index'],
            'variant_id' => !empty($validated['variant_id']) ? (int) $validated['variant_id'] : null,
        ]);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Urutan & asosiasi foto multi-angle berhasil diperbarui.');
    }

    /**
     * Hapus foto produk multi-angle.
     */
    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $wasPrimary = $image->is_primary;
        $this->deletePhysicalImageIfUploaded($image->image_path);
        $image->delete();

        if ($wasPrimary) {
            $nextImage = $product->images()->orderBy('order_index')->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Foto produk berhasil dihapus.');
    }

    /**
     * Sinkronkan base_price produk induk dengan harga jual varian terendah.
     */
    protected function syncBasePrice(Product $product): void
    {
        $minSellingPrice = $product->variants()->min('selling_price');

        if (!is_null($minSellingPrice) && (float) $minSellingPrice > 0) {
            $product->update(['base_price' => $minSellingPrice]);
        }
    }

    /**
     * Buat slug unik untuk produk.
     */
    protected function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($source) ?: 'iphone-' . strtolower(Str::random(5));
        $slug = $baseSlug;
        $counter = 1;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $counter++;
            $slug = "{$baseSlug}-{$counter}";
        }

        return $slug;
    }

    /**
     * Buat SKU unik otomatis untuk varian produk.
     */
    protected function generateUniqueSku(string $productName, string $colorName, string $storageCapacity, string $condition = 'new'): string
    {
        $prefix = $condition === 'second' ? 'SEC-' : '';
        $prodCode = strtoupper(Str::slug($productName));
        $prodCode = preg_replace('/IPHONE-?/', 'IP', $prodCode) ?: 'IP';
        $colorCode = strtoupper(substr(Str::slug($colorName, ''), 0, 4)) ?: 'CLR';
        $storageCode = strtoupper(preg_replace('/\s+/', '', $storageCapacity));

        $baseSku = "{$prefix}{$prodCode}-{$colorCode}-{$storageCode}";
        $sku = $baseSku;
        $counter = 1;

        while (ProductVariant::where('sku', $sku)->exists()) {
            $counter++;
            $sku = "{$baseSku}-{$counter}";
        }

        return $sku;
    }

    /**
     * Simpan file foto ke public/images/products/uploads agar langsung kompatibel dengan asset() & public_path().
     *
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     */
    protected function saveUploadedImages(
        Product $product,
        array $files,
        ?int $variantId = null,
        bool $makeFirstPrimary = false,
        int $startingOrder = 1
    ): void {
        $uploadDir = public_path('images/products/uploads');

        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $hasExistingPrimary = $product->images()->where('is_primary', true)->exists();
        if ($makeFirstPrimary && $hasExistingPrimary) {
            $product->images()->update(['is_primary' => false]);
            $hasExistingPrimary = false;
        }

        foreach (array_values($files) as $idx => $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
            $filename = $product->slug . '-' . now()->format('YmdHis') . '-' . ($idx + 1) . '-' . strtolower(Str::random(4)) . '.' . $extension;
            $file->move($uploadDir, $filename);

            $isPrimary = ($idx === 0) && ($makeFirstPrimary || !$hasExistingPrimary);
            if ($isPrimary) {
                $hasExistingPrimary = true;
            }

            ProductImage::create([
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'image_path' => 'images/products/uploads/' . $filename,
                'is_primary' => $isPrimary,
                'order_index' => $startingOrder + $idx,
            ]);
        }
    }

    /**
     * Hapus file fisik jika berada di dalam folder uploads produk.
     */
    protected function deletePhysicalImageIfUploaded(?string $imagePath): void
    {
        if (!$imagePath || !str_starts_with($imagePath, 'images/products/uploads/')) {
            return;
        }

        $fullPath = public_path($imagePath);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}

