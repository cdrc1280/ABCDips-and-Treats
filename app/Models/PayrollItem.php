<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'basic_pay',
        'overtime_pay',
        'bonuses',
        'sss_deduction',
        'philhealth_deduction',
        'pagibig_deduction',
        'withholding_tax',
        'gross_pay',
        'total_deductions',
        'net_pay',
    ];

    protected $casts = [
        'basic_pay'            => 'decimal:2',
        'overtime_pay'         => 'decimal:2',
        'bonuses'              => 'decimal:2',
        'sss_deduction'        => 'decimal:2',
        'philhealth_deduction' => 'decimal:2',
        'pagibig_deduction'    => 'decimal:2',
        'withholding_tax'     => 'decimal:2',
        'gross_pay'            => 'decimal:2',
        'total_deductions'     => 'decimal:2',
        'net_pay'              => 'decimal:2',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
