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
        Schema::create('service_shipping_policies', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Service & Shipping Policy');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('heading')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('faq_content')->nullable();
            $table->json('faqs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_shipping_policies');
    }
};
