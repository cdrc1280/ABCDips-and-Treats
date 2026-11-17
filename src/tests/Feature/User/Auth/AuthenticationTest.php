<?php

namespace Tests\Feature\User\Auth;

use App\Domains\Access\Models\Role;
use App\Domains\User\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertInertia(fn (Assert $page) => $page->component('auth/login')->where('app.area', 'storefront'));
    }

    public function test_customer_logs_in_and_lands_on_the_storefront(): void
    {
        $user = User::factory()->customer()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_staff_with_dashboard_access_lands_in_the_back_office(): void
    {
        $user = $this->userWith('dashboard-view');

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/admin');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_creates_a_customer_and_sends_verification(): void
    {
        Event::fake([Registered::class]);
        Role::findOrCreate('customer', 'web');

        $this->post('/register', [
            'name' => 'Ana Cruz',
            'email' => 'Ana@Example.test',
            'password' => 'Sweet!Bread9',
            'password_confirmation' => 'Sweet!Bread9',
        ])->assertRedirect('/');

        $user = User::where('email', 'ana@example.test')->firstOrFail();
        $this->assertTrue($user->hasRole('customer'));
        Event::assertDispatched(Registered::class);
    }

    public function test_registration_enforces_the_password_policy(): void
    {
        $this->post('/register', [
            'name' => 'Weak',
            'email' => 'weak@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');
    }

    public function test_logout(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
