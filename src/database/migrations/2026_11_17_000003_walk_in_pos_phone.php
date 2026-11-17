<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** POS walk-in sales used '0000000000' (10 digits); the strict phone rule needs 11 (Order::WALK_IN_PHONE). */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->where('customer_phone', '0000000000')->update(['customer_phone' => '09000000000']);
    }

    public function down(): void
    {
        DB::table('orders')->where('customer_phone', '09000000000')->update(['customer_phone' => '0000000000']);
    }
};
