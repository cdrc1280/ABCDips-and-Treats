<?php

namespace Tests\Feature\Employee\Payroll;

use App\Domains\Employee\Models\Employee;
use App\Domains\Setting\Models\Setting;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Statutory rates come from Store settings, so they can be updated without a code change. */
class PayrollRatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_uses_the_rates_from_settings(): void
    {
        Employee::query()->create(['first_name' => 'Joy', 'last_name' => 'Reyes', 'email' => 'joy@example.test', 'role_title' => 'Baker', 'basic_monthly_salary' => 30000, 'hired_at' => now()->subYear(), 'is_active' => true]);

        $default = app(PayrollService::class)->generatePayroll(now()->startOfMonth()->toDateString(), now()->startOfMonth()->addDays(14)->toDateString());
        $item = $default->items()->sole();
        $this->assertEquals(100, (float) $item->pagibig_deduction);          // default
        $this->assertEquals(750, (float) $item->sss_deduction);              // 15,000 × 5% (2025+ SSS employee share)

        Setting::set('payroll_pagibig_amount', '200');
        Setting::set('payroll_sss_rate', '4.5');
        $updated = app(PayrollService::class)->generatePayroll(now()->startOfMonth()->addDays(15)->toDateString(), now()->endOfMonth()->toDateString());
        $item = $updated->items()->sole();
        $this->assertEquals(200, (float) $item->pagibig_deduction);
        $this->assertEquals(675, (float) $item->sss_deduction);              // 15,000 × 4.5%
    }
}
