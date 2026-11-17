<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'production.purchase_orders';

    public function up(): void
    {
        MenuSeeder::add('production', ['title' => 'Production & Purchasing', 'icon' => 'factory', 'sort_order' => 40], 'production');
        MenuSeeder::add('production.purchase_orders', [
            'title' => 'Purchase orders',
            'icon' => 'clipboard-list',
            'sort_order' => 3,
            'parent' => 'production',
            'permission' => 'purchase-orders-view',
            'route_name' => 'admin.purchase-orders.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
