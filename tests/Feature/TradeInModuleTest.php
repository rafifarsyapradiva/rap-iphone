<?php

namespace Tests\Feature;

use App\Models\TradeIn;
use App\Models\User;
use App\Services\TradeInCalculatorService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class TradeInModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;
    protected User $otherCustomer;
    protected User $admin;
    protected TradeInCalculatorService $calculatorService;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(5);

        $this->customer = User::create([
            'name' => 'Andi Wijaya',
            'email' => "andi.{$suffix}@example.com",
            'phone_number' => '081298765432',
            'role' => 'customer',
        ]);

        $this->otherCustomer = User::create([
            'name' => 'Rina Kusuma',
            'email' => "rina.{$suffix}@example.com",
            'role' => 'customer',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Teknisi',
            'email' => "admin.{$suffix}@rap-iphone.com",
            'role' => 'admin',
        ]);

        $this->calculatorService = app(TradeInCalculatorService::class);
    }

    public function test_trade_in_wizard_page_is_accessible_and_displays_4_step_wizard(): void
    {
        $response = $this->get(route('trade-in.index'));

        $response->assertStatus(200);
        $response->assertSee('Jual & Tukar Tambah');
        $response->assertSee('Pilih Model iPhone');
        $response->assertSee('iPhone 15 Pro Max');
        $response->assertSee('Transfer Bank Rekening Customer');
        $response->assertSee('Kupon Potongan Belanja iPhone Baru');
    }

    public function test_calculator_service_computes_accurate_estimated_price_based_on_conditions(): void
    {
        // Case 1: iPhone 15 Pro Max 256GB, Grade A+ (0%), Normal Screen (0%), BH 95% (0%), Fullset (0)
        // Base price = 14,500,000 -> Estimated = 14,500,000
        $optimal = $this->calculatorService->calculateEstimate([
            'phone_model' => 'iPhone 15 Pro Max',
            'storage_capacity' => '256GB',
            'battery_health' => 95,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
        ]);

        $this->assertEquals(14500000.0, $optimal['base_price']);
        $this->assertEquals(0.0, $optimal['total_deduction']);
        $this->assertEquals(14500000.0, $optimal['estimated_price']);

        // Case 2: iPhone 13 Pro 128GB (Base 7,800,000), Grade B (-15% = 1,170,000), Normal Screen (0%), BH 86% (-4% = 312,000), Unit Only (-500,000)
        // Total Deduction = 1,982,000 -> Estimated = 5,818,000
        $degraded = $this->calculatorService->calculateEstimate([
            'phone_model' => 'iPhone 13 Pro',
            'storage_capacity' => '128GB',
            'battery_health' => 86,
            'physical_condition' => 'Lecet Pemakaian (Grade B)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Unit Only',
        ]);

        $this->assertEquals(7800000.0, $degraded['base_price']);
        $this->assertEquals(1170000.0, $degraded['physical_deduction']);
        $this->assertEquals(312000.0, $degraded['battery_deduction']);
        $this->assertEquals(500000.0, $degraded['accessories_deduction']);
        $this->assertEquals(5818000.0, $degraded['estimated_price']);
    }

    public function test_ajax_calculate_endpoint_returns_json_breakdown(): void
    {
        $response = $this->postJson(route('trade-in.calculate'), [
            'phone_model' => 'iPhone 14 Pro',
            'storage_capacity' => '256GB',
            'battery_health' => 92,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'formatted_estimated_price' => 'Rp 11.000.000',
        ]);
    }

    public function test_authenticated_customer_can_submit_trade_in_with_bank_transfer_payout(): void
    {
        $payload = [
            'phone_model' => 'iPhone 15 Pro',
            'storage_capacity' => '256GB',
            'battery_health' => 91,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'imei' => '359876543210123',
            'payout_method' => 'bank_transfer',
            'customer_bank_name' => 'BCA (Bank Central Asia)',
            'customer_account_number' => '8720112233',
            'customer_account_holder' => 'Andi Wijaya',
        ];

        $response = $this->actingAs($this->customer)->post(route('trade-in.store'), $payload);

        $tradeIn = TradeIn::where('user_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($tradeIn);

        $response->assertRedirect(route('trade-in.show', $tradeIn->trade_in_code));

        $this->assertDatabaseHas('trade_ins', [
            'id' => $tradeIn->id,
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 15 Pro',
            'storage_capacity' => '256GB',
            'battery_health' => 91,
            'estimated_price' => 13400000.00,
            'status' => 'submitted',
            'customer_bank_name' => 'BCA (Bank Central Asia)',
            'customer_account_number' => '8720112233',
            'customer_account_holder' => 'Andi Wijaya',
        ]);
        $this->assertFalse($tradeIn->isVoucherPayout());
    }

    public function test_authenticated_customer_can_submit_trade_in_with_voucher_payout(): void
    {
        $payload = [
            'phone_model' => 'iPhone 13',
            'storage_capacity' => '128GB',
            'battery_health' => 90,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'payout_method' => 'voucher',
        ];

        $response = $this->actingAs($this->customer)->post(route('trade-in.store'), $payload);

        $tradeIn = TradeIn::where('user_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($tradeIn);

        $response->assertRedirect(route('trade-in.show', $tradeIn->trade_in_code));
        $this->assertTrue($tradeIn->isVoucherPayout());
        $this->assertEquals(TradeIn::VOUCHER_BANK_MARKER, $tradeIn->customer_bank_name);
        $this->assertStringStartsWith('VCH-', (string) $tradeIn->customer_account_number);
    }

    public function test_customer_can_view_own_trade_in_detail_but_not_others(): void
    {
        $tradeIn = TradeIn::create([
            'trade_in_code' => 'TI-TEST-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 14 Pro',
            'storage_capacity' => '128GB',
            'battery_health' => 88,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'estimated_price' => 9100000,
            'status' => 'submitted',
            'customer_bank_name' => 'Bank Mandiri',
            'customer_account_number' => '123000999888',
            'customer_account_holder' => 'Andi Wijaya',
        ]);

        // Own customer can view
        $ownResponse = $this->actingAs($this->customer)->get(route('trade-in.show', $tradeIn->trade_in_code));
        $ownResponse->assertStatus(200);
        $ownResponse->assertSee($tradeIn->trade_in_code);

        // Other customer gets 403
        $otherResponse = $this->actingAs($this->otherCustomer)->get(route('trade-in.show', $tradeIn->trade_in_code));
        $otherResponse->assertStatus(403);
    }

    public function test_admin_can_view_trade_in_queue_and_customer_is_forbidden(): void
    {
        $tradeIn = TradeIn::create([
            'trade_in_code' => 'TI-QUEUE-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 15 Pro Max',
            'storage_capacity' => '512GB',
            'battery_health' => 94,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'estimated_price' => 15800000,
            'status' => 'submitted',
        ]);

        // Customer blocked from admin route and redirected to home with error
        $customerResp = $this->actingAs($this->customer)->get(route('admin.trade-ins.index'));
        $customerResp->assertRedirect('/');
        $customerResp->assertSessionHas('error');

        // Admin can view queue
        $adminResp = $this->actingAs($this->admin)->get(route('admin.trade-ins.index'));
        $adminResp->assertStatus(200);
        $adminResp->assertSee($tradeIn->trade_in_code);
        $adminResp->assertSee('iPhone 15 Pro Max');
    }

    public function test_admin_can_perform_technical_inspection_and_adjust_final_price(): void
    {
        $tradeIn = TradeIn::create([
            'trade_in_code' => 'TI-INSP-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 14 Pro',
            'storage_capacity' => '256GB',
            'battery_health' => 92,
            'physical_condition' => 'Mulus (Grade A+)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'estimated_price' => 11000000,
            'status' => 'submitted',
        ]);

        // Technician inspects unit in store and finds slight scratches (Grade B) & BH 87%
        $inspectPayload = [
            'physical_condition' => 'Lecet Pemakaian (Grade B)',
            'screen_condition' => 'Normal',
            'battery_health' => 87,
            'accessories_status' => 'Fullset Box Original',
            'imei' => '351122334455667',
            'status' => 'inspected',
            'final_price' => 9000000,
            'admin_notes' => 'Cek 3uTools 98% Hijau, ada lecet halus di bezel bawah.',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.trade-ins.inspect', $tradeIn->trade_in_code), $inspectPayload);

        $response->assertRedirect(route('admin.trade-ins.show', $tradeIn->trade_in_code));

        $tradeIn->refresh();
        $this->assertEquals('inspected', $tradeIn->status);
        $this->assertEquals('Lecet Pemakaian (Grade B)', $tradeIn->physical_condition);
        $this->assertEquals(87, $tradeIn->battery_health);
        $this->assertEquals('351122334455667', $tradeIn->imei);
        $this->assertEquals(9000000.00, (float) $tradeIn->final_price);
        $this->assertEquals('Cek 3uTools 98% Hijau, ada lecet halus di bezel bawah.', $tradeIn->admin_notes);
    }

    public function test_admin_can_approve_reject_and_mark_paid_out_trade_in(): void
    {
        $tradeIn = TradeIn::create([
            'trade_in_code' => 'TI-DEC-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 13 Pro',
            'storage_capacity' => '256GB',
            'battery_health' => 89,
            'physical_condition' => 'Mulus (Grade A)',
            'screen_condition' => 'Normal',
            'accessories_status' => 'Fullset Box Original',
            'estimated_price' => 7735000,
            'status' => 'inspected',
        ]);

        // 1. Admin Approves with adjusted final offer price
        $approveResponse = $this->actingAs($this->admin)
            ->patch(route('admin.trade-ins.decision', $tradeIn->trade_in_code), [
                'decision' => 'approved',
                'final_price' => 7600000,
                'admin_notes' => 'Disetujui bersama customer di toko.',
            ]);

        $approveResponse->assertRedirect(route('admin.trade-ins.show', $tradeIn->trade_in_code));
        $tradeIn->refresh();
        $this->assertEquals('approved', $tradeIn->status);
        $this->assertEquals(7600000.00, (float) $tradeIn->final_price);

        // 2. Admin Marks as Paid Out
        $payoutResponse = $this->actingAs($this->admin)
            ->patch(route('admin.trade-ins.decision', $tradeIn->trade_in_code), [
                'decision' => 'paid_out',
                'final_price' => 7600000,
                'admin_notes' => 'Transfer BCA berhasil Ref #998877.',
            ]);

        $payoutResponse->assertRedirect(route('admin.trade-ins.show', $tradeIn->trade_in_code));
        $tradeIn->refresh();
        $this->assertEquals('paid_out', $tradeIn->status);

        // 3. Admin Rejects another submission
        $rejectedTradeIn = TradeIn::create([
            'trade_in_code' => 'TI-REJ-' . strtoupper(Str::random(5)),
            'user_id' => $this->customer->id,
            'phone_model' => 'iPhone 12',
            'storage_capacity' => '64GB',
            'battery_health' => 75,
            'physical_condition' => 'Dent / Penyok (Grade C)',
            'screen_condition' => 'Retak',
            'accessories_status' => 'Unit Only',
            'estimated_price' => 1500000,
            'status' => 'unit_received',
        ]);

        $rejectResponse = $this->actingAs($this->admin)
            ->patch(route('admin.trade-ins.decision', $rejectedTradeIn->trade_in_code), [
                'decision' => 'rejected',
                'final_price' => 0,
                'admin_notes' => 'Unit terindikasi bypass iCloud / mesin pernah kena air.',
            ]);

        $rejectResponse->assertRedirect(route('admin.trade-ins.show', $rejectedTradeIn->trade_in_code));
        $rejectedTradeIn->refresh();
        $this->assertEquals('rejected', $rejectedTradeIn->status);
    }
}
