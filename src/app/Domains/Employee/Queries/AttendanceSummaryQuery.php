<?php

namespace App\Domains\Employee\Queries;

use App\Domains\Employee\Models\Attendance;
use App\Shared\Resources\StatData;

final class AttendanceSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('today', __('Present today'), Attendance::query()->whereDate('date', today())->whereIn('status', ['present', 'late', 'half_day'])->count(), __('Logged for today')),
            new StatData('late', __('Late this month'), Attendance::query()->where('status', 'late')->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->count(), __('Since the 1st')),
            new StatData('overtime', __('Overtime this month'), (float) Attendance::query()->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])->sum('overtime_hours'), __('Hours')),
        ];
    }
}
