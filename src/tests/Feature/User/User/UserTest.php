<?php

namespace Tests\Feature\User\User;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Access\Models\Role;
use App\Domains\User\Enums\UserPermission;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class UserTest extends TestCase
{
    use RefreshDatabase;

    private function admin(UserPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: UserPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): User
    {
        static $n = 0;
        $n++;
        Role::findOrCreate('staff', 'web');

        $model = User::query()->create(array_merge(['name' => "Staff {$n}", 'email' => "staff{$n}@example.test", 'password' => 'Secret!123'], $attributes));
        $model->assignRole('staff');

        return $model;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
        $this->post('/admin/users', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/users')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(UserPermission::UsersView));

        $this->get('/admin/users')->assertOk();
        $this->get('/admin/users/create')->assertForbidden();
        $this->get("/admin/users/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/users/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/users/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/users')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('user/index')
                ->has('records.data', 2)
                ->where('table.name', 'users')
                ->missing('summary'));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('staff', 'web');

        $this->actingAs($this->admin())
            ->post('/admin/users', ['name' => 'Mae Torres', 'email' => 'MAE@example.test', 'roles' => ['manager'], 'password' => 'Bread!2026x', 'password_confirmation' => 'Bread!2026x'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/users')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('users', ['email' => 'mae@example.test', 'name' => 'Mae Torres']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/users', [])
            ->assertSessionHasErrors(['name', 'email', 'roles']);
    }

    public function test_unique_email_ignores_soft_deleted_rows(): void
    {
        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('staff', 'web');

        $existing = $this->record();
        $payload = array_merge(['name' => 'Mae Torres', 'email' => 'MAE@example.test', 'roles' => ['manager'], 'password' => 'Bread!2026x', 'password_confirmation' => 'Bread!2026x'], ['email' => $existing->email]);

        $this->actingAs($this->admin())->post('/admin/users', $payload)->assertSessionHasErrors(['email']);

        $existing->delete();
        $this->post('/admin/users', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('staff', 'web');

        $this->actingAs($this->admin())
            ->get("/admin/users/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('user/edit')->where('record.id', $record->id));

        $this->put("/admin/users/{$record->id}", ['name' => 'Mae T.', 'email' => 'mae@example.test', 'roles' => ['staff'], 'password' => '', 'password_confirmation' => ''])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/users')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('users', array_merge(['id' => $record->id], ['name' => 'Mae T.']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/users/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'Rogelio Bautista']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/users?search[keyword]=rogelio')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
