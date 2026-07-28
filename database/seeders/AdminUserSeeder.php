<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@abcdips.test'],
            [
                'name' => 'ABCDips Admin',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('super_admin');

        $this->command->info('✅  Super admin created: admin@abcdips.test / Password123!');
        $this->command->warn('⚠️   Change the admin password immediately after first login!');
    }
}
