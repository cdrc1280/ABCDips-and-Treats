<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'system.settings';

    public function up(): void
    {
        MenuSeeder::add('system', ['title' => 'System', 'icon' => 'settings', 'sort_order' => 70], 'system');
        MenuSeeder::add('system.settings', [
            'title' => 'Store settings',
            'icon' => 'sliders-horizontal',
            'sort_order' => 4,
            'parent' => 'system',
            'permission' => 'settings-view',
            'route_name' => 'admin.settings.edit',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
