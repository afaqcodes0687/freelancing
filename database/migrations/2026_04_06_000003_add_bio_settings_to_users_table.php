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
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio_description')->nullable();
            $table->string('bio_avatar')->nullable();
            $table->string('bio_theme')->default('default');
            $table->boolean('bio_enabled')->default(true);
            $table->integer('bio_views')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio_description',
                'bio_avatar', 
                'bio_theme',
                'bio_enabled',
                'bio_views'
            ]);
        });
    }
};
