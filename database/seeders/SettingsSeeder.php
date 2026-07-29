<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Store Info
            ['group' => 'store',    'key' => 'store_name',            'value' => 'ABCDips & Treats'],
            ['group' => 'store',    'key' => 'store_address',         'value' => 'Bacoor, Cavite, Philippines'],
            ['group' => 'store',    'key' => 'store_lat',             'value' => '14.4597'],
            ['group' => 'store',    'key' => 'store_lng',             'value' => '120.9640'],
            ['group' => 'store',    'key' => 'store_phone',           'value' => ''],
            ['group' => 'store',    'key' => 'store_email',           'value' => ''],

            // Lalamove
            ['group' => 'lalamove', 'key' => 'lalamove_api_key',      'value' => ''],
            ['group' => 'lalamove', 'key' => 'lalamove_api_secret',   'value' => ''],
            ['group' => 'lalamove', 'key' => 'lalamove_sandbox',      'value' => '1'],
            ['group' => 'lalamove', 'key' => 'lalamove_service_type', 'value' => 'MOTORCYCLE'],

            // PayMongo
            ['group' => 'paymongo', 'key' => 'paymongo_public_key',   'value' => ''],
            ['group' => 'paymongo', 'key' => 'paymongo_secret_key',   'value' => ''],
            ['group' => 'paymongo', 'key' => 'paymongo_sandbox',      'value' => '1'],

            // Bank Transfer
            ['group' => 'bank',     'key' => 'bank_name',             'value' => 'BDO'],
            ['group' => 'bank',     'key' => 'bank_account_name',     'value' => 'ABCDips & Treats'],
            ['group' => 'bank',     'key' => 'bank_account_number',   'value' => ''],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
