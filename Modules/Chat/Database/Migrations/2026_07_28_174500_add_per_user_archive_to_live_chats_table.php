<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('live_chats', function (Blueprint $table) {
            $table->boolean('client_archived')->default(0)->after('ended_at');
            $table->boolean('freelancer_archived')->default(0)->after('client_archived');
        });
    }

    public function down(): void {
        Schema::table('live_chats', function (Blueprint $table) {
            $table->dropColumn(['client_archived', 'freelancer_archived']);
        });
    }
};
