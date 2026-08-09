<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ReferralService;
use Illuminate\Support\Facades\Log;

class ProcessReferral
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only process referral if user was successfully created
        if ($request->has('ref') && $request->has('user_id')) {
            try {
                $referralCode = $request->get('ref');
                $userId = $request->get('user_id');

                // Process the referral
                $this->referralService->processReferral($referralCode, $userId);

                Log::info("Referral processed successfully: User {$userId} referred by code {$referralCode}");

            } catch (\Exception $e) {
                Log::error("Referral processing failed: " . $e->getMessage());
                // Don't fail the registration, just log the error
            }
        }

        return $response;
    }
} 