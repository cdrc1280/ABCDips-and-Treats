<?php

namespace Tests\Feature\Employee\Attendance;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Employee\Enums\AttendancePermission;
use App\Domains\Employee\Models\Attendance;
use App\Domains\Employee\Models\Employee;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(AttendancePermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: AttendancePermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Attendance
    {
        static $n = 0;
        $n++;
        $employee = Employee::query()->create(['first_name' => "Emp{$n}", 'last_name' => 'Dela Cruz', 'email' => "emp{$n}@example.test", 'hired_at' => '2026-01-01']);

        return Attendance::query()->create(array_merge(['employee_id' => $employee->id, 'date' => '2026-11-0'.min(9, $n), 'status' => 'present', 'hours_worked' => 8, 'overtime_hours' => 0], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/attendances')->assertRedirect('/login');
        $this->post('/admin/attendances', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/attendances')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(AttendancePermission::AttendancesView));

        $this->get('/admin/attendances')->assertOk();
        $this->get('/admin/attendances/create')->assertForbidden();
        $this->get("/admin/attendances/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/attendances/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/attendances/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/attendances')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('attendance/index')
                ->has('records.data', 2)
                ->where('table.name', 'attendances')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        $employee = Employee::query()->create(['first_name' => 'Ana', 'last_name' => 'Lim', 'email' => 'ana@example.test', 'hired_at' => '2026-01-01']);

        $this->actingAs($this->admin())
            ->post('/admin/attendances', ['employee_id' => $employee->id, 'date' => '2026-11-14', 'status' => 'late', 'time_in' => '08:15', 'time_out' => '17:00', 'hours_worked' => '7.75', 'overtime_hours' => '0', 'is_overtime' => '0', 'is_holiday' => '0'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/attendances')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('attendances', ['employee_id' => $employee->id, 'status' => 'late']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/attendances', [])
            ->assertSessionHasErrors(['employee_id', 'date']);
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        $employee = Employee::query()->create(['first_name' => 'Ana', 'last_name' => 'Lim', 'email' => 'ana@example.test', 'hired_at' => '2026-01-01']);

        $this->actingAs($this->admin())
            ->get("/admin/attendances/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('attendance/edit')->where('record.id', $record->id));

        $this->put("/admin/attendances/{$record->id}", ['employee_id' => $employee->id, 'date' => '2026-11-14', 'status' => 'absent', 'hours_worked' => '0', 'overtime_hours' => '0'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/attendances')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('attendances', array_merge(['id' => $record->id], ['status' => 'absent']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/attendances/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['notes' => 'Doctor appointment']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/attendances?search[keyword]=doctor')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
