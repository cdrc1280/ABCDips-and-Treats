<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_costings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('yield_qty', 12, 3)->default(1);
            $table->string('yield_unit')->default('tub (70g)');
            $table->decimal('overhead_pct', 5, 2)->default(40.00);
            $table->decimal('markup_pct', 5, 2)->default(66.00);
            $table->decimal('labor_pct', 5, 2)->default(60.00);
            $table->timestamps();
        });

        Schema::create('costing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_costing_id')->constrained('product_costings')->cascadeOnDelete();
            $table->enum('group', ['ingredient', 'packaging'])->default('ingredient');
            $table->string('name');
            $table->enum('unit', ['grams', 'ml', 'piece'])->default('grams');
            $table->decimal('package_amount', 12, 3)->default(0);
            $table->decimal('package_price', 10, 2)->default(0);
            $table->decimal('qty_used', 12, 3)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costing_items');
        Schema::dropIfExists('product_costings');
    }
};
