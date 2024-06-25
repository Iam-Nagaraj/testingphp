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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1=Wallet,2=Cashback');
            $table->double('amount', 8, 2)->default(0);
            $table->double('fee', 8, 2)->default(0);
            $table->integer('status')->default(0);
            $table->foreignId('sender_user_id')->nullable();
            $table->foreignId('receiver_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
