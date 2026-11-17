<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Decides WHEN a product's recipe consumes ingredients, so they're consumed exactly once:
 *  - made_to_order: when it's sold (checkout / POS)
 *  - baked_ahead:   when a production batch is completed; a sale only uses finished stock
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('inventory_mode', 20)->default('made_to_order')->after('min_stock_qty');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('inventory_mode');
        });
    }
};
