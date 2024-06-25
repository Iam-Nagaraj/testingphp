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
        Schema::table('dwolla_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('dwolla_accounts', 'is_default')) {
                $table->tinyInteger('is_default')->default(0);
            }
            if (!Schema::hasColumn('dwolla_accounts', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'account_id')) {
                $table->integer('account_id')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dwolla_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('dwolla_accounts', 'is_default')) {
                $table->dropColumn('is_default');
            }
            if (Schema::hasColumn('dwolla_accounts', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'account_id')) {
                $table->dropColumn('account_id');
            }
        });

    }
};
