<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    /**
     * Get the country code for a given IP address.
     *
     * @param string $ip
     * @return string|null
     */
    public function getCountryCode(string $ip): ?string
    {
        // Skip local IPs
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return null;
        }

        return Cache::remember('geo_ip_' . $ip, 86400, function () use ($ip) {
            return $this->fetchCountryCodeFromApi($ip);
        });
    }

    /**
     * Fetch country code from external API.
     *
     * @param string $ip
     * @return string|null
     */
    protected function fetchCountryCodeFromApi(string $ip): ?string
    {
        $driver = config('services.geolocation.driver', 'ipregistry');
        $apiKey = config('services.geolocation.key');

        if (empty($apiKey)) {
           // Log only once per request to avoid flooding if key is missing, 
           // but for now, we just return null safely so we don't block everyone if config is missing.
           return null; 
        }

        try {
            if ($driver === 'ipregistry') {
                $response = Http::get("https://api.ipregistry.co/{$ip}", [
                    'key' => $apiKey,
                    'fields' => 'location.country.code',
                ]);

                if ($response->successful()) {
                    return $response->json('location.country.code');
                }
            } elseif ($driver === 'ipbase') {
                $response = Http::get("https://api.ipbase.com/v2/info", [
                    'apikey' => $apiKey,
                    'ip' => $ip,
                ]);

                if ($response->successful()) {
                    return $response->json('data.location.country.alpha2');
                }
            }
        } catch (\Exception $e) {
            Log::error("Geolocation API error: " . $e->getMessage());
        }

        return null;
    }
}
