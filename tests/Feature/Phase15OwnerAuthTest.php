<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Phase15OwnerAuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Owner login page renders with Apple Executive Daylight & Titanium Accent UI and owner demo chip.
     */
    public function test_owner_login_page_renders_with_executive_daylight_ui_and_demo_chip(): void
    {
        $response = $this->get('/owner/login');

        $response->assertStatus(200);
        $response->assertSee('Portal Eksekutif & Finansial');
        $response->assertSee('Executive & Financial Hub');
        $response->assertSee('owner@rap-iphone.com');
        $response->assertSee('Strict Owner Role Guard Active');
        $response->assertSee('Akun Uji Coba Pemilik Usaha (Owner)');
    }

    /**
     * 2. Owner login page is completely isolated from customer registration and Google OAuth.
     */
    public function test_owner_login_page_is_isolated_from_customer_elements(): void
    {
        $response = $this->get('/owner/login');

        $response->assertStatus(200);
        $response->assertDontSee('Masuk dengan Google');
        $response->assertDontSee('Daftar Sekarang');
        $response->assertDontSee('Belum memiliki akun Rap-iPhone');
        $response->assertDontSee('Daftar 1-Klik');
    }

    /**
     * 3. Owner can login with valid owner credentials and is redirected to owner dashboard.
     */
    public function test_owner_can_login_with_valid_owner_credentials_and_redirects_to_dashboard(): void
    {
        $email = 'owner_' . uniqid() . '@rap-iphone.com';
        $owner = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        $response = $this->post('/owner/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($owner);
        $response->assertRedirect(route('owner.dashboard'));
        $response->assertSessionHas('success');
    }

    /**
     * 4. Strict Role Guard rejects customer account attempting to login via /owner/login.
     */
    public function test_strict_owner_role_guard_rejects_customer_account(): void
    {
        $email = 'cust_' . uniqid() . '@test.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $response = $this->from('/owner/login')->post('/owner/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/owner/login');
        $response->assertSessionHasErrors('email');

        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Pemilik Usaha (Owner)', $errorMessage);
    }

    /**
     * 5. Strict Role Guard rejects admin account attempting to login via /owner/login.
     */
    public function test_strict_owner_role_guard_rejects_admin_account(): void
    {
        $email = 'admin_' . uniqid() . '@rap-iphone.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->from('/owner/login')->post('/owner/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/owner/login');
        $response->assertSessionHasErrors('email');

        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Portal Operasional (/admin/login)', $errorMessage);
    }

    /**
     * 6. Owner login fails with invalid credentials.
     */
    public function test_owner_login_fails_with_invalid_credentials(): void
    {
        $email = 'owner_' . uniqid() . '@rap-iphone.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('correct_password'),
            'role' => 'owner',
        ]);

        $response = $this->from('/owner/login')->post('/owner/login', [
            'email' => $email,
            'password' => 'wrong_password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/owner/login');
        $response->assertSessionHasErrors('email');
    }

    /**
     * 7. Owner rate limiter triggers after consecutive failed attempts.
     */
    public function test_owner_rate_limiter_triggers_after_consecutive_failed_attempts(): void
    {
        $email = 'ratelimit_owner_' . uniqid() . '@rap-iphone.com';

        for ($i = 0; $i < 5; $i++) {
            $this->from('/owner/login')->post('/owner/login', [
                'email' => $email,
                'password' => 'wrong',
            ]);
        }

        // 6th attempt should be blocked by rate limiter
        $response = $this->from('/owner/login')->post('/owner/login', [
            'email' => $email,
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Terlalu banyak percobaan masuk owner', $errorMessage);
    }

    /**
     * 8. Unauthenticated guest accessing /owner/* is redirected to /owner/login.
     */
    public function test_unauthenticated_guest_accessing_owner_area_is_redirected_to_owner_login(): void
    {
        $response = $this->get('/owner/dashboard');

        $response->assertRedirect(route('owner.login'));
    }

    /**
     * 9. Owner can logout and is redirected back to /owner/login with flash message.
     */
    public function test_owner_can_logout_and_is_redirected_to_owner_login(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
        ]);

        $response = $this->actingAs($owner)->post('/owner/logout');

        $this->assertGuest();
        $response->assertRedirect(route('owner.login'));
        $response->assertSessionHas('success', 'Anda telah berhasil keluar dari Portal Eksekutif Owner.');
    }

    /**
     * 10. Authenticated owner visiting /owner/login is redirected to /owner/dashboard.
     */
    public function test_authenticated_owner_visiting_login_page_is_redirected_to_dashboard(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
        ]);

        $response = $this->actingAs($owner)->get('/owner/login');

        $response->assertRedirect(route('owner.dashboard'));
    }

    /**
     * 11. Authenticated admin visiting /owner/login is redirected to /admin/dashboard.
     */
    public function test_authenticated_admin_visiting_owner_login_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/owner/login');

        $response->assertRedirect(route('admin.dashboard'));
    }
}

