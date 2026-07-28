<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Raw Ingredients Inventory
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('unit')->default('kg'); // kg, g, L, ml, pcs, box
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->decimal('stock_qty', 12, 3)->default(0);
            $table->decimal('min_stock_qty', 12, 3)->default(5);
            $table->decimal('reorder_qty', 12, 3)->default(20);
            $table->string('supplier_name')->nullable();
            $table->timestamps();
        });

        // Stock Movements Log (Audit Trail)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->string('type'); // purchase, production_usage, adjustment, waste
            $table->decimal('qty', 12, 3); // Positive for addition, negative for deduction
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Recipes (Bill of Materials - BOM)
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained('products')->onDelete('cascade');
            $table->string('name');
            $table->integer('yield_qty')->default(1); // Standard batch yield (e.g., 1 loaf, 12 cookies)
            $table->integer('prep_time_minutes')->default(20);
            $table->integer('baking_time_minutes')->default(40);
            $table->longText('instructions')->nullable();
            $table->timestamps();
        });

        // Recipe Ingredients (BOM Line Items)
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->decimal('qty_required', 12, 3);
            $table->string('unit')->default('g');
            $table->timestamps();
        });

        // Production Batches
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number', 32)->unique();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('planned_qty')->default(1);
            $table->integer('actual_yield_qty')->nullable();
            
            // Status: planned -> in_prep -> baking -> completed -> cancelled
            $table->string('status')->default('planned');
            $table->foreignId('baker_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_batches');
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('ingredients');
    }
};
