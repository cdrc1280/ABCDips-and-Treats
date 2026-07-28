<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDummyDataCommand extends Command
{
    protected $signature = 'db:clean-dummy-data';
    protected $description = 'Wipe all dummy/seed data from database while preserving Super Admin user and RBAC roles.';

    public function handle(): int
    {
        $this->info('Cleaning dummy data from ABCDips & Treats database...');

        Schema::disableForeignKeyConstraints();

        $tablesToWipe = [
            'payroll_items',
            'payrolls',
            'employees',
            'purchase_order_items',
            'purchase_orders',
            'suppliers',
            'production_batches',
            'recipe_ingredients',
            'recipes',
            'stock_movements',
            'ingredients',
            'custom_orders',
            'review_votes',
            'reviews',
            'order_status_histories',
            'order_items',
            'orders',
            'wishlists',
            'cart_items',
            'carts',
            'coupons',
            'product_nutritions',
            'product_allergens',
            'product_tag',
            'tags',
            'products',
            'product_categories',
            'media',
        ];

        foreach ($tablesToWipe as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("  ✓ Truncated table: {$table}");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('All dummy data successfully removed!');
        $this->info('Database is now in a clean state ready for real production data.');

        return Command::SUCCESS;
    }
}
