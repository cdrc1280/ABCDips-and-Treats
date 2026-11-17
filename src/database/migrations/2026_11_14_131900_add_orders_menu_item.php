<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'sales.orders';

    public function up(): void
    {
        MenuSeeder::add('sales', ['title' => 'Orders & Sales', 'icon' => 'shopping-cart', 'sort_order' => 30], 'sales');
        MenuSeeder::add('sales.orders', [
            'title' => 'Orders',
            'icon' => 'shopping-bag',
            'sort_order' => 1,
            'parent' => 'sales',
            'permission' => 'orders-view',
            'route_name' => 'admin.orders.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
