<?php

namespace Modules\Subscription\Http\Controllers\Frontend;

use App\Helper\PaymentGatewayRequestHelper;
use App\Mail\BasicMail;
use App\Models\AdminNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Modules\Subscription\Entities\Subscription;
use Modules\Subscription\Entities\UserSubscription;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;

class BuySubscriptionController extends Controller
{
    private const CANCEL_ROUTE = 'subscriptions.buy.payment.cancel.static';

    public function subscription_payment_cancel_static()
    {
        return view('subscription::frontend.subscriptions.cancel');
    }

    /**
     * Handle PayPro return URL after payment
     */
    public function paypro_return(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('user.login');
        }

        $user = Auth::user();
        $user_type = $user->user_type == 1 ? 'client' : 'freelancer';

        // Retrieve session context if available
        $sessionCtx = session('sub_paypro');

        // Get payment type
        $paymentType = $request->query('payment_type') ??
            session('payment_type') ??
            ($sessionCtx['payment_type'] ?? 'membership');

        // Set redirect route based on payment type
        $redirectRoute = match ($paymentType) {
            'one_dollar_game' => 'frontend.onedollargame.index',
            'promotional_profile' => $user_type . '.profile.edit',
            'promotional_project', 'featured_job' => $user_type . '.job.manage',
            'ad_payment' => $user_type . '.ad.manage',
            default => $user_type . '.subscriptions.all'
        };

        // Get context from Request or Session
        // PayPro might append ordId (their ID) or status
        $reqOrderId = $request->query('order') ?? $request->query('ordId');

        // Prefer "OrderNumber" (our SUB-...) from session for API Verification
        $orderId = $sessionCtx['order_number'] ?? $reqOrderId;

        // Subscription ID lookup
        $subscriptionId = $request->query('subscription_id') ?? ($sessionCtx['subscription_id'] ?? null);

        // Status from PayPro callback
        $status = $request->query('status') ?? $request->query('Status');

        // Message from PayPro callback
        $msg = $request->query('msg') ?? $request->query('Message');

        \Log::info('PayPro Return Params', [
            'request_all' => $request->all(),
            'session_ctx' => $sessionCtx,
            'determined_order_id' => $orderId,
            'determined_sub_id' => $subscriptionId,
            'status' => $status,
            'msg' => $msg
        ]);

        $subscription = null;

        if ($subscriptionId) {
            $subscription = UserSubscription::find($subscriptionId);
        } elseif ($orderId) {
            // Try to find by transaction_id (which might be SUB-... or PayProID)
            $subscription = UserSubscription::where('transaction_id', $orderId)->first();
            // Or try to parse SUB-ID-TIMESTAMP
            if (!$subscription && str_starts_with($orderId, 'SUB-')) {
                $parts = explode('-', $orderId);
                if (isset($parts[1])) {
                    $subscription = UserSubscription::find($parts[1]);
                }
            }
        }

        if (!$subscription) {
            return redirect()->route($redirectRoute)
                ->with(['msg' => __('Subscription not found or session expired.'), 'type' => 'error']);
        }

        if ($subscription->payment_status === 'complete') {
            $successMessage = $this->getSuccessMessage($paymentType);
            return redirect()->route($redirectRoute)
                ->with(['msg' => $successMessage, 'type' => 'success']);
        }

