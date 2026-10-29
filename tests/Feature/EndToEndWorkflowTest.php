<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TradeIn;
use App\Models\User;
use App\Services\FinancialAnalyticsService;
use App\Services\MidtransService;
use App\Services\NotificationOrchestrator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class EndToEndWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;
    protected User $admin;
    protected User $owner;
    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::lower(Str::random(6));

        $this->customer = User::create([
            'name' => 'Budi Customer',
            'email' => "budi.{$suffix}@example.com",
            'role' => 'customer',
            'phone_number' => '081234567890',
            'address' => 'Jl. Jenderal Sudirman Kav 21, Jakarta Selatan',
        ]);

        $this->admin = User::create([
            'name' => 'Staf Operasional Toko',
            'email' => "staf.{$suffix}@rap-iphone.com",
            'role' => 'admin',
        ]);

        $this->owner = User::create([
            'name' => 'Direktur Utama Toko',
            'email' => "direktur.{$suffix}@rap-iphone.com",
            'role' => 'owner',
        ]);

        $category = Category::firstOrCreate(
            ['slug' => 'iphone-series'],
            ['name' => 'iPhone Series', 'icon' => 'smartphone']
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'iPhone 16 Pro E2E',
            'slug' => "iphone-16-pro-{$suffix}",
            'series' => 'iPhone 16 Series',
            'condition' => 'new',
            'warranty_type' => 'resmi_ibox',
            'base_price' => 20999000.00,
            'description' => 'Flagship iPhone 16 Pro Garansi Resmi TAM.',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'storage_capacity' => '256GB',
            'color_name' => 'Natural Titanium',
            'color_hex' => '#9A9895',
            'cost_price' => 17500000.00, // HPP modal
            'selling_price' => 20999000.00, // Harga jual
            'stock' => 10,
            'sku' => "IPH16P-256-{$suffix}",
            'battery_health' => 100,
            'imei' => '352984110' . rand(100000, 999999),
        ]);
    }

    /**
     * Test Complete Workflow 1:
     * Customer Checkout -> Midtrans Paid Webhook -> Admin Fulfillment & IMEI Allocation -> Owner Profit Ledger.
     */
    public function test_complete_order_lifecycle_from_customer_to_admin_to_owner(): void
    {
        // 1. Customer adds unit to cart
        $cartData = [
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
                'battery_health' => 100,
                'imei' => $this->variant->imei,
                'image' => null,
            ],
        ];

        // 2. Customer submits checkout
        $checkoutResponse = $this->actingAs($this->customer)
            ->withSession(['cart' => $cartData, 'cart_count' => 1])
            ->post(route('checkout.process'), [
                'recipient_name' => 'Budi Customer',
                'recipient_phone' => '081234567890',
                'shipping_address' => 'Jl. Jenderal Sudirman Kav 21, Jakarta Selatan',
                'shipping_courier' => 'JNE Express (Reguler / YES)',
                'customer_notes' => 'Tolong bubble wrap tebal.',
            ]);

        $checkoutResponse->assertRedirect();

        $order = Order::where('user_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(20999000.00, (float) $order->grand_total);
        $this->assertEquals(10, $this->variant->fresh()->stock); // Belum berkurang sebelum lunas

        // Verify snapshot HPP is saved
        $orderItem = $order->items->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals(17500000.00, (float) $orderItem->cost_price);

        // 3. Webhook Midtrans Settlement (Order Marked as Paid & Stock Decremented Atomically)
        $midtransService = app(MidtransService::class);
        $serverKey = $midtransService->getServerKey();
        $grossFormatted = number_format((float) $order->grand_total, 2, '.', '');
        $signature = hash('sha512', $order->order_number . '200' . $grossFormatted . $serverKey);

        $webhookPayload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => $grossFormatted,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_id' => 'mid-settled-trans-12345',
            'payment_type' => 'qris',
            'transaction_time' => now()->format('Y-m-d H:i:s'),
        ];

        $webhookResponse = $this->postJson(route('api.midtrans.callback'), $webhookPayload);
        $webhookResponse->assertStatus(200);

        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(9, $this->variant->fresh()->stock); // Stok berkurang tepat 1 unit

        // 4. Admin fulfills order: allocates IMEI & inputs courier tracking number
        $adminOrderResponse = $this->actingAs($this->admin)->get(route('admin.orders.show', $order->order_number));
        $adminOrderResponse->assertStatus(200);

        $shippingUpdateResponse = $this->actingAs($this->admin)
            ->patch(route('admin.orders.shipping', $order->order_number), [
                'shipping_courier' => 'JNE Express',
                'shipping_tracking_number' => 'JNE1234567890ID',
                'allocated_imei' => [
                    $orderItem->id => '352984110022334',
                ],
            ]);

        $shippingUpdateResponse->assertSessionHas('success');
        $order->refresh();
        $this->assertEquals('shipped', $order->status);
        $this->assertEquals('JNE1234567890ID', $order->shipping_tracking_number);
        $this->assertEquals('352984110022334', $orderItem->fresh()->allocated_imei);

        // 5. Customer views tracking and downloads invoice
        $customerInvoiceResponse = $this->actingAs($this->customer)
            ->get(route('orders.invoice.preview', $order->order_number));
        $customerInvoiceResponse->assertStatus(200);

        // 6. Owner audits business performance on Executive Dashboard & Financial Reports
        $ownerDashboardResponse = $this->actingAs($this->owner)->get(route('owner.dashboard'));
        $ownerDashboardResponse->assertStatus(200);
        $ownerDashboardResponse->assertSee($order->order_number);

        // Verify calculation of Revenue, COGS, and Net Profit
        $analytics = app(FinancialAnalyticsService::class);
        $summary = $analytics->calculatePeriodFinancials();

        $this->assertEquals(1, $summary['orders_count']);
        $this->assertEquals(1, $summary['units_sold']);
        $this->assertEquals(20999000.00, $summary['revenue']);
        $this->assertEquals(17500000.00, $summary['cogs']);
        $this->assertEquals(3499000.00, $summary['net_profit']); // 20.999.000 - 17.500.000
    }

    /**
     * Test Complete Workflow 2:
     * Customer Trade-In Voucher -> Admin Inspection & Approval -> Customer Uses Voucher at Checkout -> Owner Cash Flow Reconciliation.
     */
    public function test_complete_trade_in_voucher_lifecycle_and_checkout_discount(): void
    {
        // 1. Customer submits trade-in with voucher payout
        $tradeInResponse = $this->actingAs($this->customer)->post(route('trade-in.store'), [
            'phone_model' => 'iPhone 13',
            'storage_capacity' => '128GB',
            'battery_health' => 88,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal / Mulus',
            'accessories_status' => 'Fullset Box Original',
            'imei' => '351234567890123',
            'payout_method' => 'voucher',
        ]);

        $tradeInResponse->assertRedirect();
        $tradeIn = TradeIn::where('user_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($tradeIn);
        $this->assertEquals('submitted', $tradeIn->status);
        $this->assertTrue($tradeIn->isVoucherPayout());
        $voucherCode = $tradeIn->customer_account_number;
        $this->assertStringStartsWith('VCH-', $voucherCode);

        // 2. Admin inspects and approves the unit with a final price of Rp 6.000.000
        $this->actingAs($this->admin)->put(route('admin.trade-ins.inspect', $tradeIn->trade_in_code), [
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal / Mulus',
            'battery_health' => 88,
            'accessories_status' => 'Fullset Box Original',
            'status' => 'inspected',
            'final_price' => 6000000.00,
            'admin_notes' => 'Unit diverifikasi teknisi: mulus & TrueTone aktif.',
        ]);

        $this->actingAs($this->admin)->patch(route('admin.trade-ins.decision', $tradeIn->trade_in_code), [
            'decision' => 'approved',
            'final_price' => 6000000.00,
        ]);

        $tradeIn->refresh();
        $this->assertEquals('approved', $tradeIn->status);
        $this->assertEquals(6000000.00, (float) $tradeIn->final_price);

        // 3. Customer checks out an iPhone 16 Pro using the voucher code
        $cartData = [
            $this->variant->id => [
                'variant_id' => $this->variant->id,
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'product_slug' => $this->product->slug,
                'storage_capacity' => $this->variant->storage_capacity,
                'color_name' => $this->variant->color_name,
                'color_hex' => $this->variant->color_hex,
                'price' => (float) $this->variant->selling_price, // 20.999.000
                'cost_price' => (float) $this->variant->cost_price, // 17.500.000
                'quantity' => 1,
                'stock' => $this->variant->stock,
                'condition' => 'new',
                'grade' => null,
                'warranty_type' => 'resmi_ibox',
                'battery_health' => 100,
                'imei' => $this->variant->imei,
                'image' => null,
            ],
        ];

        $checkoutResponse = $this->actingAs($this->customer)
            ->withSession(['cart' => $cartData, 'cart_count' => 1])
            ->post(route('checkout.process'), [
                'recipient_name' => 'Budi Customer',
                'recipient_phone' => '081234567890',
                'shipping_address' => 'Jl. Sudirman No 10, Jakarta Pusat',
                'shipping_courier' => 'SiCepat Ekspres (BEST)',
                'voucher_code' => $voucherCode,
            ]);

        $checkoutResponse->assertRedirect();

        $order = Order::where('user_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(6000000.00, (float) $order->discount_amount);
        $this->assertEquals(14999000.00, (float) $order->grand_total); // 20.999.000 - 6.000.000

        // Verify TradeIn is linked and marked paid_out
        $tradeIn->refresh();
        $this->assertEquals($order->id, $tradeIn->target_order_id);
        $this->assertEquals('paid_out', $tradeIn->status);

        // 4. Owner audits cash flow
        $analytics = app(FinancialAnalyticsService::class);
        $cashFlow = $analytics->getCashFlowSummary();

        $this->assertEquals(1, $cashFlow['trade_in_units_in']);
        $this->assertEquals(6000000.00, $cashFlow['trade_in_voucher_out']);
        $this->assertEquals(0.00, $cashFlow['trade_in_cash_out']); // Karena voucher, bukan transfer tunai
    }

    /**
     * Test Role Security Boundaries:
     * Strict isolation of Customer, Admin, and Owner access.
     */
    public function test_strict_role_security_and_privilege_boundaries(): void
    {
        // 1. Unauthenticated guests are redirected to their respective login portals
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('owner.dashboard'))->assertRedirect(route('owner.login'));

        // 2. Authenticated Customer forbidden from accessing Admin and Owner consoles (redirected to storefront home with error)
        $this->actingAs($this->customer)->get(route('admin.dashboard'))->assertRedirect('/')->assertSessionHas('error');
        $this->actingAs($this->customer)->get(route('owner.dashboard'))->assertRedirect('/')->assertSessionHas('error');
        $this->actingAs($this->customer)->get(route('owner.financial.report'))->assertRedirect('/')->assertSessionHas('error');
        $this->actingAs($this->customer)->get(route('owner.staff.index'))->assertRedirect('/')->assertSessionHas('error');

        // 3. Authenticated Admin cannot access Owner exclusive workspace (redirected to Admin dashboard with error)
        $this->actingAs($this->admin)->get(route('owner.dashboard'))->assertRedirect(route('admin.dashboard'))->assertSessionHas('error');
        $this->actingAs($this->admin)->get(route('owner.financial.report'))->assertRedirect(route('admin.dashboard'))->assertSessionHas('error');
        $this->actingAs($this->admin)->get(route('owner.staff.index'))->assertRedirect(route('admin.dashboard'))->assertSessionHas('error');

        // 4. Admin can access Admin console
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertStatus(200);

        // 5. Owner has full access to both Owner and Admin consoles
        $this->actingAs($this->owner)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('owner.dashboard'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('owner.financial.report'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('owner.staff.index'))->assertStatus(200);

        // 6. Owner cannot delete their own account
        $deleteSelfResponse = $this->actingAs($this->owner)->delete(route('owner.staff.destroy', $this->owner->id));
        $deleteSelfResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->owner->id]);
    }
}
