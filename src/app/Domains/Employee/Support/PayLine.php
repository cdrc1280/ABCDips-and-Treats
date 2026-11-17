<?php

namespace App\Domains\Employee\Support;

/**
 * One payroll line's totals, shared by generation (PayrollService) and manual edits (UpdatePayrollAction)
 * so both use the same formula:
 *   gross = max(0, basic − attendance deduction) + overtime + holiday + bonuses
 *   net   = max(0, gross − SSS − PhilHealth − Pag-IBIG − tax)
 */
final class PayLine
{
    /**
     * @param  array<string, mixed>  $row  raw values (strings from the form or floats)
     * @return array<string, float|string|null>
     */
    public static function totals(array $row): array
    {
        $n = fn (string $key): float => isset($row[$key]) && $row[$key] !== '' && is_numeric($row[$key]) ? round((float) $row[$key], 2) : 0.0;

        $earned = max(0.0, $n('basic_pay') - $n('attendance_deduction'));
        $gross = round($earned + $n('overtime_pay') + $n('holiday_pay') + $n('bonuses'), 2);
        $deductions = round($n('sss_deduction') + $n('philhealth_deduction') + $n('pagibig_deduction') + $n('withholding_tax'), 2);

        return [
            'basic_pay' => $n('basic_pay'),
            'overtime_pay' => $n('overtime_pay'),
            'holiday_pay' => $n('holiday_pay'),
            'bonuses' => $n('bonuses'),
            'attendance_deduction' => min($n('attendance_deduction'), $n('basic_pay')),
            'attendance_note' => isset($row['attendance_note']) && trim((string) $row['attendance_note']) !== '' ? mb_substr(trim((string) $row['attendance_note']), 0, 255) : null,
            'sss_deduction' => $n('sss_deduction'),
            'philhealth_deduction' => $n('philhealth_deduction'),
            'pagibig_deduction' => $n('pagibig_deduction'),
            'withholding_tax' => $n('withholding_tax'),
            'gross_pay' => $gross,
            'total_deductions' => $deductions,
            'net_pay' => max(0.0, round($gross - $deductions, 2)),
        ];
    }
}
