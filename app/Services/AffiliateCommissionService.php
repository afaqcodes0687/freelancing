<?php

namespace App\Services;

use App\Models\AffiliateRegistration;
use App\Models\AffiliateCommission;
use App\Models\AffiliateProgram;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AffiliateCommissionService
{
    /**
     * Create commission for an order if buyer or freelancer is attributed.
     */
    public function createForOrder(int $orderId): void
    {
        $order = Order::find($orderId);
        if (!$order) {
            Log::info("AffiliateCommissionService: Order not found for ID {$orderId}");
            return;
        }

        // 🔹 Process commission for Buyer (Client)
        $buyerId = $order->user_id ?? $order->buyer_id;
        if ($buyerId) {
            $this->processUserCommission($order, $buyerId, 'buyer');
        }

        // 🔹 Process commission for Freelancer
        $freelancerId = $order->freelancer_id;
        if ($freelancerId) {
            $this->processUserCommission($order, $freelancerId, 'freelancer');
        }
    }

    /**
     * Universal method to create commission for ANY payment (Subscriptions, Boosts, etc.)
     */
    public function createGeneric(int $referredUserId, float $amount, string $description, ?int $orderId = null): void
    {
        // 🔹 Find attribution
        $attribution = $this->getAttributionData($referredUserId);
        if (!$attribution) {
            return;
        }

        $referrerId = $attribution['referrer_id'];
        $referrerType = $attribution['type']; // 'user' | 'affiliate'

        // 🔹 Determine commission rate
        $isFirst = !AffiliateCommission::where('user_id', $referredUserId)->exists();

        // Get from settings (Admin panel)
        $firstPercent = (float) (get_static_option('affiliate_first_purchase_percent') ?? 0.05);
        $recurringPercent = (float) (get_static_option('affiliate_recurring_percent') ?? 0.025);

        // Window check (12 months)
        $registrationDate = $attribution['registration_date'] ?? null;
        $isWithinWindow = $registrationDate ? Carbon::parse($registrationDate)->addMonths(12)->isFuture() : false;

        $rate = 0;
        if ($isFirst) {
            $rate = $firstPercent;
        } elseif ($isWithinWindow) {
            $rate = $recurringPercent;
        }

        if ($rate <= 0)
            return;

        // 🔹 Calculate Amount
        $commissionAmount = round(($amount * $rate) / 100, 2);

        if ($commissionAmount <= 0)
            return;

        // 🔹 Record Commission
        \DB::beginTransaction();
        try {
            AffiliateCommission::create([
                'affiliate_id' => $referrerType === 'affiliate' ? $referrerId : null,
                'user_id' => $referredUserId,
                'referrer_user_id' => $referrerType === 'user' ? $referrerId : null,
                'order_id' => $orderId,
                'order_amount' => $amount,
                'commission_rate' => $rate,
                'commission_amount' => $commissionAmount,
                'status' => 'pending',
                'description' => $description . " (Referrer: {$referrerType} ID {$referrerId})",
            ]);

            \DB::commit();
            Log::info("AffiliateCommissionService: Generic commission created for user {$referredUserId}. Amount: {$commissionAmount}");
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error("AffiliateCommissionService: Generic commission failed for user {$referredUserId}. Error: " . $e->getMessage());
        }
    }

    /**
     * Internal method to process commission for a specific user in an order.
     */
    private function processUserCommission(Order $order, int $referredUserId, string $role): void
    {
        // 🔹 Avoid duplicates per user per order
        if (AffiliateCommission::where('order_id', $order->id)->where('user_id', $referredUserId)->exists()) {
            return;
        }

        $orderAmount = (float) ($role === 'freelancer' ? ($order->payable_amount ?? $order->price) : ($order->price ?? 0));
        $description = "Commission from order #{$order->id} ({$role})";

        $this->createGeneric($referredUserId, $orderAmount, $description, $order->id);
    }

    protected function getAttributionData(int $userId): ?array
    {
        // 1. Check User-to-User referral
        $user = \App\Models\User::find($userId);
        if ($user && $user->referred_by) {
            $referralRecord = \App\Models\Referral::where('referred_id', $userId)->first();
            return [
                'referrer_id' => (int) $user->referred_by,
                'type' => 'user',
                'registration_date' => $referralRecord ? $referralRecord->created_at : $user->created_at
            ];
        }

        // 2. Check Affiliate-to-User registration
        $affiliateReg = AffiliateRegistration::where('user_id', $userId)->first();
        if ($affiliateReg) {
            return [
                'referrer_id' => (int) $affiliateReg->affiliate_id,
                'type' => 'affiliate',
                'registration_date' => $affiliateReg->created_at
            ];
        }

        return null;
    }

    protected function creditUserWallet($userId, $amount, $commission)
    {
        $wallet = \Modules\Wallet\Entities\Wallet::firstOrCreate(['user_id' => $userId], [
            'balance' => 0,
            'remaining_balance' => 0,
            'status' => 1
        ]);

        $wallet->increment('balance', $amount);
        $wallet->increment('remaining_balance', $amount);

        \Modules\Wallet\Entities\WalletHistory::create([
            'user_id' => $userId,
            'payment_gateway' => 'referral_commission',
            'payment_status' => 'complete',
            'amount' => $amount,
            'transaction_id' => 'COMM_' . $commission->id,
            'status' => 1
        ]);
    }

    protected function updateAffiliateBalance($affiliateId, $amount)
    {
        $affiliate = AffiliateProgram::find($affiliateId);
        if ($affiliate) {
            $affiliate->increment('balance', $amount);
            $affiliate->increment('total_earned', $amount);
        }
    }
}
