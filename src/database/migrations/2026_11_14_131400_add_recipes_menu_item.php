<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'catalog.recipes';

    public function up(): void
    {
        MenuSeeder::add('catalog', ['title' => 'Catalog', 'icon' => 'store', 'sort_order' => 10], 'catalog');
        MenuSeeder::add('catalog.recipes', [
            'title' => 'Recipes',
            'icon' => 'chef-hat',
            'sort_order' => 3,
            'parent' => 'catalog',
            'permission' => 'recipes-view',
            'route_name' => 'admin.recipes.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
