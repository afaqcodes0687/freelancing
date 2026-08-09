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
        Schema::table('live_chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('live_chat_messages', 'is_delivered')) {
                $table->boolean('is_delivered')->default(0)->after('is_seen');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_chat_messages', function (Blueprint $table) {
            $table->dropColumn('is_delivered');
        });
    }
};