        // Verify with PayPro API
        try {
            $baseUrl = get_static_option('paypro_base_url') ?? 'https://api.PayPro.com.pk';
            $baseUrl = rtrim($baseUrl, '/');
            if (!str_starts_with($baseUrl, 'http')) {
                $baseUrl = 'https://' . $baseUrl;
            }

            $clientId = get_static_option('paypro_client_id');
            $clientSecret = get_static_option('paypro_client_secret');
            $username = get_static_option('paypro_username');

            // Get auth token
            $authResponse = Http::asJson()->post($baseUrl . '/v2/ppro/auth', [
                'clientid' => $clientId,
                'clientsecret' => $clientSecret,
            ]);
            if (!$authResponse->ok()) {
                throw new \Exception('Failed to authenticate with PayPro');
            }

            $token = $authResponse->header('token') ?? $authResponse->header('Token');

            // Check order status
            // IMPORTANT: "OrderNumber" parameter for checkorder MUST be the one we sent (SUB-...)
            // If $orderId is not starting with SUB, use session one if available
            $checkOrderNumber = $orderId;
            if (!str_starts_with($checkOrderNumber, 'SUB-') && !empty($sessionCtx['order_number'])) {
                $checkOrderNumber = $sessionCtx['order_number'];
            }

            $statusResponse = Http::withHeaders(['token' => $token])
                ->asJson()
                ->post($baseUrl . '/v2/ppro/checkorder', [
                    [
                        'OrderNumber' => $checkOrderNumber,
                        'MerchantId' => $username
                    ]
                ]);

            $statusData = $statusResponse->json();
            \Log::info('PayPro Status Check Result', ['response' => $statusData]);

            // Logic to determine success:
            // 1. If callback says "Success" (trusted if we want, but verifying API is safer)
            // 2. API says "Paid"
            $apiPaid = false;
            if (isset($statusData[0]['Status']) && $statusData[0]['Status'] === '00') {
                // Check various places status might appear based on array structure
                if (
                    (isset($statusData[1]['OrderStatus']) && $statusData[1]['OrderStatus'] === 'Paid') ||
                    (isset($statusData[0]['OrderStatus']) && $statusData[0]['OrderStatus'] === 'Paid')
                ) {
                    $apiPaid = true;
                }
            }

            if ($apiPaid || strtolower($status) === 'success') {
                // SUCCESS
                $subscription->update([
                    'payment_status' => 'complete',
                    'status' => 1,
                    'transaction_id' => $request->query('ordId') ?? $orderId,
                    'updated_at' => now()
                ]);

                // Cleanup
                if (function_exists('cache')) {
                    cache()->forget('user_subscription_' . $subscription->user_id);
                }

                if ($paymentType === 'membership') {
                    UserSubscription::where('user_id', $subscription->user_id)
                        ->where('id', '!=', $subscription->id)
                        ->where('status', 1)
                        ->update(['status' => 0]);
                }

                $this->adminNotification($subscription->id, $subscription->user_id);
                $this->sendEmail(
                    $subscription->user->name ?? 'User',
                    $subscription->id,
                    $subscription->user->email ?? ''
                );

                // ✅ Create affiliate commission for subscription purchase
                try {
                    app(\App\Services\AffiliateCommissionService::class)->createGeneric(
                        (int) $subscription->user_id,
                        (float) $subscription->price,
                        "Commission from PayPro subscription purchase #{$subscription->id}"
                    );
                } catch (\Exception $e) {
                    \Log::error("Affiliate Subscription Commission Error (PayPro Return): " . $e->getMessage());
                }

                session()->forget('sub_paypro');
                session()->forget('paypro_payment_' . ($checkOrderNumber ?? ''));

                $successMessage = $this->getSuccessMessage($paymentType);
                return redirect()->route($redirectRoute)
                    ->with(['msg' => $successMessage, 'type' => 'success']);

            } else {
                // PENDING / FAILED
                $orderStatus = $statusData[1]['OrderStatus'] ?? 'Processing';
                return redirect()->route($redirectRoute)
                    ->with([
                        'msg' => __('Payment status: ') . $orderStatus . '. If you paid, please wait a moment.',
                        'type' => 'info'
                    ]);
            }

        } catch (\Exception $e) {
            \Log::error('PayPro Return Error', ['msg' => $e->getMessage()]);
            return redirect()->route($redirectRoute)
                ->with(['msg' => __('Unable to verify payment status. Please contact support.'), 'type' => 'warning']);
        }
    }

    /**
     * Handle PayPro webhook for automatic payment notifications
     */
    public function handlePayProWebhook(Request $request)
    {
        \Log::info('PayPro Webhook Received', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            // PayPro sends webhook data - extract order details
            $orderNumber = $request->input('OrderNumber') ?? $request->input('order_number');
            $status = $request->input('Status') ?? $request->input('status');
            $orderStatus = $request->input('OrderStatus') ?? $request->input('order_status');

            if (!$orderNumber) {
                \Log::error('PayPro Webhook: No OrderNumber provided');
                return response()->json(['status' => 'error', 'message' => 'No OrderNumber'], 400);
            }

            // Find subscription by order number
            $subscription = null;
            if (str_starts_with($orderNumber, 'SUB-')) {
                $parts = explode('-', $orderNumber);
                if (isset($parts[1])) {
                    $subscription = UserSubscription::find($parts[1]);
                }
            }

            if (!$subscription) {
                $subscription = UserSubscription::where('transaction_id', $orderNumber)->first();
            }

            if (!$subscription) {
                \Log::error('PayPro Webhook: Subscription not found', ['order_number' => $orderNumber]);
                return response()->json(['status' => 'error', 'message' => 'Subscription not found'], 404);
            }

            // Check if already completed
            if ($subscription->payment_status === 'complete') {
                \Log::info('PayPro Webhook: Payment already completed', ['subscription_id' => $subscription->id]);
                return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
            }

            // Verify payment status from webhook
            // Status '00' means success, OrderStatus 'Paid' confirms payment
            if ($status === '00' && $orderStatus === 'Paid') {
                $subscription->update([
                    'payment_status' => 'complete',
                    'status' => 1,
                    'updated_at' => now()
                ]);

                // Cleanup cache
                if (function_exists('cache')) {
                    cache()->forget('user_subscription_' . $subscription->user_id);
                }

                // Deactivate other subscriptions for membership type
                $paymentType = session('payment_type', 'membership');
                if ($paymentType === 'membership') {
                    UserSubscription::where('user_id', $subscription->user_id)
                        ->where('id', '!=', $subscription->id)
                        ->where('status', 1)
                        ->update(['status' => 0]);
                }

                $this->adminNotification($subscription->id, $subscription->user_id);
                $this->sendEmail(
                    $subscription->user->name ?? 'User',
                    $subscription->id,
                    $subscription->user->email ?? ''
                );

                // ✅ Create affiliate commission for subscription purchase
                try {
                    app(\App\Services\AffiliateCommissionService::class)->createGeneric(
                        (int) $subscription->user_id,
                        (float) $subscription->price,
                        "Commission from PayPro subscription purchase #{$subscription->id} (Webhook)"
                    );
                } catch (\Exception $e) {
                    \Log::error("Affiliate Subscription Commission Error (PayPro Webhook): " . $e->getMessage());
                }

                \Log::info('PayPro Webhook: Payment processed successfully', ['subscription_id' => $subscription->id]);
                return response()->json(['status' => 'success', 'message' => 'Payment processed'], 200);
            }

            \Log::info('PayPro Webhook: Payment not yet completed', [
                'status' => $status,
                'order_status' => $orderStatus
            ]);
            return response()->json(['status' => 'pending', 'message' => 'Payment pending'], 200);

        } catch (\Exception $e) {
            \Log::error('PayPro Webhook Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get success message based on payment type
     */
    private function getSuccessMessage($paymentType)
    {
        return match ($paymentType) {
            'one_dollar_game' => __('Payment successful! Your game entry has been confirmed.'),
            'promotional_profile' => __('Payment successful! Your profile is now promoted.'),
            'promotional_project' => __('Payment successful! Your project is now promoted.'),
            'featured_job' => __('Payment successful! Your job is now featured.'),
            'ad_payment' => __('Payment successful! Your ad has been published.'),
            default => __('Payment successful! Your subscription has been activated.')
        };
    }

    //buy subscription
    public function buy_subscription(Request $request)
    {
        if (isset($request->subscription_id)) {
            $user = Auth::user();

            $pending_subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 2)->where('expire_date', '>', now())->first();

            $subscription_details = Subscription::with('subscription_type:id,validity')->select(['id', 'subscription_type_id', 'price', 'limit'])
                ->where('id', $pending_subscription ? $pending_subscription->subscription_id : $request->subscription_id)
                ->where('status', '1')->first();

            if (!$subscription_details) {
                return back()->with(['msg' => __('Invalid subscription details'), 'type' => 'warning']);
            }

            $expire_date = Carbon::now()->addDays($subscription_details?->subscription_type?->validity);
            $title = __('Buy Subscription');
            // tax calculation
            $base_price = (float) $subscription_details->price;
            $tax_rate = (float) (get_static_option('subscription_tax_rate') ?? env('SUBSCRIPTION_TAX_RATE', 0));
            $tax_amount = round(($base_price * $tax_rate) / 100, 2);
            
            // For wallet payments, no tax applies - only base price
            if ($request->selected_payment_gateway === 'wallet') {
                $tax_amount = 0;
                $total = $base_price;
            } else {
                $total = $base_price + $tax_amount;
            }
            
            $limit = $subscription_details->limit;
            $name = $user->first_name . ' ' . $user->last_name;
            $email = $user->email;
            $user_type = $user->user_type == 1 ? 'client' : 'freelancer';
            $payment_status = $request->selected_payment_gateway === 'wallet' ? 'complete' : 'pending';
            $status = $request->selected_payment_gateway === 'wallet' ? 1 : 0;
            session()->put('user_id', $user->id);
            session()->put('user_type', $user_type);


            if ($request->selected_payment_gateway === 'manual_payment') {
                $request->validate([
                    'manual_payment_image' => 'required|mimes:jpg,jpeg,png,pdf',
                    'manual_payment_method' => 'required|string|in:bank,jazzcash,easypaisa',
                    'manual_account_name' => 'required|string',
                    'manual_account_number' => 'required|string',
                    'manual_transaction_id' => 'required|string',
                    'manual_note' => 'nullable|string|max:500',
                ]);

                $stored_manual_image_path = null;
                if ($request->hasFile('manual_payment_image')) {
                    $manual_payment_image = $request->file('manual_payment_image');
                    $img_ext = $manual_payment_image->extension();
                    $manual_payment_image_name = 'manual_attachment_' . time() . '.' . $img_ext;
                    if (in_array($img_ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
                        $manual_image_path = 'assets/uploads/manual-payment/subscription';
                        $manual_payment_image->move($manual_image_path, $manual_payment_image_name);
                        $stored_manual_image_path = $manual_image_path . '/' . $manual_payment_image_name;
                    } else {
                        return back()->with(['msg' => __('Image type not supported'), 'type' => 'warning']);
                    }
                }

                // Build a readable transaction details string for admin verification
                $method = $request->manual_payment_method;
                $accName = $request->manual_account_name;
                $accNo = $request->manual_account_number;
                $ref = $request->manual_transaction_id;
                $note = $request->manual_note;
                $txn_full = $ref . ' [' . $method . ' | ' . $accName . ' | ' . $accNo . (empty($note) ? '' : ' | ' . $note) . ']';

                if ($pending_subscription) {
                    $pending_subscription->update([
                        'price' => $total,
                        'limit' => $limit,
                        'expire_date' => $expire_date,
                        'payment_gateway' => $request->selected_payment_gateway,
                        'manual_payment_image' => $stored_manual_image_path,
                        'transaction_id' => $txn_full,
                        'payment_status' => $payment_status,
                        'status' => $status,
                    ]);
                    $subscription_id = $pending_subscription->id;
                } else {

                    $buy_subscription = UserSubscription::create([
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_details->id,
                        'price' => $total,
                        'limit' => $limit,
                        'expire_date' => $expire_date,
                        'payment_gateway' => $request->selected_payment_gateway,
                        'manual_payment_image' => $stored_manual_image_path,
                        'transaction_id' => $txn_full,
                        'payment_status' => $payment_status,
                        'status' => $status,
                    ]);
                    $subscription_id = $buy_subscription->id;
                }

                $this->adminNotification($subscription_id, $user->id);
                $this->sendEmail($name, $subscription_id, $email);
                toastr_success(__('Subscription purchase success. Your subscription will be usable after admin approval'));
                return redirect()->route($user_type . '.' . 'subscriptions.all');
            } elseif ($request->selected_payment_gateway === 'wallet') {
                // Use relationship if exists, otherwise fetch directly
                $wallet = $user->user_wallet ?? Wallet::where('user_id', $user->id)->first();
                $bonus = (float)($wallet->signup_bonus ?? 0);
                
                // Restriction: Signup bonus only usable for $10 package (Weekly Starter)
                $is_bonus_eligible = ($subscription_details->price == 10);
                $available_balance = $wallet->balance;
                if (!$is_bonus_eligible) {
                    $available_balance -= $bonus;
                }

                if (!$wallet || $available_balance < $total) {
                    $error_msg = $bonus > 0 && !$is_bonus_eligible 
                        ? __('Insufficient balance. Note: Signup bonus is only usable for the Weekly Starter package.')
                        : __('Insufficient wallet balance.');
                    return back()->withErrors(['wallet' => $error_msg]);
                }

                // Deduct from wallet
                $wallet->balance -= $total;
                if (isset($wallet->remaining_balance)) {
                    $wallet->remaining_balance -= $total;
                }
                
                // Deduct from signup_bonus if it was used
                if ($is_bonus_eligible && $bonus > 0) {
                    $bonus_to_deduct = min($bonus, $total);
                    $wallet->signup_bonus -= $bonus_to_deduct;
                }
                
                $wallet->save();

                // Assign subscription to user
                if ($pending_subscription) {
                    $pending_subscription->update([
                        'price' => $total,
                        'limit' => $limit,
                        'expire_date' => $expire_date,
                        'payment_gateway' => $request->selected_payment_gateway,
                        'payment_status' => $payment_status,
                        'status' => $status,
                    ]);
                    $subscription_id = $pending_subscription->id;
                } else {
                    $buy_subscription = UserSubscription::create([
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_details->id,
                        'price' => $total,
                        'limit' => $limit,
                        'expire_date' => $expire_date,
                        'payment_gateway' => $request->selected_payment_gateway,
                        'payment_status' => $payment_status,
                        'status' => $status,
                    ]);
                    $subscription_id = $buy_subscription->id;
                }

                $this->adminNotification($subscription_id, $user->id);
                $this->sendEmail($name, $subscription_id, $email);

                toastr_success(__('Package purchased successfully using wallet.'));
                // ensure only one active subscription after success
                UserSubscription::where('user_id', $user->id)
                    ->where('id', '!=', $subscription_id)
                    ->where('status', 1)
                    ->update(['status' => 0]);

                // Clear subscription cache for the user
                if (function_exists('cache')) {
                    cache()->forget('user_subscription_' . $user->id);
                }

                // log wallet history for wallet payment
                try {
                    WalletHistory::create([
                        'user_id' => $user->id,
                        'payment_gateway' => 'wallet',
                        'payment_status' => 'complete',
                        'amount' => $total,
                        'transaction_id' => 'wallet-' . $subscription_id . '-' . time(),
                        'status' => 1,
                    ]);
                } catch (\Exception $e) {
                }
                return redirect()->route($user_type . '.' . 'subscriptions.all');
            } else {

                if ($pending_subscription) {
                    $pending_subscription->update([
                        'price' => $total,
                        'limit' => $limit,
                        'expire_date' => $expire_date,
                        'payment_gateway' => $request->selected_payment_gateway,
                    ]);
                    $subscription_id = $pending_subscription->id;
                } else {

                    $buy_subscription = UserSubscription::create([
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_details->id,
                        'price' => $total,
                        'limit' => $limit,
                        'expire_date' => $expire_date,
                        'payment_gateway' => $request->selected_payment_gateway,
                    ]);
                    $subscription_id = $buy_subscription->id;
                }

                $description = sprintf(
                    __('Order id #%1$d Email: %2$s, Name: %3$s | Subtotal: %4$s | Tax(%5$s%%): %6$s | Total: %7$s'),
                    $subscription_id,
                    $email,
                    $name,
                    float_amount_with_currency_symbol($base_price),
                    rtrim(rtrim(number_format($tax_rate, 2, '.', ''), '0'), '.'),
                    float_amount_with_currency_symbol($tax_amount),
                    float_amount_with_currency_symbol($total)
                );

                if ($request->selected_payment_gateway === 'paypal') {
                    try {
                        return PaymentGatewayRequestHelper::paypal()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.paypal.ipn.subscription')));
                    } catch (\Exception $e) {
                        return redirect()->route(self::CANCEL_ROUTE);
                    }
                } elseif ($request->selected_payment_gateway === 'paytm') {
                    try {
                        return PaymentGatewayRequestHelper::paytm()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.paytm.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'mollie') {
                    try {
                        return PaymentGatewayRequestHelper::mollie()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.mollie.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'stripe') {
                    try {
                        return PaymentGatewayRequestHelper::stripe()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.stripe.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                }



                /* 
                 * Removed incorrect PayPro local redirect block to allow fall-through to the correct API implementation below.
                 */ elseif ($request->selected_payment_gateway === 'razorpay') {
                    try {
                        return PaymentGatewayRequestHelper::razorpay()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.razorpay.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'flutterwave') {
                    try {
                        return PaymentGatewayRequestHelper::flutterwave()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.flutterwave.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'paystack') {
                    try {
                        return PaymentGatewayRequestHelper::paystack()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('paystack.ipn.all'), 'subscription'));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'payfast') {
                    try {
                        $ipn_url = get_static_option('payfast_itn_url') ?: route('bs.payfast.ipn.subscription');
                        return PaymentGatewayRequestHelper::payfast()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, $ipn_url));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'cashfree') {
                    try {
                        return PaymentGatewayRequestHelper::cashfree()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.cashfree.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'instamojo') {
                    try {
                        return PaymentGatewayRequestHelper::instamojo()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.instamojo.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'marcadopago') {
                    try {
                        return PaymentGatewayRequestHelper::marcadopago()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.marcadopago.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }

                } elseif ($request->selected_payment_gateway === 'midtrans') {
                    try {
                        return PaymentGatewayRequestHelper::midtrans()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.midtrans.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'squareup') {
                    try {
                        return PaymentGatewayRequestHelper::squareup()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.squareup.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'cinetpay') {
                    try {
                        return PaymentGatewayRequestHelper::cinetpay()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.cinetpay.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'paytabs') {

                    try {
                        return PaymentGatewayRequestHelper::paytabs()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.paytabs.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'billplz') {
                    try {
                        return PaymentGatewayRequestHelper::billplz()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.billplz.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'zitopay') {
                    try {
                        return PaymentGatewayRequestHelper::zitopay()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.zitopay.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'toyyibpay') {
                    try {
                        return PaymentGatewayRequestHelper::toyyibpay()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.toyyibpay.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'paypro') {
                    // dd("here");
                    // PayPro integration: Auth -> Create Order -> Redirect to Click2Pay
                    $baseUrl = get_static_option('paypro_base_url') ?? 'https://api.PayPro.com.pk';
                    $baseUrl = rtrim($baseUrl, '/');
                    if (!str_starts_with($baseUrl, 'http')) {
                        $baseUrl = 'https://' . $baseUrl;
                    }

                    $clientId = get_static_option('paypro_client_id');
                    $clientSecret = get_static_option('paypro_client_secret');
                    $merchantId = get_static_option('paypro_username');

                    if (empty($clientId) || empty($clientSecret) || empty($merchantId)) {
                        \Log::error('PayPro: Missing required credentials');
                        return redirect()->route(self::CANCEL_ROUTE);
                    }

                    try {
                        // Get auth token
                        $authResponse = Http::asJson()->post($baseUrl . '/v2/ppro/auth', [
                            'clientid' => $clientId,
                            'clientsecret' => $clientSecret,
                        ]);

                        if (!$authResponse->ok()) {
                            throw new \Exception('Failed to authenticate with PayPro');
                        }

                        $token = $authResponse->header('token') ?? $authResponse->header('Token');
                        if (empty($token)) {
                            throw new \Exception('No auth token received from PayPro');
                        }

                        // Create order
                        $orderNumber = 'SUB-' . $subscription_id . '-' . time();
                        $currency = get_static_option('site_global_currency') ?? 'USD';
                        $now = Carbon::now();
                        $dueDate = $now->copy()->addDays(1);

                        // Build the return URL with all necessary parameters
                        $paymentType = session('payment_type', 'membership');
                        $returnParams = [
                            'order' => $orderNumber,
                            'payment_type' => $paymentType,
                            'user_id' => $user->id,
                            'subscription_id' => $subscription_id,
                            'amount' => $total,
                            'currency' => $currency,
                            'timestamp' => time()
                        ];
                        // dd($returnParams);


                        // Generate a CLEAN return URL without parameters to avoid gateway issues
                        // We will rely on Session to retrieve order context
                        $returnUrl = route('subscriptions.paypro.return');

                        // Log the generated return URL for debugging
                        \Log::info('Generated PayPro Return URL', [
                            'callback_url' => $returnUrl,
                            'payment_type' => $paymentType,
                            'order_number' => $orderNumber,
                            'user_id' => $user->id
                        ]);

                        $webhookUrl = route('subscriptions.paypro.webhook');

                        $payload = [
                            [
                                'MerchantId' => $merchantId,
                            ],
                            [
                                'OrderNumber' => $orderNumber,
                                'CurrencyAmount' => (string) $total,
                                'OrderDueDate' => $dueDate->format('d/m/Y'),
                                'OrderType' => 'Service',
                                'IssueDate' => $now->format('d/m/Y'),
                                'OrderExpireAfterSeconds' => '86400', // 24 hours
                                'CustomerName' => trim($name),
                                'CustomerMobile' => '', // Using empty string as it's more reliable for international numbers with PayPro
                                'CustomerEmail' => $email,
                                'CustomerAddress' => !empty($user->address) ? trim($user->address) : 'N/A',
                                'Currency' => $currency,
                                'IsConverted' => 'true',
                                // Do NOT include callback_url here - it causes redirect issues
                                // We append it to Click2Pay URL instead
                                'WebhookURL' => $webhookUrl,
                            ],
                        ];

                        // Store payment data in session for verification
                        session([
                            'paypro_payment_' . $orderNumber => [
                                'subscription_id' => $subscription_id,
                                'user_id' => $user->id,
                                'payment_type' => session('payment_type', 'membership'),
                                'amount' => $total,
                                'currency' => $currency,
                                'callback_url' => $returnUrl
                            ]
                        ]);

                        \Log::info('Sending order to PayPro', ['payload' => $payload]);

                        $orderResponse = Http::withHeaders(['token' => $token])
                            ->asJson()
                            ->post($baseUrl . '/v2/ppro/co', $payload);

                        if (!$orderResponse->ok()) {
                            throw new \Exception('Failed to create order: ' . $orderResponse->body());
                        }

                        $orderData = $orderResponse->json();
                        \Log::info('PayPro Order Response', $orderData);

                        $status = $orderData[0]['Status'] ?? null;

                        if ($status !== '00') {
                            $errorDesc = $orderData[0]['Description'] ?? __('Unknown error');
                            \Log::error('PayPro Order Creation Failed', [
                                'status' => $status,
                                'description' => $errorDesc,
                                'order_number' => $orderNumber
                            ]);
                            return back()->with(['msg' => __('PayPro Error: ') . $errorDesc, 'type' => 'danger']);
                        }

                        $details = $orderData[1] ?? [];
                        $click2Pay = $details['Click2Pay'] ?? null;
                        $payProId = $details['PayProId'] ?? null;

                        if (empty($click2Pay)) {
                            \Log::error('PayPro: Missing Click2Pay URL', $orderData);
                            return back()->with(['msg' => __('PayPro Error: Missing payment link.'), 'type' => 'danger']);
                        }

                        // Store PayPro reference in transaction_id but keep subscription pending until verified
                        $txId = $payProId ?: $orderNumber;

                        UserSubscription::where('id', $subscription_id)->update([
                            'transaction_id' => $txId,
                            'payment_status' => 'pending',
                            'status' => 0,
                        ]);

                        $callbackUrl = $returnUrl;

                        // Store context in session for paypro_return
                        session([
                            'sub_paypro' => [
                                'subscription_id' => $subscription_id,
                                'order_number' => $orderNumber,
                                'paypro_id' => $payProId,
                                'user_id' => $user->id,
                                'payment_type' => $paymentType,
                                'amount' => $total,
                                'currency' => $currency,
                                'callback_url' => $callbackUrl
                            ]
                        ]);

                        // Also store in a separate session key for easier access
                        session([
                            'current_payment_context' => [
                                'type' => 'subscription',
                                'payment_type' => $paymentType,
                                'order_number' => $orderNumber,
                                'subscription_id' => $subscription_id,
                                'amount' => $total,
                                'currency' => $currency,
                                'user_id' => $user->id,
                                'callback_url' => $callbackUrl
                            ]
                        ]);

                        // Ensure proper URL formatting for PayPro
                        // If PayPro appends params, a clean URL is safer.
                        $separator = str_contains($click2Pay, needle: '?') ? '&' : '?';
                        $click2PayUrl = $click2Pay . $separator . 'callback_url=' . urlencode($callbackUrl);

                        // Store payment data in session
                        session([
                            'paypro_payment_' . $orderNumber => [
                                'subscription_id' => $subscription_id,
                                'user_id' => $user->id,
                                'payment_type' => $paymentType,
                                'amount' => $total,
                                'currency' => $currency,
                                'callback_url' => $callbackUrl,
                                'order_number' => $orderNumber,
                                'paypro_id' => $payProId ?? null,
                                'timestamp' => now()
                            ]
                        ]);

                        // Log the redirect details for debugging
                        \Log::info('Redirecting to PayPro', [
                            'original_url' => $click2Pay,
                            'final_url' => $click2PayUrl,
                            'order_number' => $orderNumber,
                            'user_id' => $user->id,
                            'payment_type' => $paymentType,
                            'session_data' => session('paypro_payment_' . $orderNumber, [])
                        ]);

                        return redirect()->away($click2PayUrl);

                    } catch (\Exception $e) {
                        // dd($e->getMessage());
                        \Log::error('PayPro Payment Error: ' . $e->getMessage());
                        return redirect()->route(self::CANCEL_ROUTE)
                            ->with(toastr_error(__('Failed to process PayPro payment: ') . $e->getMessage()));
                    }
                } elseif ($request->selected_payment_gateway === 'pagali') {
                    try {
                        return PaymentGatewayRequestHelper::pagalipay()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.pagali.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'sitesway') {
                    try {
                        return PaymentGatewayRequestHelper::sitesway()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.siteways.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'iyzipay') {
                    try {
                        return PaymentGatewayRequestHelper::iyzipay()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.iyzipay.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'kineticpay') {
                    try {
                        return PaymentGatewayRequestHelper::kineticpay()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.kineticpay.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'awdpay') {
                    try {
                        return PaymentGatewayRequestHelper::awdpay()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('bs.awdpay.ipn.subscription')));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'yoomoney') {
                    try {
                        return PaymentGatewayRequestHelper::yoomoney()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('yoomoney.ipn.all'), 'subscription'));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                } elseif ($request->selected_payment_gateway === 'coinpayments') {
                    try {
                        return PaymentGatewayRequestHelper::coinpayments()->charge_customer($this->buildPaymentArg($total, $title, $description, $subscription_id, $email, $name, $user_type, route('coinpayment.ipn.all'), 'subscription'));
                    } catch (\Exception $e) {
                        toastr_error($e->getMessage());
                        return back();
                    }
                }
            }
        }
    }

    private function buildPaymentArg($total, $title, $description, $last_subscription_id, $email, $name, $user_type, $ipn_route, $source = null)
    {
        // Store payment type in session if provided
        if (!empty($source)) {
            session(['payment_type' => $source]);
            \Log::info('Payment type set in session', ['payment_type' => $source]);
        } else {
            session(['payment_type' => 'membership']);
            \Log::info('Default payment type (membership) set in session');
        }

        return [
            'amount' => $total,
            'title' => $title,
            'description' => $description,
            'ipn_url' => $ipn_route,
            'order_id' => $last_subscription_id,
            'track' => \Str::random(36),
            'cancel_url' => route(self::CANCEL_ROUTE, $last_subscription_id),
            'success_url' => route($user_type . '.' . 'subscriptions.all'),
            'email' => $email,
            'name' => $name,
            'payment_type' => $source,
        ];
    }

    //send email
    private function sendEmail($name, $last_subscription_id, $email)
    {
        //Send subscription email to admin
        try {
            $message = get_static_option('user_subscription_purchase_admin_email_message') ?? __('A user just purchase a subscription.');
            $message = str_replace(["@name", "@subscription_id"], [$name, $last_subscription_id], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => get_static_option('user_subscription_purchase_admin_email_subject') ?? __('Subscription purchase email'),
                'message' => $message
            ]));
        } catch (\Exception $e) {

        }

        //Send subscription email to user
        try {
            $message = get_static_option('user_subscription_purchase_message') ?? __('Your subscription purchase successfully completed.');
            $message = str_replace(["@name", "@subscription_id"], [$name, $last_subscription_id], $message);
            Mail::to($email)->send(new BasicMail([
                'subject' => get_static_option('user_subscription_purchase_subject') ?? __('Subscription purchase email'),
                'message' => $message
            ]));
        } catch (\Exception $e) {

        }
    }

    //admin notification
    private function adminNotification($last_subscription_id, $user_id)
    {
        AdminNotification::create([
            'identity' => $last_subscription_id,
            'user_id' => $user_id,
            'type' => __('Buy Subscription'),
            'message' => __('User subscription purchase'),
        ]);
    }
}
