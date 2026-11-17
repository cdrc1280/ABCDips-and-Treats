<?php

namespace Tests\Feature\Analytics\Dashboard;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_users_without_permission_get_403(): void
    {
        $this->actingAs(User::factory()->customer()->create())->get('/admin')->assertForbidden();
    }

    public function test_dashboard_renders_with_deferred_analytics_and_menus(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))
            ->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard/index')
                ->missing('analytics')
                ->where('period', '30d')
                ->where('app.area', 'admin')
                ->has('menus', 1)
                ->where('menus.0.title', 'Dashboard')
                ->where('auth.permissions', ['dashboard-view'])
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('analytics.points', 30)->has('analytics.previous')));
    }

    public function test_super_admin_passes_every_check(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())
            ->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('auth.is_super_admin', true));
    }
}
