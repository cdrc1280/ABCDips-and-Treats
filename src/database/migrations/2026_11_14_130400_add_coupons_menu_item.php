<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'sales.coupons';

    public function up(): void
    {
        MenuSeeder::add('sales', ['title' => 'Orders & Sales', 'icon' => 'shopping-cart', 'sort_order' => 30], 'sales');
        MenuSeeder::add('sales.coupons', [
            'title' => 'Coupons',
            'icon' => 'ticket-percent',
            'sort_order' => 5,
            'parent' => 'sales',
            'permission' => 'coupons-view',
            'route_name' => 'admin.coupons.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
