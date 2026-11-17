<?php

namespace App\Domains\Employee\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Employee\Data\EmployeeFilterData;
use App\Domains\User\Models\User;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['employee_number'];

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
        'hired_at' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee) {
            if (empty($employee->employee_number)) {
                // Next number after the highest existing EMP-#### (soft-deleted ones included, so numbers are never reused).
                $last = static::withTrashed()->whereLike('employee_number', 'EMP-%')
                    ->orderByRaw('length(employee_number) desc')->orderByDesc('employee_number')->value('employee_number');
                $next = $last ? ((int) substr((string) $last, 4)) + 1 : 1;
                $employee->employee_number = 'EMP-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<PayrollItem, $this> */
    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * string → global search; array → EmployeeFilterData fields.
     *
     * @param  Builder<Employee>  $query
     * @return Builder<Employee>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('first_name', "%{$search}%")
                ->orWhereLike('last_name', "%{$search}%")
                ->orWhereLike('employee_number', "%{$search}%")
                ->orWhereLike('email', "%{$search}%")
                ->orWhereLike('phone', "%{$search}%"));
        }

        $filters = EmployeeFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->roleTitle !== null && $filters->roleTitle !== '', fn (Builder $q) => $q->where('role_title', $filters->roleTitle))
            ->when($filters->employmentType !== null && $filters->employmentType !== '', fn (Builder $q) => $q->where('employment_type', $filters->employmentType))
            ->when($filters->isActive !== null, fn (Builder $q) => $q->where('is_active', $filters->isActive));
    }
}
