<?php

namespace App\Services;

use App\Exceptions\WalletInsufficientBalance;
use App\Exceptions\WalletNotFoundException;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Wallet\Entities\Wallet;

class AdService
{
    public function payByWallet(Request $request)
    {
        $ad = Ad::findOrFail($request->id);

        $adTotalPrice = $ad->ppq * $ad->quantity;
        $wallet = Wallet::where('user_id', auth()->id())->first();

        throw_if(empty($wallet), WalletNotFoundException::class);
        throw_if($wallet->balance < $adTotalPrice, WalletInsufficientBalance::class);

        $ad->update(['is_paid' => true, 'gateway_slug' => 'wallet', 'status' => 'pending']);
        $wallet->update([
            'balance' => $wallet->balance - $adTotalPrice
        ]);

        // ✅ Create affiliate commission for ad purchase via wallet
        try {
            app(\App\Services\AffiliateCommissionService::class)->createGeneric(
                (int) auth()->id(),
                (float) $adTotalPrice,
                "Commission from ad purchase #{$ad->id} (Wallet)"
            );
        } catch (\Exception $e) {
            \Log::error("Affiliate Ad Wallet Commission Error: " . $e->getMessage());
        }
    }

    public function payByPayPro(Ad $ad)
    {
        $adTotalPrice = $ad->ppq * $ad->quantity;
        $ad->update(['is_paid' => true, 'gateway_slug' => 'paypro', 'status' => 'pending']);

        // ✅ Create affiliate commission for ad purchase via PayPro
        try {
            app(\App\Services\AffiliateCommissionService::class)->createGeneric(
                (int) $ad->user_id,
                (float) $adTotalPrice,
                "Commission from ad purchase #{$ad->id} (PayPro)"
            );
        } catch (\Exception $e) {
            \Log::error("Affiliate Ad PayPro Commission Error: " . $e->getMessage());
        }
    }

    public function payByPayFast(Ad $ad, $transaction_id = null)
    {
        $adTotalPrice = $ad->ppq * $ad->quantity;
        $ad->update([
            'is_paid' => true, 
            'gateway_slug' => 'payfast', 
            'status' => 'active', 
            'transaction_id' => $transaction_id
        ]);

        // ✅ Create affiliate commission for ad purchase via PayFast
        try {
            app(\App\Services\AffiliateCommissionService::class)->createGeneric(
                (int) $ad->user_id,
                (float) $adTotalPrice,
                "Commission from ad purchase #{$ad->id} (PayFast)"
            );
        } catch (\Exception $e) {
            \Log::error("Affiliate Ad PayFast Commission Error: " . $e->getMessage());
        }
    }
}