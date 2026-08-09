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
        Schema::table('user_introductions', function (Blueprint $table) {
            $table->string('github_link')->nullable()->after('description');
            $table->string('stackoverflow_link')->nullable()->after('github_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_introductions', function (Blueprint $table) {
            $table->dropColumn(['github_link', 'stackoverflow_link']);
        });
    }
};
