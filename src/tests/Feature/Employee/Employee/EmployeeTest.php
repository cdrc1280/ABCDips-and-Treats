<?php

namespace Tests\Feature\Employee\Employee;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Employee\Enums\EmployeePermission;
use App\Domains\Employee\Models\Employee;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(EmployeePermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: EmployeePermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Employee
    {
        static $n = 0;
        $n++;

        return Employee::query()->create(array_merge(['first_name' => "Staff{$n}", 'last_name' => 'Cruz', 'email' => "e{$n}@example.test", 'role_title' => 'Cashier', 'employment_type' => 'full_time', 'basic_monthly_salary' => 18000, 'hired_at' => '2026-01-05', 'is_active' => true], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/employees')->assertRedirect('/login');
        $this->post('/admin/employees', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/employees')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(EmployeePermission::EmployeesView));

        $this->get('/admin/employees')->assertOk();
        $this->get('/admin/employees/create')->assertForbidden();
        $this->get("/admin/employees/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/employees/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/employees/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/employees')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('employee/index')
                ->has('records.data', 2)
                ->where('table.name', 'employees')
                ->missing('summary'));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/employees', ['first_name' => 'Lia', 'last_name' => 'Santos', 'email' => 'lia@example.test', 'role_title' => 'Head Baker', 'employment_type' => 'full_time', 'basic_monthly_salary' => '25000', 'hired_at' => '2026-03-01', 'is_active' => '1'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/employees')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('employees', ['first_name' => 'Lia', 'role_title' => 'Head Baker']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/employees', [])
            ->assertSessionHasErrors(['first_name', 'last_name', 'email', 'hired_at']);
    }

    public function test_unique_employee_number_ignores_soft_deleted_rows(): void
    {

        $existing = $this->record();
        $payload = array_merge(['first_name' => 'Lia', 'last_name' => 'Santos', 'email' => 'lia@example.test', 'role_title' => 'Head Baker', 'employment_type' => 'full_time', 'basic_monthly_salary' => '25000', 'hired_at' => '2026-03-01', 'is_active' => '1'], ['employee_number' => $existing->employee_number]);

        $this->actingAs($this->admin())->post('/admin/employees', $payload)->assertSessionHasErrors(['employee_number']);

        $existing->delete();
        $this->post('/admin/employees', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/employees/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('employee/edit')->where('record.id', $record->id));

        $this->put("/admin/employees/{$record->id}", ['first_name' => 'Lia', 'last_name' => 'Reyes', 'email' => 'lia@example.test', 'role_title' => 'Manager', 'employment_type' => 'part_time', 'basic_monthly_salary' => '15000', 'hired_at' => '2026-03-01', 'is_active' => '0'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/employees')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('employees', array_merge(['id' => $record->id], ['last_name' => 'Reyes', 'employment_type' => 'part_time', 'is_active' => false]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/employees/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['first_name' => 'Marisol']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/employees?search[keyword]=marisol')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
