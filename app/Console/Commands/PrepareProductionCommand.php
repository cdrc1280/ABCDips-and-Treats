<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class PrepareProductionCommand extends Command
{
    protected $signature = 'app:prepare-production {--admin-email=admin@abcdips.test} {--admin-password=Password123!}';

    protected $description = 'Wipe all test/demo data, re-seed roles and settings, and retain exactly 1 primary super admin account for production.';

    public function handle(): int
    {
        $this->warn('⚠️  Preparing application for production...');
        $this->warn('This will wipe all demo data, orders, products, inventory, and test customer accounts.');

        $adminEmail    = $this->option('admin-email');
        $adminPassword = $this->option('admin-password');

        Schema::disableForeignKeyConstraints();

        $tablesToTruncate = [
            'orders',
            'order_items',
            'order_status_histories',
            'carts',
            'cart_items',
            'custom_orders',
            'reviews',
            'review_votes',
            'contact_messages',
            'coupons',
            'gift_cards',
            'products',
            'product_categories',
            'product_allergens',
            'product_nutritions',
            'product_tag',
            'tags',
            'ingredients',
            'packaging_materials',
            'product_costings',
            'costing_items',
            'recipes',
            'recipe_ingredients',
            'recipe_packagings',
            'suppliers',
            'purchase_orders',
            'purchase_order_items',
            'production_batches',
            'stock_movements',
            'packaging_movements',
            'employees',
            'payrolls',
            'payroll_items',
            'attendances',
            'personal_access_tokens',
            'sessions',
            'failed_jobs',
            'job_batches',
            'jobs',
            'media',
            'notifications',
            'audit_logs',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("Truncated: {$table}");
            }
        }

        // Remove non-admin users
        DB::table('model_has_roles')->truncate();
        DB::table('users')->truncate();

        Schema::enableForeignKeyConstraints();

        // 1. Re-seed system roles & permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Re-seed store settings
        $this->call(SettingsSeeder::class);

        // 3. Create 1 primary Super Admin account
        $admin = User::create([
            'name'              => 'ABCDips Admin',
            'email'             => $adminEmail,
            'password'          => Hash::make($adminPassword),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('super_admin');

        $this->newLine();
        $this->info("✅ Production Database Prepared Successfully!");
        $this->info("👤 Retained Admin Account: {$adminEmail}");
        $this->info("🔑 Password: {$adminPassword}");
        $this->warn("⚠️  Log in to /admin immediately and update the admin password for security.");

        return Command::SUCCESS;
    }
}
