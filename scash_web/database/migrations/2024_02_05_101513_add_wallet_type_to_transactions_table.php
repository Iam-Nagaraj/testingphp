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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'wallet_type')) {
                $table->integer('wallet_type')
                ->comment("0=deposit, 1=withdraw, 2=wallet_to_wallet")->nullable();
            }
            if (Schema::hasColumn('transactions', 'sender_user_id')) {
                $table->dropColumn('sender_user_id');
            }
            if (Schema::hasColumn('transactions', 'receiver_user_id')) {
                $table->dropColumn('receiver_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'wallet_type')) {
                $table->dropColumn('wallet_type');
            }
            if (Schema::hasColumn('transactions', 'sender_user_id')) {
                $table->foreignId('sender_user_id')->nullable();
            }
            if (Schema::hasColumn('transactions', 'wallet_treceiver_user_idype')) {
                $table->foreignId('receiver_user_id')->nullable();
            }
        });
    }
};
