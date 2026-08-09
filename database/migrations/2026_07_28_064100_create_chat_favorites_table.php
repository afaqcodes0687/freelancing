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
        Schema::create('chat_favorites', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('chat_with_user_id')->nullable();
            $table->string('user_type')->default('client'); // client or freelancer
            $table->timestamps();

            // Unique constraint to prevent duplicate favorites
            $table->unique(['user_id', 'chat_with_user_id', 'user_type'], 'unique_chat_favorite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_favorites');
    }
};
