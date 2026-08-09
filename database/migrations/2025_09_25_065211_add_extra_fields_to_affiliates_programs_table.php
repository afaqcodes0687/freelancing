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
        Schema::table('affiliates_programs', function (Blueprint $table) {
            // Status / Approval
            $table->tinyInteger('status')->default(0)->comment('0: pending, 1: approved, 2: rejected, 3: blocked')->after('company_website');

            // Commission Settings
            $table->decimal('commission_rate', 8, 2)->default(0)->after('status');
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage')->after('commission_rate');

            // Wallet
            $table->decimal('balance', 12, 2)->default(0)->after('commission_type');
            $table->decimal('total_earned', 12, 2)->default(0)->after('balance');

            // Payment / Payout Info
            $table->string('payment_method')->nullable()->after('total_earned');
            $table->string('paypal_email')->nullable()->after('payment_method');
            $table->string('bank_account')->nullable()->after('paypal_email');
            $table->string('iban')->nullable()->after('bank_account');

            // Tracking last login
            $table->timestamp('last_login_at')->nullable()->after('iban');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliates_programs', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'commission_rate',
                'commission_type',
                'balance',
                'total_earned',
                'payment_method',
                'paypal_email',
                'bank_account',
                'iban',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
