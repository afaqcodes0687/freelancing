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
        Schema::create('link_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bio_link_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->comment('Link owner');
            $table->foreignId('visitor_id')->nullable()->comment('Logged-in visitor');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();

            $table->index(['bio_link_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['visitor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_clicks');
    }
};
