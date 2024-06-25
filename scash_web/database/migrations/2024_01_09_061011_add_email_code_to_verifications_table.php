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
        Schema::table('verifications', function (Blueprint $table) {
            if (!Schema::hasColumn('verifications', 'email_code')) {
                $table->string('email_code')->nullable();
            }
            if (!Schema::hasColumn('verifications', 'email_status')) {
                $table->integer('email_status')->default(0)->comment('pending=0,failed=2,verified=1');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
            if (Schema::hasColumn('verifications', 'email_code')) {
                $table->dropColumn('email_code');
            }
            if (Schema::hasColumn('verifications', 'email_status')) {
                $table->dropColumn('email_status');
            }
        });
    }
};
