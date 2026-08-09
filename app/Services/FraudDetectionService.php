<?php

namespace App\Services;

use App\Models\User;
use App\Models\AffiliateProgram;
use App\Models\AffiliateClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FraudDetectionService
{
    /**
     * Check if a referral is suspicious.
     * 
     * @param Request $request
     * @param int|string $referrerId
     * @param string $referrerType 'user'|'affiliate'
     * @param int|null $referredUserId
     * @return bool
     */
    public function isSuspicious(Request $request, $referrerId, $referrerType, $referredUserId = null): bool
    {
        // 1. Self-referral check (by IP)
        if ($this->isSelfReferralByIP($request, $referrerId, $referrerType)) {
            Log::warning("FraudDetection: Self-referral detected via IP for {$referrerType} ID {$referrerId}");
            return true;
        }

        // 2. Self-referral check (by Auth ID)
        if ($referredUserId && $referrerType === 'user' && (int) $referrerId === (int) $referredUserId) {
            Log::warning("FraudDetection: Self-referral detected via ID matching for user {$referrerId}");
            return true;
        }

        // 3. Same device check (User Agent + IP fingerprint - simple version)
        if ($this->isDuplicateDevice($request, $referrerId, $referrerType)) {
            Log::warning("FraudDetection: Duplicate device/fingerprint detected for {$referrerType} ID {$referrerId}");
            // return true; // Could be strict or just log/flag
        }

        // 4. Rate limiting signups from same IP (optional, usually handled by Laravel built-in)

        return false;
    }

    /**
     * Check if the current IP matches the referrer's last login IP.
     */
    protected function isSelfReferralByIP(Request $request, $referrerId, $referrerType): bool
    {
        $ip = $request->ip();

        if ($referrerType === 'affiliate') {
            $referrer = AffiliateProgram::find($referrerId);
            return $referrer && ($referrer->last_login_ip === $ip);
        }

        // For regular users, we might need a login history table or check user model if IP is stored
        // Many Laravel apps have last_login_ip on users table
        $referrer = User::find($referrerId);
        // Assuming user model might have something like last_login_ip or checking recent clicks

        return false; // Implement based on available data
    }

    /**
     * Check for same device fingerprint.
     */
    protected function isDuplicateDevice(Request $request, $referrerId, $referrerType): bool
    {
        $userAgent = $request->userAgent();
        $ip = $request->ip();

        // Check if this referrer has a very high click-to-signup ratio from the same fingerprint
        // This is a more advanced check for production
        return false;
    }
}
