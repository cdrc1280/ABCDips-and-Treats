<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A16: payroll follows attendance. Holiday premium is an earning; absences and lateness
 * reduce pay before statutory contributions ("no work, no pay"); the note explains the numbers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->decimal('holiday_pay', 12, 2)->default(0)->after('overtime_pay');
            $table->decimal('attendance_deduction', 12, 2)->default(0)->after('bonuses');
            $table->string('attendance_note', 255)->nullable()->after('attendance_deduction');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_items', function (Blueprint $table) {
            $table->dropColumn(['holiday_pay', 'attendance_deduction', 'attendance_note']);
        });
    }
};
