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
        Schema::table('promotional_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('promotional_notifications', 'time')) {
                $table->time('time')->nullable();
            }
            if (!Schema::hasColumn('promotional_notifications', 'send_to')) {
                $table->integer('send_to')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotional_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('promotional_notifications', 'time')) {
                $table->dropColumn('time');
            }
            if (Schema::hasColumn('promotional_notifications', 'send_to')) {
                $table->dropColumn('send_to');
            }
        });
    }
};
