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
        Schema::table('states', function (Blueprint $table) {
            if (!Schema::hasColumn('states', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable();
            }
        });
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            if (Schema::hasColumn('states', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
