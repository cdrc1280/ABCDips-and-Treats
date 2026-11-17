<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'inventory.packaging_materials';

    public function up(): void
    {
        MenuSeeder::add('inventory', ['title' => 'Inventory', 'icon' => 'boxes', 'sort_order' => 20], 'inventory');
        MenuSeeder::add('inventory.packaging_materials', [
            'title' => 'Packaging',
            'icon' => 'package-open',
            'sort_order' => 3,
            'parent' => 'inventory',
            'permission' => 'packaging-materials-view',
            'route_name' => 'admin.packaging-materials.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
