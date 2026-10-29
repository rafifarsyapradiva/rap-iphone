<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Phase14AdminAuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Admin login page renders with Digimap Enterprise Light UI and admin demo chip.
     */
    public function test_admin_login_page_renders_with_enterprise_light_ui_and_demo_chip(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Login Staf Operasional');
        $response->assertSee('Store Operations Hub');
        $response->assertSee('admin@rap-iphone.com');
        $response->assertSee('Strict Role Guard Active');
        $response->assertSee('Akun Uji Coba Admin Operasional');
    }

    /**
     * 2. Admin login page is completely isolated from customer registration and Google OAuth.
     */
    public function test_admin_login_page_is_isolated_from_customer_elements(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertDontSee('Masuk dengan Google');
        $response->assertDontSee('Daftar Sekarang');
        $response->assertDontSee('Belum memiliki akun Rap-iPhone');
        $response->assertDontSee('Daftar 1-Klik');
    }

    /**
     * 3. Admin can login with valid admin credentials and is redirected to admin dashboard.
     */
    public function test_admin_can_login_with_valid_admin_credentials_and_redirects_to_dashboard(): void
    {
        $email = 'admin_' . uniqid() . '@rap-iphone.com';
        $admin = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');
    }

    /**
     * 4. Strict Role Guard rejects customer account attempting to login via /admin/login.
     */
    public function test_strict_admin_role_guard_rejects_customer_account(): void
    {
        $email = 'cust_' . uniqid() . '@test.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');

        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Staf Operasional Toko (Admin)', $errorMessage);
    }

    /**
     * 5. Strict Role Guard rejects owner account attempting to login via /admin/login.
     */
    public function test_strict_admin_role_guard_rejects_owner_account(): void
    {
        $email = 'owner_' . uniqid() . '@rap-iphone.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');

        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Portal Eksekutif (/owner/login)', $errorMessage);
    }

    /**
     * 6. Admin login fails with invalid credentials.
     */
    public function test_admin_login_fails_with_invalid_credentials(): void
    {
        $email = 'admin_' . uniqid() . '@rap-iphone.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('correct_password'),
            'role' => 'admin',
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => $email,
            'password' => 'wrong_password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');
    }

    /**
     * 7. Admin rate limiter triggers after consecutive failed attempts.
     */
    public function test_admin_rate_limiter_triggers_after_consecutive_failed_attempts(): void
    {
        $email = 'ratelimit_' . uniqid() . '@rap-iphone.com';

        for ($i = 0; $i < 5; $i++) {
            $this->from('/admin/login')->post('/admin/login', [
                'email' => $email,
                'password' => 'wrong',
            ]);
        }

        // 6th attempt should be blocked by rate limiter
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => $email,
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Terlalu banyak percobaan masuk admin', $errorMessage);
    }

    /**
     * 8. Unauthenticated guest accessing /admin/* is redirected to /admin/login.
     */
    public function test_unauthenticated_guest_accessing_admin_area_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * 9. Admin can logout and is redirected back to /admin/login with flash message.
     */
    public function test_admin_can_logout_and_is_redirected_to_admin_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('success', 'Anda telah berhasil keluar dari Portal Operasional Admin.');
    }

    /**
     * 10. Authenticated admin visiting /admin/login is redirected to /admin/dashboard.
     */
    public function test_authenticated_admin_visiting_login_page_is_redirected_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/login');

        $response->assertRedirect(route('admin.dashboard'));
    }
}

