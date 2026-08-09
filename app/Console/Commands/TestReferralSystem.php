<?php

namespace App\Console\Commands;

use App\Models\Referral;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;

class TestReferralSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:test {--user-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the referral system and wallet integration';

    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        parent::__construct();
        $this->referralService = $referralService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');

        if ($userId) {
            $this->testUserReferrals($userId);
        } else {
            $this->testAllReferrals();
        }
    }

    private function testUserReferrals($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User not found with ID: {$userId}");
            return;
        }

        $this->info("Testing referrals for user: {$user->first_name} {$user->last_name}");
        $this->info("Referral code: {$user->referral_code}");
        
        // Check wallet
        $wallet = Wallet::where('user_id', $userId)->first();
        if ($wallet) {
            $this->info("Wallet balance: \${$wallet->balance}");
            $this->info("Remaining balance: \${$wallet->remaining_balance}");
        } else {
            $this->info("No wallet found for user");
        }

        // Check referral statistics
        $stats = $this->referralService->getReferralStats($userId);
        if ($stats) {
            $this->info("Total earnings: \${$stats['total_earnings']}");
            $this->info("Remaining potential: \${$stats['remaining_potential']}");
            $this->info("Pending referrals: {$stats['pending_referrals']}");
            $this->info("Completed referrals: {$stats['completed_referrals']}");
        }

        // Check wallet history
        $walletHistory = WalletHistory::where('user_id', $userId)
            ->where('payment_gateway', 'referral_reward')
            ->get();

        $this->info("Referral reward transactions: {$walletHistory->count()}");
        foreach ($walletHistory as $transaction) {
            $this->info("- Transaction: {$transaction->transaction_id}, Amount: \${$transaction->amount}");
        }
    }

    private function testAllReferrals()
    {
        $this->info("Testing all referrals in the system...");

        $completedReferrals = Referral::where('status', 'completed')->get();
        $this->info("Total completed referrals: {$completedReferrals->count()}");

        $pendingReferrals = Referral::where('status', 'pending')->get();
        $this->info("Total pending referrals: {$pendingReferrals->count()}");

        $totalRewards = $completedReferrals->sum('reward_amount');
        $this->info("Total rewards distributed: \${$totalRewards}");

        // Check wallet transactions
        $referralTransactions = WalletHistory::where('payment_gateway', 'referral_reward')->get();
        $this->info("Total referral wallet transactions: {$referralTransactions->count()}");
        $this->info("Total amount in wallet transactions: \${$referralTransactions->sum('amount')}");

        // Verify consistency
        if ($totalRewards != $referralTransactions->sum('amount')) {
            $this->warn("WARNING: Referral rewards and wallet transactions don't match!");
            $this->warn("Referral rewards: \${$totalRewards}");
            $this->warn("Wallet transactions: \${$referralTransactions->sum('amount')}");
        } else {
            $this->info("✓ Referral rewards and wallet transactions match!");
        }
    }
} 