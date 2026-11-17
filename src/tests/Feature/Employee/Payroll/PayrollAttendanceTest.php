<?php

namespace Tests\Feature\Employee\Payroll;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Employee\Enums\PayrollPermission;
use App\Domains\Employee\Models\Attendance;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Models\Payroll;
use App\Domains\Employee\Models\PayrollItem;
use App\Domains\Setting\Models\Setting;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Audit A16: payroll follows attendance under the pay policy in Settings.
 * ₱31,300/month with the default 313 working days → ₱1,200/day, ₱150/hour, ₱2.50/minute.
 */
class PayrollAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...PayrollPermission::cases());
    }

    private function employeeWithAttendance(): Employee
    {
        $employee = Employee::query()->create(['first_name' => 'Ana', 'last_name' => 'Lim', 'email' => 'ana@example.test', 'hired_at' => '2026-01-01', 'basic_monthly_salary' => 31300, 'is_active' => true]);
        $day = fn (string $date, array $attrs) => Attendance::query()->create(['employee_id' => $employee->id, 'date' => $date, ...$attrs]);

        $day('2026-10-31', ['status' => 'absent']); // outside the period: ignored
        $day('2026-11-03', ['status' => 'absent']);
        $day('2026-11-04', ['status' => 'half_day']);
        $day('2026-11-05', ['status' => 'late', 'time_in' => '08:20']);
        $day('2026-11-06', ['status' => 'present', 'is_overtime' => true, 'overtime_hours' => 2]);
        $day('2026-11-07', ['status' => 'present', 'is_holiday' => true]);
        $day('2026-11-08', ['status' => 'on_leave']); // paid leave: no deduction

        return $employee;
    }

    private function generate(): PayrollItem
    {
        $this->actingAs($this->admin())
            ->post('/admin/payrolls', ['period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft'])
            ->assertSessionHasNoErrors();

        return Payroll::query()->sole()->items()->sole();
    }

    public function test_default_policy_applies_absences_lateness_overtime_and_holidays(): void
    {
        $this->employeeWithAttendance();
        $item = $this->generate();

        // 1.5 days × 1,200 + 20 min × 2.50 = 1,850 · 2 h × 150 × 1.25 = 375 · 1 holiday × 1,200 × 100% = 1,200
        $this->assertSame('15650.00', $item->basic_pay);
        $this->assertSame('1850.00', $item->attendance_deduction);
        $this->assertSame('375.00', $item->overtime_pay);
        $this->assertSame('1200.00', $item->holiday_pay);
        $this->assertSame('15375.00', $item->gross_pay); // 15,650 − 1,850 + 375 + 1,200
        $this->assertSame('1.5 days absent · 20 min late · 2 h overtime · 1 holiday worked', $item->attendance_note);

        // Contributions on what was earned: SSS 768.75, PhilHealth 384.38, Pag-IBIG 100, tax (15,375 − 10,416) × 15% = 743.85
        $this->assertSame('1996.98', $item->total_deductions);
        $this->assertSame('13378.02', $item->net_pay);
    }

    public function test_owner_settings_change_the_policy(): void
    {
        Setting::set('payroll_deduct_absences', '0');
        Setting::set('payroll_deduct_late', '0');
        Setting::set('payroll_overtime_premium', '50');
        Setting::set('payroll_holiday_premium', '30');

        $this->employeeWithAttendance();
        $item = $this->generate();

        $this->assertSame('0.00', $item->attendance_deduction);
        $this->assertSame('450.00', $item->overtime_pay); // 2 h × 150 × 1.5
        $this->assertSame('360.00', $item->holiday_pay); // 1,200 × 30%
    }

    public function test_grace_period_and_shift_start_move_the_late_threshold(): void
    {
        Setting::set('payroll_shift_start', '08:15');
        Setting::set('payroll_late_grace_minutes', '3');
        Setting::set('payroll_deduct_absences', '0');

        $this->employeeWithAttendance();

        // 08:20 − 08:15 − 3 min grace = 2 min × 2.50
        $this->assertSame('5.00', $this->generate()->attendance_deduction);
    }

    public function test_employee_without_attendance_gets_plain_basic_pay(): void
    {
        Employee::query()->create(['first_name' => 'Ben', 'last_name' => 'Cruz', 'email' => 'ben@example.test', 'hired_at' => '2026-01-01', 'basic_monthly_salary' => 20000, 'is_active' => true]);
        $item = $this->generate();

        $this->assertSame('0.00', $item->attendance_deduction);
        $this->assertNull($item->attendance_note);
        $this->assertSame('10000.00', $item->gross_pay);
    }

    public function test_manual_edit_uses_the_same_formula_and_keeps_the_note(): void
    {
        $employee = $this->employeeWithAttendance();
        $payroll = Payroll::query()->create(['payroll_number' => 'PAY-1', 'period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft']);

        $this->actingAs($this->admin())
            ->put("/admin/payrolls/{$payroll->id}", ['period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft', 'items' => [[
                'employee_id' => (string) $employee->id, 'basic_pay' => '10000', 'attendance_deduction' => '500', 'overtime_pay' => '300',
                'holiday_pay' => '200', 'bonuses' => '0', 'sss_deduction' => '400', 'philhealth_deduction' => '200', 'pagibig_deduction' => '100',
                'withholding_tax' => '0', 'attendance_note' => '1 day absent',
            ]]])
            ->assertSessionHasNoErrors();

        $item = $payroll->items()->sole();
        $this->assertSame('10000.00', $item->gross_pay); // 10,000 − 500 + 300 + 200
        $this->assertSame('9300.00', $item->net_pay);
        $this->assertSame('1 day absent', $item->attendance_note);
        $this->assertSame('9300.00', $payroll->fresh()?->total_net);
    }

    public function test_attendance_fields_are_validated(): void
    {
        $employee = $this->employeeWithAttendance();
        $payroll = Payroll::query()->create(['payroll_number' => 'PAY-1', 'period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft']);

        $this->actingAs($this->admin())
            ->put("/admin/payrolls/{$payroll->id}", ['period_start' => '2026-11-01', 'period_end' => '2026-11-15', 'status' => 'draft', 'items' => [[
                'employee_id' => (string) $employee->id, 'basic_pay' => '10000', 'attendance_deduction' => '-5', 'holiday_pay' => 'abc',
            ]]])
            ->assertSessionHasErrors(['items.0.attendance_deduction', 'items.0.holiday_pay']);
    }
}
