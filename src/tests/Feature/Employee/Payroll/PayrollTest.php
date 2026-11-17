<?php

namespace Tests\Feature\Employee\Payroll;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Employee\Enums\PayrollPermission;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Models\Payroll;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class PayrollTest extends TestCase
{
    use RefreshDatabase;

    private function admin(PayrollPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: PayrollPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Payroll
    {
        static $n = 0;
        $n++;

        return Payroll::query()->create(array_merge(['payroll_number' => "PAY-{$n}", 'period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/payrolls')->assertRedirect('/login');
        $this->post('/admin/payrolls', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/payrolls')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(PayrollPermission::PayrollsView));

        $this->get('/admin/payrolls')->assertOk();
        $this->get('/admin/payrolls/create')->assertForbidden();
        $this->get("/admin/payrolls/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/payrolls/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/payrolls/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/payrolls')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('payroll/index')
                ->has('records.data', 2)
                ->where('table.name', 'payrolls')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        $employee = Employee::query()->create(['first_name' => 'Ana', 'last_name' => 'Lim', 'email' => 'ana@example.test', 'hired_at' => '2026-01-01', 'basic_monthly_salary' => 20000, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post('/admin/payrolls', ['period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/payrolls')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('payrolls', ['status' => 'draft', 'total_net' => 9150]); // 10,000 − SSS 500 (5%) − PhilHealth 250 − Pag-IBIG 100
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/payrolls', [])
            ->assertSessionHasErrors(['period_start', 'period_end']);
    }

    public function test_unique_payroll_number_ignores_soft_deleted_rows(): void
    {
        $employee = Employee::query()->create(['first_name' => 'Ana', 'last_name' => 'Lim', 'email' => 'ana@example.test', 'hired_at' => '2026-01-01', 'basic_monthly_salary' => 20000, 'is_active' => true]);

        $existing = $this->record();
        $payload = array_merge(['period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft'], ['payroll_number' => $existing->payroll_number]);

        $this->actingAs($this->admin())->post('/admin/payrolls', $payload)->assertSessionHasErrors(['payroll_number']);

        $existing->delete();
        $this->post('/admin/payrolls', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        $employee = Employee::query()->create(['first_name' => 'Ana', 'last_name' => 'Lim', 'email' => 'ana@example.test', 'hired_at' => '2026-01-01', 'basic_monthly_salary' => 20000, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->get("/admin/payrolls/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('payroll/edit')->where('record.id', $record->id));

        $this->put("/admin/payrolls/{$record->id}", ['period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'approved', 'items' => [['employee_id' => (string) $employee->id, 'basic_pay' => '10000', 'overtime_pay' => '500', 'bonuses' => '0', 'sss_deduction' => '450', 'philhealth_deduction' => '250', 'pagibig_deduction' => '100', 'withholding_tax' => '0']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/payrolls')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('payrolls', array_merge(['id' => $record->id], ['status' => 'approved', 'total_gross' => 10500, 'total_deductions' => 800, 'total_net' => 9700]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/payrolls/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_approve(): void
    {
        $record = $this->record(['status' => 'draft']);

        $this->actingAs($this->admin())->from('/admin/payrolls')->patch("/admin/payrolls/{$record->id}/approve", [])->assertSessionHasNoErrors()->assertRedirect('/admin/payrolls');

        $this->assertSame('approved', $record->refresh()->status);
    }

    public function test_mark_paid(): void
    {
        $record = $this->record(['status' => 'approved']);

        $this->actingAs($this->admin())->from('/admin/payrolls')->patch("/admin/payrolls/{$record->id}/mark-paid", [])->assertSessionHasNoErrors()->assertRedirect('/admin/payrolls');

        $this->assertSame('paid', $record->refresh()->status);
        $this->assertNotNull($record->paid_at);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['notes' => '13th month']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/payrolls?search[keyword]=13th')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
