<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('live_chats', function (Blueprint $table) {
            $table->enum('status', ['active', 'archived', 'ended'])->default('active')->after('admin_id');
            $table->timestamp('ended_at')->nullable()->after('status');
        });
    }

    public function down(): void {
        Schema::table('live_chats', function (Blueprint $table) {
            $table->dropColumn(['status', 'ended_at']);
        });
    }
};
