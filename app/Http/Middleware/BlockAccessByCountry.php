<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\GeolocationService;
use Illuminate\Support\Facades\Log;

class BlockAccessByCountry
{
    protected $geolocationService;

    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
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
        $blockedCountries = array_filter(explode(',', config('services.geolocation.blocked_countries', 'IL')));
        
        // If no countries are blocked, proceed
        if (empty($blockedCountries)) {
            return $next($request);
        }

        $ip = $request->ip();
        $countryCode = $this->geolocationService->getCountryCode($ip);

        if ($countryCode && in_array($countryCode, $blockedCountries)) {
            abort(403, 'Access denied from your country.');
        }

        return $next($request);
    }
}
