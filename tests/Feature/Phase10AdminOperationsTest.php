<?php

namespace Tests\Feature;

use App\Mail\OrderShippedMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase10AdminOperationsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $customer;
    protected Category $category;
    protected Product $product;
    protected ProductVariant $variant;
    protected Order $paidOrder;

    /**
     * 1x1 valid PNG binary (tidak membutuhkan ekstensi GD).
     */
    protected const TINY_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(5);

        $this->admin = User::create([
            'name' => 'Admin Operasional',
            'email' => "admin.p10.{$suffix}@rap-iphone.com",
            'phone_number' => '081199887766',
            'role' => 'admin',
        ]);

        $this->customer = User::create([
            'name' => 'Dian Sastro',
            'email' => "dian.p10.{$suffix}@example.com",
            'phone_number' => '081288990011',
            'address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
            'role' => 'customer',
        ]);

        $this->category = Category::firstOrCreate(
            ['slug' => 'iphone-16-series'],
            ['name' => 'iPhone 16 Series', 'icon' => 'smartphone']
        );

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 16 Pro ' . $suffix,
            'slug' => 'iphone-16-pro-p10-' . strtolower($suffix),
            'series' => 'iPhone 16 Series',
            'condition' => 'new',
            'warranty_type' => 'resmi_ibox',
            'base_price' => 20999000.00,
            'description' => 'Apple A18 Pro chip dengan layar Super Retina XDR.',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'storage_capacity' => '256GB',
            'color_name' => 'Natural Titanium',
            'color_hex' => '#9A958E',
            'cost_price' => 19000000.00,
            'selling_price' => 20999000.00,
            'stock' => 8,
            'sku' => 'IP16P-256-NT-' . strtoupper($suffix),
            'imei' => '358899001122334',
        ]);

        $this->paidOrder = Order::create([
            'order_number' => 'RAP-20261028-P10' . strtoupper($suffix),
            'user_id' => $this->customer->id,
            'total_items_price' => 20999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 20999000.00,
            'status' => 'paid',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
            'recipient_name' => 'Dian Sastro',
            'recipient_phone' => '0812-8899-0011',
        ]);

        OrderItem::create([
            'order_id' => $this->paidOrder->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'variant_details' => 'Natural Titanium / 256GB',
            'price' => 20999000.00,
            'cost_price' => 19000000.00,
            'quantity' => 1,
            'subtotal' => 20999000.00,
        ]);

        Payment::create([
            'order_id' => $this->paidOrder->id,
            'midtrans_transaction_id' => 'mid-p10-' . $suffix,
            'gross_amount' => 20999000.00,
            'snap_token' => 'snap_p10_' . $suffix,
            'payment_type' => 'qris',
            'transaction_status' => 'settlement',
            'payment_time' => now(),
        ]);
    }

    public function test_admin_can_view_product_inventory_list_and_filter_products(): void
    {
        $secondProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 15 Pro Second Khusus',
            'slug' => 'iphone-15-pro-second-khusus-' . strtolower(Str::random(4)),
            'series' => 'iPhone 15 Series',
            'condition' => 'second',
            'grade' => 'Grade A+',
            'warranty_type' => 'toko',
            'base_price' => 15500000.00,
            'description' => 'Unit second mulus.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $response->assertOk();
        $response->assertSee($this->product->name);
        $response->assertSee($secondProduct->name);

        // Filter khusus kondisi second
        $secondOnly = $this->actingAs($this->admin)->get(route('admin.products.index', ['condition' => 'second']));
        $secondOnly->assertOk();
        $secondOnly->assertSee($secondProduct->name);
        $secondOnly->assertDontSee($this->product->name);

        // Pencarian berdasarkan IMEI varian
        $byImei = $this->actingAs($this->admin)->get(route('admin.products.index', ['q' => '358899001122334']));
        $byImei->assertOk();
        $byImei->assertSee($this->product->name);
    }

    public function test_admin_can_create_update_and_delete_product_with_initial_variants(): void
    {
        $createPage = $this->actingAs($this->admin)->get(route('admin.products.create'));
        $createPage->assertOk();

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'iPhone 16 Ultra Test',
            'series' => 'iPhone 16 Series',
            'condition' => 'second',
            'grade' => 'Grade A+',
            'warranty_type' => 'toko',
            'base_price' => 19000000,
            'description' => 'Unit second Grade A+ baterai 98%.',
            'is_active' => '1',
            'variants' => [
                [
                    'color_name' => 'Desert Titanium',
                    'color_hex' => '#C5B49F',
                    'storage_capacity' => '512GB',
                    'cost_price' => 16500000,
                    'selling_price' => 18200000,
                    'stock' => 4,
                    'battery_health' => 98,
                    'imei' => '357766554433221',
                ],
            ],
        ]);

        $createdProduct = Product::where('name', 'iPhone 16 Ultra Test')->firstOrFail();
        $storeResponse->assertRedirect(route('admin.products.edit', $createdProduct));

        // Base price disinkronkan ke harga jual varian terendah (18.200.000)
        $this->assertEquals(18200000.00, (float) $createdProduct->base_price);
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $createdProduct->id,
            'color_name' => 'Desert Titanium',
            'storage_capacity' => '512GB',
            'cost_price' => 16500000.00,
            'selling_price' => 18200000.00,
            'stock' => 4,
            'battery_health' => 98,
            'imei' => '357766554433221',
        ]);

        // Update produk induk
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.products.update', $createdProduct), [
            'category_id' => $this->category->id,
            'name' => 'iPhone 16 Ultra Test Updated',
            'slug' => $createdProduct->slug,
            'series' => 'iPhone 16 Series',
            'condition' => 'second',
            'grade' => 'Grade A',
            'warranty_type' => 'toko',
            'base_price' => 18200000,
            'description' => 'Deskripsi terbaru.',
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.products.edit', $createdProduct));
        $this->assertDatabaseHas('products', [
            'id' => $createdProduct->id,
            'name' => 'iPhone 16 Ultra Test Updated',
            'grade' => 'Grade A',
        ]);

        // Hapus produk
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $createdProduct));
        $deleteResponse->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $createdProduct->id]);
    }

    public function test_admin_can_crud_product_variants_with_cost_selling_price_stock_and_imei(): void
    {
        // 1. Tambah varian baru
        $addResponse = $this->actingAs($this->admin)->post(route('admin.products.variants.store', $this->product), [
            'color_name' => 'Desert Titanium',
            'color_hex' => '#C5B49F',
            'storage_capacity' => '128GB',
            'cost_price' => 17500000,
            'selling_price' => 19499000,
            'stock' => 6,
            'battery_health' => 100,
            'imei' => '351122334455667',
        ]);

        $addResponse->assertRedirect(route('admin.products.edit', $this->product));

        $newVariant = ProductVariant::where('product_id', $this->product->id)
            ->where('storage_capacity', '128GB')
            ->firstOrFail();

        $this->assertEquals(17500000.00, (float) $newVariant->cost_price);
        $this->assertEquals(19499000.00, (float) $newVariant->selling_price);
        $this->assertEquals(6, $newVariant->stock);
        $this->assertEquals('351122334455667', $newVariant->imei);

        // Base price produk induk tersinkronisasi ke 19.499.000 (varian termurah)
        $this->assertEquals(19499000.00, (float) $this->product->fresh()->base_price);

        // 2. Update varian (ubah stok, HPP, harga jual, IMEI)
        $updateResponse = $this->actingAs($this->admin)->put(
            route('admin.products.variants.update', [$this->product, $newVariant]),
            [
                'color_name' => 'Black Titanium',
                'color_hex' => '#2D2F33',
                'storage_capacity' => '128GB',
                'cost_price' => 17000000,
                'selling_price' => 18999000,
                'stock' => 12,
                'sku' => $newVariant->sku,
                'battery_health' => 99,
                'imei' => '359900112233445',
            ]
        );

        $updateResponse->assertRedirect(route('admin.products.edit', $this->product));

        $newVariant->refresh();
        $this->assertEquals('Black Titanium', $newVariant->color_name);
        $this->assertEquals(17000000.00, (float) $newVariant->cost_price);
        $this->assertEquals(18999000.00, (float) $newVariant->selling_price);
        $this->assertEquals(12, $newVariant->stock);
        $this->assertEquals('359900112233445', $newVariant->imei);
        $this->assertEquals(18999000.00, (float) $this->product->fresh()->base_price);

        // 3. Hapus varian
        $deleteResponse = $this->actingAs($this->admin)->delete(
            route('admin.products.variants.destroy', [$this->product, $newVariant])
        );
        $deleteResponse->assertRedirect(route('admin.products.edit', $this->product));
        $this->assertDatabaseMissing('product_variants', ['id' => $newVariant->id]);
    }

    public function test_admin_can_upload_multi_angle_product_images_set_primary_and_reorder(): void
    {
        $pngBytes = base64_decode(self::TINY_PNG_BASE64);
        $angleFront = UploadedFile::fake()->createWithContent('front-angle.png', $pngBytes);
        $angleBack = UploadedFile::fake()->createWithContent('back-camera-angle.png', $pngBytes);

        $uploadResponse = $this->actingAs($this->admin)->post(
            route('admin.products.images.store', $this->product),
            [
                'images' => [$angleFront, $angleBack],
                'variant_id' => $this->variant->id,
                'order_index' => 1,
                'is_primary' => '1',
            ]
        );

        $uploadResponse->assertRedirect(route('admin.products.edit', $this->product));

        $images = ProductImage::where('product_id', $this->product->id)->orderBy('order_index')->get();
        $this->assertCount(2, $images);

        $firstImg = $images[0];
        $secondImg = $images[1];

        $this->assertTrue($firstImg->is_primary);
        $this->assertFalse($secondImg->is_primary);
        $this->assertFileExists(public_path($firstImg->image_path));
        $this->assertFileExists(public_path($secondImg->image_path));

        // Jadikan foto kedua sebagai foto utama (primary)
        $primaryResponse = $this->actingAs($this->admin)->patch(
            route('admin.products.images.primary', [$this->product, $secondImg])
        );
        $primaryResponse->assertRedirect(route('admin.products.edit', $this->product));
        $this->assertFalse($firstImg->fresh()->is_primary);
        $this->assertTrue($secondImg->fresh()->is_primary);

        // Ubah urutan angle (order_index)
        $orderResponse = $this->actingAs($this->admin)->patch(
            route('admin.products.images.order', [$this->product, $firstImg]),
            [
                'order_index' => 4,
                'variant_id' => $this->variant->id,
            ]
        );
        $orderResponse->assertRedirect(route('admin.products.edit', $this->product));
        $this->assertEquals(4, $firstImg->fresh()->order_index);

        // Hapus kedua foto agar direktori bersih setelah pengujian
        foreach ($images as $img) {
            $path = public_path($img->image_path);
            $this->actingAs($this->admin)->delete(route('admin.products.images.destroy', [$this->product, $img]));
            $this->assertFileDoesNotExist($path);
        }
    }

    public function test_admin_can_filter_orders_by_all_statuses_and_view_order_detail(): void
    {
        $statusesToCreate = ['pending', 'processing', 'shipped', 'completed'];
        $createdOrders = ['paid' => $this->paidOrder];

        foreach ($statusesToCreate as $st) {
            $createdOrders[$st] = Order::create([
                'order_number' => 'RAP-ST-' . strtoupper($st) . '-' . strtoupper(Str::random(4)),
                'user_id' => $this->customer->id,
                'total_items_price' => 10000000,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'grand_total' => 10000000,
                'status' => $st,
                'shipping_courier' => 'J&T Express',
                'shipping_address' => 'Jl. Thamrin No. 1, Jakarta',
                'recipient_name' => 'Dian Sastro',
                'recipient_phone' => '081288990011',
            ]);
        }

        foreach ($createdOrders as $statusKey => $orderObj) {
            $res = $this->actingAs($this->admin)->get(route('admin.orders.index', ['status' => $statusKey]));
            $res->assertOk();
            $res->assertSee($orderObj->order_number);
        }

        // Halaman detail operasional pesanan
        $detailRes = $this->actingAs($this->admin)->get(route('admin.orders.show', $this->paidOrder->order_number));
        $detailRes->assertOk();
        $detailRes->assertSee($this->paidOrder->order_number);
        $detailRes->assertSee('Input Resi &amp; Pengiriman Kurir', false);
    }

    public function test_admin_can_update_order_status_and_allocate_item_imei(): void
    {
        $orderItem = $this->paidOrder->items()->firstOrFail();

        $response = $this->actingAs($this->admin)->patch(
            route('admin.orders.status', $this->paidOrder->order_number),
            [
                'status' => 'processing',
                'allocated_imei' => [
                    $orderItem->id => '356677889900112',
                ],
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('processing', $this->paidOrder->fresh()->status);
        $this->assertEquals('356677889900112', $orderItem->fresh()->allocated_imei);
    }

    public function test_admin_inputting_tracking_number_automatically_updates_status_to_shipped_and_triggers_wa_and_email_notifications(): void
    {
        config(['services.fonnte.token' => 'fonnte_test_token_phase10']);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'detail' => 'sent',
            ], 200),
        ]);

        Mail::fake();

        $orderItem = $this->paidOrder->items()->firstOrFail();

        $response = $this->actingAs($this->admin)->patch(
            route('admin.orders.shipping', $this->paidOrder->order_number),
            [
                'shipping_courier' => 'SiCepat Ekspres',
                'shipping_tracking_number' => 'sicepat-0099887766',
                'allocated_imei' => [
                    $orderItem->id => '358899001122334',
                ],
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->paidOrder->refresh();
        $this->assertEquals('shipped', $this->paidOrder->status);
        $this->assertEquals('SiCepat Ekspres', $this->paidOrder->shipping_courier);
        $this->assertEquals('SICEPAT-0099887766', $this->paidOrder->shipping_tracking_number);
        $this->assertEquals('358899001122334', $orderItem->fresh()->allocated_imei);

        // Pastikan Fonnte WhatsApp terkirim dengan nomor resi & kurir
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '6281288990011'
                && str_contains($request['message'], 'SICEPAT-0099887766')
                && str_contains($request['message'], 'SiCepat Ekspres');
        });

        // Pastikan Email OrderShippedMail terkirim ke customer
        Mail::assertSent(OrderShippedMail::class, function (OrderShippedMail $mail) {
            return $mail->hasTo($this->customer->email)
                && $mail->order->shipping_tracking_number === 'SICEPAT-0099887766';
        });

        // Pastikan tercatat di notifications_log untuk kedua channel (whatsapp & email)
        $this->assertDatabaseHas('notifications_log', [
            'channel' => 'whatsapp',
            'recipient' => '6281288990011',
            'event_name' => 'order_shipped',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('notifications_log', [
            'channel' => 'email',
            'recipient' => $this->customer->email,
            'event_name' => 'order_shipped',
            'status' => 'sent',
        ]);
    }

    public function test_customer_cannot_access_admin_product_and_order_management_routes(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.products.index'))
            ->assertRedirect('/');

        $this->actingAs($this->customer)
            ->postJson(route('admin.products.store'), [])
            ->assertForbidden();

        $this->actingAs($this->customer)
            ->patchJson(route('admin.orders.shipping', $this->paidOrder->order_number), [
                'shipping_courier' => 'JNE Express',
                'shipping_tracking_number' => 'JNE-123',
            ])
            ->assertForbidden();
    }
}

