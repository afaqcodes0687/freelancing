<?php

namespace App\Services;

use App\Models\User;
use App\Models\AffiliateRegistration;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferralRegistrationService
{
    protected $trackingService;

    public function __construct(ReferralTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Bind a newly registered user to their referrer.
     */
    public function bindReferral(Request $request, int $userId): void
    {
        $attribution = $this->trackingService->getAttributionData($request);
        if (!$attribution)
            return;

        $referrerId = $attribution['id'];
        $type = $attribution['type'];
        $code = $attribution['code'];

        try {
            if ($type === 'affiliate') {
                $this->createAffiliateBinding($referrerId, $userId);
            } elseif ($type === 'user') {
                $this->createUserBinding($referrerId, $userId, $code);
            }

            Log::info("Referral Registration: User {$userId} successfully bound to {$type} ID {$referrerId}");
        } catch (\Exception $e) {
            Log::error("Failed to bind referral: " . $e->getMessage());
        }
    }

    protected function createAffiliateBinding($affiliateId, $userId)
    {
        AffiliateRegistration::firstOrCreate([
            'affiliate_id' => $affiliateId,
            'user_id' => $userId
        ]);
    }

    protected function createUserBinding($referrerId, $userId, $code)
    {
        // Update user model column
        $user = User::find($userId);
        if ($user) {
            $user->update(['referred_by' => $referrerId]);
        }

        // Create record in referrals table
        Referral::firstOrCreate([
            'referrer_id' => $referrerId,
            'referred_id' => $userId
        ], [
            'referral_code' => $code,
            'status' => 'pending'
        ]);
    }
}
