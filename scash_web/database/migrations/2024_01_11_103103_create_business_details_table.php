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
        Schema::create('business_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('tax_type')->nullable();
            $table->integer('registration_type')->nullable();
            $table->string('logo')->nullable();
            $table->string('business_name')->nullable();
            $table->string('username')->nullable();
            $table->string('about_business')->nullable();
            $table->string('business_type')->nullable();
            $table->string('business_category')->nullable();
            $table->string('leagal_name')->nullable();
            $table->string('business_street_address')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_state')->nullable();
            $table->string('business_zip_code')->nullable();
            $table->string('business_ein')->nullable();
            $table->string('business_phone_number')->nullable();
            $table->string('business_contact_address')->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_state')->nullable();
            $table->string('contact_zip_code')->nullable();
            $table->date('dob')->nullable();
            $table->string('home_address')->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_state')->nullable();
            $table->string('home_zip_code')->nullable();
            $table->string('ssn_itin')->nullable();
            $table->string('email')->nullable();
            $table->string('Address_city_state')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('instagram_username')->nullable();
            $table->string('mesanger_username')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_details');
    }
};
