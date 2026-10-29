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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase11OwnerDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected User $owner;
    protected User $admin;
    protected User $customer;
    protected Category $category;
    protected Product $product16Pro;
    protected Product $product15;
    protected ProductVariant $variant16Pro;
    protected ProductVariant $variant15;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(5);

        $this->owner = User::create([
            'name' => 'Owner Eksekutif',
            'email' => "owner.p11.{$suffix}@rap-iphone.com",
            'password' => Hash::make('password123'),
            'phone_number' => '081100001111',
            'role' => 'owner',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Toko P11',
            'email' => "admin.p11.{$suffix}@rap-iphone.com",
            'password' => Hash::make('password123'),
            'phone_number' => '081100002222',
            'role' => 'admin',
        ]);

        $this->customer = User::create([
            'name' => 'Pelanggan P11',
            'email' => "cust.p11.{$suffix}@example.com",
            'password' => Hash::make('password123'),
            'phone_number' => '081200003333',
            'role' => 'customer',
        ]);

        $this->category = Category::firstOrCreate(
            ['slug' => 'iphone-flagship-p11'],
            ['name' => 'iPhone Flagship P11', 'icon' => 'smartphone']
        );

        $this->product16Pro = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 16 Pro Max P11 ' . $suffix,
            'slug' => 'iphone-16-pro-max-p11-' . strtolower($suffix),
            'series' => 'iPhone 16 Series',
            'condition' => 'new',
            'warranty_type' => 'resmi_ibox',
            'base_price' => 24000000.00,
            'description' => 'Unit BNIB iBox.',
            'is_active' => true,
        ]);

        $this->variant16Pro = ProductVariant::create([
            'product_id' => $this->product16Pro->id,
            'storage_capacity' => '256GB',
            'color_name' => 'Desert Titanium',
            'color_hex' => '#C5B49F',
            'cost_price' => 21000000.00, // HPP 21 Juta
            'selling_price' => 24000000.00, // Jual 24 Juta (Laba 3 Juta / unit)
            'stock' => 15,
            'sku' => 'IP16PM-P11-' . strtoupper($suffix),
        ]);

        $this->product15 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 15 P11 ' . $suffix,
            'slug' => 'iphone-15-p11-' . strtolower($suffix),
            'series' => 'iPhone 15 Series',
            'condition' => 'second',
            'grade' => 'Grade A+',
            'warranty_type' => 'toko',
            'base_price' => 12000000.00,
            'description' => 'Unit Second Like New.',
            'is_active' => true,
        ]);

        $this->variant15 = ProductVariant::create([
            'product_id' => $this->product15->id,
            'storage_capacity' => '128GB',
            'color_name' => 'Ultramarine Blue',
            'color_hex' => '#496495',
            'cost_price' => 10000000.00, // HPP 10 Juta
            'selling_price' => 12000000.00, // Jual 12 Juta (Laba 2 Juta / unit)
            'stock' => 10,
            'sku' => 'IP15-P11-' . strtoupper($suffix),
        ]);
    }

    /**
     * Helper untuk membuat pesanan lengkap dengan item pada waktu tertentu.
     */
    protected function createOrderWithItem(
        ProductVariant $variant,
        int $qty,
        string $status,
        Carbon $createdAt,
        float $discount = 0.0,
        float $shipping = 0.0
    ): Order {
        $itemsPrice = (float) $variant->selling_price * $qty;
        $grandTotal = $itemsPrice + $shipping - $discount;

        $order = new Order([
            'order_number' => 'RAP-P11-' . strtoupper(Str::random(8)),
            'user_id' => $this->customer->id,
            'total_items_price' => $itemsPrice,
            'shipping_cost' => $shipping,
            'discount_amount' => $discount,
            'grand_total' => $grandTotal,
            'status' => $status,
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jl. Sudirman Jakarta',
            'recipient_name' => $this->customer->name,
            'recipient_phone' => '081200003333',
        ]);
        $order->created_at = $createdAt;
        $order->updated_at = $createdAt;
        $order->save();

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_name' => $variant->product->name,
            'variant_details' => "{$variant->color_name} / {$variant->storage_capacity}",
            'price' => $variant->selling_price,
            'cost_price' => $variant->cost_price,
            'quantity' => $qty,
            'subtotal' => $itemsPrice,
        ]);

        return $order;
    }

    /**
     * Test 1: RBAC Guard — Hanya Owner yang dapat mengakses rute finansial & manajemen staf.
     */
    public function test_rbac_blocks_admin_and_customer_from_owner_routes(): void
    {
        $ownerRoutes = [
            route('owner.dashboard'),
            route('owner.financial.report'),
            route('owner.financial.export.csv'),
            route('owner.financial.export.pdf'),
            route('owner.staff.index'),
        ];

        foreach ($ownerRoutes as $url) {
            $resAdmin = $this->actingAs($this->admin)->get($url);
            $resAdmin->assertRedirect(route('admin.dashboard'));

            $resCustomer = $this->actingAs($this->customer)->get($url);
            $resCustomer->assertRedirect('/');
        }

        $resOwner = $this->actingAs($this->owner)->get(route('owner.dashboard'));
        $resOwner->assertStatus(200);
        $resOwner->assertSee('Portal Finansial');
    }

    /**
     * Test 2 (Phase 11.1): Kalkulasi Omzet (Harian, Mingguan, Bulanan) & Laba Bersih Otomatis (Omzet - HPP).
     */
    public function test_executive_metrics_calculate_revenue_and_net_profit_accurately(): void
    {
        $now = Carbon::now();

        // Pesanan Lunas Hari Ini: 1x iPhone 16 Pro Max (Jual 24jt, HPP 21jt, Diskon 500rb -> Grand Total 23.5jt, Laba Bersih 2.5jt)
        $todayOrder = $this->createOrderWithItem($this->variant16Pro, 1, 'paid', $now->copy(), 500000.00);

        // Pesanan Lunas 3 Hari Lalu (Masuk Mingguan & Bulanan): 2x iPhone 15 (Jual 24jt, HPP 20jt -> Laba Bersih 4jt)
        $threeDaysAgoOrder = $this->createOrderWithItem($this->variant15, 2, 'completed', $now->copy()->subDays(3));

        // Pesanan Pending & Cancelled (TIDAK boleh dihitung ke dalam Omzet maupun Laba Bersih)
        $this->createOrderWithItem($this->variant16Pro, 1, 'pending', $now);
        $this->createOrderWithItem($this->variant16Pro, 1, 'cancelled', $now);

        /** @var FinancialAnalyticsService $service */
        $service = app(FinancialAnalyticsService::class);

        // Uji kalkulasi spesifik pada window waktu hari ini
        $todaySummary = $service->calculatePeriodFinancials($now->copy()->startOfDay(), $now->copy()->endOfDay());
        $this->assertGreaterThanOrEqual(23500000.00, $todaySummary['revenue']);
        $this->assertGreaterThanOrEqual(21000000.00, $todaySummary['cogs']);
        $this->assertGreaterThanOrEqual(2500000.00, $todaySummary['net_profit']);

        // Uji kalkulasi 7 hari terakhir (mencakup pesanan hari ini + 3 hari lalu)
        $weeklySummary = $service->calculatePeriodFinancials($now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay());
        $this->assertGreaterThanOrEqual(23500000.00 + 24000000.00, $weeklySummary['revenue']);
        $this->assertGreaterThanOrEqual(21000000.00 + 20000000.00, $weeklySummary['cogs']);
        $this->assertGreaterThanOrEqual(2500000.00 + 4000000.00, $weeklySummary['net_profit']);

        // Pastikan halaman Dashboard Owner merender metrik dengan sukses
        $response = $this->actingAs($this->owner)->get(route('owner.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Metrik Keuangan Eksekutif');
        $response->assertSee('Total Laba Bersih Otomatis');
        $response->assertSee($todayOrder->order_number);
        $response->assertSee($threeDaysAgoOrder->order_number);
    }

    /**
     * Test 3 (Phase 11.1): Ringkasan Arus Kas Trade-In Masuk vs Penjualan Keluar.
     */
    public function test_cash_flow_summary_compares_trade_in_inflow_vs_sales_outflow(): void
    {
        $start = Carbon::now()->addDays(10)->startOfDay();
        $end = Carbon::now()->addDays(10)->endOfDay();

        // Penjualan Keluar: 1 unit iPhone 16 Pro Max (Rp 24.000.000)
        $this->createOrderWithItem($this->variant16Pro, 1, 'paid', $start->copy()->addHours(2));

        // Trade-In 1: Pencairan Transfer Bank (Kas Keluar Tunai Rp 8.500.000)
        $tiCash = new TradeIn([
            'trade_in_code' => 'TI-P11-CASH-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 13 Pro',
            'storage_capacity' => '128GB',
            'battery_health' => 88,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'estimated_price' => 8800000.00,
            'final_price' => 8500000.00,
            'status' => 'paid_out',
            'customer_bank_name' => 'BCA',
            'customer_account_number' => '1234567890',
            'customer_account_holder' => 'Pelanggan P11',
        ]);
        $tiCash->created_at = $start->copy()->addHours(3);
        $tiCash->save();

        // Trade-In 2: Kupon Potongan Belanja (Voucher Rp 6.000.000)
        $tiVoucher = new TradeIn([
            'trade_in_code' => 'TI-P11-VCH-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 12',
            'storage_capacity' => '128GB',
            'battery_health' => 85,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Unit Only',
            'estimated_price' => 6000000.00,
            'final_price' => 6000000.00,
            'status' => 'approved',
            'customer_bank_name' => TradeIn::VOUCHER_BANK_MARKER,
            'customer_account_number' => 'VCH-P11-999',
            'customer_account_holder' => 'Pelanggan P11',
        ]);
        $tiVoucher->created_at = $start->copy()->addHours(4);
        $tiVoucher->save();

        /** @var FinancialAnalyticsService $service */
        $service = app(FinancialAnalyticsService::class);
        $cashFlow = $service->getCashFlowSummary($start, $end);

        $this->assertEquals(24000000.00, $cashFlow['sales_cash_in']);
        $this->assertEquals(1, $cashFlow['sales_units_out']);
        $this->assertEquals(2, $cashFlow['trade_in_units_in']);
        $this->assertEquals(8500000.00, $cashFlow['trade_in_cash_out']);
        $this->assertEquals(6000000.00, $cashFlow['trade_in_voucher_out']);
        $this->assertEquals(14500000.00, $cashFlow['trade_in_total_value']);
        $this->assertEquals(9500000.00, $cashFlow['net_cash_flow']); // 24jt - 14.5jt
        $this->assertEquals(15500000.00, $cashFlow['net_liquid_cash_flow']); // 24jt - 8.5jt
    }

    /**
     * Test 4 (Phase 11.2): Grafik Tren 30 Hari Terakhir & Analisis Produk Terlaris (Top Selling iPhone Models).
     */
    public function test_sales_trend_30_days_and_top_selling_products_analysis(): void
    {
        $windowStart = Carbon::now()->addDays(20)->startOfDay();
        $windowEnd = Carbon::now()->addDays(20)->endOfDay();

        // Buat 3 unit penjualan iPhone 15 dan 1 unit penjualan iPhone 16 Pro Max
        $this->createOrderWithItem($this->variant15, 3, 'paid', $windowStart->copy()->addHours(2));
        $this->createOrderWithItem($this->variant16Pro, 1, 'shipped', $windowStart->copy()->addHours(4));

        /** @var FinancialAnalyticsService $service */
        $service = app(FinancialAnalyticsService::class);

        // Uji Top Selling Products pada window tersebut
        $topProducts = $service->getTopSellingProducts(5, $windowStart, $windowEnd);
        $this->assertCount(2, $topProducts);

        // Rank #1 harus iPhone 15 (3 unit terjual)
        $this->assertEquals(1, $topProducts[0]['rank']);
        $this->assertEquals($this->product15->name, $topProducts[0]['product_name']);
        $this->assertEquals(3, $topProducts[0]['units_sold']);
        $this->assertEquals(36000000.00, $topProducts[0]['revenue']);
        $this->assertEquals(6000000.00, $topProducts[0]['gross_profit']);

        // Rank #2 harus iPhone 16 Pro Max (1 unit terjual)
        $this->assertEquals(2, $topProducts[1]['rank']);
        $this->assertEquals($this->product16Pro->name, $topProducts[1]['product_name']);
        $this->assertEquals(1, $topProducts[1]['units_sold']);

        // Uji struktur Grafik Tren 30 Hari Terakhir
        $trend = $service->getSalesTrend30Days($windowEnd);
        $this->assertCount(30, $trend['days']);
        $this->assertGreaterThanOrEqual(60000000.00, $trend['summary']['total_revenue_30d']);
        $this->assertGreaterThanOrEqual(9000000.00, $trend['summary']['total_profit_30d']);
    }

    /**
     * Test 5 (Phase 11.3): Halaman Laporan Keuangan & Ekspor Data ke Excel/CSV serta PDF Resmi.
     */
    public function test_owner_can_view_financial_report_and_export_csv_and_pdf(): void
    {
        $order = $this->createOrderWithItem($this->variant16Pro, 1, 'paid', Carbon::now());

        // 1. Halaman Laporan Laba Rugi
        $resReport = $this->actingAs($this->owner)->get(route('owner.financial.report', ['period' => 'today']));
        $resReport->assertStatus(200);
        $resReport->assertSee('Laporan Penjualan & Laba Rugi Eksekutif');
        $resReport->assertSee($order->order_number);

        // 2. Ekspor ke Excel / CSV
        $resCsv = $this->actingAs($this->owner)->get(route('owner.financial.export.csv', ['period' => 'today']));
        $resCsv->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $resCsv->headers->get('Content-Type'));

        $csvContent = $resCsv->streamedContent();
        $this->assertStringContainsString('LAPORAN PENJUALAN & LABA RUGI EKSEKUTIF', $csvContent);
        $this->assertStringContainsString($order->order_number, $csvContent);
        $this->assertStringContainsString('Laba Bersih Otomatis', $csvContent);

        // 3. Ekspor ke PDF Resmi (DomPDF)
        $resPdf = $this->actingAs($this->owner)->get(route('owner.financial.export.pdf', ['period' => 'today']));
        $resPdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $resPdf->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $resPdf->getContent());
    }

    /**
     * Test 6 (Phase 11.3): Manajemen Akun Staf dan Admin Toko (Create, Update, Delete & Self-Delete Guard).
     */
    public function test_owner_can_manage_staff_and_admin_accounts_with_security_guards(): void
    {
        // 1. Buka halaman Manajemen Staf
        $resIndex = $this->actingAs($this->owner)->get(route('owner.staff.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Manajemen Akun Staf');
        $resIndex->assertSee($this->admin->email);

        // 2. Tambah akun Staf Admin baru
        $newStaffEmail = 'new.staff.' . uniqid() . '@rap-iphone.com';
        $resStore = $this->actingAs($this->owner)->post(route('owner.staff.store'), [
            'name' => 'Rendra Staf Gudang',
            'email' => $newStaffEmail,
            'phone_number' => '081377889900',
            'role' => 'admin',
            'city' => 'Jakarta Selatan',
            'password' => 'staffpass123',
        ]);
        $resStore->assertRedirect(route('owner.staff.index'));
        $resStore->assertSessionHas('success');

        $newStaff = User::where('email', $newStaffEmail)->firstOrFail();
        $this->assertEquals('admin', $newStaff->role);
        $this->assertTrue(Hash::check('staffpass123', $newStaff->password));

        // 3. Update profil & password Staf Admin
        $resUpdate = $this->actingAs($this->owner)->put(route('owner.staff.update', $newStaff), [
            'name' => 'Rendra Kepala Gudang',
            'email' => $newStaffEmail,
            'phone_number' => '081399990000',
            'role' => 'admin',
            'city' => 'Surabaya',
            'password' => 'updatedpass456',
        ]);
        $resUpdate->assertRedirect(route('owner.staff.index'));

        $newStaff->refresh();
        $this->assertEquals('Rendra Kepala Gudang', $newStaff->name);
        $this->assertEquals('Surabaya', $newStaff->city);
        $this->assertTrue(Hash::check('updatedpass456', $newStaff->password));

        // 4. Proteksi: Owner tidak dapat menghapus akun dirinya sendiri
        $resSelfDelete = $this->actingAs($this->owner)->delete(route('owner.staff.destroy', $this->owner));
        $resSelfDelete->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->owner->id]);

        // 5. Hapus akun Staf Admin
        $resDelete = $this->actingAs($this->owner)->delete(route('owner.staff.destroy', $newStaff));
        $resDelete->assertRedirect(route('owner.staff.index'));
        $resDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $newStaff->id]);
    }
}
