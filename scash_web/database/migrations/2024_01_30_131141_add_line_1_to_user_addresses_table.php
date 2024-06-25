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
            if (!Schema::hasColumn('user_addresses', 'line_1')) {
                $table->string('line_1')->nullable();
            }
            if (!Schema::hasColumn('user_addresses', 'line_2')) {
                $table->string('line_2')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('user_addresses', 'line_1')) {
                $table->dropColumn('line_1');
            }
            if (Schema::hasColumn('user_addresses', 'line_2')) {
                $table->dropColumn('line_2');
            }
        });
    }
};
