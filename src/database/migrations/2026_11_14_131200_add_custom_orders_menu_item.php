<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'sales.custom_orders';

    public function up(): void
    {
        MenuSeeder::add('sales', ['title' => 'Orders & Sales', 'icon' => 'shopping-cart', 'sort_order' => 30], 'sales');
        MenuSeeder::add('sales.custom_orders', [
            'title' => 'Custom orders',
            'icon' => 'cake',
            'sort_order' => 2,
            'parent' => 'sales',
            'permission' => 'custom-orders-view',
            'route_name' => 'admin.custom-orders.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
