<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('affiliates_programs', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('referral_code')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('username')->unique();
            $table->string('password');

            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('city_id')->nullable();

            $table->string('email_verify_token')->nullable();
            $table->tinyInteger('is_email_verified')->default(0)->comment('0: not verified, 1: verified');
            $table->string('account_display_name')->nullable();
            $table->string('company_website')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates_programs');
    }
};
