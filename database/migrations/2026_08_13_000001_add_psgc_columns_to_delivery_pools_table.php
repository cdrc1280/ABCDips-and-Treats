<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_pools', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_pools', 'region')) {
                $table->string('region')->nullable()->after('zone_name');
            }
            if (!Schema::hasColumn('delivery_pools', 'province')) {
                $table->string('province')->nullable()->after('region');
            }
            if (!Schema::hasColumn('delivery_pools', 'barangay')) {
                $table->string('barangay')->nullable()->after('city');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_pools', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('delivery_pools', 'region') ? 'region' : null,
                Schema::hasColumn('delivery_pools', 'province') ? 'province' : null,
                Schema::hasColumn('delivery_pools', 'barangay') ? 'barangay' : null,
            ]));
        });
    }
};
