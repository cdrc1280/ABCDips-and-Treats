<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Models\Attendance;
use Illuminate\Support\Facades\DB;

final class DeleteAttendanceAction
{
    public function execute(Attendance $attendance): void
    {
        DB::transaction(fn () => $attendance->delete());
    }
}
