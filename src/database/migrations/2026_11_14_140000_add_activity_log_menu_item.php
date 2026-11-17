<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'system.activity_log';

    public function up(): void
    {
        MenuSeeder::add('system', ['title' => 'System', 'icon' => 'settings', 'sort_order' => 70], 'system');
        MenuSeeder::add('system.activity_log', [
            'title' => 'Activity log',
            'icon' => 'history',
            'sort_order' => 3,
            'parent' => 'system',
            'permission' => 'audit-logs-view',
            'route_name' => 'admin.activity-log.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
