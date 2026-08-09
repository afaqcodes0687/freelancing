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
            $table->text('github_meta')->nullable()->after('stackoverflow_link');
            $table->text('stackoverflow_meta')->nullable()->after('github_meta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_introductions', function (Blueprint $table) {
            $table->dropColumn(['github_meta', 'stackoverflow_meta']);
        });
    }
};
