<?php

namespace App\Console\Commands;

use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;

class FixReferralRewards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:fix-rewards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix referral rewards that were not added to wallets';

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
        $this->info("Checking for referral rewards that need to be added to wallets...");

        // Find completed referrals that don't have corresponding wallet transactions
        $completedReferrals = Referral::where('status', 'completed')
            ->where('reward_amount', '>', 0)
            ->get();

        $fixedCount = 0;
        $totalAmount = 0;

        foreach ($completedReferrals as $referral) {
            // Check if wallet transaction exists for this referral
            $existingTransaction = WalletHistory::where('user_id', $referral->referrer_id)
                ->where('payment_gateway', 'referral_reward')
                ->where('amount', $referral->reward_amount)
                ->where('transaction_id', 'like', '%REF_%')
                ->first();

            if (!$existingTransaction) {
                try {
                    // Add the reward to wallet
                    $this->addRewardToWallet($referral);
                    $fixedCount++;
                    $totalAmount += $referral->reward_amount;
                    
                    $this->info("Fixed referral {$referral->id}: \${$referral->reward_amount} added to wallet");
                    
                } catch (\Exception $e) {
                    $this->error("Failed to fix referral {$referral->id}: " . $e->getMessage());
                }
            }
        }

        if ($fixedCount > 0) {
            $this->info("Successfully fixed {$fixedCount} referrals with total amount: \${$totalAmount}");
        } else {
            $this->info("No referral rewards need fixing.");
        }
    }

    /**
     * Add reward to user's wallet
     */
    private function addRewardToWallet($referral)
    {
        $user = $referral->referrer;
        $amount = $referral->reward_amount;

        // Find or create wallet for the user
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'remaining_balance' => 0,
                'withdraw_amount' => 0,
                'status' => 1
            ]);
        }

        // Add the referral reward to both balance and remaining_balance
        $wallet->increment('balance', $amount);
        $wallet->increment('remaining_balance', $amount);

        // Create wallet history record for tracking
        $transactionId = 'REF_' . time() . '_' . $user->id . '_FIXED';
        
        WalletHistory::create([
            'user_id' => $user->id,
            'payment_gateway' => 'referral_reward',
            'payment_status' => 'complete',
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'status' => 1,
            'email_send' => 0
        ]);

        Log::info("Fixed referral reward of \${$amount} to user {$user->id} wallet. Transaction ID: {$transactionId}");
    }
} 