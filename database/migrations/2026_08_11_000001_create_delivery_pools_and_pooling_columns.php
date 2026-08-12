<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_pools', function (Blueprint $table) {
            $table->id();
            $table->string('pool_code')->unique();
            $table->string('zone_name')->nullable();
            $table->string('city')->nullable();
            $table->decimal('total_delivery_fee', 8, 2)->default(0.00);
            $table->decimal('shared_fee_per_order', 8, 2)->default(0.00);
            $table->string('status')->default('open'); // open, building, settled, dispatched
            $table->timestamp('settled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_mode')->default('priority')->after('fulfillment_type'); // priority, pooling
            $table->foreignId('delivery_pool_id')->nullable()->after('delivery_mode')->constrained('delivery_pools')->nullOnDelete();
            $table->string('pooling_status')->default('not_pooled')->after('delivery_pool_id'); // not_pooled, awaiting_assignment, pooled, settled
            $table->decimal('estimated_shared_fee', 8, 2)->default(0.00)->after('delivery_fee');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_pool_id']);
            $table->dropColumn(['delivery_mode', 'delivery_pool_id', 'pooling_status', 'estimated_shared_fee']);
        });

        Schema::dropIfExists('delivery_pools');
    }
};
