<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reviews Table
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reviewer_name');
            $table->string('reviewer_email');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->string('title')->nullable();
            $table->text('comment');
            $table->boolean('is_verified_buyer')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('helpful_votes')->default(0);
            $table->timestamps();
        });

        // Review Helpful Votes Pivot
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->unique(['review_id', 'ip_address']);
        });

        // Custom Orders Table
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 32)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->date('event_date');
            $table->integer('servings_count')->default(20);
            $table->integer('tiers_count')->default(1);
            $table->string('flavor_preference')->nullable(); // e.g. Chocolate Fudge, Red Velvet, Vanilla, Ube
            $table->string('frosting_type')->nullable(); // Cream Cheese, Buttercream, Fondant, Naked
            $table->text('theme_description');
            $table->decimal('budget_range_min', 10, 2)->nullable();
            $table->decimal('budget_range_max', 10, 2)->nullable();
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->text('staff_notes')->nullable();
            
            // Status Pipeline: inquiry -> quoted -> deposit_paid -> in_production -> ready -> completed -> cancelled
            $table->string('status')->default('inquiry');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_orders');
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('reviews');
    }
};
