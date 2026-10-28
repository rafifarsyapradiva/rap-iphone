<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TradeIn;
use App\Models\User;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    /**
     * Test seeded data counts.
     */
    public function test_seeded_data_exists(): void
    {
        $this->assertGreaterThanOrEqual(3, User::count());
        $this->assertGreaterThanOrEqual(5, Category::count());
        $this->assertGreaterThanOrEqual(6, Product::count());
        $this->assertGreaterThanOrEqual(20, ProductVariant::count());
    }

    /**
     * Test user role helpers.
     */
    public function test_user_roles(): void
    {
        $owner = User::where('role', 'owner')->first();
        $admin = User::where('role', 'admin')->first();
        $customer = User::where('role', 'customer')->first();

        $this->assertNotNull($owner);
        $this->assertTrue($owner->isOwner());
        $this->assertTrue($owner->hasAdminAccess());
        $this->assertFalse($owner->isCustomer());

        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasAdminAccess());

        $this->assertNotNull($customer);
        $this->assertTrue($customer->isCustomer());
        $this->assertFalse($customer->hasAdminAccess());
    }

    /**
     * Test product and variant relationships and profit margin.
     */
    public function test_product_variants_relationship(): void
    {
        $product = Product::with(['category', 'variants', 'images'])->first();

        $this->assertNotNull($product);
        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertNotEmpty($product->variants);

        $variant = $product->variants->first();
        $this->assertNotNull($variant);
        $this->assertEquals($product->id, $variant->product->id);
        $this->assertGreaterThan(0, $variant->selling_price);
        $this->assertGreaterThan(0, $variant->cost_price);
        $this->assertEquals($variant->selling_price - $variant->cost_price, $variant->profit_margin);
    }

    /**
     * Test order, order item, and payment lifecycle and relationships.
     */
    public function test_order_creation_with_items_and_payment(): void
    {
        $customer = User::where('role', 'customer')->first();
        $variant = ProductVariant::first();

        $order = Order::create([
            'order_number' => 'RAP-TEST-' . uniqid(),
            'user_id' => $customer->id,
            'total_items_price' => $variant->selling_price,
            'shipping_cost' => 20000,
            'discount_amount' => 0,
            'grand_total' => $variant->selling_price + 20000,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => $customer->address,
            'recipient_name' => $customer->name,
            'recipient_phone' => $customer->phone_number,
        ]);

        $item = $order->items()->create([
            'product_variant_id' => $variant->id,
            'product_name' => $variant->product->name,
            'variant_details' => $variant->color_name . ' / ' . $variant->storage_capacity,
            'price' => $variant->selling_price,
            'cost_price' => $variant->cost_price,
            'quantity' => 1,
            'subtotal' => $variant->selling_price,
        ]);

        $payment = $order->payment()->create([
            'gross_amount' => $order->grand_total,
            'snap_token' => 'mock-snap-token-' . uniqid(),
            'transaction_status' => 'pending',
        ]);

        $this->assertEquals(1, $order->items()->count());
        $this->assertInstanceOf(Payment::class, $order->payment);
        $this->assertEquals($customer->id, $order->user->id);

        // Cleanup test order
        $order->delete();
    }
}
