<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AffiliateProgram;
use App\Models\AffiliateClick;
use Illuminate\Support\Facades\Cookie;

class TrackAffiliateClick
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ Check if referral link has ?ref=code
        if ($request->has('ref')) {
            $referralCode = $request->get('ref');

            // ✅ Find the affiliate who owns this referral code
            $affiliate = AffiliateProgram::where('referral_code', $referralCode)->first();

            if ($affiliate) {
                // ✅ Avoid recording duplicate clicks from the same IP within 10 minutes
                $recentClick = AffiliateClick::where('affiliate_id', $affiliate->id)
                    ->where('ip_address', $request->ip())
                    ->where('created_at', '>=', now()->subMinutes(10))
                    ->first();

                if (!$recentClick) {
                    $country = $request->headers->get('CF-IPCountry');
                    AffiliateClick::create([
                        'affiliate_id' => $affiliate->id,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'referer' => $request->headers->get('referer') ?? $request->fullUrl(),
                        'country' => $country,
                        'clicked_at' => now(),
                    ]);
                }

                // ✅ Store the affiliate ID in session (for registration tracking)
                session(['referral_affiliate_id' => $affiliate->id]);

                // ✅ Persist attribution for 60 days using a cookie
                // Name kept simple and scoped to the site. Minutes = 60 * 24 * 60
                $cookieMinutes = 60 * 24 * 60;
                $existingCookie = $request->cookie('ref_aid');

                if ($existingCookie != $affiliate->id) {
                    Cookie::queue(
                        Cookie::make('ref_aid', (string) $affiliate->id, $cookieMinutes, null, null, false, false)
                    );
                }
            }
        }

        return $next($request);
    }
}


