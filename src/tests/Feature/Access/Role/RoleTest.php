<?php

namespace Tests\Feature\Access\Role;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Access\Enums\RolePermission;
use App\Domains\Access\Models\Permission;
use App\Domains\Access\Models\Role;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class RoleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(RolePermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: RolePermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Role
    {
        static $n = 0;
        $n++;

        $model = Role::query()->create(array_merge(['name' => "custom_role_{$n}", 'guard_name' => 'web'], $attributes));

        return $model;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/roles')->assertRedirect('/login');
        $this->post('/admin/roles', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/roles')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(RolePermission::RolesView));

        $this->get('/admin/roles')->assertOk();
        $this->get('/admin/roles/create')->assertForbidden();
        $this->get("/admin/roles/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/roles/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/roles/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/roles')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('role/index')
                ->has('records.data', 2)
                ->where('table.name', 'roles')
                ->missing('summary'));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        Permission::findOrCreate('dashboard-view', 'web');
        Permission::findOrCreate('orders-view', 'web');

        $this->actingAs($this->admin())
            ->post('/admin/roles', ['name' => 'Pastry Lead', 'permissions' => ['dashboard-view', 'orders-view']])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/roles')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('roles', ['name' => 'pastry_lead', 'guard_name' => 'web']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/roles', [])
            ->assertSessionHasErrors(['name']);
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        Permission::findOrCreate('dashboard-view', 'web');
        Permission::findOrCreate('orders-view', 'web');

        $this->actingAs($this->admin())
            ->get("/admin/roles/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('role/edit')->where('record.id', $record->id));

        $this->put("/admin/roles/{$record->id}", ['name' => 'custom_role_x', 'permissions' => ['orders-view']])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/roles')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('roles', array_merge(['id' => $record->id], ['name' => 'custom_role_x']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/roles/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertModelMissing($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'night_baker']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/roles?search[keyword]=night')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
