<?php

namespace App\Services;

use App\Models\User;
use App\Models\AffiliateProgram;
use App\Models\AffiliateCommission;
use App\Models\Order;
use App\Models\Referral;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionProcessorService
{
    /**
     * Process commission for a successful order.
     */
    public function processOrderCommission(int $orderId): void
    {
        $order = Order::find($orderId);
        if (!$order || $order->status != 3)
            return; // Only process completed orders

        // 1. Try to find if buyer or freelancer was referred
        $referredUserId = $order->user_id ?? $order->buyer_id;
        if (!$referredUserId)
            return;

        // Check attribution
        $attribution = $this->getAttribution($referredUserId);
        if (!$attribution)
            return;

        $referrerId = $attribution['referrer_id'];
        $referrerType = $attribution['type']; // 'user' | 'affiliate'

        // 2. Prevent duplicate commission for same order
        if (AffiliateCommission::where('order_id', $orderId)->exists()) {
            return;
        }

        // 3. Calculate Commission Amount
        $commissionAmount = $this->calculateCommission($order->payable_amount, $referrerId, $referrerType, $referredUserId);

        if ($commissionAmount <= 0)
            return;

        // 4. Record and potentially auto-payout
        $this->recordCommission($order, $referrerId, $referrerType, $referredUserId, $commissionAmount);
    }

    /**
     * Get attribution for a user.
     */
    protected function getAttribution(int $userId): ?array
    {
        // Check referrals table (User-to-User)
        $referral = Referral::where('referred_id', $userId)->first();
        if ($referral) {
            return ['referrer_id' => $referral->referrer_id, 'type' => 'user'];
        }

        // Check affiliate registrations (Affiliate-to-User)
        // Note: You might have an AffiliateRegistration model that bridges AffiliateProgram to User
        $affiliateReg = \App\Models\AffiliateRegistration::where('user_id', $userId)->first();
        if ($affiliateReg) {
            return ['referrer_id' => $affiliateReg->affiliate_id, 'type' => 'affiliate'];
        }

        return null;
    }

    /**
     * Logic to calculate commission.
     */
    protected function calculateCommission($amount, $referrerId, $referrerType, $referredUserId): float
    {
        // Upwork style: Higher commission on first payment, lower on recurring
        $isFirst = !AffiliateCommission::where('user_id', $referredUserId)->exists();

        $rate = $isFirst ? 70 : 5; // Example: 70% first, 5% recurring

        // Check caps (e.g. max $150 per referred user)
        $totalEarnedFromUser = AffiliateCommission::where('user_id', $referredUserId)
            ->where('status', 'approved')
            ->sum('commission_amount');

        $cap = 150.0;
        if ($totalEarnedFromUser >= $cap)
            return 0;

        $calculated = round(($amount * $rate) / 100, 2);

        if (($totalEarnedFromUser + $calculated) > $cap) {
            $calculated = $cap - $totalEarnedFromUser;
        }

        return $calculated;
    }

    /**
     * Save commission and update wallet for users.
     */
    protected function recordCommission($order, $referrerId, $referrerType, $referredUserId, $amount): void
    {
        DB::beginTransaction();
        try {
            $commission = AffiliateCommission::create([
                'affiliate_id' => $referrerType === 'affiliate' ? $referrerId : null,
                'user_id' => $referredUserId,
                'referrer_user_id' => $referrerType === 'user' ? $referrerId : null,
                'order_id' => $order->id,
                'order_amount' => $order->payable_amount,
                'commission_amount' => $amount,
                'status' => 'pending', // Usually pending until admin approves or auto-approved
                'description' => "Commission from order #{$order->id} (Referrer Type: {$referrerType})"
            ]);

            // If referrer is a user, we can auto-credit wallet (or keep pending)
            if ($referrerType === 'user') {
                $this->creditUserWallet($referrerId, $amount, $commission);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to record commission: " . $e->getMessage());
        }
    }

    protected function creditUserWallet($userId, $amount, $commission)
    {
        $wallet = Wallet::firstOrCreate(['user_id' => $userId], [
            'balance' => 0,
            'remaining_balance' => 0,
            'status' => 1
        ]);

        $wallet->increment('balance', $amount);
        $wallet->increment('remaining_balance', $amount);

        WalletHistory::create([
            'user_id' => $userId,
            'payment_gateway' => 'referral_commission',
            'payment_status' => 'complete',
            'amount' => $amount,
            'transaction_id' => 'COMM_' . $commission->id,
            'status' => 1
        ]);

        $commission->update(['status' => 'approved']);
    }
}
