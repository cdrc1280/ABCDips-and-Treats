<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Packaging Materials ──────────────────────────────────
        Schema::create('packaging_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->enum('type', ['box', 'bag', 'board', 'container', 'ribbon', 'label', 'sticker', 'tape', 'wrap', 'other'])->default('box');
            $table->string('unit')->default('pcs'); // pcs, roll, sheet
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->decimal('stock_qty', 10, 2)->default(0);
            $table->decimal('min_stock_qty', 10, 2)->default(5);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Packaging Stock Movements ────────────────────────────
        Schema::create('packaging_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packaging_material_id')->constrained('packaging_materials')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment', 'waste', 'damaged'])->default('in');
            $table->decimal('qty', 10, 2);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('reference_type')->nullable(); // production_batch, purchase_order, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // ── Employee Attendance ──────────────────────────────────
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_overtime')->default(false);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
        });

        // ── Blog Posts ───────────────────────────────────────────
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('cover_image')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('category')->default('General');
            $table->json('tags')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // ── FAQs ────────────────────────────────────────────────
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('General');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Contact Messages ─────────────────────────────────────
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['unread', 'read', 'replied', 'archived'])->default('unread');
            $table->text('admin_notes')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        // ── Audit Logs ───────────────────────────────────────────
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event'); // created, updated, deleted, login, logout, etc.
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('user_id');
            $table->index('created_at');
        });

        // ── Gift Cards ───────────────────────────────────────────
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('initial_balance', 10, 2);
            $table->decimal('remaining_balance', 10, 2);
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->foreignId('purchased_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('packaging_movements');
        Schema::dropIfExists('packaging_materials');
    }
};
