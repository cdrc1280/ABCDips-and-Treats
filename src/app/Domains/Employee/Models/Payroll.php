<?php

namespace App\Domains\Employee\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Employee\Data\PayrollFilterData;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['payroll_number'];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PAID = 'paid';

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
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payroll $payroll) {
            if (empty($payroll->payroll_number)) {
                $date = date('Ym');
                $count = static::whereLike('payroll_number', "PAY-{$date}-%")->count() + 1;
                $payroll->payroll_number = "PAY-{$date}-".str_pad((string) $count, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /** @return HasMany<PayrollItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    /**
     * string → global search; array → PayrollFilterData fields.
     *
     * @param  Builder<Payroll>  $query
     * @return Builder<Payroll>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('payroll_number', "%{$search}%")
                ->orWhereLike('notes', "%{$search}%"));
        }

        $filters = PayrollFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status));
    }
}
