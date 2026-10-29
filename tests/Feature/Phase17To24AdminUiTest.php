<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\TradeIn;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase17To24AdminUiTest extends TestCase
{
    protected User $admin;
    protected User $owner;
    protected User $customer;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $this->owner = User::where('role', 'owner')->first() ?? User::factory()->create(['role' => 'owner']);
        $this->customer = User::where('role', 'customer')->first() ?? User::factory()->create(['role' => 'customer']);
        $this->category = Category::first() ?? Category::create([
            'slug' => 'iphone-series',
            'name' => 'iPhone Series',
            'icon' => 'smartphone',
        ]);
    }

    /**
     * Test Fase 17: Admin Dashboard UI
     */
    public function test_fase_17_admin_dashboard_renders_clean_light_ui(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Halo, ' . $this->admin->name);
        $response->assertSee('Pesanan Perlu Diproses');
        $response->assertSee('Trade-In Menunggu');
        $response->assertSee('Stok Menipis');
        $response->assertSee('Total Produk');
        $response->assertDontSee('bg-[#161B22]', false);
    }

    /**
     * Test Fase 18: Admin Products List, Create & Edit UI
     */
    public function test_fase_18_admin_products_management_ui(): void
    {
        $product = Product::first();

        // 1. Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $resIndex->assertOk();
        $resIndex->assertSee('Katalog & Varian Stok iPhone', false);

        // 2. Create
        $resCreate = $this->actingAs($this->admin)->get(route('admin.products.create'));
        $resCreate->assertOk();
        $resCreate->assertSee('Tambah Produk iPhone Baru');

        // 3. Edit
        if ($product) {
            $resEdit = $this->actingAs($this->admin)->get(route('admin.products.edit', $product));
            $resEdit->assertOk();
            $resEdit->assertSee($product->name);
        }
    }

    /**
     * Test Fase 19: Admin Orders Index & Detail UI
     */
    public function test_fase_19_admin_orders_management_ui(): void
    {
        $order = Order::first() ?? Order::create([
            'order_number' => 'RAP-ORD-TEST-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'total_items_price' => 15000000,
            'shipping_cost' => 50000,
            'discount_amount' => 0,
            'grand_total' => 15050000,
            'status' => 'paid',
            'shipping_courier' => 'JNE Express',
            'shipping_address' => 'Jl Test No 123',
            'recipient_name' => $this->customer->name,
            'recipient_phone' => '08123456789',
        ]);

        // 1. Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $resIndex->assertOk();
        $resIndex->assertSee('Pesanan &amp; Logistik', false);

        // 2. Show Detail
        $resShow = $this->actingAs($this->admin)->get(route('admin.orders.show', $order->order_number));
        $resShow->assertOk();
        $resShow->assertSee('Input Resi &amp; Pengiriman Kurir', false);
    }

    /**
     * Test Fase 20: Admin Trade-In Queue & Detail UI
     */
    public function test_fase_20_admin_trade_ins_queue_and_inspection_ui(): void
    {
        $tradeIn = TradeIn::first() ?? TradeIn::create([
            'trade_in_code' => 'TI-TEST-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 13',
            'storage_capacity' => '128GB',
            'battery_health' => 88,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box',
            'estimated_price' => 7000000,
            'final_price' => 7000000,
            'status' => 'submitted',
            'customer_bank_name' => 'BCA',
            'customer_account_number' => '1234567890',
            'customer_account_holder' => $this->customer->name,
        ]);

        // 1. Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.trade-ins.index'));
        $resIndex->assertOk();
        $resIndex->assertSee('Antrean Pengajuan Trade-In', false);

        // 2. Show
        $resShow = $this->actingAs($this->admin)->get(route('admin.trade-ins.show', $tradeIn->trade_in_code));
        $resShow->assertOk();
        $resShow->assertSee('Form Inspeksi Fisik &amp; Fungsi Teknisi Toko', false);
    }

    /**
     * Test Fase 21: Owner Dashboard UI
     */
    public function test_fase_21_owner_dashboard_renders_clean_executive_ui(): void
    {
        $response = $this->actingAs($this->owner)->get(route('owner.dashboard'));

        $response->assertOk();
        $response->assertSee('Portal Finansial &amp; Analitik Laba', false);
        $response->assertSee('Metrik Keuangan Eksekutif');
        $response->assertSee('Grafik Tren Transaksi Penjualan &amp; Laba Bersih', false);
        $response->assertSee('Ringkasan Arus Kas: Trade-In Masuk vs Penjualan Keluar');
    }

    /**
     * Test Fase 22: Owner Financial Report UI
     */
    public function test_fase_22_owner_financial_report_renders_accounting_ui(): void
    {
        $response = $this->actingAs($this->owner)->get(route('owner.financial.report'));

        $response->assertOk();
        $response->assertSee('Laporan Penjualan &amp; Laba Rugi Eksekutif', false);
        $response->assertSee('Laporan Laba Rugi Penjualan (P&amp;L)', false);
        $response->assertSee('Trade-In Masuk vs Penjualan Keluar');
        $response->assertSee('Unduh Excel / CSV');
        $response->assertSee('Unduh Laporan PDF');
    }

    /**
     * Test Fase 23: Owner Staff Management UI
     */
    public function test_fase_23_owner_staff_management_ui(): void
    {
        $response = $this->actingAs($this->owner)->get(route('owner.staff.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Akun Staf &amp; Admin Toko', false);
        $response->assertSee('Tambah Akun Staf / Admin Baru');
        $response->assertSee('Daftar Akun Tim Internal');
        $response->assertSee($this->admin->email);
    }

    /**
     * Test Fase 24: Admin Settings UI
     */
    public function test_fase_24_admin_settings_renders_clean_service_layer_cards(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings'));

        $response->assertOk();
        $response->assertSee('Konfigurasi Sistem & Integrasi API', false);
        $response->assertSee('Arsitektur Service Layer Aktif');
        $response->assertSee('Payment Gateway Snap');
        $response->assertSee('Notifikasi WhatsApp Otomatis');
        $response->assertSee('Pengiriman Email &amp; Faktur PDF', false);
        $response->assertSee('Single Sign-On (SSO)');
        $response->assertDontSee('bg-[#161B22]', false);
    }
}
