<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('talent_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('heading')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('faq_content')->nullable();
            $table->json('benefits')->nullable(); // For structured benefits data
            $table->json('faqs')->nullable(); // For structured FAQ data
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_benefits');
    }
};
