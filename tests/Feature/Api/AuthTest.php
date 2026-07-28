<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_me_endpoint(): void
    {
        $response = $this->getJson('/api/me');
        $response->assertStatus(401);
    }

    public function test_user_can_register(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Test Customer',
            'email'                 => 'test@abcdips.test',
            'password'              => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['user', 'token'],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'test@abcdips.test']);
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'     => '',
            'email'    => 'not-an-email',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login(): void
    {
        // Seed roles first
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'email'    => 'login@abcdips.test',
            'password' => bcrypt('Password@123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'login@abcdips.test',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['user', 'token'],
            ]);
    }

    public function test_wrong_credentials_return_401(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'nobody@abcdips.test',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_me(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('customer');

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_user_can_logout(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/auth/logout')
            ->assertStatus(200);

        // Verify the token no longer exists in DB
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_only_super_admin_and_admin_can_access_filament_panel(): void
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $panel = Filament::getPanel('admin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertTrue($superAdmin->canAccessPanel($panel));
        $this->assertFalse($customer->canAccessPanel($panel));
        $this->assertFalse($staff->canAccessPanel($panel));
    }
}
