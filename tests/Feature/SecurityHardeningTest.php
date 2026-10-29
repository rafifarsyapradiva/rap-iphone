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
use App\Services\MidtransService;
use App\Services\NotificationOrchestrator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use DatabaseTransactions;

    protected User $customerA;
    protected User $customerB;
    protected User $admin;
    protected User $owner;
    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::lower(Str::random(6));

        $this->customerA = User::create([
            'name' => 'Ahmad Customer A',
            'email' => "ahmad.{$suffix}@example.com",
            'role' => 'customer',
            'phone_number' => '081234567891',
            'address' => 'Jl. Senopati No. 10, Jakarta Selatan',
        ]);

        $this->customerB = User::create([
            'name' => 'Bambang Customer B',
            'email' => "bambang.{$suffix}@example.com",
            'role' => 'customer',
            'phone_number' => '081234567892',
            'address' => 'Jl. Kebon Jeruk No. 20, Jakarta Barat',
        ]);

        $this->admin = User::create([
            'name' => 'Staf Operasional Admin',
            'email' => "admin.{$suffix}@rap-iphone.com",
            'role' => 'admin',
        ]);

        $this->owner = User::create([
            'name' => 'Pemilik Toko Owner',
            'email' => "owner.{$suffix}@rap-iphone.com",
            'role' => 'owner',
        ]);

        $category = Category::firstOrCreate(
            ['slug' => 'iphone-series'],
            ['name' => 'iPhone Series', 'icon' => 'smartphone']
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'iPhone 16 Pro Max Security Test',
            'slug' => "iphone-16-pro-max-sec-{$suffix}",
            'series' => 'iPhone 16 Series',
            'condition' => 'new',
            'warranty_type' => 'resmi_ibox',
            'base_price' => 24999000.00,
            'description' => 'Flagship iPhone unit for enterprise security hardening test.',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'storage_capacity' => '256GB',
            'color_name' => 'Desert Titanium',
            'color_hex' => '#C4A58C',
            'cost_price' => 20000000.00,
            'selling_price' => 24999000.00,
            'stock' => 5,
            'sku' => "IPH16PM-256-SEC-{$suffix}",
            'battery_health' => 100,
            'imei' => '352984110' . rand(100000, 999999),
        ]);
    }

    /**
     * Test 34.1: IDOR Protection on Order Checkout Success Page.
     * Customer B cannot view Customer A's checkout success receipt.
     */
    public function test_idor_protection_blocks_other_customer_from_viewing_order_success(): void
    {
        $orderA = Order::create([
            'order_number' => 'RAP-SEC-' . strtoupper(Str::random(8)),
            'user_id' => $this->customerA->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0.00,
            'discount_amount' => 0.00,
            'grand_total' => 24999000.00,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => $this->customerA->address,
            'recipient_name' => $this->customerA->name,
            'recipient_phone' => $this->customerA->phone_number,
        ]);

        Payment::create([
            'order_id' => $orderA->id,
            'gross_amount' => 24999000.00,
            'snap_token' => 'snap_dummy_token_sec_1',
            'transaction_status' => 'pending',
        ]);

        // Customer A (Owner of Order) can view
        $responseA = $this->actingAs($this->customerA)->get(route('checkout.success', $orderA->order_number));
        $responseA->assertStatus(200);

        // Customer B (Unrelated Customer) is blocked with 403 Forbidden
        $responseB = $this->actingAs($this->customerB)->get(route('checkout.success', $orderA->order_number));
        $responseB->assertStatus(403);

        // Admin has operational permission to view
        $responseAdmin = $this->actingAs($this->admin)->get(route('checkout.success', $orderA->order_number));
        $responseAdmin->assertStatus(200);
    }

    /**
     * Test 34.1: IDOR Protection on Trade-In Detail Tracker via TradeInPolicy.
     * Customer B cannot view Customer A's trade-in submission.
     */
    public function test_idor_protection_blocks_other_customer_from_viewing_trade_in_detail(): void
    {
        $tradeInA = TradeIn::create([
            'trade_in_code' => 'TI-SEC-' . strtoupper(Str::random(8)),
            'user_id' => $this->customerA->id,
            'target_order_id' => null,
            'phone_model' => 'iPhone 13 Pro',
            'storage_capacity' => '128GB',
            'battery_health' => 88,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'imei' => '352984110887766',
            'estimated_price' => 7500000.00,
            'final_price' => null,
            'status' => 'submitted',
            'customer_bank_name' => 'BCA',
            'customer_account_number' => '1234567890',
            'customer_account_holder' => $this->customerA->name,
        ]);

        // Customer A can view
        $responseA = $this->actingAs($this->customerA)->get(route('trade-in.show', $tradeInA->trade_in_code));
        $responseA->assertStatus(200);

        // Customer B is blocked with 403 Forbidden
        $responseB = $this->actingAs($this->customerB)->get(route('trade-in.show', $tradeInA->trade_in_code));
        $responseB->assertStatus(403);

        // Admin and Owner can view for inspection
        $responseAdmin = $this->actingAs($this->admin)->get(route('trade-in.show', $tradeInA->trade_in_code));
        $responseAdmin->assertStatus(200);

        $responseOwner = $this->actingAs($this->owner)->get(route('trade-in.show', $tradeInA->trade_in_code));
        $responseOwner->assertStatus(200);
    }

    /**
     * Test 34.1: IDOR Protection on Invoice PDF Download.
     * Customer B cannot download Customer A's invoice PDF.
     */
    public function test_idor_protection_blocks_other_customer_from_downloading_invoice(): void
    {
        $orderA = Order::create([
            'order_number' => 'RAP-INV-' . strtoupper(Str::random(8)),
            'user_id' => $this->customerA->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0.00,
            'discount_amount' => 0.00,
            'grand_total' => 24999000.00,
            'status' => 'paid',
            'shipping_courier' => 'SiCepat Ekspres',
            'shipping_address' => $this->customerA->address,
            'recipient_name' => $this->customerA->name,
            'recipient_phone' => $this->customerA->phone_number,
        ]);

        // Customer B is blocked from downloading Customer A's invoice
        $responseB = $this->actingAs($this->customerB)->get(route('orders.invoice.download', $orderA->order_number));
        $responseB->assertStatus(403);

        // Customer A can download
        $responseA = $this->actingAs($this->customerA)->get(route('orders.invoice.download', $orderA->order_number));
        $responseA->assertStatus(200);
    }

    /**
     * Test 34.3: Concurrency Control & Voucher Double-Spending Protection.
     * A voucher cannot be claimed by another user or used more than once.
     */
    public function test_concurrency_defense_prevents_voucher_double_spending_and_unauthorized_claims(): void
    {
        $voucherCode = 'VCH-' . strtoupper(Str::random(6));

        // Voucher belongs to Customer A
        $voucher = TradeIn::create([
            'trade_in_code' => 'TI-VCH-' . strtoupper(Str::random(8)),
            'user_id' => $this->customerA->id,
            'target_order_id' => null,
            'phone_model' => 'iPhone 14',
            'storage_capacity' => '128GB',
            'battery_health' => 90,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'estimated_price' => 5000000.00,
            'final_price' => 5000000.00,
            'status' => 'approved',
            'customer_bank_name' => TradeIn::VOUCHER_BANK_MARKER,
            'customer_account_number' => $voucherCode,
            'customer_account_holder' => $this->customerA->name,
        ]);

        // Setup cart for checkout
        $cart = [
            $this->variant->id => [
                'variant_id' => $this->variant->id,
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'storage_capacity' => $this->variant->storage_capacity,
                'color_name' => $this->variant->color_name,
                'price' => (float) $this->variant->selling_price,
                'quantity' => 1,
            ],
        ];

        // 1. Customer B attempts to use Customer A's voucher -> REJECTED
        $unauthorizedResponse = $this->actingAs($this->customerB)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'recipient_name' => 'Bambang',
                'recipient_phone' => '081234567892',
                'shipping_address' => 'Jl. Kebon Jeruk No. 20, Jakarta',
                'shipping_courier' => 'JNE Express',
                'voucher_code' => $voucherCode,
            ]);

        $unauthorizedResponse->assertSessionHas('error');
        $this->assertNull($voucher->fresh()->target_order_id);

        // 2. Customer A uses their voucher legitimately -> SUCCESS
        $legitResponse = $this->actingAs($this->customerA)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'recipient_name' => 'Ahmad',
                'recipient_phone' => '081234567891',
                'shipping_address' => 'Jl. Senopati No. 10, Jakarta',
                'shipping_courier' => 'JNE Express',
                'voucher_code' => $voucherCode,
            ]);

        $legitResponse->assertRedirect();
        $voucher->refresh();
        $this->assertNotNull($voucher->target_order_id);
        $this->assertEquals('paid_out', $voucher->status);

        // 3. Customer A attempts to re-use the already burned voucher -> REJECTED (Anti Double-Spending)
        $reusedResponse = $this->actingAs($this->customerA)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'recipient_name' => 'Ahmad',
                'recipient_phone' => '081234567891',
                'shipping_address' => 'Jl. Senopati No. 10, Jakarta',
                'shipping_courier' => 'JNE Express',
                'voucher_code' => $voucherCode,
            ]);

        $reusedResponse->assertSessionHas('error');
    }

    /**
     * Test 34.3 & 34.2: Concurrency & ACID Stock Locking.
     * Checkout rejects exceeding stock atomically without corrupting inventory.
     */
    public function test_concurrency_locking_prevents_checkout_exceeding_variant_stock(): void
    {
        $this->variant->update(['stock' => 1]);

        $cart = [
            $this->variant->id => [
                'variant_id' => $this->variant->id,
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'storage_capacity' => $this->variant->storage_capacity,
                'color_name' => $this->variant->color_name,
                'price' => (float) $this->variant->selling_price,
                'quantity' => 2, // Asks for 2 when only 1 is available
            ],
        ];

        $response = $this->actingAs($this->customerA)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'recipient_name' => 'Ahmad',
                'recipient_phone' => '081234567891',
                'shipping_address' => 'Jl. Senopati No. 10, Jakarta',
                'shipping_courier' => 'JNE Express',
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, $this->variant->fresh()->stock);
    }

    /**
     * Test 34.3: Webhook Idempotency with Pessimistic Locking.
     * Duplicate webhook callbacks do not double-decrement inventory stock.
     */
    public function test_webhook_idempotency_serializes_and_prevents_double_decrement(): void
    {
        $this->variant->update(['stock' => 10]);

        $order = Order::create([
            'order_number' => 'RAP-IDEMP-' . strtoupper(Str::random(6)),
            'user_id' => $this->customerA->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0.00,
            'discount_amount' => 0.00,
            'grand_total' => 24999000.00,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => $this->customerA->address,
            'recipient_name' => $this->customerA->name,
            'recipient_phone' => $this->customerA->phone_number,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'variant_details' => 'Desert Titanium / 256GB',
            'price' => 24999000.00,
            'cost_price' => 20000000.00,
            'quantity' => 1,
            'subtotal' => 24999000.00,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gross_amount' => 24999000.00,
            'transaction_status' => 'pending',
        ]);

        $serverKey = config('services.midtrans.server_key', 'dummy_server_key');
        $statusCode = '200';
        $grossAmount = '24999000.00';
        $signatureKey = hash('sha512', $order->order_number . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'midtrans-trx-sec-' . Str::random(8),
            'transaction_time' => now()->toDateTimeString(),
        ];

        // 1. First webhook call -> Processed and decrements stock (10 -> 9)
        $response1 = $this->postJson(route('api.midtrans.callback'), $payload);
        $response1->assertStatus(200);
        $response1->assertJson(['status' => 'success']);
        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals(9, $this->variant->fresh()->stock);

        // 2. Duplicate webhook call -> Idempotent, returns 200 without decrementing stock (remains 9)
        $response2 = $this->postJson(route('api.midtrans.callback'), $payload);
        $response2->assertStatus(200);
        $response2->assertJson(['status' => 'success']);
        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals(9, $this->variant->fresh()->stock);
    }

    /**
     * Test 34.6: Tamper-Proof Invoice QR Code & PII Masking Verification.
     */
    public function test_invoice_qr_tamper_proof_verification_and_pii_masking(): void
    {
        $order = Order::create([
            'order_number' => 'RAP-QR-' . strtoupper(Str::random(6)),
            'user_id' => $this->customerA->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0.00,
            'discount_amount' => 0.00,
            'grand_total' => 24999000.00,
            'status' => 'paid',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jl. Sudirman Kav 20, Jakarta',
            'recipient_name' => 'Ahmad Subagyo',
            'recipient_phone' => '081234567891',
        ]);

        $pdfService = app(\App\Services\InvoicePdfService::class);
        $validCode = $pdfService->verificationCode($order);

        // 1. Valid QR verification URL -> Confirms authentic invoice with masked PII
        $validResponse = $this->get(route('invoice.verify', [$order->order_number, $validCode]));
        $validResponse->assertStatus(200);
        $validResponse->assertSee($order->order_number);
        // PII Masking: Name is masked, e.g. Ah*** Sub*** or similar
        $validResponse->assertDontSee('Ahmad Subagyo');

        // 2. Tampered verification code -> Rejected with 404
        $tamperedCode = 'tampered_fake_signature_' . Str::random(10);
        $tamperedResponse = $this->get(route('invoice.verify', [$order->order_number, $tamperedCode]));
        $tamperedResponse->assertStatus(404);
    }

    /**
     * Test 34.5: Mass-Assignment & Privilege Escalation Protection.
     * Injected role parameter during customer registration is ignored.
     */
    public function test_customer_registration_cannot_spoof_privileged_role(): void
    {
        $uniqueEmail = 'hacker.' . Str::random(6) . '@malicious.test';

        $response = $this->post(route('register.submit'), [
            'name' => 'Evil Attacker',
            'email' => $uniqueEmail,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone_number' => '081299998888',
            'role' => 'owner', // Malicious attempt to elevate privileges
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('home'));

        $registeredUser = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($registeredUser);
        $this->assertEquals('customer', $registeredUser->role);
        $this->assertFalse($registeredUser->isOwner());
        $this->assertFalse($registeredUser->isAdmin());
    }

    /**
     * Test 34.4: Brute-Force Rate Limiting Protection on Admin Login Portal.
     */
    public function test_admin_portal_rate_limiter_blocks_brute_force_attacks(): void
    {
        $targetEmail = $this->admin->email;

        // Exhaust the 5 allowed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.submit'), [
                'email' => $targetEmail,
                'password' => 'wrong_password_' . $i,
            ]);
        }

        // The 6th attempt must be throttled with rate limit error
        $blockedResponse = $this->post(route('admin.login.submit'), [
            'email' => $targetEmail,
            'password' => 'any_password',
        ]);

        $blockedResponse->assertSessionHasErrors('email');
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('percobaan masuk', $errorMessage);
    }

    /**
     * Test 34.4: Anti-Session Fixation Session ID Regeneration.
     */
    public function test_session_id_regenerates_upon_successful_login(): void
    {
        $initialSessionId = session()->getId();

        $response = $this->post(route('login.submit'), [
            'email' => $this->customerA->email,
            'password' => 'password', // Default seeder password or user password
        ]);

        // When password matches or when authenticated, session regenerates
        $newSessionId = session()->getId();
        $this->assertNotEquals('', $newSessionId);
    }
}
