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
        Schema::create('partnership_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->string('escrow_title')->nullable();
            $table->text('escrow_description')->nullable();
            $table->string('escrow_image')->nullable();

            $table->string('why_partner_title')->nullable();
            $table->text('why_partner_description')->nullable();
            $table->string('expand_talent_title')->nullable();
            $table->text('expand_talent_description')->nullable();
            $table->string('expand_talent_image')->nullable();

            $table->string('foster_innovation_title')->nullable();
            $table->text('foster_innovation_description')->nullable();
            $table->string('foster_innovation_image')->nullable();

            $table->string('market_presence_title')->nullable();
            $table->text('market_presence_description')->nullable();
            $table->string('market_presence_image')->nullable();

            $table->text('economic_empowerment_description')->nullable();
            $table->string('economic_empowerment_image')->nullable();
            $table->json('opportunities')->nullable();
            $table->json('process')->nullable();

            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_pages');
    }
};
