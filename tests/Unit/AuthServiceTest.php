<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuthService();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_register_creates_user_with_customer_role(): void
    {
        $result = $this->service->register([
            'name'     => 'Jane Doe',
            'email'    => 'jane@abcdips.test',
            'password' => 'Password@123',
        ]);

        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertNotEmpty($result['token']);
        $this->assertTrue($result['user']->hasRole('customer'));
    }

    public function test_login_returns_null_for_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'wrong@abcdips.test',
            'password' => bcrypt('CorrectPassword@1'),
        ]);

        $result = $this->service->login([
            'email'    => 'wrong@abcdips.test',
            'password' => 'WrongPassword@1',
        ]);

        $this->assertNull($result);
    }

    public function test_login_returns_token_for_correct_credentials(): void
    {
        User::factory()->create([
            'email'    => 'correct@abcdips.test',
            'password' => bcrypt('CorrectPassword@1'),
        ]);

        $result = $this->service->login([
            'email'    => 'correct@abcdips.test',
            'password' => 'CorrectPassword@1',
        ]);

        $this->assertNotNull($result);
        $this->assertNotEmpty($result['token']);
    }
}
