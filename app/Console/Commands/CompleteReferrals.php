<?php

namespace App\Console\Commands;

use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CompleteReferrals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:complete {--referral-id=} {--order-amount=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete pending referrals and award credits';

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
        $referralId = $this->option('referral-id');
        $orderAmount = $this->option('order-amount');

        if ($referralId) {
            // Complete specific referral
            $this->completeSpecificReferral($referralId, $orderAmount);
        } else {
            // Complete all pending referrals (for testing)
            $this->completeAllPendingReferrals();
        }
    }

    private function completeSpecificReferral($referralId, $orderAmount = null)
    {
        try {
            $referral = $this->referralService->completeReferral($referralId, $orderAmount);
            
            $this->info("Referral {$referralId} completed successfully!");
            $this->info("Reward amount: $" . number_format($referral->reward_amount, 2));
            
        } catch (\Exception $e) {
            $this->error("Failed to complete referral {$referralId}: " . $e->getMessage());
            Log::error("Referral completion failed: " . $e->getMessage());
        }
    }

    private function completeAllPendingReferrals()
    {
        $pendingReferrals = Referral::where('status', 'pending')->get();
        
        if ($pendingReferrals->isEmpty()) {
            $this->info("No pending referrals found.");
            return;
        }

        $this->info("Found {$pendingReferrals->count()} pending referrals.");

        foreach ($pendingReferrals as $referral) {
            try {
                $this->referralService->completeReferral($referral->id);
                $this->info("Completed referral {$referral->id} - Reward: $" . number_format($referral->reward_amount, 2));
            } catch (\Exception $e) {
                $this->error("Failed to complete referral {$referral->id}: " . $e->getMessage());
            }
        }

        $this->info("Referral completion process finished.");
    }
} 