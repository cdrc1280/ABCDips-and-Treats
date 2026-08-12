<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'region')) {
                $table->string('region')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'province')) {
                $table->string('province')->nullable()->after('region');
            }
            if (!Schema::hasColumn('users', 'barangay')) {
                $table->string('barangay')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'street_address')) {
                $table->string('street_address')->nullable()->after('barangay');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'region')) {
                $table->string('region')->nullable()->after('delivery_address');
            }
            if (!Schema::hasColumn('orders', 'province')) {
                $table->string('province')->nullable()->after('region');
            }
            if (!Schema::hasColumn('orders', 'city')) {
                $table->string('city')->nullable()->after('province');
            }
            if (!Schema::hasColumn('orders', 'barangay')) {
                $table->string('barangay')->nullable()->after('city');
            }
            if (!Schema::hasColumn('orders', 'street_address')) {
                $table->string('street_address')->nullable()->after('barangay');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['region', 'province', 'barangay', 'street_address']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['region', 'province', 'city', 'barangay', 'street_address']);
        });
    }
};
