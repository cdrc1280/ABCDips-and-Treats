<?php

namespace Tests\Feature\User\Customer;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Access\Models\Role;
use App\Domains\User\Enums\CustomerPermission;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(CustomerPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: CustomerPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): User
    {
        static $n = 0;
        $n++;
        Role::findOrCreate('customer', 'web');

        $model = User::query()->create(array_merge(['name' => "Customer {$n}", 'email' => "cust{$n}@example.test", 'password' => 'Secret!123'], $attributes));
        $model->assignRole('customer');

        return $model;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/customers')->assertRedirect('/login');
        $this->put('/admin/customers/1', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/customers')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(CustomerPermission::CustomersView));

        $this->get('/admin/customers')->assertOk();
        $this->get("/admin/customers/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/customers/{$record->id}", [])->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/customers')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('customer/index')
                ->has('records.data', 2)
                ->where('table.name', 'customers')
                ->missing('summary'));
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/customers/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('customer/edit')->where('record.id', $record->id));

        $this->put("/admin/customers/{$record->id}", ['name' => 'Edited', 'email' => 'edited@example.test', 'city' => 'Imus'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/customers')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('users', array_merge(['id' => $record->id], ['name' => 'Edited', 'city' => 'Imus']));
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'Corazon Aquino']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/customers?search[keyword]=corazon')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
