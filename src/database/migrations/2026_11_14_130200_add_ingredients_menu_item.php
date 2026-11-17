<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'inventory.ingredients';

    public function up(): void
    {
        MenuSeeder::add('inventory', ['title' => 'Inventory', 'icon' => 'boxes', 'sort_order' => 20], 'inventory');
        MenuSeeder::add('inventory.ingredients', [
            'title' => 'Ingredients',
            'icon' => 'wheat',
            'sort_order' => 2,
            'parent' => 'inventory',
            'permission' => 'ingredients-view',
            'route_name' => 'admin.ingredients.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
