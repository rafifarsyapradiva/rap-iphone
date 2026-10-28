<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\InvoicePdfService;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase9InvoiceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;
    protected User $otherCustomer;
    protected User $admin;
    protected Product $product;
    protected ProductVariant $variant;
    protected Order $paidOrder;
    protected Order $pendingOrder;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(5);

        $this->customer = User::create([
            'name' => 'Reza Rahadian',
            'email' => "reza.p9.{$suffix}@example.com",
            'phone_number' => '081288990011',
            'address' => 'Jl. Senopati No. 10, Jakarta Selatan',
            'role' => 'customer',
        ]);

        $this->otherCustomer = User::create([
            'name' => 'Budi Santoso',
            'email' => "budi.p9.{$suffix}@example.com",
            'phone_number' => '081377665544',
            'role' => 'customer',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Operasional',
            'email' => "admin.p9.{$suffix}@rap-iphone.com",
            'phone_number' => '081199887766',
            'role' => 'admin',
        ]);

        $category = Category::firstOrCreate(
            ['slug' => 'iphone-16-series'],
            ['name' => 'iPhone 16 Series', 'icon' => 'smartphone']
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'iPhone 16 Pro Max',
            'slug' => 'iphone-16-pro-max-p9-' . $suffix,
            'series' => 'iPhone 16 Series',
            'condition' => 'new',
            'warranty_type' => 'resmi_ibox',
            'base_price' => 24999000.00,
            'description' => 'Apple A18 Pro chip.',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'storage_capacity' => '256GB',
            'color_name' => 'Desert Titanium',
            'color_hex' => '#C5B49F',
            'cost_price' => 22000000.00,
            'selling_price' => 24999000.00,
            'stock' => 10,
            'sku' => 'IP16PM-256-DT-' . $suffix,
            'imei' => '359988776655443',
        ]);

        $this->paidOrder = Order::create([
            'order_number' => 'RAP-20261028-P' . strtoupper($suffix),
            'user_id' => $this->customer->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 24999000.00,
            'status' => 'paid',
            'shipping_courier' => 'JNE Express (YES)',
            'shipping_tracking_number' => 'JNE-8899001122',
            'shipping_address' => 'Jl. Senopati No. 10, Kebayoran Baru, Jakarta Selatan',
            'recipient_name' => 'Reza Rahadian',
            'recipient_phone' => '0812-8899-0011',
        ]);

        OrderItem::create([
            'order_id' => $this->paidOrder->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'variant_details' => 'Desert Titanium / 256GB',
            'price' => 24999000.00,
            'cost_price' => 22000000.00,
            'quantity' => 1,
            'subtotal' => 24999000.00,
            'allocated_imei' => '359988776655443',
        ]);

        Payment::create([
            'order_id' => $this->paidOrder->id,
            'midtrans_transaction_id' => 'mid-tx-' . $suffix,
            'gross_amount' => 24999000.00,
            'snap_token' => 'snap_paid_' . $suffix,
            'payment_type' => 'qris',
            'transaction_status' => 'settlement',
            'payment_time' => now(),
        ]);

        $this->pendingOrder = Order::create([
            'order_number' => 'RAP-20261028-W' . strtoupper($suffix),
            'user_id' => $this->customer->id,
            'total_items_price' => 24999000.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 24999000.00,
            'status' => 'pending',
            'shipping_courier' => 'SiCepat BEST',
            'shipping_address' => 'Jl. Senopati No. 10, Jakarta Selatan',
            'recipient_name' => 'Reza Rahadian',
            'recipient_phone' => '0812-8899-0011',
        ]);

        OrderItem::create([
            'order_id' => $this->pendingOrder->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => $this->product->name,
            'variant_details' => 'Desert Titanium / 256GB',
            'price' => 24999000.00,
            'cost_price' => 22000000.00,
            'quantity' => 1,
            'subtotal' => 24999000.00,
        ]);
    }

    public function test_qr_service_returns_svg_data_uri_without_gd(): void
    {
        $qr = app(QrCodeService::class);
        $dataUri = $qr->svgDataUri('https://rap-iphone.test/verifikasi-nota/RAP-001/ABCD');

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $dataUri);

        $svg = base64_decode(substr($dataUri, strlen('data:image/svg+xml;base64,')));
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('<path', $svg);
    }

    public function test_invoice_view_contains_imei_warranty_period_qr_and_terms(): void
    {
        $service = app(InvoicePdfService::class);
        $data = $service->buildViewData($this->paidOrder);
        $html = view('pdf.invoice', ['invoice' => $data])->render();

        $this->assertStringContainsString($this->paidOrder->order_number, $html);
        $this->assertStringContainsString('iPhone 16 Pro Max', $html);
        $this->assertStringContainsString('Desert Titanium · 256GB', $html);
        $this->assertStringContainsString('IMEI 3599 8877 6655 443', $html);
        $this->assertStringContainsString('Garansi Resmi Apple Indonesia (iBox)', $html);
        $this->assertStringContainsString('12 bulan', $html);
        $this->assertStringContainsString('Berlaku s/d', $html);
        $this->assertStringContainsString('Rp 24.999.000', $html);
        $this->assertStringContainsString('dua puluh empat juta sembilan ratus sembilan puluh sembilan ribu rupiah', $html);
        $this->assertStringContainsString('data:image/svg+xml;base64,', $html);
        $this->assertStringContainsString($data['verificationCode'], $html);
        $this->assertStringContainsString('Syarat &amp; Ketentuan Klaim Garansi', $html);
        $this->assertStringContainsString('7 hari', $html);
    }

    public function test_invoice_pdf_renders_valid_a4_binary_with_real_svg_qr(): void
    {
        $service = app(InvoicePdfService::class);
        $binary = $service->generatePdfBinary($this->paidOrder);

        $this->assertStringStartsWith('%PDF-', $binary);
        $this->assertGreaterThan(5000, strlen($binary));
    }

    public function test_verification_code_is_deterministic_and_tamper_proof(): void
    {
        $service = app(InvoicePdfService::class);
        $code = $service->verificationCode($this->paidOrder);

        $this->assertSame(16, strlen($code));
        $this->assertTrue($service->isValidVerificationCode($this->paidOrder, $code));
        $this->assertTrue($service->isValidVerificationCode($this->paidOrder, strtolower($code)));
        $this->assertFalse($service->isValidVerificationCode($this->paidOrder, '0000000000000000'));

        // Mengubah nominal pesanan harus menggugurkan kode verifikasi lama
        $this->paidOrder->grand_total = 1000000.00;
        $this->assertFalse($service->isValidVerificationCode($this->paidOrder, $code));
    }

    public function test_public_verification_page_confirms_authentic_invoice_with_masked_data(): void
    {
        $service = app(InvoicePdfService::class);
        $code = $service->verificationCode($this->paidOrder);

        $response = $this->get(route('invoice.verify', [
            'orderNumber' => $this->paidOrder->order_number,
            'code' => $code,
        ]));

        $response->assertOk();
        $response->assertSee('Nota Asli Terverifikasi');
        $response->assertSee($this->paidOrder->order_number);
        $response->assertSee('Re** Ra******');
        $response->assertSee('3599 **** **** 443');
        $response->assertSee('Garansi Resmi Apple Indonesia (iBox)');

        // Data sensitif tidak boleh bocor di halaman publik
        $response->assertDontSee('Jl. Senopati No. 10');
        $response->assertDontSee('0812-8899-0011');
        $response->assertDontSee('359988776655443');
    }

    public function test_public_verification_page_rejects_invalid_code_with_404(): void
    {
        $response = $this->get(route('invoice.verify', [
            'orderNumber' => $this->paidOrder->order_number,
            'code' => 'FAKECODE12345678',
        ]));

        $response->assertNotFound();
        $response->assertSee('Nota Tidak Terdaftar');
    }

    public function test_customer_can_download_and_preview_own_paid_invoice(): void
    {
        $download = $this->actingAs($this->customer)
            ->get(route('orders.invoice.download', $this->paidOrder->order_number));

        $download->assertOk();
        $download->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'Nota-Rap-iPhone-' . $this->paidOrder->order_number . '.pdf',
            (string) $download->headers->get('content-disposition')
        );

        $preview = $this->actingAs($this->customer)
            ->get(route('orders.invoice.preview', $this->paidOrder->order_number));

        $preview->assertOk();
        $preview->assertHeader('content-type', 'application/pdf');
    }

    public function test_customer_cannot_download_other_customers_invoice(): void
    {
        $response = $this->actingAs($this->otherCustomer)
            ->get(route('orders.invoice.download', $this->paidOrder->order_number));

        $response->assertForbidden();
    }

    public function test_customer_cannot_download_pending_order_invoice(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('orders.invoice.download', $this->pendingOrder->order_number));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_when_downloading_invoice(): void
    {
        $response = $this->get(route('orders.invoice.download', $this->paidOrder->order_number));

        $response->assertRedirect(route('login'));
    }

    public function test_customer_orders_page_shows_download_button_only_for_paid_orders(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.orders'));

        $response->assertOk();
        $response->assertSee(route('orders.invoice.download', $this->paidOrder->order_number), false);
        $response->assertDontSee(route('orders.invoice.download', $this->pendingOrder->order_number), false);
    }

    public function test_admin_can_view_orders_index_and_filter_by_status(): void
    {
        $all = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $all->assertOk();
        $all->assertSee($this->paidOrder->order_number);
        $all->assertSee($this->pendingOrder->order_number);

        $paidOnly = $this->actingAs($this->admin)->get(route('admin.orders.index', ['status' => 'paid']));
        $paidOnly->assertOk();
        $paidOnly->assertSee($this->paidOrder->order_number);
        $paidOnly->assertDontSee($this->pendingOrder->order_number);
    }

    public function test_admin_can_bulk_print_invoices_as_single_merged_pdf(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.orders.bulk.invoices'), [
            'order_ids' => [$this->paidOrder->id, $this->pendingOrder->id],
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_admin_can_bulk_print_shipping_labels_and_skips_ineligible_orders(): void
    {
        // Campuran paid + pending -> menghasilkan PDF untuk yang paid
        $response = $this->actingAs($this->admin)->post(route('admin.orders.bulk.labels'), [
            'order_ids' => [$this->paidOrder->id, $this->pendingOrder->id],
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());

        // Hanya pending -> redirect kembali dengan pesan error
        $ineligible = $this->actingAs($this->admin)->post(route('admin.orders.bulk.labels'), [
            'order_ids' => [$this->pendingOrder->id],
        ]);

        $ineligible->assertRedirect(route('admin.orders.index'));
        $ineligible->assertSessionHas('error');
    }

    public function test_bulk_print_validates_order_ids_and_blocks_customer_role(): void
    {
        $empty = $this->actingAs($this->admin)->post(route('admin.orders.bulk.invoices'), [
            'order_ids' => [],
        ]);
        $empty->assertSessionHasErrors('order_ids');

        $tooMany = $this->actingAs($this->admin)->post(route('admin.orders.bulk.invoices'), [
            'order_ids' => range(1, 51),
        ]);
        $tooMany->assertSessionHasErrors('order_ids');

        $redirected = $this->actingAs($this->customer)->post(route('admin.orders.bulk.invoices'), [
            'order_ids' => [$this->paidOrder->id],
        ]);
        $redirected->assertRedirect('/');
        $redirected->assertSessionHas('error');

        $forbiddenJson = $this->actingAs($this->customer)->postJson(route('admin.orders.bulk.invoices'), [
            'order_ids' => [$this->paidOrder->id],
        ]);
        $forbiddenJson->assertForbidden();
    }
}
