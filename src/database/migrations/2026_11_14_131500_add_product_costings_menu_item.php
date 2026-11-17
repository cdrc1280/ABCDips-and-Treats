<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'catalog.product_costings';

    public function up(): void
    {
        MenuSeeder::add('catalog', ['title' => 'Catalog', 'icon' => 'store', 'sort_order' => 10], 'catalog');
        MenuSeeder::add('catalog.product_costings', [
            'title' => 'Product costing',
            'icon' => 'calculator',
            'sort_order' => 4,
            'parent' => 'catalog',
            'permission' => 'product-costings-view',
            'route_name' => 'admin.product-costings.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
