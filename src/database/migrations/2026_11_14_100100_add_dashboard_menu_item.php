<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'dashboard';

    public function up(): void
    {
        MenuSeeder::add('dashboard', [
            'title' => 'Dashboard',
            'icon' => 'layout-dashboard',
            'sort_order' => 0,
            'permission' => 'dashboard-view',
            'route_name' => 'admin.dashboard',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
