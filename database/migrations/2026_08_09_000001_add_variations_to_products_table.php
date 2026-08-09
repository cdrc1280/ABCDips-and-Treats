<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('variation_type')->default('none')->after('gallery'); // none, weight, pieces, size
            $table->json('variations')->nullable()->after('variation_type'); // [{"label": "250g", "price_modifier": 0}]
        });
    }
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['variation_type', 'variations']);
        });
    }
};
