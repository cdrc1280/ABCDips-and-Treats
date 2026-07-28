<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_number',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'role_title',
        'employment_type',
        'basic_monthly_salary',
        'hired_at',
        'is_active',
    ];

    protected $casts = [
        'basic_monthly_salary' => 'decimal:2',
        'hired_at'             => 'date',
        'is_active'            => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
