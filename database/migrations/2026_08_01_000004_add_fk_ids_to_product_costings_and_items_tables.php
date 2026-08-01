<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_costings', function (Blueprint $table) {
            if (! Schema::hasColumn('product_costings', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->nullOnDelete();
            }
        });

        Schema::table('costing_items', function (Blueprint $table) {
            if (! Schema::hasColumn('costing_items', 'ingredient_id')) {
                $table->foreignId('ingredient_id')->nullable()->after('group')->constrained('ingredients')->nullOnDelete();
            }
            if (! Schema::hasColumn('costing_items', 'packaging_material_id')) {
                $table->foreignId('packaging_material_id')->nullable()->after('ingredient_id')->constrained('packaging_materials')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('costing_items', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropForeign(['packaging_material_id']);
            $table->dropColumn(['ingredient_id', 'packaging_material_id']);
        });

        Schema::table('product_costings', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id']);
        });
    }
};
