<?php

namespace App\Helper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoPayFast
{
    protected $merchant_id;
    protected $secured_key;
    protected $passphrase;
    protected $currency;
    protected $env = true; // true = sandbox/UAT, false = live/production
    protected $exchange_rate;
    protected $store_id;

    public function __construct()
    {
        $this->currency = get_static_option('site_global_currency') ?? 'USD';
        
        // Load exchange rate
        if (class_exists(\App\Helper\CurrencyHelper::class)) {
            $this->exchange_rate = \App\Helper\CurrencyHelper::getExchangeRate();
        } else {
            $this->exchange_rate = get_static_option('site_usd_to_pkr_exchange_rate') ?? 280.0;
        }
    }

    public function setMerchantId($merchant_id)
    {
        $this->merchant_id = $merchant_id;
        return $this;
    }

    public function setMerchantKey($secured_key)
    {
        $this->secured_key = $secured_key;
        return $this;
    }

    public function setPassphrase($passphrase)
    {
        $this->passphrase = $passphrase;
        return $this;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }

    public function setExchangeRate($exchange_rate)
    {
        $this->exchange_rate = $exchange_rate;
        return $this;
    }

    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
        return $this;
    }

    protected function getTokenUrl()
    {
        return $this->env
            ? 'https://ipguat.apps.net.pk/Ecommerce/api/Transaction/GetAccessToken'
            : 'https://ipg.gopayfast.com/Ecommerce/api/Transaction/GetAccessToken';
    }

    protected function getCheckoutUrl()
    {
        return $this->env
            ? 'https://ipguat.apps.net.pk/Ecommerce/api/Transaction/PostTransaction'
            : 'https://ipg.gopayfast.com/Ecommerce/api/Transaction/PostTransaction';
    }

    public function charge_customer($args)
    {
        $amount = round($args['amount'], 2);
        // If the currency is not PKR, we can use the exchange rate if available
        if ($this->currency !== 'PKR' && $this->exchange_rate) {
            $amount = round($args['amount'] * $this->exchange_rate, 2);
        }

        $order_id = $args['order_id'];
        $email = $args['email'] ?? '';
        $name = $args['name'] ?? '';

        $token_url = $this->getTokenUrl() . sprintf(
            "?MERCHANT_ID=%s&SECURED_KEY=%s&TXNAMT=%s&BASKET_ID=%s",
            $this->merchant_id,
            $this->secured_key,
            $amount,
            $order_id
        );

        Log::info('GoPayFast: Fetching token', [
            'url' => $token_url,
            'merchant_id' => $this->merchant_id,
            'amount' => $amount,
            'order_id' => $order_id
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=utf-8'
            ])->withUserAgent('WooCommerce-WordPress PayFast Plugin')->get($token_url);

            if ($response->failed()) {
                throw new \Exception('Failed to connect to GoPayFast API. Status code: ' . $response->status());
            }

            $res_data = $response->json();

            Log::info('GoPayFast: Token response', ['data' => $res_data]);

            // Get token checking all typical variations: ACCESS_TOKEN, token, access_token
            $token = $res_data['token']
                ?? ($res_data['ACCESS_TOKEN']
                    ?? ($res_data['access_token']
                        ?? ($res_data['data']['ACCESS_TOKEN']
                            ?? ($res_data['data']['token']
                                ?? ($res_data['data']['access_token'] ?? null)))));

            if (empty($token)) {
                $msg = $res_data['message'] ?? ($res_data['data']['message'] ?? 'Unknown authentication error.');
                throw new \Exception('GoPayFast Token generation failed: ' . $msg);
            }

            // Step 2: Render a form that auto-submits via POST to the checkout page
            $checkout_url = $this->getCheckoutUrl();
            $merchant_name = $res_data['data']['NAME'] ?? (get_static_option('site_title') ?: 'RightFreelancer');
            $mobile_no = auth()->check() ? (auth()->user()->phone ?? '03001234567') : '03001234567';

            // Format signature as expected: bin2hex(random_bytes(6)) . '-' . $order_id
            $signatureRaw = sprintf(
                "%s:%s:%s:%s",
                $this->merchant_id,
                $this->secured_key,
                $amount,
                $order_id
            );

            $signature = hash('sha256', $signatureRaw);

            // Format date as expected: Y-m-d H:i:s
            $order_date = date('Y-m-d H:i:s');

            $form_fields = [
                'MERCHANT_ID' => $this->merchant_id,
                'MERCHANT_NAME' => $merchant_name,
                'TOKEN' => $token,
                'PROCCODE' => '00',
                'TRAN_TYPE' => 'ECOMM_PURCHASE',
                'STORE_ID' => $this->store_id ?? '',
                'CURRENCY_CODE' => 'PKR',
                'TXNAMT' => $amount,
                'BASKET_ID' => $order_id,
                'CUSTOMER_MOBILE_NO' => $mobile_no,
                'CUSTOMER_EMAIL_ADDRESS' => $email,
                'SIGNATURE' => $signature,
                'VERSION' => 'WOOCOM-PAYFAST-PAYMENT-1.0',
                'TXNDESC' => $args['description'] ?? 'Payfast Payment',
                'SUCCESS_URL' => $args['ipn_url'] ?? $args['success_url'] ?? route('homepage'),
                'FAILURE_URL' => $args['cancel_url'] ?? route('homepage'),
                'CHECKOUT_URL' => $args['cancel_url'] ?? route('homepage'),
                'ORDER_DATE' => $order_date,
            ];

            Log::info('GoPayFast Form Fields', $form_fields);
            Log::info('GoPayFast Store ID', [
                'store_id' => $this->store_id,
            ]);
            Log::info('GoPayFast Checkout URL', [
                'url' => $checkout_url,
            ]);

            $html = '<html><head><title>Redirecting to PayFast...</title></head><body>';
            $html .= '<form id="gopayfast_form" action="' . e($checkout_url) . '" method="POST">';
            foreach ($form_fields as $key => $val) {
                $html .= '<input type="hidden" name="' . e($key) . '" value="' . e($val) . '">';
            }
            $html .= '</form>';
            $html .= '<script type="text/javascript">document.getElementById("gopayfast_form").submit();</script>';
            $html .= '</body></html>';

            return response($html);

        } catch (\Exception $e) {
            Log::error('GoPayFast Charge Customer Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    public function ipn_response()
    {
        $request = request();

        Log::info('GoPayFast: IPN/Callback received', [
            'get_data' => $request->query(),
            'post_data' => $request->all(),
        ]);

        $order_id = $request->input('basket_id') ?? $request->query('basket_id');
        $errcode = $request->input('err_code') ?? ($request->query('err_code') ?? ($request->input('errcode') ?? $request->query('errcode')));
        $transaction_id = $request->input('transaction_id') ?? ($request->input('txnid') ?? ($request->query('transaction_id') ?? uniqid()));

        // PayFast Pakistan successful errcode is "000" or "00"
        if ($errcode === '000' || $errcode === '00') {
            return [
                'status' => 'complete',
                'order_id' => $order_id,
                'transaction_id' => $transaction_id,
            ];
        }

        return [
            'status' => 'failed',
            'order_id' => $order_id,
            'transaction_id' => $transaction_id,
        ];
    }
}
