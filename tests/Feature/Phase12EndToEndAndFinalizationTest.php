<?php

namespace Tests\Feature;

use App\Mail\OrderInvoiceMail;
use App\Mail\OrderShippedMail;
use App\Mail\PaymentConfirmedMail;
use App\Mail\TradeInStatusMail;
use App\Models\Category;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\TradeIn;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase12EndToEndAndFinalizationTest extends TestCase
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

        $suffix = strtolower(Str::random(5));

        $this->customer = User::create([
            'name' => 'Prilly Latuconsina',
            'email' => "prilly.p12.{$suffix}@example.com",
            'password' => bcrypt('password123'),
            'phone_number' => '081299887766',
            'address' => 'Jl. Kemang Raya No. 88, Jakarta Selatan',
            'city' => 'Jakarta Selatan',
            'postal_code' => '12730',
            'role' => 'customer',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Operasional P12',
            'email' => "admin.p12.{$suffix}@rap-iphone.com",
            'password' => bcrypt('password123'),
            'phone_number' => '081122334455',
            'role' => 'admin',
        ]);

        $this->owner = User::create([
            'name' => 'Owner Eksekutif P12',
            'email' => "owner.p12.{$suffix}@rap-iphone.com",
            'password' => bcrypt('password123'),
            'phone_number' => '081100998877',
            'role' => 'owner',
        ]);

        $category = Category::firstOrCreate(
            ['slug' => 'iphone-16-series'],
            ['name' => 'iPhone 16 Series', 'icon' => 'smartphone']
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'iPhone 16 Pro Max Flagship ' . strtoupper($suffix),
            'slug' => 'iphone-16-pro-max-p12-' . $suffix,
            'series' => 'iPhone 16 Series',
            'condition' => 'new',
            'warranty_type' => 'resmi_ibox',
            'base_price' => 24999000.00,
            'description' => 'Unit BNIB Garansi Resmi Apple Indonesia (iBox / TAM).',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'storage_capacity' => '256GB',
            'color_name' => 'Desert Titanium',
            'color_hex' => '#C5B49F',
            'cost_price' => 22500000.00,
            'selling_price' => 24999000.00,
            'stock' => 10,
            'sku' => 'IP16PM-DT-256-' . strtoupper($suffix),
            'imei' => '358877665544332',
        ]);

        ProductImage::create([
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'image_path' => 'images/products/iphone-16-pro-max.webp',
            'is_primary' => true,
            'order_index' => 1,
        ]);
    }

    /**
     * 12.1.1 & 12.1.2 & 12.1.3:
     * Simulasi checkout end-to-end dengan Sandbox Midtrans (QRIS), perubahan status otomatis via webhook,
     * pengurangan stok, pengiriman WhatsApp Fonnte & Email SMTP berlampiran PDF, serta unduh nota.
     */
    public function test_e2e_checkout_with_midtrans_sandbox_qris_and_auto_status_transition_and_notifications(): void
    {
        config(['services.fonnte.token' => 'fonnte_sandbox_e2e_token']);
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true, 'detail' => 'queued'], 200),
        ]);
        Mail::fake();

        // 1. Customer adds iPhone variant to cart
        $addCartResponse = $this->actingAs($this->customer)->postJson(route('cart.add'), [
            'product_variant_id' => $this->variant->id,
            'quantity' => 2,
        ]);
        $addCartResponse->assertOk()->assertJson(['success' => true, 'cart_count' => 2]);

        // 2. Customer submits checkout
        $checkoutResponse = $this->actingAs($this->customer)->postJson(route('checkout.process'), [
            'recipient_name' => 'Prilly Latuconsina',
            'recipient_phone' => '0812-9988-7766',
            'shipping_address' => 'Jl. Kemang Raya No. 88, Jakarta Selatan 12730',
            'shipping_courier' => 'JNE Express (Reguler / YES)',
            'customer_notes' => 'Mohon packing kayu dan asuransi penuh.',
        ]);

        $checkoutResponse->assertOk()->assertJson(['success' => true]);
        $orderNumber = $checkoutResponse->json('order_number');
        $this->assertNotEmpty($orderNumber);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(49998000.00, (float) $order->grand_total);

        // Verify initial order_created notifications (Fonnte WA + OrderInvoiceMail)
        Mail::assertSent(OrderInvoiceMail::class, 1);
        $this->assertDatabaseHas('notifications_log', [
            'channel' => 'whatsapp',
            'recipient' => '6281299887766',
            'event_name' => 'order_created',
            'status' => 'sent',
        ]);

        // 3. Simulate QRIS Sandbox Payment on Checkout Success page
        $simulateResponse = $this->actingAs($this->customer)->postJson(
            route('checkout.simulate', $order->order_number),
            [
                'payment_method' => 'qris',
                'simulation_status' => 'settlement',
            ]
        );

        $simulateResponse->assertOk()->assertJson([
            'success' => true,
            'order_number' => $order->order_number,
            'order_status' => 'paid',
            'payment_type' => 'qris',
        ]);

        // 4. Verify Order status is now PAID, Payment is recorded, and Variant stock decremented from 10 -> 8
        $order->refresh();
        $this->variant->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(8, $this->variant->stock);
        $this->assertEquals('qris', $order->payment->payment_type);
        $this->assertEquals('settlement', $order->payment->transaction_status);

        // 5. Verify PaymentConfirmedMail (with PDF Invoice attachment) & WhatsApp Fonnte sent
        Mail::assertSent(PaymentConfirmedMail::class, function (PaymentConfirmedMail $mail) use ($order) {
            return $mail->order->id === $order->id && count($mail->attachments()) === 1;
        });

        $this->assertDatabaseHas('notifications_log', [
            'channel' => 'whatsapp',
            'recipient' => '6281299887766',
            'event_name' => 'payment_success',
            'status' => 'sent',
        ]);

        // 6. Verify Customer can download the official A4 PDF Invoice
        $pdfResponse = $this->actingAs($this->customer)->get(route('orders.invoice.download', $order->order_number));
        $pdfResponse->assertOk();
        $this->assertStringStartsWith('%PDF-', $pdfResponse->getContent());
    }

    /**
     * 12.1.1 & 12.1.2:
     * Simulasi checkout dengan Sandbox Midtrans Virtual Account (BCA VA: pending -> settlement)
     * serta pengujian idempotensi webhook.
     */
    public function test_e2e_checkout_with_midtrans_sandbox_virtual_account_and_webhook_idempotency(): void
    {
        Mail::fake();

        // 1. Buy Now flow -> Cart -> Checkout
        $this->actingAs($this->customer)->post(route('cart.buyNow'), [
            'product_variant_id' => $this->variant->id,
            'quantity' => 1,
        ])->assertRedirect(route('cart.index'));

        $checkoutResp = $this->actingAs($this->customer)->postJson(route('checkout.process'), [
            'recipient_name' => 'Prilly Latuconsina',
            'recipient_phone' => '081299887766',
            'shipping_address' => 'Jl. Kemang Raya No. 88, Jakarta Selatan',
            'shipping_courier' => 'SiCepat Ekspres (BEST)',
        ]);

        $orderNumber = $checkoutResp->json('order_number');
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // 2. Simulate BCA Virtual Account issuance (pending)
        $pendingVaResp = $this->actingAs($this->customer)->postJson(
            route('checkout.simulate', $order->order_number),
            [
                'payment_method' => 'bca_va',
                'simulation_status' => 'pending',
            ]
        );
        $pendingVaResp->assertOk()->assertJson([
            'order_status' => 'pending',
            'payment_type' => 'bank_transfer',
        ]);

        $order->refresh();
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('bank_transfer', $order->payment->payment_type);
        $this->assertEquals('bca', $order->payment->raw_payload['va_numbers'][0]['bank'] ?? null);

        // 3. Simulate BCA Virtual Account payment completion (settlement)
        $settleVaResp = $this->actingAs($this->customer)->postJson(
            route('checkout.simulate', $order->order_number),
            [
                'payment_method' => 'bca_va',
                'simulation_status' => 'settlement',
            ]
        );
        $settleVaResp->assertOk()->assertJson([
            'order_status' => 'paid',
            'payment_type' => 'bank_transfer',
        ]);

        $order->refresh();
        $this->variant->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(9, $this->variant->stock);

        // 4. Duplicate settlement webhook call must be idempotent (stock stays 9)
        $duplicateResp = $this->actingAs($this->customer)->postJson(
            route('checkout.simulate', $order->order_number),
            [
                'payment_method' => 'bca_va',
                'simulation_status' => 'settlement',
            ]
        );
        $duplicateResp->assertOk();
        $this->variant->refresh();
        $this->assertEquals(9, $this->variant->stock);
        Mail::assertSent(PaymentConfirmedMail::class, 1);
    }

    /**
     * 12.1.3:
     * Pengujian alur pengiriman kurir oleh Admin, pemicuan WhatsApp Fonnte & Email Resi,
     * serta pelacakan pesanan & cek IMEI oleh pelanggan.
     */
    public function test_e2e_admin_shipping_tracking_triggers_whatsapp_fonnte_and_smtp_email(): void
    {
        config(['services.fonnte.token' => 'fonnte_ship_token_12']);
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);
        Mail::fake();

        $order = Order::create([
            'order_number' => 'RAP-20261028-SHP12',
            'user_id' => $this->customer->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 24999000.00,
            'status' => 'paid',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jl. Kemang Raya No. 88, Jakarta Selatan',
            'recipient_name' => 'Prilly Latuconsina',
            'recipient_phone' => '081299887766',
        ]);

        $orderItem = $order->items()->create([
            'product_variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'variant_details' => 'Desert Titanium / 256GB',
            'price' => 24999000.00,
            'cost_price' => 22500000.00,
            'quantity' => 1,
            'subtotal' => 24999000.00,
        ]);

        // Admin inputs tracking number & physical IMEI
        $shipResponse = $this->actingAs($this->admin)->patch(
            route('admin.orders.shipping', $order->order_number),
            [
                'shipping_courier' => 'JNE Express',
                'shipping_tracking_number' => 'JNE-8899001122ID',
                'allocated_imei' => [
                    $orderItem->id => '358877665544332',
                ],
            ]
        );

        $shipResponse->assertRedirect();
        $order->refresh();
        $orderItem->refresh();

        $this->assertEquals('shipped', $order->status);
        $this->assertEquals('JNE-8899001122ID', $order->shipping_tracking_number);
        $this->assertEquals('358877665544332', $orderItem->allocated_imei);

        Mail::assertSent(OrderShippedMail::class, 1);
        $this->assertDatabaseHas('notifications_log', [
            'channel' => 'whatsapp',
            'recipient' => '6281299887766',
            'event_name' => 'order_shipped',
            'status' => 'sent',
        ]);

        // Customer tracks order on /lacak-pesanan
        $trackPage = $this->get(route('orders.track', ['order_number' => $order->order_number]));
        $trackPage->assertOk();
        $trackPage->assertSee('JNE-8899001122ID');

        // Customer verifies IMEI warranty on /cek-garansi
        $warrantyPage = $this->get(route('warranty.check', ['imei' => '358877665544332']));
        $warrantyPage->assertOk();
        $warrantyPage->assertSee('Unit Terdaftar & Terverifikasi Resmi', false);
        $warrantyPage->assertSee('358877665544332');
    }

    /**
     * 12.1.4:
     * Pengujian flow tukar tambah (Trade-In) end-to-end dari kalkulasi & submit pelanggan,
     * inspeksi teknisi admin, persetujuan harga final, pencairan dana, hingga analitik Owner.
     */
    public function test_e2e_trade_in_flow_from_customer_submit_to_admin_inspection_approval_and_owner_analytics(): void
    {
        config(['services.fonnte.token' => 'fonnte_ti_token_12']);
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);
        Mail::fake();

        // 1. Customer calculates instant estimate via AJAX
        $calcResponse = $this->postJson(route('trade-in.calculate'), [
            'phone_model' => 'iPhone 15 Pro Max',
            'storage_capacity' => '256GB',
            'battery_health' => 94,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
        ]);
        $calcResponse->assertOk()->assertJson(['success' => true]);
        $estimatedPrice = $calcResponse->json('breakdown.estimated_price');
        $this->assertEquals(14500000.0, (float) $estimatedPrice);

        // 2. Customer submits Trade-In application
        $submitResponse = $this->actingAs($this->customer)->post(route('trade-in.store'), [
            'phone_model' => 'iPhone 15 Pro Max',
            'storage_capacity' => '256GB',
            'battery_health' => 94,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'imei' => '354433221100998',
            'payout_method' => 'bank_transfer',
            'customer_bank_name' => 'BCA (Bank Central Asia)',
            'customer_account_number' => '5210998877',
            'customer_account_holder' => 'Prilly Latuconsina',
        ]);

        $tradeIn = TradeIn::where('user_id', $this->customer->id)->latest()->firstOrFail();
        $submitResponse->assertRedirect(route('trade-in.show', $tradeIn->trade_in_code));
        $this->assertEquals('submitted', $tradeIn->status);

        // 3. Admin views Trade-In queue & performs physical inspection
        $this->actingAs($this->admin)
            ->get(route('admin.trade-ins.index'))
            ->assertOk()
            ->assertSee($tradeIn->trade_in_code);

        $inspectResponse = $this->actingAs($this->admin)->put(
            route('admin.trade-ins.inspect', $tradeIn->trade_in_code),
            [
                'physical_condition' => 'Mulus (Grade A)',
                'screen_condition' => 'Normal',
                'battery_health' => 92,
                'accessories_status' => 'Fullset Box Original',
                'imei' => '354433221100998',
                'status' => 'inspected',
                'final_price' => 13900000,
                'admin_notes' => 'Lulus uji 32 titik QC, TrueTone & Face ID normal 100%.',
            ]
        );
        $inspectResponse->assertRedirect(route('admin.trade-ins.show', $tradeIn->trade_in_code));
        $tradeIn->refresh();
        $this->assertEquals('inspected', $tradeIn->status);
        $this->assertEquals(13900000.00, (float) $tradeIn->final_price);

        // 4. Admin approves the final offer price
        $approveResponse = $this->actingAs($this->admin)->patch(
            route('admin.trade-ins.decision', $tradeIn->trade_in_code),
            [
                'decision' => 'approved',
                'final_price' => 13900000,
                'admin_notes' => 'Disetujui pelanggan, proses pencairan ke BCA.',
            ]
        );
        $approveResponse->assertRedirect(route('admin.trade-ins.show', $tradeIn->trade_in_code));
        $tradeIn->refresh();
        $this->assertEquals('approved', $tradeIn->status);

        // 5. Admin marks payout as Paid Out
        $paidOutResponse = $this->actingAs($this->admin)->patch(
            route('admin.trade-ins.decision', $tradeIn->trade_in_code),
            [
                'decision' => 'paid_out',
                'final_price' => 13900000,
                'admin_notes' => 'Dana Rp 13.900.000 telah ditransfer ke rekening BCA pelanggan.',
            ]
        );
        $paidOutResponse->assertRedirect(route('admin.trade-ins.show', $tradeIn->trade_in_code));
        $tradeIn->refresh();
        $this->assertEquals('paid_out', $tradeIn->status);

        // Verify TradeInStatusMail was sent at each milestone (submit, inspect, approve, paid_out)
        Mail::assertSent(TradeInStatusMail::class, 4);

        // 6. Customer sees updated Paid Out status & final offer on detail page
        $customerDetail = $this->actingAs($this->customer)->get(route('trade-in.show', $tradeIn->trade_in_code));
        $customerDetail->assertOk();
        $customerDetail->assertSee('13.900.000');

        // 7. Owner Executive Dashboard reflects Trade-In outflow
        $ownerDashboard = $this->actingAs($this->owner)->get(route('owner.dashboard'));
        $ownerDashboard->assertOk();
        $ownerDashboard->assertSee('Portal Finansial & Analitik Laba');
        $ownerDashboard->assertSee('Rp 13.900.000');
    }

    /**
     * 12.2.1 & 12.2.2:
     * Validasi UI/UX Glassmorphism, responsivitas (Mobile Drawer & Admin Sidebar),
     * kecepatan halaman (< 2 detik), aset gambar WebP + lazy loading, dan Security Headers.
     */
    public function test_ui_ux_glassmorphism_responsiveness_security_headers_and_webp_performance(): void
    {
        // 1. Verify all 6 WebP product images exist on disk and have valid RIFF..WEBP binary header
        $expectedWebpFiles = [
            'images/products/iphone-16-pro-max.webp',
            'images/products/iphone-16-pro.webp',
            'images/products/iphone-16.webp',
            'images/products/iphone-15-pro.webp',
            'images/products/iphone-14.webp',
            'images/products/iphone-13.webp',
        ];

        foreach ($expectedWebpFiles as $relPath) {
            $fullPath = public_path($relPath);
            $this->assertFileExists($fullPath, "File WebP {$relPath} harus tersedia di public/");
            $bytes = file_get_contents($fullPath);
            $this->assertNotFalse($bytes);
            $this->assertEquals('RIFF', substr($bytes, 0, 4), "Header RIFF tidak valid pada {$relPath}");
            $this->assertEquals('WEBP', substr($bytes, 8, 4), "Format WEBP tidak valid pada {$relPath}");
            $this->assertLessThan(102400, strlen($bytes), "Ukuran file WebP {$relPath} harus ringan (< 100 KB)");
        }

        // 2. Measure page speed & verify Glassmorphism + WebP + lazy loading on Storefront pages
        $startTime = microtime(true);
        $homeResp = $this->get(route('home'));
        $homeElapsedMs = (microtime(true) - $startTime) * 1000;

        $homeResp->assertOk();
        $this->assertLessThan(2000, $homeElapsedMs, "Waktu muat halaman utama ({$homeElapsedMs}ms) harus di bawah 2000ms (PRD 5.2)");

        // Assert Security Headers (PRD 5.1)
        $homeResp->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $homeResp->assertHeader('X-Content-Type-Options', 'nosniff');
        $homeResp->assertHeader('X-XSS-Protection', '1; mode=block');
        $homeResp->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Assert Glassmorphism & Responsive Mobile Navigation Elements
        $homeHtml = $homeResp->getContent();
        $this->assertStringContainsString('glass-navbar', $homeHtml);
        $this->assertStringContainsString('glass-card', $homeHtml);
        $this->assertStringContainsString('id="mobile-nav-toggle"', $homeHtml);
        $this->assertStringContainsString('id="mobile-nav-drawer"', $homeHtml);
        $this->assertStringContainsString('.webp', $homeHtml);
        $this->assertStringContainsString('loading="lazy"', $homeHtml);
        $this->assertStringContainsString('decoding="async"', $homeHtml);

        // 3. Check PDP page speed & WebP hero image attributes
        $pdpStart = microtime(true);
        $pdpResp = $this->get(route('catalog.show', $this->product->slug));
        $pdpElapsedMs = (microtime(true) - $pdpStart) * 1000;

        $pdpResp->assertOk();
        $this->assertLessThan(2000, $pdpElapsedMs);
        $pdpResp->assertSee('images/products/iphone-16-pro-max.webp', false);
        $pdpResp->assertSee('fetchpriority="high"', false);

        // 4. Verify all storefront utility & admin settings routes return 200 OK (zero 404 links)
        $this->get(route('warranty.check'))->assertOk()->assertSee('Cek Status Garansi & IMEI', false);
        $this->get(route('orders.track'))->assertOk()->assertSee('Lacak Status Pesanan Anda', false);
        $this->get(route('warranty.terms'))->assertOk()->assertSee('Syarat & Ketentuan Garansi', false);
        $this->actingAs($this->admin)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Konfigurasi Sistem & Integrasi API', false)
            ->assertSee('id="admin-sidebar-backdrop"', false);
    }
}
