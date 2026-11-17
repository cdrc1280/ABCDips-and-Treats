<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'system.users';

    public function up(): void
    {
        MenuSeeder::add('system', ['title' => 'System', 'icon' => 'settings', 'sort_order' => 70], 'system');
        MenuSeeder::add('system.users', [
            'title' => 'Staff accounts',
            'icon' => 'user-cog',
            'sort_order' => 1,
            'parent' => 'system',
            'permission' => 'users-view',
            'route_name' => 'admin.users.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
