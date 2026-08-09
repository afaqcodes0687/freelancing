<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ReferralTrackingService;

class UnifiedReferralMiddleware
{
    protected $trackingService;

    public function __construct(ReferralTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Automatically track referral clicks if 'ref' parameter is present
        if ($request->has('ref')) {
            $this->trackingService->trackClick($request);
        }

        return $next($request);
    }
}
