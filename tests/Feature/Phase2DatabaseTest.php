<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TradeIn;
use App\Models\User;
use Tests\TestCase;

class Phase2DatabaseTest extends TestCase
{
    /**
     * Test user accounts and role helper methods.
     */
    public function test_users_and_role_helpers(): void
    {
        $owner = User::where('email', 'owner@rap-iphone.com')->first();
        $this->assertNotNull($owner);
        $this->assertTrue($owner->isOwner());
        $this->assertTrue($owner->hasAdminAccess());
        $this->assertFalse($owner->isCustomer());

        $admin = User::where('email', 'admin@rap-iphone.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasAdminAccess());
        $this->assertFalse($admin->isCustomer());

        $customer = User::where('email', 'customer@rap-iphone.com')->first();
        $this->assertNotNull($customer);
        $this->assertTrue($customer->isCustomer());
        $this->assertFalse($customer->hasAdminAccess());
    }

    /**
     * Test categories and product seeders.
     */
    public function test_catalog_seeders_and_relationships(): void
    {
        $this->assertGreaterThanOrEqual(5, Category::count());
        $this->assertGreaterThanOrEqual(6, Product::count());
        $this->assertGreaterThanOrEqual(30, ProductVariant::count());

        $product = Product::where('slug', 'iphone-16-pro-max')->with(['variants', 'images', 'category'])->first();
        $this->assertNotNull($product);
        $this->assertEquals('iPhone 16 Series', $product->series);
        $this->assertEquals('resmi_ibox', $product->warranty_type);
        $this->assertGreaterThan(0, $product->variants->count());
        $this->assertGreaterThan(0, $product->images->count());

        $variant = $product->variants->first();
        $this->assertNotNull($variant->color_hex);
        $this->assertGreaterThan(0, $variant->margin);
    }

    /**
     * Test second-hand unit specifics (battery health & IMEI).
     */
    public function test_second_hand_unit_specifics(): void
    {
        $secondProduct = Product::where('condition', 'second')->with('variants')->first();
        $this->assertNotNull($secondProduct);
        $this->assertNotNull($secondProduct->grade);

        $secondVariant = $secondProduct->variants->first();
        $this->assertNotNull($secondVariant->battery_health);
        $this->assertNotNull($secondVariant->imei);
    }

    /**
     * Test orders, order items, and payment relationships.
     */
    public function test_order_payment_and_item_relationships(): void
    {
        $customer = User::where('role', 'customer')->first();
        $variant = ProductVariant::first();

        $order = Order::create([
            'order_number' => 'RAP-TEST-' . uniqid(),
            'user_id' => $customer->id,
            'total_items_price' => $variant->selling_price,
            'shipping_cost' => 50000,
            'discount_amount' => 0,
            'grand_total' => $variant->selling_price + 50000,
            'status' => 'pending',
            'shipping_courier' => 'JNE REG',
            'shipping_tracking_number' => null,
            'shipping_address' => 'Jl. Kebon Jeruk No. 10',
            'recipient_name' => 'Budi Pratama',
            'recipient_phone' => '081234567892',
            'customer_notes' => 'Tolong bubble wrap tebal',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_name' => $variant->product->name,
            'variant_details' => $variant->color_name . ' / ' . $variant->storage_capacity,
            'price' => $variant->selling_price,
            'cost_price' => $variant->cost_price,
            'quantity' => 1,
            'subtotal' => $variant->selling_price,
            'allocated_imei' => '359999999999999',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_transaction_id' => 'MID-TRANS-12345',
            'payment_type' => 'qris',
            'gross_amount' => $order->grand_total,
            'snap_token' => 'mock-snap-token-xyz',
            'transaction_status' => 'pending',
            'raw_payload' => ['order_id' => 'RAP-TEST-001', 'status' => 'pending'],
        ]);

        $this->assertEquals($order->id, $item->order->id);
        $this->assertEquals($order->id, $payment->order->id);
        $this->assertEquals($customer->id, $order->user->id);
        $this->assertCount(1, $order->items);
        $this->assertNotNull($order->payment);
        $this->assertIsArray($payment->raw_payload);
        $this->assertGreaterThan(0, $item->profit);

        // Clean up test order
        $order->delete();
    }

    /**
     * Test trade-in submission and notification log models.
     */
    public function test_trade_in_and_notification_log(): void
    {
        $customer = User::where('role', 'customer')->first();

        $tradeIn = TradeIn::create([
            'trade_in_code' => 'TI-TEST-' . uniqid(),
            'user_id' => $customer->id,
            'target_order_id' => null,
            'phone_model' => 'iPhone 13 Pro',
            'storage_capacity' => '128GB',
            'battery_health' => 86,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'imei' => '351234567890123',
            'estimated_price' => 7500000,
            'final_price' => null,
            'status' => 'submitted',
        ]);

        $this->assertNotNull($tradeIn->id);
        $this->assertEquals($customer->id, $tradeIn->user->id);

        $log = NotificationLog::create([
            'channel' => 'whatsapp',
            'recipient' => '081234567892',
            'event_name' => 'order_created',
            'status' => 'sent',
            'response_payload' => ['status' => true, 'target' => '081234567892'],
        ]);

        $this->assertNotNull($log->id);
        $this->assertIsArray($log->response_payload);
        $this->assertTrue($log->response_payload['status']);

        // Clean up
        $tradeIn->delete();
        $log->delete();
    }
}
