<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Data\AttendanceData;
use App\Domains\Employee\Models\Attendance;
use Illuminate\Support\Facades\DB;

final class UpdateAttendanceAction
{
    public function execute(Attendance $attendance, AttendanceData $data): Attendance
    {
        return DB::transaction(function () use ($attendance, $data) {
            $attendance->update([
                'employee_id' => $data->employeeId,
                'date' => $data->date,
                'status' => $data->status,
                'time_in' => $data->timeIn,
                'time_out' => $data->timeOut,
                'hours_worked' => $data->hoursWorked,
                'is_overtime' => $data->isOvertime,
                'overtime_hours' => $data->overtimeHours,
                'is_holiday' => $data->isHoliday,
                'notes' => $data->notes,
            ]);

            return $attendance;
        });
    }
}
