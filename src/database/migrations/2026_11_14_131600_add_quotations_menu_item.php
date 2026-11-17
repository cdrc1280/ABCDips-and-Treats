<?php

use App\Domains\Menu\Support\MenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const REFERENCE = 'production.quotations';

    public function up(): void
    {
        MenuSeeder::add('production', ['title' => 'Production & Purchasing', 'icon' => 'factory', 'sort_order' => 40], 'production');
        MenuSeeder::add('production.quotations', [
            'title' => 'Quotations',
            'icon' => 'file-spreadsheet',
            'sort_order' => 2,
            'parent' => 'production',
            'permission' => 'quotations-view',
            'route_name' => 'admin.quotations.index',
        ], self::REFERENCE);
    }

    public function down(): void
    {
        MenuSeeder::remove(self::REFERENCE);
    }
};
