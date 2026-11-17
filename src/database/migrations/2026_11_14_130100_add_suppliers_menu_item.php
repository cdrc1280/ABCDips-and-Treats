<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'inventory.suppliers';

    public function up(): void
    {
        MenuSeeder::add('inventory', ['title' => 'Inventory', 'icon' => 'boxes', 'sort_order' => 20], 'inventory');
        MenuSeeder::add('inventory.suppliers', [
            'title' => 'Suppliers',
            'icon' => 'truck',
            'sort_order' => 1,
            'parent' => 'inventory',
            'permission' => 'suppliers-view',
            'route_name' => 'admin.suppliers.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
