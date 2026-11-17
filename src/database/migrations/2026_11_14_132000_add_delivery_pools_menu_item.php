<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'sales.delivery_pools';

    public function up(): void
    {
        MenuSeeder::add('sales', ['title' => 'Orders & Sales', 'icon' => 'shopping-cart', 'sort_order' => 30], 'sales');
        MenuSeeder::add('sales.delivery_pools', [
            'title' => 'Delivery pools',
            'icon' => 'route',
            'sort_order' => 3,
            'parent' => 'sales',
            'permission' => 'delivery-pools-view',
            'route_name' => 'admin.delivery-pools.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
