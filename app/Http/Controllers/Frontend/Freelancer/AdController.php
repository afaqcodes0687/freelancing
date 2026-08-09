<?php

namespace App\Http\Controllers\Frontend\Freelancer;

use App\Exceptions\WalletInsufficientBalance;
use App\Exceptions\WalletNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Order;
use App\Models\Project;
use App\Services\AdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::where('user_id', Auth::id())->latest()->get();
        return view('frontend.user.freelancer.ad.list', compact('ads'));
    }

    public function pay(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', Rule::exists('ads', 'id')->where('user_id', auth()->id())],
        ]);

        $ad = Ad::where('id', $request->id)->where('user_id', Auth::id())->firstOrFail();
        $gateway = $request->input('selected_payment_gateway', 'wallet');

        if ($gateway === 'wallet') {
            try {
                DB::beginTransaction();
                (new AdService())->payByWallet($request);
                DB::commit();
            }catch (WalletNotFoundException $exception){
                return back()->with(toastr_error('Wallet not found'));
            }catch (WalletInsufficientBalance $exception){
                return back()->with(toastr_warning('Insufficient balance'));
            }

            return redirect()->route('freelancer.ad.manage')->with(toastr_success('Payment successfull'));
        }

        if ($gateway === 'paypro') {
            $baseUrl = rtrim(get_static_option('paypro_base_url') ?? 'https://api.paypro.com.pk', '/');
            $clientId = get_static_option('paypro_client_id');
            $clientSecret = get_static_option('paypro_client_secret');
            $merchantId = get_static_option('paypro_username');

            if (empty($clientId) || empty($clientSecret) || empty($merchantId)) {
                return back()->with(toastr_error(__('Payment gateway not configured')));
            }

            try {
                $authResponse = Http::asJson()->post($baseUrl.'/v2/ppro/auth', [
                    'clientid' => $clientId,
                    'clientsecret' => $clientSecret,
                ]);
            } catch (\Exception $e) {
                return back()->with(toastr_error(__('Unable to initialize payment')));
            }

            if (!$authResponse->ok()) {
                return back()->with(toastr_error(__('Unable to initialize payment')));
            }

            $token = $authResponse->header('token') ?? $authResponse->header('Token');
            if (empty($token)) {
                return back()->with(toastr_error(__('Unable to initialize payment')));
            }

            $amount = $ad->ppq * $ad->quantity;
            $amountFormatted = number_format($amount, 2, '.', '');
            $user = Auth::user();
            $customerName = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
            if ($customerName === '') {
                $customerName = $user->email ?? 'Customer';
            }
            $orderNumber = 'AD-F-'.$ad->id.'-'.time();
            $now = Carbon::now();
            $dueDate = $now->copy()->addDays(1);
            $currency = get_static_option('site_global_currency') ?? 'USD';

            $payload = [
                [
                    'MerchantId' => $merchantId,
                ],
                [
                    'OrderNumber' => $orderNumber,
                    'CurrencyAmount' => (string) $amountFormatted,
                    'OrderDueDate' => $dueDate->format('d/m/Y'),
                    'OrderType' => 'Service',
                    'IssueDate' => $now->format('d/m/Y'),
                    'OrderExpireAfterSeconds' => '0',
                    'CustomerName' => $customerName,
                    'CustomerMobile' => '',
                    'CustomerEmail' => Auth::user()->email,
                    'CustomerAddress' => '',
                    'Currency' => $currency,
                    'IsConverted' => 'true',
                    // Do NOT include callback_url here - it causes redirect issues
                    // We append it to Click2Pay URL instead
                ],
            ];

            try {
                $orderResponse = Http::withHeaders(['token' => $token])
                    ->asJson()
                    ->post($baseUrl.'/v2/ppro/co', $payload);
            } catch (\Exception $e) {
                return back()->with(toastr_error(__('PayPro request failed: ').$e->getMessage()));
            }

            if (!$orderResponse->ok()) {
                $body = $orderResponse->json(null);
                $msg = is_array($body)
                    ? ($body[0]['Message'] ?? $body[1]['Message'] ?? $body['Message'] ?? __('Unknown PayPro error'))
                    : __('Unknown PayPro error');
                return back()->with(toastr_error(__('PayPro error (HTTP ').$orderResponse->status().'): '.$msg));
            }

            $orderData = $orderResponse->json();
            $statusCode = $orderData[0]['Status'] ?? null;
            $details = $orderData[1] ?? [];
            $click2Pay = $details['Click2Pay'] ?? null;
            $payProId = $details['PayProId'] ?? null;

            // Some environments may return non-'00' status codes while still
            // providing a valid Click2Pay URL. As long as Click2Pay exists,
            // we trust PayPro and redirect the user.
            if (empty($click2Pay)) {
                $raw = json_encode($orderData);
                $msg = $details['Message'] ?? __('Unexpected PayPro status');
                return back()->with(toastr_error(__('PayPro error (Status ').$statusCode.'): '.$msg.' | Raw: '.$raw));
            }

            $ad->update([ 'gateway_slug' => 'paypro' ]);

            session(['ad_paypro' => [
                'ad_id' => $ad->id,
                'role' => 'freelancer',
                'paypro_id' => $payProId,
                'order_number' => $orderNumber,
            ]]);

            $click2PayUrl = $click2Pay;
            $separator = str_contains($click2PayUrl, '?') ? '&' : '?';
            $click2PayUrl .= $separator . 'callback_url=' . urlencode(route('freelancer.ad.paypro.return'));

            return redirect()->away($click2PayUrl);
        }

        if ($gateway === 'payfast') {
            $amount = $ad->ppq * $ad->quantity;
            $user = Auth::user();
            $name = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
            if ($name === '') {
                $name = $user->email ?? 'Customer';
            }

            try {
                $callbackUrl = route('freelancer.ad.payfast.return');
                $cancelUrl = route('freelancer.ad.manage');

                session(['ad_payfast' => [
                    'ad_id' => $ad->id,
                    'role' => 'freelancer'
                ]]);

                $ad->update(['gateway_slug' => 'payfast']);

                $payfast = new \App\Helper\GoPayFast();
                $payfast->setMerchantId(get_static_option('payfast_merchant_id') ?: '14833');
                $payfast->setMerchantKey(get_static_option('payfast_merchant_key') ?: 'rPcy4T7GQkSCFsHBLdn26s');
                $payfast->setStoreId(get_static_option('payfast_store_id') ?? '');
                $payfast->setEnv(get_static_option('payfast_test_mode') == 'on');

                return $payfast->charge_customer([
                    'amount' => $amount,
                    'order_id' => $ad->id,
                    'email' => $user->email ?? 'test@test.com',
                    'name' => $name,
                    'description' => 'Payment for Ad #'.$ad->id,
                    'success_url' => $callbackUrl,
                    'cancel_url' => $cancelUrl
                ]);
            } catch (\Exception $e) {
                return back()->with(toastr_error($e->getMessage()));
            }
        }

        return back()->with(toastr_error(__('Invalid payment gateway')));
    }

    public function paypro_return(Request $request)
    {
        $sessionCtx = session('ad_paypro');
        $adId = $sessionCtx['ad_id'] ?? null;

        if (!$adId) {
            return redirect()->route('freelancer.ad.manage')->with(toastr_error(__('Ad not found or session expired.')));
        }

        $ad = Ad::where('id', $adId)->where('user_id', Auth::id())->first();
        if (!$ad) {
            return redirect()->route('freelancer.ad.manage')->with(toastr_error(__('Ad not found.')));
        }

        if ($ad->is_paid) {
            return redirect()->route('freelancer.ad.manage')->with(toastr_success(__('Payment already completed')));
        }

        // Verify with PayPro API
        try {
            $baseUrl = get_static_option('paypro_base_url') ?? 'https://api.PayPro.com.pk';
            $baseUrl = rtrim($baseUrl, '/');
            if (!str_starts_with($baseUrl, 'http')) { $baseUrl = 'https://' . $baseUrl; }
            
            $clientId = get_static_option('paypro_client_id');
            $clientSecret = get_static_option('paypro_client_secret');
            $username = get_static_option('paypro_username');

            // Get auth token
            $authResponse = Http::asJson()->post($baseUrl.'/v2/ppro/auth', [
                'clientid' => $clientId,
                'clientsecret' => $clientSecret,
            ]);
            if (!$authResponse->ok()) {
                throw new \Exception('Failed to authenticate with PayPro');
            }

            $token = $authResponse->header('token') ?? $authResponse->header('Token');
            
            $checkOrderNumber = $sessionCtx['order_number'] ?? null;
            if (!$checkOrderNumber) {
                throw new \Exception('Order number missing for verification');
            }

            $statusResponse = Http::withHeaders(['token' => $token])
                ->asJson()
                ->post($baseUrl.'/v2/ppro/checkorder', [[
                    'OrderNumber' => $checkOrderNumber,
                    'MerchantId' => $username
                ]]);

            $statusData = $statusResponse->json();
            \Log::info('PayPro Ad Status Check Result (Freelancer)', ['response' => $statusData]);

            $apiPaid = false;
            if (isset($statusData[0]['Status']) && $statusData[0]['Status'] === '00') {
               if ((isset($statusData[1]['OrderStatus']) && $statusData[1]['OrderStatus'] === 'Paid') ||
                   (isset($statusData[0]['OrderStatus']) && $statusData[0]['OrderStatus'] === 'Paid')) {
                   $apiPaid = true;
               }
            }

            if ($apiPaid || strtolower($request->query('status')) === 'success') {
                // Process the payment
                DB::beginTransaction();
                try {
                    (new AdService())->payByPayPro($ad);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
                
                session()->forget('ad_paypro');
                
                return redirect()->route('freelancer.ad.manage')->with(toastr_success(__('Payment successful')));

            } else {
                $orderStatus = $statusData[1]['OrderStatus'] ?? 'Processing';
                return redirect()->route('freelancer.ad.manage')
                    ->with(toastr_warning(__('Payment status: ') . $orderStatus . '. If you paid, please wait a moment.'));
            }

        } catch (\Exception $e) {
            \Log::error('PayPro Ad Return Error (Freelancer)', ['msg' => $e->getMessage()]);
            return redirect()->route('freelancer.ad.manage')
                ->with(toastr_error(__('Unable to verify payment status. Please contact support.')));
        }
    }

    // Impression count
public function trackImpression($id)
{
    $ad = Ad::findOrFail($id);
    if($ad->impressions < $ad->quantity && $ad->optimize_for == 'impression'){
        $ad->increment('impressions');
    }
    return response()->json(['success' => true]);
}

// Click count
public function trackClick($id)
{
    $ad = Ad::findOrFail($id);
    if($ad->clicks < $ad->quantity && $ad->optimize_for == 'click'){
        $ad->increment('clicks');
    }
    return redirect()->away($ad->url);
}

    public function payfast_return(Request $request)
    {
        $sessionCtx = session('ad_payfast');
        $adId = $sessionCtx['ad_id'] ?? null;

        if (!$adId) {
            return redirect()->route('freelancer.ad.manage')->with(toastr_error(__('Ad not found or session expired.')));
        }

        $ad = Ad::where('id', $adId)->where('user_id', Auth::id())->first();
        if (!$ad) {
            return redirect()->route('freelancer.ad.manage')->with(toastr_error(__('Ad not found.')));
        }

        if ($ad->is_paid) {
            return redirect()->route('freelancer.ad.manage')->with(toastr_success(__('Payment already completed')));
        }

        try {
            $payfast = new \App\Helper\GoPayFast();
            $payment_data = $payfast->ipn_response();

            if (isset($payment_data['status']) && $payment_data['status'] === 'complete') {
                DB::beginTransaction();
                try {
                    (new \App\Services\AdService())->payByPayFast($ad, $payment_data['transaction_id'] ?? '');
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
                
                // Clear session
                session()->forget('ad_payfast');
                
                return redirect()->route('freelancer.ad.manage')->with(toastr_success(__('Payment successful')));
            } else {
                return redirect()->route('freelancer.ad.manage')
                    ->with(toastr_warning(__('Payment cancelled or failed.')));
            }
        } catch (\Exception $e) {
            \Log::error('PayFast Ad Return Error (Freelancer)', ['msg' => $e->getMessage()]);
            return redirect()->route('freelancer.ad.manage')
                ->with(toastr_error(__('Unable to verify payment status. Please contact support.')));
        }
    }
}
