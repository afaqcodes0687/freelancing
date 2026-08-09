<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order)
    {
        // Check if order status changed to completed
        if ($order->wasChanged('status') && $order->status == 1) { // Assuming 1 is completed status
            $this->processReferralCompletion($order);
        }
    }

    /**
     * Process referral completion when order is completed
     */
    private function processReferralCompletion(Order $order)
    {
        try {
            Log::info("Processing referral completion for order: {$order->id}");

            // Find pending referrals for the client (user_id)
            $pendingReferrals = Referral::where('referred_id', $order->user_id)
                ->where('status', 'pending')
                ->get();

            if ($pendingReferrals->isEmpty()) {
                Log::info("No pending referrals found for user: {$order->user_id}");
                return;
            }

            foreach ($pendingReferrals as $referral) {
                try {
                    // Complete the referral with the order amount
                    $this->referralService->completeReferral($referral->id, $order->payable_amount);
                    
                    Log::info("Successfully completed referral {$referral->id} for order {$order->id}");
                    
                } catch (\Exception $e) {
                    Log::error("Failed to complete referral {$referral->id}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error("Error processing referral completion for order {$order->id}: " . $e->getMessage());
        }
    }
} 