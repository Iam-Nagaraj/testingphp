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
        Schema::table('dwolla_customers', function (Blueprint $table) {
            if (!Schema::hasColumn('dwolla_customers', 'type')) {
                $table->tinyInteger('type')->default(1)->comment('1=>customer_id,2=>account_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dwolla_customers', function (Blueprint $table) {
            if (Schema::hasColumn('dwolla_customers', 'user_id')) {
                $table->dropColumn('type');
            }
        });
    }
};
