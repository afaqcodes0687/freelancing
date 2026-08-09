<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('how_it_works', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('meta_keywords')->nullable();
            $table->string('banner_title');
            
            // Hiring Tab Content
            $table->string('hiring_content_title');
            $table->text('hiring_content_subtitle');
            $table->text('hiring_main_content');
            $table->json('hiring_faqs');
            $table->string('hiring_side_image')->nullable();
            
            // Hiring Progress Section
            $table->string('hiring_progress_title');
            $table->text('hiring_progress_subtitle');
            $table->text('hiring_progress_content');
            $table->json('hiring_progress_faqs');
            $table->string('hiring_progress_image')->nullable();
            
            // Hiring Payment Section
            $table->string('hiring_payment_title');
            $table->text('hiring_payment_subtitle');
            $table->text('hiring_payment_content');
            $table->json('hiring_payment_faqs');
            $table->string('hiring_payment_image')->nullable();
            
            // Talents Tab Content
            $table->string('talents_content_title');
            $table->text('talents_content_subtitle');
            $table->text('talents_main_content');
            $table->json('talents_faqs');
            $table->string('talents_side_image')->nullable();
            
            // Talents Payment Section
            $table->string('talents_payment_title');
            $table->text('talents_payment_subtitle');
            $table->text('talents_payment_content');
            $table->json('talents_payment_faqs');
            $table->string('talents_payment_image')->nullable();
            
            // FAQ Tab Content
            $table->string('faq_content_title');
            $table->text('faq_content_subtitle');
            $table->text('faq_main_content');
            $table->json('faq_faqs');
            $table->string('faq_side_image')->nullable();
            
            // Projects Tab Content
            $table->string('projects_content_title');
            $table->text('projects_content_subtitle');
            $table->text('projects_main_content');
            $table->json('projects_faqs');
            $table->string('projects_side_image')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('how_it_works');
    }
};
