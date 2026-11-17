<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();          // machine key, e.g. catalog.products
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('icon')->nullable();        // lucide-react icon name
            $table->unsignedInteger('sort_order')->default(0);
            $table->uuid('parent_id')->nullable()->index();
            $table->foreignUuid('permission_id')->nullable()->constrained('permissions')->nullOnDelete();
            $table->string('route_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('reference')->nullable();   // which migration/module created it
            $table->timestamps();
            $table->softDeletes();
        });

        // Self-reference added after the table exists: the UUID primary key is created after
        // inline foreign keys, so a same-blueprint FK to menus.id would have no target yet.
        Schema::table('menus', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('menus')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
