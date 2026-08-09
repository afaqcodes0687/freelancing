<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;

class DetectCurrencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Skip admin routes — always USD
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        // If user manually changed currency (via the USD|PKR switcher), always respect that
        if ($request->session()->has('manual_currency_selected')) {
            return $next($request);
        }

        // Check if there is a manual selection cookie from a previous session
        if ($request->hasCookie('manual_currency_selected')) {
            $cookieVal = $request->cookie('active_currency');
            if (in_array($cookieVal, ['USD', 'PKR'])) {
                $request->session()->put('active_currency', $cookieVal);
                $request->session()->put('manual_currency_selected', true);
            }
            return $next($request);
        }
        
        // Check if auto-detection was already performed in this session or cookie
        if ($request->session()->has('active_currency')) {
            return $next($request);
        }
        
        if ($request->hasCookie('active_currency')) {
            $cookieVal = $request->cookie('active_currency');
            if (in_array($cookieVal, ['USD', 'PKR'])) {
                $request->session()->put('active_currency', $cookieVal);
                return $next($request);
            }
        }

        // Auto-detect the best currency for this user/visitor
        $currency = $this->resolveCurrency($request);
        
        // Save detected currency in session and cookie (but NO manual_currency_selected flag)
        $request->session()->put('active_currency', $currency);
        Cookie::queue('active_currency', $currency, 60 * 24 * 30); // 30 days

        return $next($request);
    }

    /**
     * Resolve the correct currency using multiple strategies.
     */
    private function resolveCurrency(Request $request): string
    {
        // 1. Cloudflare country header (works on production with Cloudflare proxy)
        $cfCountry = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? null;
        if (!empty($cfCountry) && strtoupper($cfCountry) !== 'XX') {
            return strtoupper($cfCountry) === 'PK' ? 'PKR' : 'USD';
        }

        // 2. Logged-in user profile country
        if (auth('web')->check()) {
            $user = auth('web')->user();
            if ($user && $user->country_id) {
                try {
                    $country = \Modules\CountryManage\Entities\Country::find($user->country_id);
                    if ($country && strcasecmp(trim($country->country), 'Pakistan') === 0) {
                        return 'PKR';
                    }
                } catch (\Exception $e) {
                    // DB lookup failed
                }
                return 'USD';
            }
        }

        // 3. Reliable GeoIP fallback via ip-api.com (free, 45 requests/min)
        $ip = $request->header('X-Forwarded-For') ?? $request->ip();
        // If multiple IPs in X-Forwarded-For, get the first one
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        
        $localIps = ['127.0.0.1', '::1', 'localhost'];
        if ($ip && !in_array($ip, $localIps) && filter_var($ip, FILTER_VALIDATE_IP)) {
            try {
                // Using ip-api.com (very reliable for server-side IP detection)
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=countryCode");
                
                if ($response->successful()) {
                    $code = strtoupper($response->json('countryCode') ?? '');
                    if (!empty($code)) {
                        return $code === 'PK' ? 'PKR' : 'USD';
                    }
                }
                
                // Fallback to geoplugin if ip-api fails
                $data = @json_decode(
                    @file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}"),
                    true
                );
                $code = strtoupper($data['geoplugin_countryCode'] ?? '');
                if (!empty($code)) {
                    return $code === 'PK' ? 'PKR' : 'USD';
                }
            } catch (\Exception $e) {
                // GeoIP failed
            }
        }

        // 4. Default — USD
        return 'USD';
    }
}
