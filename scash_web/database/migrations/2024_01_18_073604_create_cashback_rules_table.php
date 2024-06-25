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
        Schema::create('cashback_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->decimal('standard_cashback_percentage', 5, 2)->default(0);
            $table->decimal('ts_total_amount', 12, 2)->comment('Transactional Size');
            $table->decimal('ts_extra_percentage', 5, 2)->default(0);
            $table->boolean('ts_status')->default(0)->comment('0 = Off, 1= On');
            $table->decimal('rp_total_amount', 12, 2)->comment('Repeat Purchase');
            $table->decimal('rp_extra_percentage', 5, 2)->default(0);
            $table->boolean('rp_status')->default(0)->comment('0 = Off, 1= On');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashback_rules');
    }
};
