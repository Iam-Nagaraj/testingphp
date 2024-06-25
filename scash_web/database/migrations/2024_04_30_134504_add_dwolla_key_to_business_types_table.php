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
        Schema::table('business_types', function (Blueprint $table) {
            if (!Schema::hasColumn('business_types', 'dwolla_key')) {
                $table->string('dwolla_key');
            }
        });
        Schema::table('business_category', function (Blueprint $table) {
            if (!Schema::hasColumn('business_category', 'dwolla_key')) {
                $table->string('dwolla_key');
            }
        });
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->string('uuid')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_types', function (Blueprint $table) {
            if (Schema::hasColumn('business_types', 'dwolla_key')) {
                $table->dropColumn('dwolla_key');
            }
        });
        Schema::table('business_category', function (Blueprint $table) {
            if (Schema::hasColumn('business_category', 'dwolla_key')) {
                $table->dropColumn('dwolla_key');
            }
        });
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });
    }
};
