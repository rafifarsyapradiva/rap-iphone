<?php

namespace Tests\Feature;

use App\Models\NotificationLog;
use App\Models\User;
use App\Services\SystemHealthService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SystemConfigurationMonitoringTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $owner;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@rap-iphone.com',
        ]);

        $this->owner = User::where('role', 'owner')->first() ?? User::factory()->create([
            'role' => 'owner',
            'email' => 'owner@rap-iphone.com',
        ]);

        $this->customer = User::where('role', 'customer')->first() ?? User::factory()->create([
            'role' => 'customer',
            'email' => 'customer@rap-iphone.com',
        ]);
    }

    public function test_admin_can_access_system_settings_and_api_monitoring_dashboard(): void
    {
        NotificationLog::create([
            'channel' => 'whatsapp',
            'recipient' => '081234567890',
            'event_name' => 'payment_success',
            'status' => 'sent',
            'response_payload' => ['status' => true],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.settings'));

        $response->assertOk();
        $response->assertSee('Konfigurasi Sistem & Integrasi API', false);
        $response->assertSee('Arsitektur Service Layer Aktif');
        $response->assertSee('Payment Gateway Snap');
        $response->assertSee('Notifikasi WhatsApp Otomatis');
        $response->assertSee('Pengiriman Email &amp; Faktur PDF', false);
        $response->assertSee('Single Sign-On (SSO)');
        $response->assertSee('Spesifikasi & Lingkungan Runtime Sistem', false);
        $response->assertSee('payment_success');
    }

    public function test_owner_can_access_system_settings(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.settings'));

        $response->assertOk();
        $response->assertSee('Konfigurasi Sistem & Integrasi API', false);
    }

    public function test_customer_is_forbidden_from_accessing_system_settings(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.settings'));

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_unauthenticated_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.settings'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_system_health_service_returns_structured_full_status(): void
    {
        $service = app(SystemHealthService::class);
        $status = $service->getFullStatus(false);

        $this->assertArrayHasKey('midtrans', $status);
        $this->assertArrayHasKey('fonnte', $status);
        $this->assertArrayHasKey('smtp', $status);
        $this->assertArrayHasKey('google', $status);
        $this->assertArrayHasKey('system', $status);

        $this->assertEquals('Midtrans Payment Gateway (Snap API & Webhook)', $status['midtrans']['name']);
        $this->assertEquals('Fonnte WhatsApp Notification Gateway', $status['fonnte']['name']);
        $this->assertEquals('Laravel Mailer & DomPDF Invoice Engine', $status['smtp']['name']);
        $this->assertEquals('Google Console OAuth 2.0 (Socialite)', $status['google']['name']);

        $this->assertArrayHasKey('php_version', $status['system']);
        $this->assertArrayHasKey('laravel_version', $status['system']);
        $this->assertArrayHasKey('db_connection', $status['system']);
    }

    public function test_admin_can_probe_midtrans_connection_via_ajax(): void
    {
        Config::set('services.midtrans.server_key', 'SB-Mid-server-test12345');
        Config::set('services.midtrans.is_production', false);

        Http::fake([
            'https://api.sandbox.midtrans.com/v2/token' => Http::response(['status_code' => '400', 'status_message' => 'Validation error'], 400),
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test', ['gateway' => 'midtrans']));

        $response->assertOk();
        $response->assertJson([
            'key' => 'midtrans',
            'configured' => true,
            'status' => 'connected',
        ]);
        $this->assertNotNull($response->json('latency_ms'));
    }

    public function test_admin_can_probe_fonnte_connection_via_ajax(): void
    {
        Config::set('services.fonnte.token', 'valid-test-fonnte-token');

        Http::fake([
            'https://api.fonnte.com/device' => Http::response([
                'status' => true,
                'device_status' => 'connect',
                'device' => '081299998888',
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test', ['gateway' => 'fonnte']));

        $response->assertOk();
        $response->assertJson([
            'key' => 'fonnte',
            'status' => 'connected',
        ]);
        $this->assertNotNull($response->json('latency_ms'));
    }

    public function test_admin_can_probe_smtp_connection_via_ajax(): void
    {
        Config::set('mail.default', 'log');

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test', ['gateway' => 'smtp']));

        $response->assertOk();
        $response->assertJson([
            'key' => 'smtp',
            'driver' => 'log',
            'status' => 'simulation',
        ]);
    }

    public function test_admin_can_probe_google_oauth_connection_via_ajax(): void
    {
        Config::set('services.google.client_id', 'test-client-id.apps.googleusercontent.com');

        Http::fake([
            'https://accounts.google.com/.well-known/openid-configuration' => Http::response([
                'issuer' => 'https://accounts.google.com',
                'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test', ['gateway' => 'google']));

        $response->assertOk();
        $response->assertJson([
            'key' => 'google',
            'configured' => true,
            'status' => 'connected',
        ]);
        $this->assertNotNull($response->json('latency_ms'));
    }

    public function test_probe_returns_404_for_unknown_gateway(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.settings.test', ['gateway' => 'unknown-service']));

        $response->assertNotFound();
        $response->assertJson([
            'status' => 'error',
            'message' => 'Gateway tidak dikenal.',
        ]);
    }

    public function test_customer_cannot_invoke_probe_endpoint(): void
    {
        $response = $this->actingAs($this->customer)
            ->postJson(route('admin.settings.test', ['gateway' => 'midtrans']));

        $response->assertForbidden();
    }
}
