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
        Schema::table('business_details', function (Blueprint $table) {
            if (Schema::hasColumn('business_details', 'business_contact_address')) {
                $table->dropColumn('business_contact_address');
            }
            if (Schema::hasColumn('business_details', 'contact_city')) {
                $table->dropColumn('contact_city');
            }
            if (Schema::hasColumn('business_details', 'contact_state')) {
                $table->dropColumn('contact_state');
            }
            if (Schema::hasColumn('business_details', 'contact_zip_code')) {
                $table->dropColumn('contact_zip_code');
            }
            if (Schema::hasColumn('business_details', 'dob')) {
                $table->dropColumn('dob');
            }
            if (Schema::hasColumn('business_details', 'home_address')) {
                $table->dropColumn('home_address');
            }
            if (Schema::hasColumn('business_details', 'home_city')) {
                $table->dropColumn('home_city');
            }
            if (Schema::hasColumn('business_details', 'home_state')) {
                $table->dropColumn('home_state');
            }
            if (Schema::hasColumn('business_details', 'home_zip_code')) {
                $table->dropColumn('home_zip_code');
            }
            if (Schema::hasColumn('business_details', 'address_city_state')) {
                $table->dropColumn('address_city_state');
            }
            if (Schema::hasColumn('business_details', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            if (Schema::hasColumn('business_details', 'instagram_username')) {
                $table->dropColumn('instagram_username');
            }
            if (Schema::hasColumn('business_details', 'mesanger_username')) {
                $table->dropColumn('mesanger_username');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_details', function (Blueprint $table) {
            if (!Schema::hasColumn('business_details', 'business_contact_address')) {
                $table->string('business_contact_address')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'contact_city')) {
                $table->string('contact_city')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'contact_state')) {
                $table->string('contact_state')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'contact_zip_code')) {
                $table->string('contact_zip_code')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'dob')) {
                $table->date('dob')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'home_address')) {
                $table->string('home_address')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'home_city')) {
                $table->string('home_city')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'home_state')) {
                $table->string('home_state')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'home_zip_code')) {
                $table->string('home_zip_code')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'Address_city_state')) {
                $table->string('Address_city_state')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'instagram_username')) {
                $table->string('instagram_username')->nullable();
            }
            if (!Schema::hasColumn('business_details', 'mesanger_username')) {
                $table->string('mesanger_username')->nullable();
            }
        });
    }
};
