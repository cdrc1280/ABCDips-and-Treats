<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'sales.pos';

    public function up(): void
    {
        MenuSeeder::add('sales', ['title' => 'Orders & Sales', 'icon' => 'shopping-cart', 'sort_order' => 30], 'sales');
        MenuSeeder::add('sales.pos', [
            'title' => 'POS register',
            'icon' => 'calculator',
            'sort_order' => 0,
            'parent' => 'sales',
            'permission' => 'pos-use',
            'route_name' => 'admin.pos.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
