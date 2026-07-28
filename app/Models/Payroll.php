<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    public const STATUS_DRAFT    = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID     = 'paid';

    protected $fillable = [
        'payroll_number',
        'period_start',
        'period_end',
        'status',
        'total_gross',
        'total_deductions',
        'total_net',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'period_start'     => 'date',
        'period_end'       => 'date',
        'paid_at'           => 'datetime',
        'total_gross'      => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net'        => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }
}
