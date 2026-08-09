<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('affiliate_commissions')) {
            Schema::table('affiliate_commissions', function (Blueprint $table) {
                if (!Schema::hasColumn('affiliate_commissions', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('affiliate_id');
                }
                if (!Schema::hasColumn('affiliate_commissions', 'order_amount')) {
                    $table->decimal('order_amount', 10, 2)->nullable()->after('order_id');
                }
                if (!Schema::hasColumn('affiliate_commissions', 'commission_rate')) {
                    $table->decimal('commission_rate', 6, 3)->nullable()->after('commission_amount');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('affiliate_commissions')) {
            Schema::table('affiliate_commissions', function (Blueprint $table) {
                if (Schema::hasColumn('affiliate_commissions', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('affiliate_commissions', 'order_amount')) {
                    $table->dropColumn('order_amount');
                }
                if (Schema::hasColumn('affiliate_commissions', 'commission_rate')) {
                    $table->dropColumn('commission_rate');
                }
            });
        }
    }
};
