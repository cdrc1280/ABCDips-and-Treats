<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'system.roles';

    public function up(): void
    {
        MenuSeeder::add('system', ['title' => 'System', 'icon' => 'settings', 'sort_order' => 70], 'system');
        MenuSeeder::add('system.roles', [
            'title' => 'Roles',
            'icon' => 'shield-check',
            'sort_order' => 2,
            'parent' => 'system',
            'permission' => 'roles-view',
            'route_name' => 'admin.roles.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
