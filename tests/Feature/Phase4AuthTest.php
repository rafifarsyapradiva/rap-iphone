<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class Phase4AuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test 1: Login page renders with glassmorphism components.
     */
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Rap-iPhone');
        $response->assertSee('Masuk dengan Google');
        $response->assertSee('Akun Uji Coba Cepat');
    }

    /**
     * Test 2: User can login with valid credentials.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $email = 'valid_' . uniqid() . '@test.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }

    /**
     * Test 3: Login fails with invalid credentials and displays error.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $email = 'someone_' . uniqid() . '@test.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test 4: Role redirection after login for Owner.
     */
    public function test_owner_is_redirected_to_admin_workspace(): void
    {
        $email = 'owner_' . uniqid() . '@rap-iphone.com';
        $owner = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($owner);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test 5: Role redirection after login for Admin.
     */
    public function test_admin_is_redirected_to_admin_workspace(): void
    {
        $email = 'admin_' . uniqid() . '@rap-iphone.com';
        $admin = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test 6: Registration page renders successfully.
     */
    public function test_register_page_renders_successfully(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Buat Akun Rap-iPhone');
        $response->assertSee('Daftar 1-Klik dengan Google');
        $response->assertSee('Nomor WhatsApp');
    }

    /**
     * Test 7: Customer can register self with Bcrypt hash and customer role.
     */
    public function test_user_can_register_new_account(): void
    {
        $email = 'reg_' . uniqid() . '@test.com';

        $response = $this->post('/register', [
            'name' => 'Kurniawan Pratama',
            'email' => $email,
            'phone_number' => '081298765432',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'terms' => 'on',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'customer',
            'phone_number' => '081298765432',
        ]);

        $user = User::where('email', $email)->first();
        $this->assertTrue(Hash::check('secret12345', $user->password));

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home'));
    }

    /**
     * Test 8: Registration validation prevents duplicate email.
     */
    public function test_registration_validation_prevents_duplicate_email(): void
    {
        $email = 'duplicate_' . uniqid() . '@test.com';
        User::factory()->create([
            'email' => $email,
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Another User',
            'email' => $email,
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'terms' => 'on',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test 9: Authenticated user can logout.
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test 10: Role middleware blocks guest and redirects to login.
     */
    public function test_role_middleware_blocks_guest(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    }

    /**
     * Test 11: Role middleware blocks customer from admin workspace.
     */
    public function test_role_middleware_blocks_customer_from_admin_area(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/admin/dashboard');

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    /**
     * Test 12: Role middleware allows admin on admin dashboard but blocks from owner area.
     */
    public function test_role_middleware_guards_admin_and_owner_areas(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Admin allowed on admin dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Operasional');

        // Admin blocked on owner exclusive area
        $responseOwner = $this->actingAs($admin)->get('/owner/dashboard');
        $responseOwner->assertRedirect(route('admin.dashboard'));
        $responseOwner->assertSessionHas('error');
    }

    /**
     * Test 13: Role middleware allows owner on both admin and owner areas.
     */
    public function test_role_middleware_allows_owner_on_all_management_areas(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        // Owner can access admin workspace
        $resAdmin = $this->actingAs($owner)->get('/admin/dashboard');
        $resAdmin->assertStatus(200);

        // Owner can access owner exclusive workspace
        $resOwner = $this->actingAs($owner)->get('/owner/dashboard');
        $resOwner->assertStatus(200);
        $resOwner->assertSee('Portal Finansial');
    }

    /**
     * Test 14: Google Socialite redirect generates redirect response.
     */
    public function test_google_socialite_redirect_generates_redirect(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('redirect')
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google');
        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    /**
     * Test 15: Google Socialite callback creates new customer when not registered.
     */
    public function test_google_socialite_callback_creates_new_customer(): void
    {
        $uniqueGoogleId = 'google-unique-' . uniqid();
        $email = 'google_' . uniqid() . '@example.com';

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn($uniqueGoogleId);
        $abstractUser->shouldReceive('getEmail')->andReturn($email);
        $abstractUser->shouldReceive('getName')->andReturn('Google Customer');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/photo.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'google_id' => $uniqueGoogleId,
            'role' => 'customer',
        ]);

        $createdUser = User::where('email', $email)->first();
        $this->assertAuthenticatedAs($createdUser);
        $response->assertRedirect(url('/'));
    }

    /**
     * Test 16: Google Socialite callback links existing user by email.
     */
    public function test_google_socialite_callback_links_existing_user_by_email(): void
    {
        $email = 'existing_' . uniqid() . '@example.com';
        $existing = User::factory()->create([
            'email' => $email,
            'google_id' => null,
            'role' => 'customer',
        ]);

        $linkedGoogleId = 'google-linked-' . uniqid();

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn($linkedGoogleId);
        $abstractUser->shouldReceive('getEmail')->andReturn($email);
        $abstractUser->shouldReceive('getName')->andReturn('Existing User');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://google.com/pic.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertDatabaseHas('users', [
            'id' => $existing->id,
            'email' => $email,
            'google_id' => $linkedGoogleId,
        ]);

        $this->assertAuthenticatedAs($existing->fresh());
        $response->assertRedirect(url('/'));
    }
}
