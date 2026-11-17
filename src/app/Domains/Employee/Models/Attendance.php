<?php

namespace App\Domains\Employee\Models;

use App\Domains\Employee\Data\AttendanceFilterData;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasUuids, LogsModelActivity, SoftDeletes;

    protected $fillable = ['employee_id', 'date', 'time_in', 'time_out', 'hours_worked', 'is_holiday', 'is_overtime', 'overtime_hours', 'status', 'notes'];

    protected $casts = ['date' => 'date', 'is_holiday' => 'boolean', 'is_overtime' => 'boolean', 'hours_worked' => 'decimal:2', 'overtime_hours' => 'decimal:2'];

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * string → global search; array → AttendanceFilterData fields.
     *
     * @param  Builder<Attendance>  $query
     * @return Builder<Attendance>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereHas('employee', fn (Builder $r) => $r->whereLike('first_name', "%{$search}%"))
                ->orWhereHas('employee', fn (Builder $r) => $r->whereLike('last_name', "%{$search}%"))
                ->orWhereLike('notes', "%{$search}%"));
        }

        $filters = AttendanceFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->employeeId !== null, fn (Builder $q) => $q->where('employee_id', $filters->employeeId))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status))
            ->when($filters->from, fn (Builder $q, string $v) => $q->whereDate('date', '>=', $v))
            ->when($filters->to, fn (Builder $q, string $v) => $q->whereDate('date', '<=', $v));
    }
}
