<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Optimize affiliates_programs
        Schema::table('affiliates_programs', function (Blueprint $table) {
            if (!$this->indexExists('affiliates_programs', 'affiliates_programs_username_index')) {
                $table->index('username');
            }

            if (!$this->indexExists('affiliates_programs', 'affiliates_programs_email_index')) {
                $table->index('email');
            }

            if (!$this->indexExists('affiliates_programs', 'affiliates_programs_referral_code_index')) {
                $table->index('referral_code');
            }
        });

        // Optimize affiliate_clicks
        Schema::table('affiliate_clicks', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliate_clicks', 'user_referrer_id')) {
                $table->unsignedBigInteger('user_referrer_id')->nullable()->after('affiliate_id');
            }

            $table->index('user_referrer_id');
            $table->index('ip_address');
            $table->index('clicked_at');
            $table->index('country');
        });

        // Optimize affiliate_commissions
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliate_commissions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('affiliate_id');
            }

            if (!Schema::hasColumn('affiliate_commissions', 'referrer_user_id')) {
                $table->unsignedBigInteger('referrer_user_id')->nullable()->after('user_id');
            }

            $table->index('user_id');
            $table->index('referrer_user_id');
            $table->index('order_id');
            $table->index('status');
        });

        // Optimize referrals
        Schema::table('referrals', function (Blueprint $table) {
            $table->index('status');
            $table->index('referral_code');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliates_programs', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['email']);
            $table->dropIndex(['referral_code']);
        });

        Schema::table('affiliate_clicks', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
            $table->dropIndex(['clicked_at']);
            $table->dropIndex(['country']);
        });

        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['order_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('referrals', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['referral_code']);
        });
    }


    /**
     * Helper (PRIVATE method, not global)
     */
    private function indexExists($table, $index)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$index}'");
        return count($indexes) > 0;
    }
};
