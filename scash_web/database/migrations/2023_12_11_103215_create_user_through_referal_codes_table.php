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
        Schema::create('user_through_referal_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger('referal_code_id');
            $table->unsignedBigInteger('referal_code_user_id');
            $table->integer('status')->default(1)->comment('0=pending,1=approved,2=inactive	');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referal_code_id')->references('id')->on('user_referal_codes')->onDelete('cascade');
            $table->foreign('referal_code_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_through_referal_codes');
    }
};
