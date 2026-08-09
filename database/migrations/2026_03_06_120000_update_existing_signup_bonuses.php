<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing wallets to set signup_bonus properly
        
        // 1. Set signup_bonus = 10 and subtract $10 from balance for freelancers who have balance = 10 and signup_bonus is null or 0
        // These are likely users who only have signup bonus in their balance
        DB::table('wallets')
            ->join('users', 'wallets.user_id', '=', 'users.id')
            ->where('users.user_type', 2) // Freelancers only
            ->where('wallets.balance', 10) // Exactly $10 (likely only signup bonus)
            ->where(function($query) {
                $query->whereNull('wallets.signup_bonus')
                      ->orWhere('wallets.signup_bonus', 0);
            })
            ->update([
                'wallets.signup_bonus' => 10,
                'wallets.balance' => DB::raw('balance - 10'), // Subtract signup bonus from balance
                'wallets.remaining_balance' => DB::raw('remaining_balance - 10') // Also subtract from remaining_balance
            ]);
        
        // 2. Set signup_bonus = 0 for freelancers who have balance > 10 (they have earnings)
        // Keep their existing balance, just set signup_bonus to 0
        DB::table('wallets')
            ->join('users', 'wallets.user_id', '=', 'users.id')
            ->where('users.user_type', 2) // Freelancers only
            ->where('wallets.balance', '>', 10) // More than signup bonus (has earnings)
            ->where(function($query) {
                $query->whereNull('wallets.signup_bonus')
                      ->orWhere('wallets.signup_bonus', 0);
            })
            ->update(['wallets.signup_bonus' => 0]);
        
        // 3. For any remaining wallets with null signup_bonus, set it to 0
        DB::table('wallets')
            ->whereNull('signup_bonus')
            ->update(['signup_bonus' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all signup_bonus back to null for rollback
        DB::table('wallets')
            ->whereNotNull('signup_bonus')
            ->update(['signup_bonus' => null]);
    }
};
