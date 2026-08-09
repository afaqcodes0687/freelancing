<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trust_and_safeties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('banner_title');
            $table->string('content_title');
            $table->text('introduction');
            $table->text('top_rated_program');
            $table->text('communication_importance');
            $table->text('escrow_system');
            $table->text('customer_support');
            $table->text('dispute_resolution');
            $table->text('freelancer_profiles');
            $table->text('project_guidelines');
            $table->string('scam_protection_title');
            $table->json('scam_protection_points');
            $table->text('contact_info');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trust_and_safeties');
    }
};
