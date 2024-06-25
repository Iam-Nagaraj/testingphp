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
        Schema::table('user_addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('user_addresses', 'address_2')) {
                $table->string('address_2')->nullable();
            }
            if (!Schema::hasColumn('user_addresses', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('user_addresses', 'country_2')) {
                $table->string('country_2')->nullable();
            }
            if (!Schema::hasColumn('user_addresses', 'state_2')) {
                $table->string('state_2')->nullable();
            }
            if (!Schema::hasColumn('user_addresses', 'city_2')) {
                $table->string('city_2')->nullable();
            }
            if (!Schema::hasColumn('user_addresses', 'postal_code_2')) {
                $table->string('postal_code_2')->nullable();
            }
            if (!Schema::hasColumn('user_addresses', 'latitude_2')) {
                $table->string('latitude_2')->nullable();
            }
            if (!Schema::hasColumn('user_addresses', 'longitude_2')) {
                $table->string('longitude_2')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('user_addresses', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('user_addresses', 'country_2')) {
                $table->dropColumn('country_2');
            }
            if (Schema::hasColumn('user_addresses', 'state_2')) {
                $table->dropColumn('state_2');
            }
            if (Schema::hasColumn('user_addresses', 'city_2')) {
                $table->dropColumn('city_2');
            }
            if (Schema::hasColumn('user_addresses', 'postal_code_2')) {
                $table->dropColumn('postal_code_2');
            }
            if (Schema::hasColumn('user_addresses', 'latitude_2')) {
                $table->dropColumn('latitude_2');
            }
            if (Schema::hasColumn('user_addresses', 'longitude_2')) {
                $table->dropColumn('longitude_2');
            }
        });
    }
};
