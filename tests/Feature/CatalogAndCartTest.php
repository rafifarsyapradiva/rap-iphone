<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CatalogAndCartTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test 1: Home page renders hero showcase, series, and both unit sections.
     */
    public function test_home_page_renders_hero_and_segmented_products(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Inovasi Terdepan');
        $response->assertSee('Garansi Terpercaya');
        $response->assertSee('Apple Flagship Showcase 2026');
        $response->assertSee('Beli iPhone Sekarang');
        $response->assertSee('Tukar Tambah 15 Menit');
        $response->assertSee('iPhone Baru Garansi Resmi');
        $response->assertSee('iPhone Second Like New Teruji');
    }

    /**
     * Test 2: Catalog page renders with filter controls.
     */
    public function test_catalog_page_renders_with_filters(): void
    {
        $response = $this->get('/katalog');

        $response->assertStatus(200);
        $response->assertSee('Katalog Lengkap iPhone');
        $response->assertSee('Filter Produk');
        $response->assertSee('Kondisi Unit');
        $response->assertSee('Seri iPhone');
        $response->assertSee('Tipe Garansi');
    }

    /**
     * Test 3: Catalog filters correctly by condition and series.
     */
    public function test_catalog_filters_by_condition_and_series(): void
    {
        // Filter by Second
        $responseSecond = $this->get('/katalog?condition=second');
        $responseSecond->assertStatus(200);
        $responseSecond->assertSee('Second Like New');

        // Filter by Series
        $responseSeries = $this->get('/katalog?series=iPhone+16+Series');
        $responseSeries->assertStatus(200);
        $responseSeries->assertSee('iPhone 16');
    }

    /**
     * Test 4: Product detail page (PDP) renders with colors, storages, and specs.
     */
    public function test_product_detail_page_loads_with_variants_and_actions(): void
    {
        $product = Product::where('slug', 'iphone-16-pro-max')->first();
        $this->assertNotNull($product, 'iPhone 16 Pro Max must exist in database.');

        $response = $this->get("/katalog/{$product->slug}");

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Desert Titanium');
        $response->assertSee('Kapasitas Penyimpanan');
        $response->assertSee('Tambah ke Keranjang');
        $response->assertSee('Beli Sekarang');
        $response->assertSee('Spesifikasi Teknis & Rincian', false);
        $response->assertSee('Super Retina XDR');
    }

    /**
     * Test 5: Pre-owned (second) PDP displays Battery Health and transparency widgets.
     */
    public function test_second_product_detail_page_displays_battery_health(): void
    {
        $product = Product::where('condition', 'second')->first();
        $this->assertNotNull($product, 'Pre-owned iPhone must exist in database.');

        $response = $this->get("/katalog/{$product->slug}");

        $response->assertStatus(200);
        $response->assertSee('Transparansi Unit Second Like New');
        $response->assertSee('Battery Health (BH)');
        $response->assertSee('Status IMEI & Jaringan', false);
        $response->assertSee('All Operator Permanen');
    }

    /**
     * Test 6: Empty cart displays friendly empty state.
     */
    public function test_cart_page_empty_state(): void
    {
        $response = $this->withSession(['cart' => []])->get('/cart');

        $response->assertStatus(200);
        $response->assertSee('Keranjang Belanja Anda Masih Kosong');
        $response->assertSee('Jelajahi Katalog iPhone');
    }

    /**
     * Test 7: Adding an item to cart via JSON stores variant in session.
     */
    public function test_add_to_cart_via_json_stores_in_session(): void
    {
        $variant = ProductVariant::where('stock', '>', 5)->first();
        $this->assertNotNull($variant);

        $response = $this->postJson('/cart/add', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 2,
        ]);

        $cart = session('cart');
        $this->assertIsArray($cart);
        $this->assertArrayHasKey($variant->id, $cart);
        $this->assertEquals(2, $cart[$variant->id]['quantity']);
    }

    /**
     * Test 8: Adding quantity exceeding available stock returns 422 error.
     */
    public function test_add_to_cart_rejects_exceeding_stock(): void
    {
        $variant = ProductVariant::first();
        $this->assertNotNull($variant);

        $response = $this->postJson('/cart/add', [
            'product_variant_id' => $variant->id,
            'quantity' => $variant->stock + 50,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test 9: Buy Now shortcut adds variant and redirects to cart.
     */
    public function test_buy_now_adds_item_and_redirects(): void
    {
        $variant = ProductVariant::where('stock', '>', 2)->first();
        $this->assertNotNull($variant);

        $response = $this->post('/cart/buy-now', [
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect('/cart');
        $cart = session('cart');
        $this->assertArrayHasKey($variant->id, $cart);
    }

    /**
     * Test 10: Updating quantity in cart recalculates subtotal and total count.
     */
    public function test_cart_update_quantity_recalculates(): void
    {
        $variant = ProductVariant::where('stock', '>', 5)->first();
        $this->assertNotNull($variant);

        // Preload session with 1 quantity
        $initialCart = [
            $variant->id => [
                'variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_name' => 'iPhone Test',
                'product_slug' => 'iphone-test',
                'storage_capacity' => '256GB',
                'color_name' => 'Natural Titanium',
                'color_hex' => '#9A958E',
                'price' => (float) $variant->selling_price,
                'cost_price' => (float) $variant->cost_price,
                'quantity' => 1,
                'stock' => $variant->stock,
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'battery_health' => null,
                'imei' => null,
                'image' => null,
            ]
        ];

        $response = $this->withSession(['cart' => $initialCart, 'cart_count' => 1])
            ->patchJson("/cart/update/{$variant->id}", [
                'quantity' => 4,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'quantity' => 4,
            'cart_count' => 4,
        ]);

        $updatedCart = session('cart');
        $this->assertEquals(4, $updatedCart[$variant->id]['quantity']);
    }

    /**
     * Test 11: Removing an item from cart unsets it from session.
     */
    public function test_cart_remove_item(): void
    {
        $variant = ProductVariant::first();
        $this->assertNotNull($variant);

        $cart = [
            $variant->id => [
                'variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_name' => 'iPhone Test',
                'quantity' => 1,
                'price' => 10000000,
            ]
        ];

        $response = $this->withSession(['cart' => $cart, 'cart_count' => 1])
            ->deleteJson("/cart/remove/{$variant->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 0,
            'is_empty' => true,
        ]);

        $this->assertArrayNotHasKey($variant->id, session('cart', []));
    }

    /**
     * Test 12: Clear cart empties entire session cart.
     */
    public function test_cart_clear_empties_session(): void
    {
        $cart = [
            1 => ['quantity' => 1, 'price' => 10000000],
            2 => ['quantity' => 2, 'price' => 20000000],
        ];

        $response = $this->withSession(['cart' => $cart, 'cart_count' => 3])
            ->post('/cart/clear');

        $response->assertRedirect('/cart');
        $this->assertEmpty(session('cart', []));
        $this->assertEquals(0, session('cart_count', 0));
    }

    /**
     * Test 13: Live search returns matched JSON products.
     */
    public function test_live_search_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/products/search?q=16');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'slug',
                'series',
                'condition',
                'formatted_price',
                'url',
            ]
        ]);

        // Short search query returns empty array
        $shortResponse = $this->getJson('/api/products/search?q=i');
        $shortResponse->assertStatus(200);
        $shortResponse->assertExactJson([]);
    }
}
