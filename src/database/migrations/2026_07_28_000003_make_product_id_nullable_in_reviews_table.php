<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * reviews.product_id is already created nullable (2026_07_27_000004), so this
     * only matters for MySQL databases created before that change. The raw
     * MODIFY syntax is MySQL-only, so other drivers (SQLite on Vercel / tests) skip it.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE reviews MODIFY product_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE reviews MODIFY product_id BIGINT UNSIGNED NOT NULL');
    }
};
