<?php

namespace App\Http\Controllers\Api\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserEarning;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\BankAccount;
use Modules\Wallet\Entities\WalletHistory;
use Modules\Wallet\Entities\WithdrawGateway;
use Modules\Wallet\Entities\WithdrawRequest;
use Modules\Wallet\Http\Requests\FreelancerHandleWithdrawRequestAPI;
use Modules\CountryManage\Entities\Country;

class WalletApiController extends Controller
{
    // Get wallet dashboard info (Balances)
    public function get_wallet_dashboard()
    {
        $user_id = auth('sanctum')->user()->id;

        // Get wallet balance including signup bonus
        $wallet = Wallet::where('user_id', $user_id)->first();
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user_id,
                'balance' => 0,
                'signup_bonus' => 0,
                'status' => 1,
            ]);
        }

        $total_balance = $wallet->balance ?? 0;
        $signup_bonus = $wallet->signup_bonus ?? 0;

        // Calculate withdrawable balance (Total Balance - Signup Bonus)
        // This includes both earnings and deposits
        $withdrawable_balance = max(0, $total_balance - $signup_bonus);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_balance' => (float) $total_balance,
                'withdrawable_balance' => (float) $withdrawable_balance,
                'signup_bonus' => (float) $signup_bonus,
                'currency_symbol' => get_static_option('site_global_currency_symbol') ?? '$',
                'currency_code' => get_static_option('site_global_currency') ?? 'USD',
            ]
        ]);
    }

    // Get wallet history (Deposits, Payments etc)
    public function get_wallet_history()
    {
        $user_id = auth('sanctum')->user()->id;
        $all_histories = WalletHistory::where('user_id', $user_id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => 'success',
            'data' => $all_histories
        ]);
    }

    // Get bank account info
    public function get_bank_info()
    {
        $user_id = auth('sanctum')->user()->id;
        $bank_account = BankAccount::where('user_id', $user_id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $bank_account
        ]);
    }

    // Update/Submit bank account info
    public function update_bank_info(Request $request)
    {
        $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'account_title' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'swis_code' => 'nullable|string|max:255',
            'iban_number' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
        ]);

        $user_id = auth('sanctum')->user()->id;

        $bank_account = BankAccount::updateOrCreate(
            ['user_id' => $user_id],
            [
                'country_id' => $request->country_id,
                'account_title' => $request->account_title,
                'bank_name' => $request->bank_name,
                'swis_code' => $request->swis_code,
                'iban_number' => $request->iban_number,
                'account_number' => $request->account_number,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Bank information saved successfully.',
            'data' => $bank_account
        ]);
    }

    // Get withdrawal settings and gateways
    public function withdraw_settings()
    {
        $user_id = auth('sanctum')->user()->id;

        $minimum_withdraw_amount = get_static_option('minimum_withdraw_amount') ?? 0;
        $maximum_withdraw_amount = get_static_option('maximum_withdraw_amount') ?? 0;
        $withdraw_fee_type = get_static_option('withdraw_fee_type') ?? '';
        $withdraw_fee = get_static_option('withdraw_fee') ?? 0;

        // Get wallet for balance calculation
        $wallet = Wallet::where('user_id', $user_id)->first();
        $total_balance = $wallet->balance ?? 0;
        $signup_bonus = $wallet->signup_bonus ?? 0;
        
        // Withdrawable balance includes both earnings and deposits (Total Balance - Signup Bonus)
        $withdrawable_balance = max(0, $total_balance - $signup_bonus);

        $withdraw_gateways = WithdrawGateway::select('id', 'name', 'field')
            ->where('status', 1)
            ->get()
            ->transform(function ($item) {
                $item->field = unserialize($item->field);
                return $item;
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'withdraw_gateways' => $withdraw_gateways,
                'withdrawable_balance' => (float) $withdrawable_balance,
                'minimum_withdraw_amount' => (float) $minimum_withdraw_amount,
                'maximum_withdraw_amount' => (float) $maximum_withdraw_amount,
                'withdraw_fee_type' => $withdraw_fee_type,
                'withdraw_fee' => (float) $withdraw_fee,
            ]
        ]);
    }

    // Submit withdrawal request
    public function withdraw_request(FreelancerHandleWithdrawRequestAPI $request)
    {
        $data = $request->validated();
        $user_id = auth('sanctum')->user()->id;

        // The Request object already serializes gateway_fields and sets user_id in prepareForValidation

        $wallet = Wallet::where("user_id", $user_id)->first();
        $user = User::find($user_id);

        if ($user->freeze_withdraw == 'freeze') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your withdraw request has been freeze. Please contact your administrator.'
            ], 422);
        }

        $requested_amount = $request->amount;
        $total_to_deduct = $requested_amount;
        $payout_amount = $requested_amount;

        if ($total_to_deduct < get_static_option('minimum_withdraw_amount') || $total_to_deduct > get_static_option('maximum_withdraw_amount')) {
            return response()->json([
                'status' => 'error',
                'message' => "Please enter a valid amount between " . float_amount_with_currency_symbol(get_static_option('minimum_withdraw_amount')) . '-' . float_amount_with_currency_symbol(get_static_option('maximum_withdraw_amount'))
            ], 422);
        }

        if ($wallet->balance >= $total_to_deduct) {
            // Restrict signup bonus withdrawal
            $signup_bonus = $wallet->signup_bonus ?? 0;
            $withdrawable = max(0, $wallet->balance - $signup_bonus);

            if ($withdrawable <= 0 || $total_to_deduct > $withdrawable) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You can only withdraw your earned balance. The $10 signup bonus cannot be withdrawn.'
                ], 422);
            }

            // Get user earnings and verify
            $user_earning = UserEarning::where('user_id', $user_id)->first();
            if (!$user_earning || $user_earning->remaining_balance < $total_to_deduct) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient earnings balance'
                ], 422);
            }

            // Prepare data for request record
            // We store the Net amount in the 'amount' field of the request, 
            // similar to how it was done in the previous fix.
            $withdraw_data = $data;
            $withdraw_data['amount'] = $payout_amount;

            // Create request
            $withdraw = WithdrawRequest::create($withdraw_data);

            // Record this withdrawal request in the wallet history
            \Modules\Wallet\Entities\WalletHistory::create([
                'user_id' => $user_id,
                'amount' => $total_to_deduct,
                'payment_gateway' => 'withdraw',
                'payment_status' => 'pending',
                'transaction_id' => $withdraw->id,
                'status' => 1,
            ]);

            // Update wallet balance (total balance)
            Wallet::where('user_id', $user_id)->update([
                'balance' => $wallet->balance - $total_to_deduct,
                'remaining_balance' => $wallet->balance - $total_to_deduct, // Legacy field
                'withdraw_amount' => $wallet->withdraw_amount + $total_to_deduct
            ]);

            // Update user earnings (actual earnings)
            UserEarning::where('user_id', $user_id)->update([
                'remaining_balance' => $user_earning->remaining_balance - $total_to_deduct
            ]);

            // Clear cache
            if (function_exists('cache')) {
                cache()->forget('user_wallet_' . $user_id);
                cache()->forget('user_earnings_' . $user_id);
            }

            notificationToAdmin($withdraw->id, $user_id, 'Withdraw', 'New withdraw request');

            return response()->json([
                'status' => 'success',
                'message' => "Successfully sent your request",
                'data' => $withdraw
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Your requested amount is greater than your wallet balance'
        ], 422);
    }

    // Get withdrawal history
    public function get_withdrawal_history()
    {
        $user_id = auth('sanctum')->user()->id;
        $all_request = WithdrawRequest::where('user_id', $user_id)
            ->latest()
            ->paginate(10)
            ->through(function ($item) {
                $item->gateway_fields_unserialized = unserialize($item->gateway_fields);
                return $item;
            });

        return response()->json([
            'status' => 'success',
            'data' => $all_request,
            'image_path' => asset('assets/uploads/withdraw-request/')
        ]);
    }

    // Get deposit settings and available payment gateways
    public function get_deposit_settings()
    {
        $user_id = auth('sanctum')->user()->id;
        
        // Get available payment gateways
        $payment_gateways = [
            [
                'id' => 'paypro',
                'name' => 'PayPro',
                'type' => 'online',
                'description' => 'Pay via PayPro payment gateway',
                'image' => asset('assets/uploads/payment-gateway/paypro.png'),
                'status' => get_static_option('paypro_enable') == 'on'
            ],
            [
                'id' => 'manual_payment',
                'name' => 'Manual Payment',
                'type' => 'manual',
                'description' => 'Upload payment receipt for manual verification',
                'image' => asset('assets/uploads/payment-gateway/manual.png'),
                'status' => true // Always available
            ]
        ];

        // Get minimum and maximum deposit amounts
        $minimum_deposit_amount = get_static_option('minimum_deposit_amount') ?? 1;
        $maximum_deposit_amount = get_static_option('maximum_deposit_amount') ?? 10000;

        return response()->json([
            'status' => 'success',
            'data' => [
                'payment_gateways' => $payment_gateways,
                'minimum_deposit_amount' => (float) $minimum_deposit_amount,
                'maximum_deposit_amount' => (float) $maximum_deposit_amount,
                'currency_symbol' => get_static_option('site_global_currency_symbol') ?? '$',
                'currency_code' => get_static_option('site_global_currency') ?? 'USD',
            ]
        ]);
    }

    // Submit deposit request
    public function deposit_request(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'payment_gateway' => 'required|in:paypro,manual_payment',
            'manual_payment_image' => 'required_if:payment_gateway,manual_payment|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $user_id = auth('sanctum')->user()->id;
        $user = User::find($user_id);
        
        $total = $request->amount;
        $payment_gateway = $request->payment_gateway;
        $payment_status = $payment_gateway === 'manual_payment' ? 'pending' : '';

        // Create or ensure wallet exists
        $wallet = Wallet::where('user_id', $user_id)->first();
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user_id,
                'balance' => 0,
                'status' => 1,
            ]);
        }

        // Create deposit record
        $deposit = WalletHistory::create([
            'user_id' => $user_id,
            'amount' => $total,
            'payment_gateway' => $payment_gateway,
            'payment_status' => $payment_status,
            'status' => 1,
        ]);

        // Handle manual payment image upload
        if ($payment_gateway === 'manual_payment') {
            if ($request->hasFile('manual_payment_image')) {
                $manual_payment_image = $request->manual_payment_image;
                $img_ext = $manual_payment_image->extension();
                
                $manual_payment_image_name = 'manual_attachment_' . time() . '.' . $img_ext;
                if (in_array($img_ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
                    $manual_image_path = 'assets/uploads/manual-payment/';
                    $manual_payment_image->move($manual_image_path, $manual_payment_image_name);
                    
                    $deposit->update([
                        'manual_payment_image' => $manual_payment_image_name
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Image type not supported'
                    ], 422);
                }
            }

            // Send notification emails for manual payment
            try {
                $user_type = $user->user_type == 1 ? 'client' : 'freelancer';
                $message_body = __('Hello a') . ' ' . $user_type . __('just deposit to his wallet. Please check and confirm') . '</br>' . '<span class="verify-code">' . __('Deposit ID: ') . $deposit->id . '</span>';
                
                \Mail::to(get_static_option('site_global_email'))->send(new \App\Mail\BasicMail([
                    'subject' => __('Deposit Confirmation'),
                    'message' => $message_body
                ]));
                
                \Mail::to($user->email)->send(new \App\Mail\BasicMail([
                    'subject' => __('Deposit Confirmation'),
                    'message' => __('Manual deposit success. Your wallet will credited after admin approval') . '</br>' . '<span class="verify-code">' . __('Deposit ID: ') . $deposit->id . '</span>'
                ]));
            } catch (\Exception $e) {
                \Log::error('Deposit email error: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Manual deposit submitted successfully. Your wallet will be credited after admin approval.',
                'data' => [
                    'deposit_id' => $deposit->id,
                    'amount' => $total,
                    'payment_gateway' => $payment_gateway,
                    'payment_status' => $payment_status,
                    'manual_payment_image' => $deposit->manual_payment_image ? asset('assets/uploads/manual-payment/' . $deposit->manual_payment_image) : null
                ]
            ]);
        }

        // Handle online payment (PayPro)
        if ($payment_gateway === 'paypro') {
            $baseUrl = rtrim(get_static_option('paypro_base_url') ?? 'https://api.paypro.com.pk', '/');
            $clientId = get_static_option('paypro_client_id');
            $clientSecret = get_static_option('paypro_client_secret');
            $merchantId = get_static_option('paypro_username');

            if (empty($clientId) || empty($clientSecret) || empty($merchantId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'PayPro payment gateway is not configured properly'
                ], 422);
            }

            try {
                // Get auth token
                $authResponse = \Illuminate\Support\Facades\Http::asJson()->post($baseUrl . '/v2/ppro/auth', [
                    'clientid' => $clientId,
                    'clientsecret' => $clientSecret,
                ]);

                if (!$authResponse->ok()) {
                    throw new \Exception('Failed to authenticate with PayPro');
                }

                $token = $authResponse->header('token') ?? $authResponse->header('Token');

                if (empty($token)) {
                    throw new \Exception('PayPro authentication token not received');
                }

                // Create order using the correct API structure
                $orderNumber = 'WALLET-' . $deposit->id . '-' . time();
                $now = \Carbon\Carbon::now();
                $dueDate = $now->copy()->addDays(1);
                $currency = get_static_option('site_global_currency') ?? 'USD';

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
                        'OrderExpireAfterSeconds' => '0',
                        'CustomerName' => trim($user->first_name . ' ' . $user->last_name),
                        'CustomerMobile' => '',
                        'CustomerEmail' => $user->email,
                        'CustomerAddress' => '',
                        'Currency' => $currency,
                        'IsConverted' => 'true',
                    ],
                ];

                $orderResponse = \Illuminate\Support\Facades\Http::withHeaders(['token' => $token])
                    ->asJson()
                    ->post($baseUrl . '/v2/ppro/co', $payload);

                $orderResult = $orderResponse->json();

                if (isset($orderResult[0]['Status']) && $orderResult[0]['Status'] === '00') {
                    $details = $orderResult[1] ?? [];
                    $click2Pay = $details['Click2Pay'] ?? null;
                    $payProId = $details['PayProId'] ?? null;

                    if (empty($click2Pay)) {
                        throw new \Exception('Payment URL not received from PayPro');
                    }

                    // Update deposit with transaction details
                    $deposit->update([
                        'payment_status' => 'pending',
                        'transaction_id' => $payProId ?: $orderNumber,
                    ]);

                    // Store session data for verification
                    session(['wallet_paypro' => [
                        'deposit_id' => $deposit->id,
                        'paypro_id' => $payProId,
                        'order_number' => $orderNumber,
                        'type' => 'freelancer',
                    ]]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment initiated successfully',
                        'data' => [
                            'deposit_id' => $deposit->id,
                            'amount' => $total,
                            'payment_gateway' => $payment_gateway,
                            'payment_url' => $click2Pay,
                            'order_number' => $orderNumber
                        ]
                    ]);
                } else {
                    $status = $orderResult[0]['Status'] ?? 'unknown';
                    $message = $orderResult[0]['ResponseMessage'] ?? 'Failed to create payment order';
                    throw new \Exception("PayPro error ({$status}): {$message}");
                }

            } catch (\Exception $e) {
                \Log::error('PayPro deposit error: ' . $e->getMessage());
                
                // Delete the deposit record since payment failed
                $deposit->delete();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to initiate payment: ' . $e->getMessage()
                ], 422);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid payment gateway'
        ], 422);
    }

    // Get deposit history
    public function get_deposit_history()
    {
        $user_id = auth('sanctum')->user()->id;
        $deposits = WalletHistory::where('user_id', $user_id)
            ->where('payment_gateway', '!=', 'withdraw')
            ->latest()
            ->paginate(10);

        // Transform data for API response
        $deposits->getCollection()->transform(function ($deposit) {
            return [
                'id' => $deposit->id,
                'amount' => (float) $deposit->amount,
                'payment_gateway' => $deposit->payment_gateway,
                'payment_status' => $deposit->payment_status,
                'transaction_id' => $deposit->transaction_id,
                'manual_payment_image' => $deposit->manual_payment_image ? asset('assets/uploads/manual-payment/' . $deposit->manual_payment_image) : null,
                'created_at' => $deposit->created_at,
                'updated_at' => $deposit->updated_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $deposits,
            'image_path' => asset('assets/uploads/manual-payment/')
        ]);
    }
}
