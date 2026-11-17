<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'sales.customers';

    public function up(): void
    {
        MenuSeeder::add('sales', ['title' => 'Orders & Sales', 'icon' => 'shopping-cart', 'sort_order' => 30], 'sales');
        MenuSeeder::add('sales.customers', [
            'title' => 'Customers',
            'icon' => 'users',
            'sort_order' => 4,
            'parent' => 'sales',
            'permission' => 'customers-view',
            'route_name' => 'admin.customers.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
