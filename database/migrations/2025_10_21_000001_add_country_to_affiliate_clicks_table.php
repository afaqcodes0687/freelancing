<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('affiliate_clicks') && !Schema::hasColumn('affiliate_clicks', 'country')) {
            Schema::table('affiliate_clicks', function (Blueprint $table) {
                $table->string('country', 2)->nullable()->after('referer');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('affiliate_clicks') && Schema::hasColumn('affiliate_clicks', 'country')) {
            Schema::table('affiliate_clicks', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }
    }
};
