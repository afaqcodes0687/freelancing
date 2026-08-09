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
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            
            // CEO Section
            $table->string('ceo_name')->nullable();
            $table->string('ceo_title')->nullable();
            $table->text('ceo_description')->nullable();
            $table->string('ceo_image')->nullable();
            
            // Main About Section
            $table->string('main_title')->nullable();
            $table->text('main_description')->nullable();
            $table->text('opportunity_text')->nullable();
            
            // Statistics
            $table->string('clients_count')->nullable();
            $table->string('freelancers_count')->nullable();
            $table->string('orders_count')->nullable();
            $table->string('jobs_handled')->nullable();
            $table->string('earned_amount')->nullable();
            $table->string('awards_count')->nullable();
            
            // Video Section
            $table->string('video_title')->nullable();
            $table->text('video_description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_thumbnail')->nullable();
            
            // What We Do Section
            $table->string('what_we_do_title')->nullable();
            $table->text('what_we_do_description')->nullable();
            
            // Certifications Section
            $table->string('certifications_title')->nullable();
            $table->text('certifications_description')->nullable();
            $table->json('certifications')->nullable(); // Store certification images and links
            
            // Team Section
            $table->string('team_title')->nullable();
            $table->json('team_members')->nullable(); // Store team member data
            
            // Meta Data
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
