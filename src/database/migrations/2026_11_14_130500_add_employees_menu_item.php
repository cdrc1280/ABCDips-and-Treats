<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'hr.employees';

    public function up(): void
    {
        MenuSeeder::add('hr', ['title' => 'HR & Payroll', 'icon' => 'users', 'sort_order' => 50], 'hr');
        MenuSeeder::add('hr.employees', [
            'title' => 'Employees',
            'icon' => 'id-card',
            'sort_order' => 1,
            'parent' => 'hr',
            'permission' => 'employees-view',
            'route_name' => 'admin.employees.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
