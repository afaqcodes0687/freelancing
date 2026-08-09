<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Subscription\Entities\Subscription;
use Modules\Subscription\Entities\SubscriptionType;
use Modules\Subscription\Entities\UserSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SubscriptionApiController extends Controller
{
    /**
     * Get all subscription types
     */
    public function getSubscriptionTypes(): JsonResponse
    {
        try {
            $subscriptionTypes = SubscriptionType::with('subscriptions')
                ->whereHas('subscriptions')
                ->select('id', 'type', 'validity')
                ->get();

            return response()->json([
                'success' => true,
                'message' => __('Subscription types retrieved successfully'),
                'data' => $subscriptionTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to retrieve subscription types'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get all available subscriptions
     */
    public function getAllSubscriptions(Request $request): JsonResponse
    {
        try {
            $query = Subscription::with(['features', 'subscription_type'])
                ->where('status', 1)
                ->orderBy('price', 'asc');

            // Filter by type if provided
            if ($request->has('type_id') && $request->type_id !== 'all') {
                $query->where('subscription_type_id', $request->type_id);
            }

            $subscriptions = $query->select([
                'id', 'subscription_type_id', 'title', 'logo', 'price', 
                'limit', 'description', 'badge_color', 'popular_tag'
            ])->paginate(18);

            return response()->json([
                'success' => true,
                'message' => __('Subscriptions retrieved successfully'),
                'data' => $subscriptions->items(),
                'pagination' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                    'has_more_pages' => $subscriptions->hasMorePages()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to retrieve subscriptions'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get subscription details
     */
    public function getSubscriptionDetails($id): JsonResponse
    {
        try {
            $subscription = Subscription::with(['features', 'subscription_type'])
                ->where('id', $id)
                ->where('status', 1)
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => __('Subscription not found'),
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Subscription details retrieved successfully'),
                'data' => $subscription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to retrieve subscription details'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get user's current subscription
     */
    public function getUserSubscription(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not authenticated'),
                    'data' => null
                ], 401);
            }

            $userSubscription = UserSubscription::with([
                'subscription:id,subscription_type_id,title,logo,price,limit',
                'subscription.features:id,subscription_id,feature,status',
                'subscription.subscription_type:id,type,validity',
            ])
                ->where('user_id', $user->id)
                ->where('payment_status', 'complete')
                ->where('status', 1)
                ->whereDate('expire_date', '>', now())
                ->latest('id')
                ->first();

            $totalLimit = UserSubscription::where('user_id', $user->id)
                ->where('payment_status', 'complete')
                ->where('status', 1)
                ->whereDate('expire_date', '>', now())
                ->sum('limit');

            if (!$userSubscription) {
                return response()->json([
                    'success' => true,
                    'message' => __('No active subscription found'),
                    'data' => [
                        'has_subscription' => false,
                        'current_subscription' => null,
                        'subscription' => null,
                        'remaining_days' => 0,
                        'remaining_limit' => 0,
                        'total_limit' => 0,
                    ]
                ]);
            }

            $expireDate = Carbon::parse($userSubscription->expire_date);
            $remainingDays = max(0, Carbon::now()->diffInDays($expireDate, false));
            $formattedSubscription = $this->formatUserSubscription($userSubscription);
            
            return response()->json([
                'success' => true,
                'message' => __('User subscription retrieved successfully'),
                'data' => [
                    'has_subscription' => true,
                    'current_subscription' => $formattedSubscription,
                    'subscription' => $formattedSubscription,
                    'remaining_days' => $remainingDays,
                    'remaining_limit' => $userSubscription->limit ?? 0,
                    'total_limit' => (int) $totalLimit,
                    'expire_date' => $expireDate->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to retrieve user subscription'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get user's subscription history
     */
    public function getSubscriptionHistory(): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not authenticated'),
                    'data' => null
                ], 401);
            }

            $subscriptions = UserSubscription::with([
                'subscription:id,subscription_type_id,title,logo,price,limit',
                'subscription.subscription_type:id,type,validity',
                'subscription.features:id,subscription_id,feature,status',
            ])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $currentSubscription = UserSubscription::with([
                'subscription:id,subscription_type_id,title,logo,price,limit',
                'subscription.subscription_type:id,type,validity',
                'subscription.features:id,subscription_id,feature,status',
            ])
                ->where('user_id', $user->id)
                ->where('payment_status', 'complete')
                ->where('status', 1)
                ->whereDate('expire_date', '>', now())
                ->latest('id')
                ->first();

            $totalLimit = UserSubscription::where('user_id', $user->id)
                ->where('payment_status', 'complete')
                ->where('status', 1)
                ->whereDate('expire_date', '>', now())
                ->sum('limit');

            return response()->json([
                'success' => true,
                'message' => __('Subscription history retrieved successfully'),
                'data' => array_map(fn ($subscription) => $this->formatUserSubscription($subscription), $subscriptions->items()),
                'current_subscription' => $currentSubscription ? $this->formatUserSubscription($currentSubscription) : null,
                'total_limit' => (int) $totalLimit,
                'pagination' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                    'has_more_pages' => $subscriptions->hasMorePages()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to retrieve subscription history'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not authenticated'),
                    'data' => null
                ], 401);
            }

            $request->validate([
                'subscription_id' => 'required|exists:subscriptions,id',
                'payment_method' => 'required|string|in:wallet,paypro'
            ]);

            $subscription = Subscription::findOrFail($request->subscription_id);

            // Check if user already has active subscription
            $existingSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 1)
                ->where('expire_date', '>', now())
                ->first();

            if ($existingSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => __('You already have an active subscription'),
                    'data' => null
                ], 400);
            }

            // Create new subscription
            $userSubscription = new UserSubscription();
            $userSubscription->user_id = $user->id;
            $userSubscription->subscription_id = $request->subscription_id;
            $userSubscription->status = 2; // Pending payment
            $userSubscription->expire_date = Carbon::now()->addDays($subscription->subscription_type->validity);
            $userSubscription->price = $subscription->price;
            $userSubscription->limit = $subscription->limit;
            $userSubscription->payment_method = $request->payment_method;
            $userSubscription->save();

            // For wallet payment, process immediately
            if ($request->payment_method === 'wallet') {
                if ($user->user_wallet->balance < $subscription->price) {
                    $userSubscription->delete();
                    return response()->json([
                        'success' => false,
                        'message' => __('Insufficient wallet balance'),
                        'data' => null
                    ], 400);
                }

                // Process wallet payment
                $user->user_wallet->decrement('balance', $subscription->price);
                $userSubscription->status = 1; // Active
                $userSubscription->save();

                return response()->json([
                    'success' => true,
                    'message' => __('Subscription activated successfully'),
                    'data' => [
                        'subscription' => $userSubscription->fresh(['subscription']),
                        'payment_status' => 'completed',
                        'expire_date' => $userSubscription->expire_date->format('Y-m-d H:i:s')
                    ]
                ]);
            }

            // For other payment methods, return payment URL
            if ($request->payment_method === 'paypro') {
                try {
                    $paymentUrl = $this->generatePayProOrder($subscription, $userSubscription);
                    
                    return response()->json([
                        'success' => true,
                        'message' => __('Subscription created. Please complete payment.'),
                        'data' => [
                            'subscription' => $userSubscription->fresh(['subscription']),
                            'payment_status' => 'pending',
                            'payment_url' => $paymentUrl
                        ]
                    ]);
                } catch (\Exception $e) {
                    $userSubscription->delete();
                    return response()->json([
                        'success' => false,
                        'message' => __('Failed to initiate PayPro payment: ') . $e->getMessage(),
                        'data' => null
                    ], 422);
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('Subscription created. Please complete payment.'),
                'data' => [
                    'subscription' => $userSubscription->fresh(['subscription']),
                    'payment_status' => 'pending',
                    'payment_url' => ''
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to create subscription'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not authenticated'),
                    'data' => null
                ], 401);
            }

            $userSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 1)
                ->where('expire_date', '>', now())
                ->first();

            if (!$userSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => __('No active subscription found'),
                    'data' => null
                ], 404);
            }

            $userSubscription->status = 0; // Cancelled
            $userSubscription->save();

            return response()->json([
                'success' => true,
                'message' => __('Subscription cancelled successfully'),
                'data' => [
                    'cancelled_at' => now()->format('Y-m-d H:i:s'),
                    'refund_eligible' => false // Customize based on your policy
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to cancel subscription'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Generate payment order using PayPro API
     */
    private function generatePayProOrder($subscription, $userSubscription): string
    {
        $settings = $this->getPayProSettings();
        $baseUrl = $settings['baseUrl'];
        $clientId = $settings['clientId'];
        $clientSecret = $settings['clientSecret'];
        $merchantId = $settings['merchantId'];

        if (empty($clientId) || empty($clientSecret) || empty($merchantId)) {
            throw new \Exception('PayPro payment gateway is not configured properly');
        }

        // 1. Get Authentication Token
        $authResponse = Http::asJson()->post($baseUrl . '/v2/ppro/auth', [
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

        // 2. Prepare Order Data
        $orderNumber = 'SUB-' . $userSubscription->id . '-' . time();
        $user = auth('sanctum')->user();
        $now = Carbon::now();
        $dueDate = $now->copy()->addDays(1);
        $currency = get_static_option('site_global_currency') ?? 'USD';

        $payload = [
            [
                'MerchantId' => $merchantId,
            ],
            [
                'OrderNumber' => $orderNumber,
                'CurrencyAmount' => (string) $subscription->price,
                'OrderDueDate' => $dueDate->format('d/m/Y'),
                'OrderType' => 'Service',
                'IssueDate' => $now->format('d/m/Y'),
                'OrderExpireAfterSeconds' => '0',
                'CustomerName' => trim($user->first_name . ' ' . $user->last_name) ?: $user->name,
                'CustomerMobile' => '',
                'CustomerEmail' => $user->email,
                'CustomerAddress' => '',
                'Currency' => $currency,
                'IsConverted' => 'true',
            ],
        ];

        // 3. Create Order
        $orderResponse = Http::withHeaders(['token' => $token])
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

            // Update subscription with transaction details
            $userSubscription->update([
                'transaction_id' => $payProId ?: $orderNumber,
            ]);

            return $click2Pay;
        } else {
            $status = $orderResult[0]['Status'] ?? 'unknown';
            $message = $orderResult[0]['ResponseMessage'] ?? 'Failed to create payment order';
            throw new \Exception("PayPro error ({$status}): {$message}");
        }
    }

    /**
     * Get PayPro settings
     */
    private function getPayProSettings(): array
    {
        $baseUrl = get_static_option('paypro_base_url') ?? 'https://api.paypro.com.pk';
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

    private function formatUserSubscription(UserSubscription $userSubscription): array
    {
        $subscription = $userSubscription->subscription;
        $logo = $subscription?->logo;

        if (!empty($logo) && function_exists('get_attachment_image_by_id')) {
            $imgDetails = get_attachment_image_by_id($logo);
            $logo = $imgDetails['img_url'] ?? $logo;
        }

        return [
            'id' => $userSubscription->id,
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->subscription_id,
            'price' => $userSubscription->price,
            'limit' => $userSubscription->limit,
            'status' => $userSubscription->status,
            'payment_status' => $userSubscription->payment_status,
            'payment_gateway' => $userSubscription->payment_gateway,
            'transaction_id' => $userSubscription->transaction_id,
            'expire_date' => Carbon::parse($userSubscription->expire_date)->format('Y-m-d H:i:s'),
            'created_at' => optional($userSubscription->created_at)?->format('Y-m-d H:i:s'),
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'title' => $subscription->title,
                'logo' => $logo,
                'price' => $subscription->price,
                'limit' => $subscription->limit,
                'subscription_type' => $subscription->subscription_type ? [
                    'id' => $subscription->subscription_type->id,
                    'type' => $subscription->subscription_type->type,
                    'validity' => $subscription->subscription_type->validity,
                ] : null,
                'features' => $subscription->features->map(function ($feature) {
                    return [
                        'id' => $feature->id,
                        'feature' => $feature->feature,
                        'status' => $feature->status,
                    ];
                })->values(),
            ] : null,
        ];
    }

    /**
     * Handle PayPro return URL
     */
    public function payproReturn(Request $request): JsonResponse
    {
        try {
            $orderId = $request->query('order') ?? $request->query('ordId');
            $status = $request->query('status');

            Log::info('PayPro Return URL accessed', [
                'order_id' => $orderId,
                'status' => $status,
                'params' => $request->all()
            ]);

            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => __('Invalid request'),
                    'data' => null
                ], 400);
            }

            // Find subscription by transaction ID
            $subscription = UserSubscription::where('transaction_id', $orderId)
                ->orWhere('id', str_replace('SUB-', '', explode('-', $orderId)[1] ?? ''))
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => __('Subscription not found'),
                    'data' => null
                ], 404);
            }

            // Return status based on PayPro response
            $paymentStatus = $status === 'success' ? 'success' : 'failed';
            $message = $status === 'success' 
                ? __('Payment completed successfully') 
                : __('Payment failed');

            return response()->json([
                'success' => $status === 'success',
                'message' => $message,
                'data' => [
                    'order_id' => $orderId,
                    'payment_status' => $paymentStatus,
                    'subscription_id' => $subscription->id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PayPro return error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Failed to process payment return'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Handle PayPro webhook
     */
    public function payproWebhook(Request $request): JsonResponse
    {
        try {
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
            $orderNumber = $request->input('OrderNumber')
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
                $this->processWebhookSuccess($orderNumber);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment marked as paid (webhook)'
                ]);
            }

            return response()->json([
                'status' => 'pending',
                'message' => 'Payment not completed'
            ]);

        } catch (\Exception $e) {
            Log::error('PayPro Webhook error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process successful webhook payment
     */
    private function processWebhookSuccess($orderNumber): void
    {
        $parts = explode('-', $orderNumber);
        $subId = $parts[1] ?? null;

        $subscription = UserSubscription::where('id', $subId)
            ->orWhere('transaction_id', $orderNumber)
            ->first();

        if ($subscription && $subscription->payment_status !== 'complete') {
            $subscription->update([
                'payment_status' => 'complete',
                'status' => 1,
                'updated_at' => now()
            ]);

            // Deactivate other subscriptions
            UserSubscription::where('user_id', $subscription->user_id)
                ->where('id', '!=', $subscription->id)
                ->update(['status' => 0]);

            // Send notifications
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

    /**
     * Manual subscription activation for testing purposes
     */
    public function manualActivateSubscription(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not authenticated'),
                    'data' => null
                ], 401);
            }

            $request->validate([
                'subscription_id' => 'required|exists:user_subscriptions,id'
            ]);

            $subscription = UserSubscription::where('id', $request->subscription_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => __('Subscription not found'),
                    'data' => null
                ], 404);
            }

            // Activate the subscription
            $subscription->update([
                'payment_status' => 'complete',
                'status' => 1,
                'updated_at' => now()
            ]);

            // Deactivate other subscriptions for this user
            UserSubscription::where('user_id', $user->id)
                ->where('id', '!=', $subscription->id)
                ->update(['status' => 0]);

            // Send notifications
            $this->sendNotifications($subscription);

            return response()->json([
                'success' => true,
                'message' => __('Subscription activated successfully'),
                'data' => [
                    'activated_subscription' => $this->formatUserSubscription($subscription),
                    'message' => __('Subscription :id is now active', ['id' => $subscription->id])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to activate subscription: ') . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Check database subscriptions for debugging
     */
    public function checkDatabaseSubscriptions(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not authenticated'),
                    'data' => null
                ], 401);
            }

            // Get all subscriptions for this user from database
            $subscriptions = UserSubscription::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'subscription_id', 'price', 'limit', 'payment_gateway', 'payment_status', 'status', 'expire_date', 'created_at']);

            return response()->json([
                'success' => true,
                'message' => __('Database subscriptions retrieved successfully'),
                'data' => [
                    'user_id' => $user->id,
                    'total_subscriptions' => $subscriptions->count(),
                    'subscriptions' => $subscriptions->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'subscription_id' => $sub->subscription_id,
                            'price' => $sub->price,
                            'limit' => $sub->limit,
                            'payment_gateway' => $sub->payment_gateway,
                            'payment_status' => $sub->payment_status,
                            'status' => $sub->status,
                            'status_text' => $sub->status == 1 ? 'Active' : ($sub->status == 0 ? 'Inactive' : 'Pending'),
                            'expire_date' => $sub->expire_date,
                            'created_at' => $sub->created_at,
                            'can_activate' => $sub->payment_status === 'pending' && $sub->status != 1
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to check database: ') . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Quick activate latest pending subscription
     */
    public function quickActivate(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => __('User not authenticated'),
                    'data' => null
                ], 401);
            }

            // Find latest pending subscription
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('payment_status', 'pending')
                ->where('status', '!=', 1)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => __('No pending subscription found to activate'),
                    'data' => null
                ], 404);
            }

            // Activate the subscription
            $subscription->update([
                'payment_status' => 'complete',
                'status' => 1,
                'updated_at' => now()
            ]);

            // Deactivate other subscriptions for this user
            UserSubscription::where('user_id', $user->id)
                ->where('id', '!=', $subscription->id)
                ->update(['status' => 0]);

            // Send notifications
            $this->sendNotifications($subscription);

            return response()->json([
                'success' => true,
                'message' => __('Latest subscription activated successfully'),
                'data' => [
                    'activated_subscription' => $this->formatUserSubscription($subscription),
                    'message' => __('Subscription :id is now active', ['id' => $subscription->id])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to activate subscription: ') . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Send notifications for successful subscription
     */
    private function sendNotifications($subscription): void
    {
        $user = $subscription->user;
        if (!$user) {
            return;
        }

        // Create admin notification
        \App\Models\AdminNotification::create([
            'identity' => $subscription->id,
            'user_id' => $user->id,
            'type' => __('Manual Subscription Activation'),
            'message' => __('User @name manually activated subscription', ['name' => $user->name]),
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->send(new \App\Mail\BasicMail([
                'subject' => __('Subscription Activated'),
                'message' => __('Your subscription has been activated successfully. Order ID: :id', ['id' => $subscription->id])
            ]));
        } catch (\Exception $e) {
            Log::error('Manual Activation Email Error: ' . $e->getMessage());
        }
    }
}
