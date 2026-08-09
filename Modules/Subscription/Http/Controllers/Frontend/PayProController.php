<?php

namespace Modules\Subscription\Http\Controllers\Frontend;

use App\Mail\BasicMail;
use App\Models\AdminNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Subscription\Entities\UserSubscription;

class PayProController extends Controller
{
    private function getPayProSettings()
    {
        $baseUrl = get_static_option('paypro_base_url') ?? 'https://api.PayPro.com.pk';
        $baseUrl = rtrim($baseUrl, '/');
        if (!str_starts_with($baseUrl, 'http')) {
            $baseUrl = 'https://' . $baseUrl;
        }

        return [
            'baseUrl' => $baseUrl,
            'clientId' => get_static_option('paypro_client_id'),
            'clientSecret' => get_static_option('paypro_client_secret'),
            'merchantId' => get_static_option('paypro_username'),
            'merchantPassword' => get_static_option('paypro_password'),
        ];
    }

    /**
     * Handle user return from PayPro browser redirect
     */
    public function returnUrl(Request $request)
    {
        $orderId = $request->query('order') ?? $request->query('ordId');
        $status = $request->query('status');

        Log::info('PayPro Return URL accessed', [
            'order_id' => $orderId,
            'status' => $status,
            'params' => $request->all()
        ]);

        if (!$orderId) {
            return redirect()->route('homepage')->with(['msg' => __('Invalid request.'), 'type' => 'danger']);
        }

        return redirect()->route('subscriptions.paypro.status', ['order_id' => $orderId]);
    }

    /**
     * Display payment status to the user
     */
    public function status($orderId)
    {
        $subscription = UserSubscription::where('transaction_id', $orderId)
            ->orWhere('id', str_replace('SUB-', '', explode('-', $orderId)[1] ?? ''))
            ->first();

        if (!$subscription) {
            return redirect()->route('homepage')->with(['msg' => __('Order not found.'), 'type' => 'danger']);
        }

        return view('subscription::frontend.subscriptions.paypro-status', compact('subscription', 'orderId'));
    }


    // public function webhook(Request $request)
    // {
    //     Log::info('PayPro Webhook received', ['payload' => $request->all()]);

    //     $orderNumber =
    //     $request->input('OrderNumber')
    //     ?? $request->input('order_number')
    //     ?? $request->input('OrderId')
    //     ?? $request->input('ordId');


    //     if (!$orderNumber) {
    //         Log::warning('PayPro Webhook: No order number found');
    //         return response()->json(['status' => 'error', 'message' => 'No order number'], 400);
    //     }

    //     try {
    //         $status = $this->verifyPayment($orderNumber);

    //         if ($status === 'Paid') {
    //             $this->processSuccess($orderNumber);
    //             return response()->json(['status' => 'success']);
    //         }

    //         return response()->json(['status' => 'pending']);
    //     } catch (\Exception $e) {
    //         Log::error('PayPro Webhook error: ' . $e->getMessage());
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    //     }
    // }


    public function webhook(Request $request)
    {
        Log::info('PayPro Webhook Received', [
            'payload' => $request->all()
        ]);

        // 🔐 AUTH FROM BODY 
        $settings = $this->getPayProSettings();
        $username = $request->input('username');
        $password = $request->input('password');

        // Check against admin settings or fallback to test if not set (careful with security here)
        $expectedUser = $settings['merchantId'] ?? 'test_user';
        $expectedPass = $settings['merchantPassword'] ?? 'test_pass_123';

        if ($username !== $expectedUser || $password !== $expectedPass) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'Invalid username or password'
            ], 401);
        }

        // 🆔 ORDER NUMBER
        $orderNumber =
            $request->input('OrderNumber')
            ?? $request->input('order_number')
            ?? $request->input('OrderId')
            ?? $request->input('ordId');

        if (!$orderNumber) {
            return response()->json([
                'status' => 'error',
                'message' => 'OrderNumber missing'
            ], 400);
        }

        // 🟢 DEMO PAYMENT SUCCESS
        if ($request->input('OrderStatus') === 'Paid') {
            $this->processSuccess($orderNumber);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment marked as paid (demo test)'
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'message' => 'Payment not completed'
        ]);
    }


    /**
     * Verify payment status using PayPro API
     */
    private function verifyPayment($orderNumber)
    {
        $settings = $this->getPayProSettings();

        // 1. Get Auth Token using /v2/ppro/auth
        $authResponse = Http::asJson()->post($settings['baseUrl'] . '/v2/ppro/auth', [
            'clientid' => $settings['clientId'],
            'clientsecret' => $settings['clientSecret'],
        ]);

        if (!$authResponse->ok()) {
            throw new \Exception('Failed to authenticate with PayPro');
        }

        $token = $authResponse->header('token') ?? $authResponse->header('Token') ?? $authResponse->json('token');
        if (empty($token)) {
            throw new \Exception('No auth token received from PayPro');
        }

        // 2. Check Order Status
        $statusResponse = Http::withHeaders(['token' => $token])
            ->asJson()
            ->post($settings['baseUrl'] . '/v2/ppro/checkorder', [
                [
                    'OrderNumber' => $orderNumber,
                    'MerchantId' => $settings['merchantId']
                ]
            ]);

        if (!$statusResponse->ok()) {
            throw new \Exception('Failed to check order status');
        }

        $statusData = $statusResponse->json();

        if (
            isset($statusData[0]['Status']) &&
            $statusData[0]['Status'] === '00' &&
            (
                ($statusData[1]['OrderStatus'] ?? null) === 'Paid'
                || ($statusData[0]['OrderStatus'] ?? null) === 'Paid'
            )
        ) {
            return 'Paid';
        }


        return 'Unpaid';
    }

    private function processSuccess($orderNumber)
    {
        $parts = explode('-', $orderNumber);
        $subId = $parts[1] ?? null;

        $subscription = UserSubscription::where('id', $subId)->orWhere('transaction_id', $orderNumber)->first();

        if ($subscription && $subscription->payment_status !== 'complete') {
            $subscription->update([
                'payment_status' => 'complete',
                'status' => 1,
                'updated_at' => now()
            ]);

            UserSubscription::where('user_id', $subscription->user_id)
                ->where('id', '!=', $subscription->id)
                ->update(['status' => 0]);

            $this->sendNotifications($subscription);

            // ✅ Create affiliate commission for PayPro subscription purchase
            try {
                app(\App\Services\AffiliateCommissionService::class)->createGeneric(
                    (int) $subscription->user_id,
                    (float) $subscription->price,
                    "Commission from subscription purchase #{$subscription->id} (PayPro)"
                );
            } catch (\Exception $e) {
                \Log::error("Affiliate PayPro Subscription Commission Error: " . $e->getMessage());
            }

            Log::info('PayPro: Order marked as PAID', ['order_id' => $orderNumber]);
        }
    }

    private function sendNotifications($subscription)
    {
        $user = $subscription->user;
        if (!$user)
            return;

        AdminNotification::create([
            'identity' => $subscription->id,
            'user_id' => $user->id,
            'type' => __('PayPro Payment'),
            'message' => __('User @name paid for subscription via PayPro', ['name' => $user->name]),
        ]);

        try {
            Mail::to($user->email)->send(new BasicMail([
                'subject' => __('Payment Successful'),
                'message' => __('Your subscription purchase was successful. Order ID: #@id', ['id' => $subscription->id])
            ]));
        } catch (\Exception $e) {
            Log::error('PayPro Email Error: ' . $e->getMessage());
        }
    }
}
