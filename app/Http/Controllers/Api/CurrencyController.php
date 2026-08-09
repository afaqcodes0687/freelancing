<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    /**
     * Detect currency based on user IP or logged-in user profile country.
     * Flutter app calls this once on startup.
     *
     * GET /api/v1/currency/detect
     */
    public function detect(Request $request)
    {
        $currency = $this->resolveCurrency($request);

        $data = [
            'currency' => $currency,
            'symbol'   => $currency === 'PKR' ? 'Rs' : '$',
            'rate'     => $currency === 'PKR' ? $this->getPkrRate() : 1,
            'country'  => $currency === 'PKR' ? 'Pakistan' : 'International',
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Resolve the correct currency.
     */
    private function resolveCurrency(Request $request): string
    {
        // 1. Cloudflare country header (if site uses Cloudflare)
        $cfCountry = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? null;
        if (!empty($cfCountry) && strtoupper($cfCountry) !== 'XX') {
            return strtoupper($cfCountry) === 'PK' ? 'PKR' : 'USD';
        }

        // 2. Logged-in user profile country (if token provided)
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user();
            if ($user && $user->country_id) {
                try {
                    $country = \Modules\CountryManage\Entities\Country::find($user->country_id);
                    if ($country && strcasecmp(trim($country->country), 'Pakistan') === 0) {
                        return 'PKR';
                    }
                } catch (\Exception $e) {
                    // DB lookup failed, continue
                }
                return 'USD';
            }
        }

        // 3. GeoIP from IP address
        $ip = $request->header('X-Forwarded-For') ?? $request->ip();
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        $localIps = ['127.0.0.1', '::1'];
        if ($ip && !in_array($ip, $localIps) && filter_var($ip, FILTER_VALIDATE_IP)) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=countryCode");
                if ($response->successful()) {
                    $code = strtoupper($response->json('countryCode') ?? '');
                    if (!empty($code)) {
                        return $code === 'PK' ? 'PKR' : 'USD';
                    }
                }
            } catch (\Exception $e) {
                // GeoIP failed
            }
        }

        // 4. Default
        return 'USD';
    }

    /**
     * Get the PKR to USD rate from settings or a fixed rate.
     */
    private function getPkrRate(): float
    {
        try {
            // Try to read from site settings if admin has set a rate
            $rate = get_static_option('pkr_to_usd_rate');
            if ($rate && is_numeric($rate) && (float)$rate > 0) {
                return (float) $rate;
            }
        } catch (\Exception $e) {
            // Fallback
        }
        // Default fallback rate
        return 280.0;
    }
}
