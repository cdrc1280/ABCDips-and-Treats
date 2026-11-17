<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'production.production_batches';

    public function up(): void
    {
        MenuSeeder::add('production', ['title' => 'Production & Purchasing', 'icon' => 'factory', 'sort_order' => 40], 'production');
        MenuSeeder::add('production.production_batches', [
            'title' => 'Production batches',
            'icon' => 'cooking-pot',
            'sort_order' => 1,
            'parent' => 'production',
            'permission' => 'production-batches-view',
            'route_name' => 'admin.production-batches.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
