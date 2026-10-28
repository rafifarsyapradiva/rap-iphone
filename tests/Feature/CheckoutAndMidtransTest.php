<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutAndMidtransTest extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;
    protected User $otherCustomer;
    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(5);

        $this->customer = User::create([
            'name' => 'Budi Santoso',
            'email' => "budi.{$suffix}@example.com",
            'phone_number' => '081234567890',
            'address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
            'role' => 'customer',
        ]);

        $this->otherCustomer = User::create([
            'name' => 'Siti Rahma',
            'email' => "siti.{$suffix}@example.com",
            'role' => 'customer',
        ]);

        $category = Category::firstOrCreate(
            ['slug' => 'iphone-16-series'],
            ['name' => 'iPhone 16 Series', 'icon' => 'smartphone']
        );

        $this->product = Product::firstOrCreate(
            ['slug' => 'iphone-16-pro-max-test-' . $suffix],
            [
                'category_id' => $category->id,
                'name' => 'iPhone 16 Pro Max',
                'series' => 'iPhone 16 Series',
                'condition' => 'new',
                'warranty_type' => 'resmi_ibox',
                'base_price' => 24999000.00,
                'description' => 'Flagship Apple A18 Pro.',
                'is_active' => true,
            ]
        );

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'storage_capacity' => '256GB',
            'color_name' => 'Desert Titanium',
            'color_hex' => '#C4A58C',
            'cost_price' => 21000000.00,
            'selling_price' => 24999000.00,
            'stock' => 5,
            'sku' => 'IPH16PM-256-DT-' . $suffix,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_checkout(): void
    {
        $response = $this->get(route('checkout.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_empty_cart_redirects_to_cart_index(): void
    {
        $response = $this->actingAs($this->customer)->get(route('checkout.index'));
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_checkout_page_renders_with_cart_items_and_prefilled_user_data(): void
    {
        $cart = [
            $this->variant->id => [
                'variant_id' => $this->variant->id,
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'product_slug' => $this->product->slug,
                'storage_capacity' => $this->variant->storage_capacity,
                'color_name' => $this->variant->color_name,
                'color_hex' => $this->variant->color_hex,
                'price' => (float) $this->variant->selling_price,
                'cost_price' => (float) $this->variant->cost_price,
                'quantity' => 1,
                'stock' => $this->variant->stock,
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'battery_health' => null,
                'imei' => null,
                'image' => null,
            ],
        ];

        $response = $this->actingAs($this->customer)
            ->withSession(['cart' => $cart])
            ->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertSee('iPhone 16 Pro Max');
        $response->assertSee('Desert Titanium');
        $response->assertSee('256GB');
        $response->assertSee('Budi Santoso');
        $response->assertSee('081234567890');
        $response->assertSee('Bayar Sekarang dengan Midtrans');
    }

    public function test_checkout_process_creates_order_and_payment_records(): void
    {
        $cart = [
            $this->variant->id => [
                'variant_id' => $this->variant->id,
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'product_slug' => $this->product->slug,
                'storage_capacity' => $this->variant->storage_capacity,
                'color_name' => $this->variant->color_name,
                'color_hex' => $this->variant->color_hex,
                'price' => (float) $this->variant->selling_price,
                'cost_price' => (float) $this->variant->cost_price,
                'quantity' => 2,
                'stock' => $this->variant->stock,
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'battery_health' => null,
                'imei' => null,
                'image' => null,
            ],
        ];

        $postData = [
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Sudirman Kav 45, Jakarta',
            'shipping_courier' => 'JNE Express (Reguler / YES)',
            'customer_notes' => 'Tolong bubble wrap tebal',
        ];

        $response = $this->actingAs($this->customer)
            ->withSession(['cart' => $cart])
            ->postJson(route('checkout.process'), $postData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'order_number',
            'snap_token',
            'redirect_url',
            'message',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->customer->id,
            'recipient_name' => 'Budi Santoso',
            'grand_total' => 49998000.00,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express (Reguler / YES)',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_variant_id' => $this->variant->id,
            'quantity' => 2,
            'price' => 24999000.00,
            'subtotal' => 49998000.00,
        ]);

        $this->assertDatabaseHas('payments', [
            'gross_amount' => 49998000.00,
            'transaction_status' => 'pending',
        ]);

        // Cart must be cleared in session
        $this->assertEmpty(session('cart'));
    }

    public function test_checkout_process_fails_if_stock_is_insufficient(): void
    {
        // Set variant stock to 1
        $this->variant->update(['stock' => 1]);

        $cart = [
            $this->variant->id => [
                'variant_id' => $this->variant->id,
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'product_slug' => $this->product->slug,
                'storage_capacity' => $this->variant->storage_capacity,
                'color_name' => $this->variant->color_name,
                'color_hex' => $this->variant->color_hex,
                'price' => (float) $this->variant->selling_price,
                'cost_price' => (float) $this->variant->cost_price,
                'quantity' => 3, // Demands 3, stock is 1
                'stock' => 1,
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'battery_health' => null,
                'imei' => null,
                'image' => null,
            ],
        ];

        $postData = [
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Sudirman Kav 45, Jakarta',
            'shipping_courier' => 'JNE Express (Reguler / YES)',
        ];

        $response = $this->actingAs($this->customer)
            ->withSession(['cart' => $cart])
            ->postJson(route('checkout.process'), $postData);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        $this->assertDatabaseMissing('orders', ['user_id' => $this->customer->id]);
    }

    public function test_midtrans_callback_rejects_missing_signature(): void
    {
        $response = $this->postJson(route('api.midtrans.callback'), [
            'order_id' => 'RAP-TEST-' . uniqid(),
            'status_code' => '200',
        ]);

        $response->assertStatus(422);
    }

    public function test_midtrans_callback_rejects_invalid_signature(): void
    {
        $response = $this->postJson(route('api.midtrans.callback'), [
            'order_id' => 'RAP-TEST-' . uniqid(),
            'status_code' => '200',
            'gross_amount' => '24999000.00',
            'signature_key' => 'invalid_sha512_hash',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
    }

    public function test_midtrans_callback_settlement_marks_order_paid_and_decrements_stock(): void
    {
        $initialStock = $this->variant->stock; // 5
        $orderNumber = 'RAP-TEST-' . uniqid();

        // Create Order and items
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $this->customer->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 24999000.00,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jakarta',
            'recipient_name' => 'Budi',
            'recipient_phone' => '081234567890',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'variant_details' => 'Desert Titanium / 256GB',
            'price' => 24999000.00,
            'cost_price' => 21000000.00,
            'quantity' => 2,
            'subtotal' => 49998000.00,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gross_amount' => 24999000.00,
            'snap_token' => 'mock_snap_token_123',
            'transaction_status' => 'pending',
        ]);

        $serverKey = config('services.midtrans.server_key');
        $grossAmount = '24999000.00';
        $signatureKey = hash('sha512', $order->order_number . '200' . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'transaction_id' => 'midtrans-trans-uuid-12345',
            'payment_type' => 'qris',
            'transaction_time' => '2026-10-28 18:30:00',
        ];

        $response = $this->postJson(route('api.midtrans.callback'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'order_status' => 'paid',
        ]);

        $order->refresh();
        $this->assertEquals('paid', $order->status);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'midtrans_transaction_id' => 'midtrans-trans-uuid-12345',
            'payment_type' => 'qris',
            'transaction_status' => 'settlement',
        ]);

        $this->variant->refresh();
        $this->assertEquals($initialStock - 2, $this->variant->stock); // 5 - 2 = 3
    }

    public function test_midtrans_callback_is_idempotent_and_does_not_double_decrement_stock(): void
    {
        $initialStock = $this->variant->stock; // 5
        $orderNumber = 'RAP-TEST-' . uniqid();

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $this->customer->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 24999000.00,
            'status' => 'paid', // Already paid
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jakarta',
            'recipient_name' => 'Budi',
            'recipient_phone' => '081234567890',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'variant_details' => 'Desert Titanium / 256GB',
            'price' => 24999000.00,
            'cost_price' => 21000000.00,
            'quantity' => 1,
            'subtotal' => 24999000.00,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gross_amount' => 24999000.00,
            'transaction_status' => 'settlement',
        ]);

        $serverKey = config('services.midtrans.server_key');
        $grossAmount = '24999000.00';
        $signatureKey = hash('sha512', $order->order_number . '200' . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
        ];

        // Second notification arrives
        $response = $this->postJson(route('api.midtrans.callback'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Pesanan sudah berstatus lunas sebelumnya (Idempotent).',
        ]);

        // Stock must remain unchanged
        $this->variant->refresh();
        $this->assertEquals($initialStock, $this->variant->stock);
    }

    public function test_midtrans_callback_cancel_marks_order_cancelled(): void
    {
        $orderNumber = 'RAP-TEST-' . uniqid();

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $this->customer->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 24999000.00,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jakarta',
            'recipient_name' => 'Budi',
            'recipient_phone' => '081234567890',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gross_amount' => 24999000.00,
            'transaction_status' => 'pending',
        ]);

        $serverKey = config('services.midtrans.server_key');
        $grossAmount = '24999000.00';
        $signatureKey = hash('sha512', $order->order_number . '200' . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
        ];

        $response = $this->postJson(route('api.midtrans.callback'), $payload);

        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_user_can_view_checkout_success_page_for_own_order(): void
    {
        $orderNumber = 'RAP-TEST-' . uniqid();

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $this->customer->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 24999000.00,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jakarta',
            'recipient_name' => 'Budi',
            'recipient_phone' => '081234567890',
        ]);

        $response = $this->actingAs($this->customer)->get(route('checkout.success', $order->order_number));
        $response->assertStatus(200);
        $response->assertSee($orderNumber);
        $response->assertSee('Terima Kasih Atas Pesanan Anda');
    }

    public function test_user_cannot_view_other_users_checkout_success_page(): void
    {
        $orderNumber = 'RAP-TEST-' . uniqid();

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $this->customer->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 24999000.00,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jakarta',
            'recipient_name' => 'Budi',
            'recipient_phone' => '081234567890',
        ]);

        $response = $this->actingAs($this->otherCustomer)->get(route('checkout.success', $order->order_number));
        $response->assertStatus(403);
    }
}
