<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'hr.attendances';

    public function up(): void
    {
        MenuSeeder::add('hr', ['title' => 'HR & Payroll', 'icon' => 'users', 'sort_order' => 50], 'hr');
        MenuSeeder::add('hr.attendances', [
            'title' => 'Attendance',
            'icon' => 'calendar-check',
            'sort_order' => 2,
            'parent' => 'hr',
            'permission' => 'attendances-view',
            'route_name' => 'admin.attendances.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
