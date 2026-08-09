<?php

namespace App\Helper;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    /**
     * Get the active currency ('USD' or 'PKR').
     * The DetectCurrencyMiddleware sets the session on every request,
     * so this simply reads the session value.
     */
    public static function getActiveCurrency(): string
    {
        // Admin panel always USD
        if (request()->is('admin') || request()->is('admin/*')) {
            return 'USD';
        }

        // Allow API / Mobile App to explicitly request a currency via Header
        if (request()->hasHeader('X-Currency')) {
            $currency = strtoupper(request()->header('X-Currency'));
            if (in_array($currency, ['USD', 'PKR'])) {
                return $currency;
            }
        }

        return Session::get('active_currency', 'USD');
    }

    /**
     * Get the USD to PKR exchange rate from admin settings (fallback 280).
     */
    public static function getExchangeRate(): float
    {
        $rate = get_static_option('usd_to_pkr_exchange_rate');
        if (empty($rate)) {
            $rate = get_static_option('site_usd_to_pkr_exchange_rate');
        }
        return !empty($rate) && is_numeric($rate) ? (float) $rate : 280.0;
    }

    /**
     * Convert a USD amount to PKR if active currency is PKR.
     */
    public static function convert($amount): float
    {
        if (self::getActiveCurrency() === 'PKR') {
            return (float) $amount * self::getExchangeRate();
        }
        return (float) $amount;
    }

    /**
     * Get currency symbol based on active currency.
     */
    public static function getSymbol(bool $text = false): string
    {
        if (self::getActiveCurrency() === 'PKR') {
            return $text ? 'PKR' : 'Rs.';
        }
        return $text ? 'USD' : '$';
    }
}
