<?php

namespace Tests\Feature;

use App\Mail\OrderInvoiceMail;
use App\Mail\OrderShippedMail;
use App\Mail\PaymentConfirmedMail;
use App\Mail\TradeInStatusMail;
use App\Models\Category;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TradeIn;
use App\Models\User;
use App\Services\FonnteService;
use App\Services\InvoicePdfService;
use App\Services\NotificationOrchestrator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase8NotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;
    protected User $admin;
    protected Product $product;
    protected ProductVariant $variant;
    protected Order $order;
    protected TradeIn $tradeIn;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(5);

        $this->customer = User::create([
            'name' => 'Reza Rahadian',
            'email' => "reza.{$suffix}@example.com",
            'phone_number' => '081288990011',
            'address' => 'Jl. Senopati No. 10, Jakarta Selatan',
            'role' => 'customer',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Operasional',
            'email' => "admin.{$suffix}@rap-iphone.com",
            'phone_number' => '081199887766',
            'role' => 'admin',
        ]);

        $category = Category::firstOrCreate(
            ['slug' => 'iphone-16-series'],
            ['name' => 'iPhone 16 Series', 'icon' => 'smartphone']
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'iPhone 16 Pro',
            'slug' => 'iphone-16-pro-notif-' . $suffix,
            'series' => 'iPhone 16 Series',
            'condition' => 'new',
            'warranty_type' => 'resmi_ibox',
            'base_price' => 21999000.00,
            'description' => 'Apple A18 Pro chip.',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'storage_capacity' => '256GB',
            'color_name' => 'Natural Titanium',
            'color_hex' => '#9A958E',
            'cost_price' => 19000000.00,
            'selling_price' => 21999000.00,
            'stock' => 10,
            'sku' => 'IP16P-256-NT-' . $suffix,
            'imei' => '359988776655443',
        ]);

        $this->order = Order::create([
            'order_number' => 'RAP-20261028-' . strtoupper($suffix),
            'user_id' => $this->customer->id,
            'total_items_price' => 21999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 21999000.00,
            'status' => 'pending',
            'shipping_courier' => 'JNE Express (Reguler / YES)',
            'shipping_address' => 'Jl. Senopati No. 10, Jakarta Selatan',
            'recipient_name' => 'Reza Rahadian',
            'recipient_phone' => '0812-8899-0011',
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'variant_details' => 'Natural Titanium / 256GB',
            'price' => 21999000.00,
            'cost_price' => 19000000.00,
            'quantity' => 1,
            'subtotal' => 21999000.00,
            'allocated_imei' => '359988776655443',
        ]);

        Payment::create([
            'order_id' => $this->order->id,
            'gross_amount' => 21999000.00,
            'snap_token' => 'snap_token_' . $suffix,
            'payment_type' => 'qris',
            'transaction_status' => 'pending',
        ]);

        $this->tradeIn = TradeIn::create([
            'trade_in_code' => 'TI-20261028-' . strtoupper($suffix),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 14 Pro Max',
            'storage_capacity' => '256GB',
            'battery_health' => 91,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'imei' => '351122334455660',
            'estimated_price' => 12500000.00,
            'final_price' => 12200000.00,
            'status' => 'approved',
            'customer_bank_name' => 'BCA (Bank Central Asia)',
            'customer_account_number' => '5430998877',
            'customer_account_holder' => 'Reza Rahadian',
            'admin_notes' => 'Unit sangat mulus, lulus cek 3uTools 100%.',
        ]);
    }

    public function test_fonnte_service_normalizes_various_indonesian_phone_formats(): void
    {
        $fonnte = app(FonnteService::class);

        $this->assertEquals('6281288990011', $fonnte->formatPhoneNumber('081288990011'));
        $this->assertEquals('6281288990011', $fonnte->formatPhoneNumber('+62 812-8899-0011'));
        $this->assertEquals('6281288990011', $fonnte->formatPhoneNumber('6281288990011'));
        $this->assertEquals('6281288990011', $fonnte->formatPhoneNumber('81288990011'));
        $this->assertEquals('6281288990011', $fonnte->formatPhoneNumber('006281288990011'));
        $this->assertEquals('', $fonnte->formatPhoneNumber('   '));
    }

    public function test_fonnte_service_builds_all_four_message_templates_accurately(): void
    {
        $fonnte = app(FonnteService::class);

        // 1. Tagihan Baru (Order Created)
        $createdMsg = $fonnte->buildOrderCreatedMessage($this->order);
        $this->assertStringContainsString($this->order->order_number, $createdMsg);
        $this->assertStringContainsString('MENUNGGU PEMBAYARAN', $createdMsg);
        $this->assertStringContainsString('iPhone 16 Pro (Natural Titanium / 256GB)', $createdMsg);
        $this->assertStringContainsString('Rp 21.999.000', $createdMsg);

        // 2. Pembayaran Lunas Terverifikasi (Payment Success)
        $this->order->update(['status' => 'paid']);
        $paidMsg = $fonnte->buildPaymentSuccessMessage($this->order);
        $this->assertStringContainsString('PEMBAYARAN LUNAS TERVERIFIKASI', $paidMsg);
        $this->assertStringContainsString($this->order->order_number, $paidMsg);
        $this->assertStringContainsString('359988776655443', $paidMsg);
        $this->assertStringContainsString('Nota Faktur Resmi (PDF)', $paidMsg);

        // 3. Nomor Resi Pengiriman (Order Shipped)
        $this->order->update([
            'status' => 'shipped',
            'shipping_tracking_number' => 'JNE-9988776655ID',
        ]);
        $shippedMsg = $fonnte->buildOrderShippedMessage($this->order);
        $this->assertStringContainsString('PESANAN ANDA TELAH DIKIRIM', $shippedMsg);
        $this->assertStringContainsString('JNE-9988776655ID', $shippedMsg);
        $this->assertStringContainsString('JNE Express', $shippedMsg);

        // 4. Status Trade-In
        $tradeInMsg = $fonnte->buildTradeInStatusMessage($this->tradeIn);
        $this->assertStringContainsString($this->tradeIn->trade_in_code, $tradeInMsg);
        $this->assertStringContainsString('iPhone 14 Pro Max (256GB)', $tradeInMsg);
        $this->assertStringContainsString('Rp 12.200.000', $tradeInMsg);
        $this->assertStringContainsString('Penawaran Disetujui', $tradeInMsg);
    }

    public function test_fonnte_service_sends_http_request_with_authorization_header(): void
    {
        config(['services.fonnte.token' => 'secret_fonnte_token_test_99']);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'id' => ['msg_001'],
                'detail' => 'success! message in queue',
            ], 200),
        ]);

        $fonnte = app(FonnteService::class);
        $result = $fonnte->sendMessage('0812-8899-0011', 'Tes Pesan Rap-iPhone');

        $this->assertTrue($result['success']);
        $this->assertEquals('6281288990011', $result['recipient']);
        $this->assertNull($result['error']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request->hasHeader('Authorization', 'secret_fonnte_token_test_99')
                && $request['target'] === '6281288990011'
                && $request['message'] === 'Tes Pesan Rap-iPhone';
        });
    }

    public function test_invoice_pdf_service_generates_valid_pdf_binary_and_payment_confirmed_mail_attaches_it(): void
    {
        $pdfService = app(InvoicePdfService::class);

        $binary = $pdfService->generatePdfBinary($this->order);
        $filename = $pdfService->getFilename($this->order);

        $this->assertEquals('Nota-Rap-iPhone-' . $this->order->order_number . '.pdf', $filename);
        $this->assertStringStartsWith('%PDF-', $binary);
        $this->assertGreaterThan(1000, strlen($binary));

        // Verify PaymentConfirmedMail renders HTML & includes the PDF attachment
        $mail = new PaymentConfirmedMail($this->order);
        $renderedHtml = $mail->render();

        $this->assertStringContainsString($this->order->order_number, $renderedHtml);
        $this->assertStringContainsString('Pembayaran Lunas Terverifikasi', $renderedHtml);
        $this->assertStringContainsString('iPhone 16 Pro', $renderedHtml);
        $this->assertCount(1, $mail->attachments());
    }

    public function test_all_mailables_render_apple_style_html_templates(): void
    {
        $invoiceMail = new OrderInvoiceMail($this->order);
        $this->assertStringContainsString('Menunggu Pembayaran', $invoiceMail->render());
        $this->assertStringContainsString($this->order->order_number, $invoiceMail->render());

        $this->order->update(['shipping_tracking_number' => 'SICEPAT-00112233']);
        $shippedMail = new OrderShippedMail($this->order);
        $this->assertStringContainsString('SICEPAT-00112233', $shippedMail->render());

        $tradeInMail = new TradeInStatusMail($this->tradeIn);
        $this->assertStringContainsString($this->tradeIn->trade_in_code, $tradeInMail->render());
        $this->assertStringContainsString('Rp 12.200.000', $tradeInMail->render());
    }

    public function test_notification_orchestrator_dispatches_simultaneous_wa_and_email_and_logs_to_database(): void
    {
        config(['services.fonnte.token' => 'fonnte_live_token_abc']);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'detail' => 'sent',
            ], 200),
        ]);

        Mail::fake();

        $orchestrator = app(NotificationOrchestrator::class);

        // 1. Order Created
        $logsCreated = $orchestrator->notifyOrderCreated($this->order);
        $this->assertEquals('sent', $logsCreated['whatsapp']->status);
        $this->assertEquals('6281288990011', $logsCreated['whatsapp']->recipient);
        $this->assertEquals('order_created', $logsCreated['whatsapp']->event_name);
        $this->assertEquals('sent', $logsCreated['email']->status);
        $this->assertEquals($this->customer->email, $logsCreated['email']->recipient);
        Mail::assertSent(OrderInvoiceMail::class);

        // 2. Payment Success (with PDF Invoice Attachment)
        $logsPaid = $orchestrator->notifyPaymentSuccess($this->order);
        $this->assertEquals('sent', $logsPaid['whatsapp']->status);
        $this->assertEquals('payment_success', $logsPaid['whatsapp']->event_name);
        $this->assertEquals('sent', $logsPaid['email']->status);
        $this->assertTrue($logsPaid['email']->response_payload['has_pdf_attachment']);
        Mail::assertSent(PaymentConfirmedMail::class);

        // 3. Order Shipped
        $this->order->update([
            'status' => 'shipped',
            'shipping_tracking_number' => 'JNT-ID-889900',
        ]);
        $logsShipped = $orchestrator->notifyOrderShipped($this->order);
        $this->assertEquals('sent', $logsShipped['whatsapp']->status);
        $this->assertEquals('order_shipped', $logsShipped['whatsapp']->event_name);
        $this->assertEquals('sent', $logsShipped['email']->status);
        Mail::assertSent(OrderShippedMail::class);

        // 4. Trade-In Status
        $logsTradeIn = $orchestrator->notifyTradeInStatus($this->tradeIn);
        $this->assertEquals('sent', $logsTradeIn['whatsapp']->status);
        $this->assertEquals('trade_in_status', $logsTradeIn['whatsapp']->event_name);
        $this->assertEquals('sent', $logsTradeIn['email']->status);
        Mail::assertSent(TradeInStatusMail::class);

        // Assert total 8 logs recorded in notifications_log (4 whatsapp + 4 email)
        $this->assertEquals(
            4,
            NotificationLog::where('recipient', '6281288990011')->where('channel', 'whatsapp')->count()
        );
        $this->assertEquals(
            4,
            NotificationLog::where('recipient', $this->customer->email)->where('channel', 'email')->count()
        );
    }

    public function test_notification_orchestrator_records_failed_status_when_fonnte_api_fails(): void
    {
        config(['services.fonnte.token' => 'invalid_token_xyz']);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => false,
                'reason' => 'Token Fonnte tidak valid atau perangkat terputus',
            ], 200),
        ]);

        Mail::fake();

        $orchestrator = app(NotificationOrchestrator::class);
        $logs = $orchestrator->notifyOrderCreated($this->order);

        $this->assertEquals('failed', $logs['whatsapp']->status);
        $this->assertEquals('Token Fonnte tidak valid atau perangkat terputus', $logs['whatsapp']->error_message);

        // Email still succeeds independently even if WhatsApp fails
        $this->assertEquals('sent', $logs['email']->status);
    }

    public function test_midtrans_webhook_settlement_triggers_payment_success_notifications_once(): void
    {
        Mail::fake();

        $serverKey = config('services.midtrans.server_key');
        $grossAmount = '21999000.00';
        $signatureKey = hash('sha512', $this->order->order_number . '200' . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $this->order->order_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'transaction_id' => 'midtrans-notif-uuid-01',
            'payment_type' => 'qris',
            'transaction_time' => '2026-10-28 19:00:00',
        ];

        $response = $this->postJson(route('api.midtrans.callback'), $payload);
        $response->assertStatus(200);

        Mail::assertSent(PaymentConfirmedMail::class, 1);

        $this->assertDatabaseHas('notifications_log', [
            'channel' => 'whatsapp',
            'recipient' => '6281288990011',
            'event_name' => 'payment_success',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('notifications_log', [
            'channel' => 'email',
            'recipient' => $this->customer->email,
            'event_name' => 'payment_success',
            'status' => 'sent',
        ]);

        // Duplicate webhook must NOT send duplicate notifications (Idempotent)
        $duplicateResponse = $this->postJson(route('api.midtrans.callback'), $payload);
        $duplicateResponse->assertStatus(200);

        Mail::assertSent(PaymentConfirmedMail::class, 1);
    }
}
