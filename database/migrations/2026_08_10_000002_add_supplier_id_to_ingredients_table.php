<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            if (! Schema::hasColumn('ingredients', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('reorder_qty')->constrained('suppliers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id']);
        });
    }
};
