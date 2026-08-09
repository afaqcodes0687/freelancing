<?php

namespace App\Services;

use App\Models\User;
use App\Models\AffiliateProgram;
use App\Models\AffiliateClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReferralTrackingService
{
    protected $fraudService;
    const COOKIE_NAME = 'ref_data';
    const COOKIE_LIFETIME = 86400; // 60 days in minutes (60 * 24 * 60)

    public function __construct(FraudDetectionService $fraudService)
    {
        $this->fraudService = $fraudService;
    }

    /**
     * Identify and track a referral click from the request.
     */
    public function trackClick(Request $request): void
    {
        $refCode = $request->get('ref');
        if (!$refCode)
            return;

        // Try finding as affiliate first, then as user
        $referrer = $this->findReferrer($refCode);
        if (!$referrer)
            return;

        $referrerId = $referrer['id'];
        $referrerType = $referrer['type'];

        // Fraud Check: Don't track if is suspicious (self-click)
        if ($this->fraudService->isSuspicious($request, $referrerId, $referrerType)) {
            return;
        }

        // Avoid duplicate click tracking within short window (1 hour)
        $exists = AffiliateClick::where('ip_address', $request->ip())
            ->where('affiliate_id', $referrerType === 'affiliate' ? $referrerId : null)
            // If it's a user referral, we might need a separate clicks table or use the same one with nullable/extra fields
            ->where('created_at', '>', now()->subHour())
            ->exists();

        if (!$exists) {
            $this->storeClick($request, $referrerId, $referrerType);
        }

        // Set persistent cookie
        $this->setReferralCookie($referrerId, $referrerType, $refCode);
    }

    /**
     * Find referrer by code (Affiliate first, then User).
     */
    protected function findReferrer(string $code): ?array
    {
        $affiliate = AffiliateProgram::where('referral_code', $code)->first();
        if ($affiliate) {
            return ['id' => $affiliate->id, 'type' => 'affiliate'];
        }

        $user = User::where('referral_code', $code)->first();
        if ($user) {
            return ['id' => $user->id, 'type' => 'user'];
        }

        return null;
    }

    /**
     * Log click to database.
     */
    protected function storeClick(Request $request, $id, $type): void
    {
        // We use the existing affiliate_clicks table but we should ideally ensure it supports user_referrals too
        // For now, let's keep it consistent or use a unified click log
        try {
            AffiliateClick::create([
                'affiliate_id' => $type === 'affiliate' ? $id : null,
                'user_referrer_id' => $type === 'user' ? $id : null, // Assuming we added this column or use it differently
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
                'country' => $request->headers->get('CF-IPCountry'),
                'clicked_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to store referral click: " . $e->getMessage());
        }
    }

    /**
     * Set a 60-day cookie for attribution.
     */
    protected function setReferralCookie($id, $type, $code): void
    {
        $data = json_encode([
            'id' => $id,
            'type' => $type,
            'code' => $code,
            'time' => time()
        ]);

        Cookie::queue(Cookie::make(self::COOKIE_NAME, $data, self::COOKIE_LIFETIME));
    }

    /**
     * Retrieve attribution data from request (Cookie or Session).
     */
    public function getAttributionData(Request $request): ?array
    {
        // Check cookie first
        $cookieData = $request->cookie(self::COOKIE_NAME);
        if ($cookieData) {
            return json_decode($cookieData, true);
        }

        // Check session for API requests (use request's session instance)
        $sessionData = $request->session()->get('referral_code');
        if ($sessionData) {
            // Find referrer from session data
            $referrer = $this->findReferrer($sessionData);
            if ($referrer) {
                return [
                    'id' => $referrer['id'],
                    'type' => $referrer['type'],
                    'code' => $sessionData
                ];
            }
        }

        // Fallback: if no referral info, use default referrer (ID 8461 / code 4FCUA0PO)
        $defaultUser = User::where('id', 8461)->orWhere('referral_code', '4FCUA0PO')->first();
        if ($defaultUser) {
            return [
                'id' => $defaultUser->id,
                'type' => 'user',
                'code' => $defaultUser->referral_code
            ];
        }

        return null;
    }
}
