<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingsSeeder::class,
            AdminUserSeeder::class,
        ]);

        if (app()->environment(['local', 'testing'])) {
            $this->call([
                ProductSeeder::class,
                PurchasingAndPayrollSeeder::class,
            ]);
        }
    }
}
