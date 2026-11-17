<?php

namespace Tests\Feature\Access;

use App\Domains\Access\Enums\AccessPermission;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * The storefront and back-office JS entries each resolve only their own pages, so an Inertia
 * visit from one shell into the other has to become a full page load (409 + X-Inertia-Location).
 */
class ShellSwitchTest extends TestCase
{
    use RefreshDatabase;

    private function versionFor(string $path): string
    {
        return (string) app(HandleInertiaRequests::class)->version(Request::create($path));
    }

    public function test_each_shell_has_its_own_asset_version(): void
    {
        $this->assertNotSame($this->versionFor('/'), $this->versionFor('/admin'));
        $this->assertSame($this->versionFor('/admin'), $this->versionFor('/admin/products'));
        $this->assertSame($this->versionFor('/'), $this->versionFor('/shop'));
    }

    public function test_inertia_visit_from_storefront_into_admin_forces_a_full_reload(): void
    {
        $user = $this->userWith(AccessPermission::DashboardView);

        $this->actingAs($user)
            ->get('/admin', ['X-Inertia' => 'true', 'X-Inertia-Version' => $this->versionFor('/login')])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', url('/admin'));
    }

    public function test_inertia_visit_from_admin_into_storefront_forces_a_full_reload(): void
    {
        $this->get('/shop', ['X-Inertia' => 'true', 'X-Inertia-Version' => $this->versionFor('/admin')])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', url('/shop'));
    }

    public function test_inertia_visit_within_a_shell_stays_a_spa_visit(): void
    {
        $this->get('/shop', ['X-Inertia' => 'true', 'X-Inertia-Version' => $this->versionFor('/')])
            ->assertOk()
            ->assertHeader('X-Inertia', 'true');
    }
}
