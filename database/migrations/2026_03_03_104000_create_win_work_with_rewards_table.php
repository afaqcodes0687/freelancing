<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('win_work_with_rewards', function (Blueprint $table) {
            $table->id();
            
            // SEO Meta Fields
            $table->string('title')->default('Win Work With Reward');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Banner Section
            $table->string('banner_title')->nullable();
            
            // Main Section
            $table->string('main_title')->nullable();
            $table->text('main_subtitle')->nullable();
            $table->string('clients_count')->nullable();
            $table->string('clients_text')->nullable();
            $table->string('freelancers_count')->nullable();
            $table->string('freelancers_text')->nullable();
            $table->string('orders_count')->nullable();
            $table->string('orders_text')->nullable();
            $table->string('main_image')->nullable();
            
            // Solutions Section
            $table->string('solutions_title')->nullable();
            $table->text('solutions_subtitle')->nullable();
            
            // Boosted Profile Section
            $table->string('boosted_profile_title')->nullable();
            $table->string('boosted_profile_subtitle')->nullable();
            $table->text('boosted_profile_content')->nullable();
            $table->string('boosted_profile_image')->nullable();
            
            // Availability Badge Section
            $table->string('availability_badge_title')->nullable();
            $table->string('availability_badge_subtitle')->nullable();
            $table->text('availability_badge_content')->nullable();
            $table->string('availability_badge_image')->nullable();
            
            // Enhanced Proposals Section
            $table->string('enhanced_proposals_title')->nullable();
            $table->string('enhanced_proposals_subtitle')->nullable();
            $table->text('enhanced_proposals_content')->nullable();
            $table->string('enhanced_proposals_image')->nullable();
            
            // Payment Section
            $table->string('payment_title')->nullable();
            $table->string('payment_subtitle')->nullable();
            $table->text('payment_content')->nullable();
            
            // Why Use Ads Section
            $table->string('why_use_title')->nullable();
            $table->text('why_use_content')->nullable();
            
            // Getting Started Section
            $table->string('getting_started_title')->nullable();
            $table->text('getting_started_content')->nullable();
            
            // Place Bid Section
            $table->string('place_bid_title')->nullable();
            $table->text('place_bid_content')->nullable();
            
            // Advertising Options Section
            $table->string('advertising_options_title')->nullable();
            $table->json('advertising_options')->nullable();
            
            // Helpful Resources Section
            $table->string('helpful_resources_title')->nullable();
            $table->text('helpful_resources_content')->nullable();
            
            // Ads Guide Section
            $table->string('ads_guide_title')->nullable();
            $table->text('ads_guide_content')->nullable();
            
            // Master Ads Section
            $table->string('master_ads_title')->nullable();
            $table->text('master_ads_content')->nullable();
            
            // CTA Section
            $table->string('cta_title')->nullable();
            $table->string('cta_button_text')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('win_work_with_rewards');
    }
};
