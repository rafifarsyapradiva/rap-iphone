<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class Phase16CustomerAuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Customer login page renders with Digimap B2C Daylight UI, customer demo chip, and Google OAuth.
     */
    public function test_customer_login_page_renders_with_b2c_daylight_ui_and_customer_demo_chip(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Rap-iPhone');
        $response->assertSee('Portal Khusus Pelanggan');
        $response->assertSee('customer@rap-iphone.com');
        $response->assertSee('Masuk dengan Google');
        $response->assertSee('100% Resmi');
        $response->assertSee('Gratis Ongkir');
        $response->assertSee('Trade-In 15');
    }

    /**
     * 2. Customer login page is completely purified from Admin and Owner demo chips.
     */
    public function test_customer_login_page_is_isolated_from_admin_and_owner_chips(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        // Ensure Admin and Owner demo chips are NOT present
        $response->assertDontSee('admin@rap-iphone.com');
        $response->assertDontSee('owner@rap-iphone.com');
    }

    /**
     * 3. Customer can login with valid customer credentials and is redirected to storefront.
     */
    public function test_customer_can_login_with_valid_credentials_and_redirects_to_storefront(): void
    {
        $email = 'cust_' . uniqid() . '@test.com';
        $customer = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($customer);
        $response->assertRedirect(url('/'));
        $response->assertSessionHas('success');
    }

    /**
     * 4. Strict Customer Role Guard rejects Admin account on customer /login and guides to /admin/login.
     */
    public function test_strict_customer_role_guard_rejects_admin_account(): void
    {
        $email = 'admin_' . uniqid() . '@rap-iphone.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');

        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Staf Operasional (Admin)', $errorMessage);
        $this->assertStringContainsString('/admin/login', $errorMessage);
    }

    /**
     * 5. Strict Customer Role Guard rejects Owner account on customer /login and guides to /owner/login.
     */
    public function test_strict_customer_role_guard_rejects_owner_account(): void
    {
        $email = 'owner_' . uniqid() . '@rap-iphone.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');

        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Pemilik Usaha (Owner)', $errorMessage);
        $this->assertStringContainsString('/owner/login', $errorMessage);
    }

    /**
     * 6. Brute-force rate limiting protects customer login portal after 5 failed attempts.
     */
    public function test_customer_login_throttling_protects_against_brute_force(): void
    {
        $email = 'throttle_' . uniqid() . '@test.com';

        for ($i = 0; $i < 5; $i++) {
            $this->from('/login')->post('/login', [
                'email' => $email,
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->from('/login')->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan', session('errors')->first('email'));
    }

    /**
     * 7. Customer registration page renders with full form, WhatsApp Fonnte note, and Google 1-click option.
     */
    public function test_customer_register_page_renders_with_fields_and_google_button(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Buat Akun Rap-iPhone');
        $response->assertSee('Registrasi Mandiri Pelanggan');
        $response->assertSee('Daftar 1-Klik dengan Google');
        $response->assertSee('Nomor WhatsApp');
        $response->assertSee('Notifikasi pesanan & resi via Fonnte WA');
        $response->assertSee('Syarat & Ketentuan');
        $response->assertSee('Kebijakan Privasi');
        $response->assertSee('Staf Toko (Admin) dikelola dan didaftarkan langsung oleh Pemilik Usaha');
    }

    /**
     * 8. Customer can self-register and is created with Bcrypt password and 'customer' role.
     */
    public function test_customer_can_self_register_and_receives_customer_role(): void
    {
        $email = 'newcust_' . uniqid() . '@test.com';

        $response = $this->post('/register', [
            'name' => 'Fajar Pratama',
            'email' => $email,
            'phone_number' => '081234567890',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'terms' => 'on',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'customer',
            'phone_number' => '081234567890',
        ]);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret12345', $user->password));
        $this->assertEquals('customer', $user->role);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');
    }

    /**
     * 9. Registration validation enforces required fields, email format, and unique constraint.
     */
    public function test_registration_validation_enforces_required_fields_and_unique_email(): void
    {
        $existingEmail = 'existing_' . uniqid() . '@test.com';
        User::factory()->create(['email' => $existingEmail]);

        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => $existingEmail,
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name', 'email', 'password', 'terms']);
    }

    /**
     * 10. Google OAuth callback authenticates existing customer.
     */
    public function test_google_oauth_authenticates_existing_customer(): void
    {
        $email = 'google_cust_' . uniqid() . '@test.com';
        $customer = User::factory()->create([
            'email' => $email,
            'role' => 'customer',
            'google_id' => null,
        ]);

        $googleId = 'google_id_' . uniqid();

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn($googleId);
        $abstractUser->shouldReceive('getEmail')->andReturn($email);
        $abstractUser->shouldReceive('getName')->andReturn('Google Customer');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.google.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticatedAs($customer);
        $this->assertEquals($googleId, $customer->fresh()->google_id);
        $response->assertRedirect(url('/'));
    }

    /**
     * 11. Google OAuth callback creates a new customer account with 'customer' role.
     */
    public function test_google_oauth_creates_new_customer_role(): void
    {
        $email = 'new_google_' . uniqid() . '@gmail.com';
        $googleId = 'gid_' . uniqid();

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn($googleId);
        $abstractUser->shouldReceive('getEmail')->andReturn($email);
        $abstractUser->shouldReceive('getName')->andReturn('New Google User');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.google.com/new.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'google_id' => $googleId,
            'role' => 'customer',
        ]);

        $created = User::where('email', $email)->first();
        $this->assertAuthenticatedAs($created);
        $response->assertRedirect(url('/'));
    }

    /**
     * 12. Strict Google OAuth guard rejects Admin account and redirects to /admin/login.
     */
    public function test_strict_google_oauth_guard_rejects_admin_account(): void
    {
        $email = 'admin_google_' . uniqid() . '@rap-iphone.com';
        User::factory()->create([
            'email' => $email,
            'role' => 'admin',
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('gid_admin_' . uniqid());
        $abstractUser->shouldReceive('getEmail')->andReturn($email);
        $abstractUser->shouldReceive('getName')->andReturn('Admin User');
        $abstractUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Staf Operasional (Admin)', session('errors')->first('email'));
    }

    /**
     * 13. Strict Google OAuth guard rejects Owner account and redirects to /owner/login.
     */
    public function test_strict_google_oauth_guard_rejects_owner_account(): void
    {
        $email = 'owner_google_' . uniqid() . '@rap-iphone.com';
        User::factory()->create([
            'email' => $email,
            'role' => 'owner',
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('gid_owner_' . uniqid());
        $abstractUser->shouldReceive('getEmail')->andReturn($email);
        $abstractUser->shouldReceive('getName')->andReturn('Owner User');
        $abstractUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertGuest();
        $response->assertRedirect(route('owner.login'));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Pemilik Usaha (Owner)', session('errors')->first('email'));
    }

    /**
     * 14. Authenticated customer visiting /login or /register is redirected to storefront.
     */
    public function test_authenticated_customer_visiting_auth_pages_is_redirected_to_storefront(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $resLogin = $this->actingAs($customer)->get('/login');
        $resLogin->assertRedirect(url('/'));

        $resRegister = $this->actingAs($customer)->get('/register');
        $resRegister->assertRedirect(url('/'));
    }

    /**
     * 15. Authenticated admin visiting customer /login is redirected to admin dashboard.
     */
    public function test_authenticated_admin_visiting_customer_login_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/login');
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * 16. Authenticated owner visiting customer /login is redirected to owner dashboard.
     */
    public function test_authenticated_owner_visiting_customer_login_is_redirected_to_owner_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get('/login');
        $response->assertRedirect(route('owner.dashboard'));
    }

    /**
     * 17. Explicit customer alias routes are fully functional.
     */
    public function test_customer_alias_routes_are_functional(): void
    {
        // GET /customer/login
        $responseLogin = $this->get(route('customer.login'));
        $responseLogin->assertStatus(200);
        $responseLogin->assertSee('Masuk ke Rap-iPhone');

        // GET /customer/register
        $responseRegister = $this->get(route('customer.register'));
        $responseRegister->assertStatus(200);
        $responseRegister->assertSee('Buat Akun Rap-iPhone');

        // POST /customer/login
        $customer = User::factory()->create([
            'email' => 'alias_' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $resPost = $this->post(route('customer.login.submit'), [
            'email' => $customer->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticatedAs($customer);
        $resPost->assertRedirect(url('/'));

        // POST /customer/logout
        $resLogout = $this->post(route('customer.logout'));
        $this->assertGuest();
        $resLogout->assertRedirect(url('/'));
    }
}

