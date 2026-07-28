<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Attendance extends Model {
    protected $fillable = ['employee_id','date','time_in','time_out','hours_worked','is_holiday','is_overtime','overtime_hours','status','notes'];
    protected $casts = ['date'=>'date','is_holiday'=>'boolean','is_overtime'=>'boolean','hours_worked'=>'decimal:2','overtime_hours'=>'decimal:2'];
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
