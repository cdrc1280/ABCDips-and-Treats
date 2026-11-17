<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'catalog.products';

    public function up(): void
    {
        MenuSeeder::add('catalog', ['title' => 'Catalog', 'icon' => 'store', 'sort_order' => 10], 'catalog');
        MenuSeeder::add('catalog.products', [
            'title' => 'Products',
            'icon' => 'croissant',
            'sort_order' => 2,
            'parent' => 'catalog',
            'permission' => 'products-view',
            'route_name' => 'admin.products.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
