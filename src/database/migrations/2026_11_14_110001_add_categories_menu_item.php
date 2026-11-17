<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'catalog.categories';

    public function up(): void
    {
        MenuSeeder::add('catalog', ['title' => 'Catalog', 'icon' => 'store', 'sort_order' => 10], 'catalog');
        MenuSeeder::add('catalog.categories', [
            'title' => 'Categories',
            'icon' => 'folder-tree',
            'sort_order' => 1,
            'parent' => 'catalog',
            'permission' => 'categories-view',
            'route_name' => 'admin.categories.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
