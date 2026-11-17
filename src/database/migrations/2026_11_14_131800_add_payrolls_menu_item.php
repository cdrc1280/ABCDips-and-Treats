<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'hr.payrolls';

    public function up(): void
    {
        MenuSeeder::add('hr', ['title' => 'HR & Payroll', 'icon' => 'users', 'sort_order' => 50], 'hr');
        MenuSeeder::add('hr.payrolls', [
            'title' => 'Payroll',
            'icon' => 'banknote',
            'sort_order' => 3,
            'parent' => 'hr',
            'permission' => 'payrolls-view',
            'route_name' => 'admin.payrolls.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
