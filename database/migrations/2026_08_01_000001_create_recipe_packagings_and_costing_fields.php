<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add Excel costing percentage fields to recipes table
        Schema::table('recipes', function (Blueprint $table) {
            if (! Schema::hasColumn('recipes', 'overhead_pct')) {
                $table->decimal('overhead_pct', 5, 2)->default(40.00)->after('instructions');
            }
            if (! Schema::hasColumn('recipes', 'markup_pct')) {
                $table->decimal('markup_pct', 5, 2)->default(66.00)->after('overhead_pct');
            }
            if (! Schema::hasColumn('recipes', 'labor_pct')) {
                $table->decimal('labor_pct', 5, 2)->default(60.00)->after('markup_pct');
            }
        });

        // Create recipe_packagings table
        if (! Schema::hasTable('recipe_packagings')) {
            Schema::create('recipe_packagings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
                $table->foreignId('packaging_material_id')->nullable()->constrained('packaging_materials')->nullOnDelete();
                $table->string('name');
                $table->string('unit')->default('pcs');
                $table->decimal('package_qty', 12, 3)->default(1);
                $table->decimal('package_cost', 10, 2)->default(0);
                $table->decimal('qty_used', 12, 3)->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_packagings');

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['overhead_pct', 'markup_pct', 'labor_pct']);
        });
    }
};
