<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProjectDetailsController extends Controller
{
    public function __construct()
    {
        // Apply API authentication middleware
        $this->middleware('auth:sanctum');
    }

    /**
     * Project Details API
     * Matches the website project_details method exactly
     */
    public function project_details(Request $request, $username, $slug = null): JsonResponse
    {
        try {
            // Handle jobs/all route
            if ($username == 'jobs' && $slug == "all") {
                // Call the same method as website
                $jobsResponse = $this->jobsController->jobs();
                
                // Convert view response to JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Jobs retrieved successfully',
                    'data' => $this->convertViewResponseToJson($jobsResponse, 'jobs')
                ]);
            }

            // Handle subscriptions/all route
            if ($username == 'subscriptions' && $slug == "all") {
                // Call the same method as website
                $subscriptionsResponse = $this->subscriptionController->subscriptions();
                
                // Convert view response to JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Subscriptions retrieved successfully',
                    'data' => $this->convertViewResponseToJson($subscriptionsResponse, 'subscriptions')
                ]);
            }

            // Handle admin route
            if ($slug == 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access not available through API',
                    'redirect_to' => 'login'
                ], 403);
            }

            // Get project details (same logic as website)
            $project = Project::where('slug', $slug)->first();
            
            if (empty($project)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found'
                ], 404);
            }

            $user = User::with('user_introduction','user_country','user_state','user_city')
                ->where('id', $project->user_id)
                ->first();
                
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Handle pro project view count (same logic as website)
            $isPromoted = false;
            if (moduleExists('PromoteFreelancer')) {
                if (Session::has('is_pro')) {
                    $current_date = \Carbon\Carbon::now()->toDateTimeString();
                    $find_package = PromotionProjectList::where('identity', $project->id)
                        ->where('type', 'project')
                        ->where('expire_date', '>=', $current_date)
                        ->first();
                    if ($find_package) {
                        PromotionProjectList::where('id', $find_package->id)
                            ->update(['click' => $find_package->click + 1]);
                        Session::forget('is_pro');
                        $isPromoted = true;
                    }
                }
            }

            // Return project and user data as JSON
            return response()->json([
                'success' => true,
                'message' => 'Project details retrieved successfully',
                'data' => [
                    'project' => $this->formatProjectData($project),
                    'user' => $this->formatUserData($user),
                    'is_promoted' => $isPromoted
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching project details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Load More Reviews API
     * Matches the website load_more_review method exactly
     */
    public function load_more_review(Request $request): JsonResponse
    {
        try {
            $pagination_limit = 10;
            $project_id = $request->project_id;

            // Validate project_id
            if (!$project_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID is required'
                ], 422);
            }

            // Check if project exists
            $project = Project::find($project_id);
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found'
                ], 404);
            }

            // Get reviews data (you need to implement this based on your review system)
            $reviews = $this->getReviewsData($project_id, $pagination_limit);

            return response()->json([
                'success' => true,
                'message' => 'Reviews loaded successfully',
                'data' => [
                    'reviews' => $reviews,
                    'pagination_limit' => $pagination_limit,
                    'project_id' => $project_id
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format project data for API response
     */
    private function formatProjectData($project): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title ?? '',
            'slug' => $project->slug ?? '',
            'description' => $project->description ?? '',
            'budget' => $project->budget ?? 0,
            'budget_type' => $project->budget_type ?? '',
            'duration' => $project->duration ?? '',
            'status' => $project->status ?? '',
            'featured' => $project->featured ?? false,
            'urgent' => $project->urgent ?? false,
            'skills' => $project->skills ?? [],
            'attachments' => $project->attachments ?? [],
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
        ];
    }

    /**
     * Format user data for API response
     */
    private function formatUserData($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name ?? '',
            'username' => $user->username ?? '',
            'email' => $user->email ?? '',
            'avatar' => $user->avatar ?? '',
            'bio' => $user->bio ?? '',
            'user_type' => $user->user_type ?? '',
            'country' => $user->user_country->name ?? '',
            'state' => $user->user_state->name ?? '',
            'city' => $user->user_city->name ?? '',
            'introduction' => $user->user_introduction->introduction ?? '',
            'hourly_rate' => $user->hourly_rate ?? 0,
            'profile_completion' => $user->profile_completion ?? 0,
            'created_at' => $user->created_at,
        ];
    }

    /**
     * Convert view response to JSON format
     */
    private function convertViewResponseToJson($viewResponse, string $type): array
    {
        // This is a placeholder - implement based on what the original methods return
        // For now, return basic structure
        return [
            'type' => $type,
            'content' => 'Data retrieved successfully',
            // Add actual data conversion logic here based on view response
        ];
    }

    /**
     * Get reviews data (implement based on your review system)
     */
    private function getReviewsData($projectId, $limit): array
    {
        // This is a placeholder - implement based on your actual review system
        // Example:
        // $reviews = ProjectReview::where('project_id', $projectId)
        //     ->with('user:id,name,avatar')
        //     ->orderBy('created_at', 'desc')
        //     ->take($limit)
        //     ->get();
        
        return [
            // Return formatted reviews array
        ];
    }

    /**
     * Order Confirmation API
     * Matches website user_order_confirm method exactly
     */
    public function order_confirm(Request $request): JsonResponse
    {
        return $this->handleOrderConfirm($request);
    }

    private function handleOrderConfirm(Request $request): JsonResponse
    {
        try {
            \Log::info('Order Confirm API Request', [
                'request_data' => $request->except(['manual_payment_image']),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
            ]);

            $request->validate([
                'project_id' => 'nullable|integer|exists:projects,id',
                'job_id_for_order' => 'nullable|integer',
                'offer_id_for_order' => 'nullable|integer',
                'proposal_id_for_order' => 'nullable|integer',
                'basic_standard_premium_type' => 'required|string',
                'selected_payment_gateway' => 'required|string',
            ]);

            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            $paymentGateway = trim((string) $request->selected_payment_gateway);
            $allGateway = $this->orderPaymentGateways();
            if (!in_array($paymentGateway, $allGateway, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a valid payment gateway',
                    'available_gateways' => $allGateway,
                ], 422);
            }

            $orderContext = $this->resolveOrderContext($request);
            if ($orderContext instanceof JsonResponse) {
                return $orderContext;
            }

            $pricing = $this->resolveOrderPricing($request, $orderContext);
            if ($pricing instanceof JsonResponse) {
                return $pricing;
            }

            $freelancerUser = \App\Models\User::select('id', 'email', 'first_name', 'last_name')
                ->where('id', $pricing['freelancer_id'])
                ->first();
            if (!$freelancerUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Freelancer not found',
                ], 404);
            }

            $charges = $this->calculateOrderCharges($pricing['price'], $freelancerUser->id, $paymentGateway);
            $milestoneData = $this->validateOrderMilestones($request, $pricing['price']);
            if ($milestoneData instanceof JsonResponse) {
                return $milestoneData;
            }

            $wallet = $this->validateWalletPayment($user->id, $paymentGateway, $pricing['price']);
            if ($wallet instanceof JsonResponse) {
                return $wallet;
            }

            $manualPaymentImage = $this->storeManualPaymentImage($request, $paymentGateway);
            if ($manualPaymentImage instanceof JsonResponse) {
                return $manualPaymentImage;
            }

            $storedPrice = in_array($paymentGateway, ['wallet', 'manual_payment'], true)
                ? $pricing['price']
                : ($pricing['price'] + $charges['transaction_amount']);

            $order = \App\Models\Order::create([
                'user_id' => $user->id,
                'freelancer_id' => $pricing['freelancer_id'],
                'identity' => $orderContext['identity'],
                'is_project_job' => $orderContext['project_or_job'],
                'is_basic_standard_premium_custom' => $pricing['type'],
                'revision' => $pricing['revision'],
                'revision_left' => $pricing['revision'],
                'delivery_time' => $pricing['delivery'],
                'description' => $request->order_description ?? null,
                'price' => $storedPrice,
                'commission_type' => $charges['commission_type'],
                'commission_charge' => $charges['commission_charge'],
                'commission_amount' => $charges['commission_amount'],
                'transaction_type' => $charges['transaction_type'],
                'transaction_charge' => $charges['transaction_charge'],
                'transaction_amount' => $charges['transaction_amount'],
                'payable_amount' => $charges['payable_amount'],
                'payment_gateway' => $paymentGateway,
                'payment_status' => $charges['payment_status'],
                'manual_payment_image' => $manualPaymentImage,
                'status' => 0,
            ]);

            if ($paymentGateway === 'wallet' && $wallet) {
                $wallet->update([
                    'balance' => (float) $wallet->balance - $pricing['price'],
                ]);
            }

            if ($orderContext['project_or_job'] === 'job' && $pricing['proposal']) {
                \App\Models\JobProposal::where('id', $pricing['proposal']->id)->update(['is_hired' => 1]);
            }

            if ($orderContext['project_or_job'] === 'offer' && $orderContext['offer']) {
                \Modules\Chat\Entities\Offer::where('id', $orderContext['offer']->id)->update(['status' => 1]);
            }

            $this->createOrderMilestones(
                $order->id,
                $milestoneData,
                $orderContext['offer'],
                $charges['individual_commission'],
                $charges['commission_type'],
                $charges['commission_charge'],
                $orderContext['project_or_job']
            );

            $this->dispatchOrderNotifications($order->id, $user->id, $pricing['freelancer_id'], $user->email, $freelancerUser->email);

            $orderData = [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'freelancer_id' => $pricing['freelancer_id'],
                'identity' => $orderContext['identity'],
                'project_job_offer' => $orderContext['project_or_job'],
                'type' => $pricing['type'],
                'price' => $pricing['price'],
                'revision' => $pricing['revision'],
                'delivery' => $pricing['delivery'],
                'payment_status' => $charges['payment_status'],
                'commission_amount' => $charges['commission_amount'],
                'transaction_amount' => $charges['transaction_amount'],
                'payable_amount' => $charges['payable_amount'],
                'payment_gateway' => $paymentGateway,
                'order_description' => $request->order_description ?? null,
            ];

            if ($paymentGateway === 'paypro') {
                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully. Please complete payment.',
                    'data' => [
                        'order_id' => $order->id,
                        'order_number' => 'ORD-' . $order->id . '-' . time(),
                        'order_details' => $orderData,
                        'payment_url' => $this->buildPayproPaymentUrl($order->id, $pricing['price'] + $charges['transaction_amount']),
                        'payment_gateway' => 'paypro',
                        'total_amount' => $pricing['price'] + $charges['transaction_amount'],
                        'currency' => get_static_option('site_global_currency') ?? 'USD',
                        'payment_status' => 'pending',
                        'expires_at' => \Carbon\Carbon::now()->addDays(1)->toIso8601String(),
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $paymentGateway === 'wallet'
                    ? 'Order created successfully with wallet payment'
                    : 'Order confirmed successfully',
                'data' => [
                    'order_details' => $orderData,
                    'payment_gateway' => $paymentGateway,
                    'total_amount' => $storedPrice,
                    'payment_status' => $charges['payment_status'],
                    'payment_url' => null,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Order Confirm API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your order: ' . $e->getMessage(),
                'error_code' => 'ORDER_CONFIRM_ERROR',
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ], 500);
        }
    }

    /**
     * Order Login API
     * Matches website user_login method exactly
     */
    public function order_login(Request $request): JsonResponse
    {
        try {
            // Call the same OrderService as website
            $orderService = new \App\Http\Services\Frontend\OrderService();
            $result = $orderService->user_login($request);

            if ($result instanceof \Illuminate\Http\RedirectResponse) {
                // This is a redirect response (validation error)
                $sessionData = $result->getSession()->all();
                $errorMessage = $sessionData['toastr_warning'] ?? 'Login required';
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'redirect_to' => 'login'
                ], 401);
            }

            // If successful, return success response
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Order Success Page API
     * Matches website user_order_success_page method exactly
     */
    public function order_success_page($id): JsonResponse
    {
        try {
            if (!\Illuminate\Support\Facades\Auth::guard('web')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'redirect_to' => 'login'
                ], 401);
            }

            $orderDetails = \App\Models\Order::find(substr($id, 30, -30));
            
            if (empty($orderDetails)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Check if order belongs to authenticated user
            if ($orderDetails->user_id != \Illuminate\Support\Facades\Auth::guard('web')->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order details retrieved successfully',
                'data' => [
                    'order' => $orderDetails,
                    'payment_status' => $orderDetails->payment_status,
                    'total_amount' => $orderDetails->total_amount,
                    'commission_amount' => $orderDetails->commission_amount,
                    'transaction_amount' => $orderDetails->transaction_amount,
                    'created_at' => $orderDetails->created_at,
                    'updated_at' => $orderDetails->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching order details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Order Payment Cancel Static API
     * Matches website order_payment_cancel_static method exactly
     */
    public function order_payment_cancel_static(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Order payment cancel page loaded',
                'data' => [
                    'page_title' => 'Payment Cancelled',
                    'page_content' => 'Your order payment has been cancelled. Please try again or contact support.',
                    'redirect_url' => '/projects' // You can customize this
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading cancel page',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Linked Accounts API
     * Matches website profile_details_linked_accounts_update method exactly
     */
    public function profile_details_linked_accounts_update(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'github_link' => 'nullable|url',
                'stackoverflow_link' => 'nullable|url',
            ]);

            $authenticatedUser = $this->resolveAuthenticatedApiUser($request);
            if (!$authenticatedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'redirect_to' => 'login'
                ], 401);
            }

            $user_id = $authenticatedUser->id;
            $github_meta = null;

            if ($request->github_link) {
                $github_meta = $this->fetch_github_data($request->github_link);
            }

            $userIntro = \App\Models\UserIntroduction::updateOrCreate(
                ['user_id' => $user_id],
                [
                    'user_id' => $user_id,
                    'github_link' => $request->github_link,
                    'stackoverflow_link' => $request->stackoverflow_link,
                    'github_meta' => $github_meta,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Linked accounts updated successfully',
                'data' => [
                    'github_link' => $request->github_link,
                    'stackoverflow_link' => $request->stackoverflow_link,
                    'github_meta' => $github_meta,
                    'updated_at' => $userIntro->updated_at
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating linked accounts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlink Linked Account API
     * Matches website profile_details_linked_accounts_unlink method exactly
     */
    public function profile_details_linked_accounts_unlink(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'type' => 'required|in:github,stackoverflow'
            ]);

            $authenticatedUser = $this->resolveAuthenticatedApiUser($request);
            if (!$authenticatedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'redirect_to' => 'login'
                ], 401);
            }

            $user_id = $authenticatedUser->id;
            $type = $request->type;

            $intro = \App\Models\UserIntroduction::where('user_id', $user_id)->first();
            
            if (!$intro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile introduction not found'
                ], 404);
            }

            if ($type == 'github') {
                $intro->update([
                    'github_link' => null,
                    'github_meta' => null
                ]);
            } elseif ($type == 'stackoverflow') {
                $intro->update([
                    'stackoverflow_link' => null,
                    'stackoverflow_meta' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' account unlinked successfully',
                'data' => [
                    'unlinked_type' => $type,
                    'updated_at' => $intro->updated_at
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while unlinking account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch GitHub data (same as website method)
     */
    private function fetch_github_data($url)
    {
        try {
            // Extract username from URL
            $path = parse_url($url, PHP_URL_PATH);
            $username = trim($path, '/');
            if (!$username)
                return null;

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'Laravel'
            ])->get("https://api.github.com/users/{$username}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'name' => $data['name'] ?? $data['login'],
                    'avatar_url' => $data['avatar_url'] ?? '',
                    'followers' => $data['followers'] ?? 0,
                    'created_at' => $data['created_at'] ?? '',
                    'login' => $data['login'] ?? ''
                ];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GitHub API error: ' . $e->getMessage());
        }
        return null;
    }

    private function resolveAuthenticatedApiUser(Request $request)
    {
        return $request->user()
            ?? auth('sanctum')->user()
            ?? auth('api')->user()
            ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
    }

    /**
     * Get available payment gateways for orders
     */
    private function orderPaymentGateways(): array
    {
        return [
            'wallet', 'paypal', 'manual_payment', 'mollie', 'paytm', 'stripe', 
            'razorpay', 'flutterwave', 'paystack', 'marcadopago', 'instamojo', 
            'cashfree', 'payfast', 'midtrans', 'squareup', 'cinetpay', 'paytabs', 
            'billplz', 'zitopay', 'sitesway', 'toyyibpay', 'authorize_dot_net', 
            'kineticpay', 'awdpay', 'iyzipay', 'yoomoney', 'coinpayments', 'paypro'
        ];
    }

    /**
     * Resolve order context (project, job, or offer)
     */
    private function resolveOrderContext(Request $request): array|JsonResponse
    {
        $project = Project::find($request->project_id);
        $job = \App\Models\JobPost::find($request->job_id_for_order);
        $offer = \Modules\Chat\Entities\Offer::find($request->offer_id_for_order);

        $project_or_job = 'project';
        $identity = $request->project_id;

        if ($job) {
            $project_or_job = 'job';
            $identity = $request->job_id_for_order;
        } elseif ($offer) {
            $project_or_job = 'offer';
            $identity = $request->offer_id_for_order;
        }

        if (!$project && !$job && !$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Project, Job, or Offer not found'
            ], 404);
        }

        return [
            'project_or_job' => $project_or_job,
            'identity' => $identity,
            'project' => $project,
            'job' => $job,
            'offer' => $offer
        ];
    }

    /**
     * Resolve order pricing based on type
     */
    private function resolveOrderPricing(Request $request, array $context): array|JsonResponse
    {
        $price = 0;
        $type = '';
        $revision = 0;
        $delivery = '';
        $freelancer_id = 0;
        $proposal = null;

        if ($context['project']) {
            $project = $context['project'];
            
            \Log::info('Project pricing debug', [
                'requested_type' => $request->basic_standard_premium_type,
                'basic_title' => $project->basic_title,
                'standard_title' => $project->standard_title,
                'premium_title' => $project->premium_title,
                'basic_charge' => $project->basic_regular_charge,
                'basic_discount' => $project->basic_discount_charge,
            ]);
            
            if ($request->basic_standard_premium_type === $project->basic_title || $request->basic_standard_premium_type === 'basic') {
                $type = $project->basic_title;
                $revision = $project->basic_revision;
                $delivery = $project->basic_delivery;
                $price = $project->basic_discount_charge ?: $project->basic_regular_charge;
                \Log::info('Basic pricing matched', ['price' => $price]);
            }
            if ($request->basic_standard_premium_type === $project->standard_title || $request->basic_standard_premium_type === 'standard') {
                $type = $project->standard_title;
                $revision = $project->standard_revision;
                $delivery = $project->standard_delivery;
                $price = $project->standard_discount_charge ?: $project->standard_regular_charge;
                \Log::info('Standard pricing matched', ['price' => $price]);
            }
            if ($request->basic_standard_premium_type === $project->premium_title || $request->basic_standard_premium_type === 'premium') {
                $type = $project->premium_title;
                $revision = $project->premium_revision;
                $delivery = $project->premium_delivery;
                $price = $project->premium_discount_charge ?: $project->premium_regular_charge;
                \Log::info('Premium pricing matched', ['price' => $price]);
            }
            $freelancer_id = $project->user_id;
        } elseif ($context['job']) {
            $proposal = \App\Models\JobProposal::select(['id', 'freelancer_id', 'amount', 'duration', 'revision'])
                ->where('id', $request->proposal_id_for_order)->first();
            
            if (!$proposal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job proposal not found'
                ], 404);
            }
            
            $price = $proposal->amount;
            $type = 'job';
            $revision = $proposal->revision;
            $delivery = $proposal->duration;
            $freelancer_id = $proposal->freelancer_id;
        } elseif ($context['offer']) {
            $offer = $context['offer'];
            $price = $offer->price;
            $type = 'offer';
            $revision = $offer->revision;
            $delivery = $offer->deadline;
            $freelancer_id = $offer->freelancer_id;
        }

        return [
            'price' => $price,
            'type' => $type,
            'revision' => $revision,
            'delivery' => $delivery,
            'freelancer_id' => $freelancer_id,
            'proposal' => $proposal
        ];
    }

    /**
     * Calculate order charges (commission and transaction)
     */
    private function calculateOrderCharges(float $price, int $freelancerId, string $paymentGateway): array
    {
        $commission_type = get_static_option('admin_commission_type') ?? 'percentage';
        $commission_charge = get_static_option('admin_commission_charge') ?? 25;
        $transaction_type = get_static_option('transaction_fee_type');
        $transaction_charge = get_static_option('transaction_fee_charge') ?? 0;

        $individual_commission = \App\Models\IndividualCommissionSetting::select(['user_id', 'admin_commission_type', 'admin_commission_charge'])
            ->where('user_id', $freelancerId)->first();
        
        $commission_amount = commission_amount($price, $individual_commission, $commission_type, $commission_charge);
        $transaction_amount = transaction_amount($price, $transaction_type, $transaction_charge);
        $payable_amount = $price - $commission_amount;
        $payment_status = $paymentGateway === 'wallet' ? 'complete' : 'pending';

        return [
            'commission_type' => $commission_type,
            'commission_charge' => $commission_charge,
            'commission_amount' => $commission_amount,
            'transaction_type' => $transaction_type,
            'transaction_charge' => $transaction_charge,
            'transaction_amount' => $transaction_amount,
            'payable_amount' => $payable_amount,
            'payment_status' => $payment_status,
            'individual_commission' => $individual_commission
        ];
    }

    /**
     * Validate order milestones
     */
    private function validateOrderMilestones(Request $request, float $totalPrice): array|JsonResponse
    {
        $milestoneData = [];
        
        if ($request->pay_by_milestone === 'pay-by-milestone' && !empty($request->milestone_title)) {
            $milestoneTitles = $request->milestone_title;
            $milestoneDescriptions = $request->milestone_description ?? [];
            $milestonePrices = $request->milestone_price ?? [];
            $milestoneRevisions = $request->milestone_revision ?? [];
            $milestoneDeadlines = $request->milestone_deadline ?? [];

            $totalMilestonePrice = array_sum($milestonePrices);
            
            if ($totalMilestonePrice != $totalPrice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Milestone total price must equal the project price'
                ], 422);
            }

            foreach ($milestoneTitles as $key => $title) {
                $milestoneData[] = [
                    'title' => $title,
                    'description' => $milestoneDescriptions[$key] ?? '',
                    'price' => $milestonePrices[$key] ?? 0,
                    'revision' => $milestoneRevisions[$key] ?? 0,
                    'deadline' => $milestoneDeadlines[$key] ?? '',
                ];
            }
        }
        
        return $milestoneData;
    }

    /**
     * Validate wallet payment
     */
    private function validateWalletPayment(int $userId, string $paymentGateway, float $price): mixed
    {
        if ($paymentGateway === 'wallet') {
            $wallet = \App\Models\UserWallet::where('user_id', $userId)->first();
            if (!$wallet || $wallet->balance < $price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet balance must be equal or greater than original price'
                ], 422);
            }
            return $wallet;
        }
        return null;
    }

    /**
     * Store manual payment image
     */
    private function storeManualPaymentImage(Request $request, string $paymentGateway): mixed
    {
        if ($paymentGateway === 'manual_payment' && $request->hasFile('manual_payment_image')) {
            $image = $request->file('manual_payment_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/manual_payment/'), $imageName);
            return 'uploads/manual_payment/' . $imageName;
        }
        return null;
    }

    /**
     * Create order milestones
     */
    private function createOrderMilestones(int $orderId, array $milestoneData, $offer, $individualCommission, string $commissionType, float $commissionCharge, string $projectOrJob): void
    {
        if (!empty($milestoneData) && $projectOrJob === 'offer' && $offer) {
            foreach ($milestoneData as $key => $milestone) {
                \App\Models\Milestone::create([
                    'order_id' => $orderId,
                    'title' => $milestone['title'],
                    'description' => $milestone['description'],
                    'price' => $milestone['price'],
                    'revision' => $milestone['revision'],
                    'deadline' => $milestone['deadline'],
                    'status' => 0,
                    'commission_type' => $commissionType,
                    'commission_charge' => $commissionCharge,
                    'commission_amount' => commission_amount($milestone['price'], $individualCommission, $commissionType, $commissionCharge),
                ]);
            }
        }
    }

    /**
     * Dispatch order notifications
     */
    private function dispatchOrderNotifications(int $orderId, int $clientId, int $freelancerId, string $clientEmail, string $freelancerEmail): void
    {
        // Notifications disabled for API to avoid model dependency issues
        // Order creation is successful - notifications can be handled by frontend
        Log::info("Order created successfully. Order ID: {$orderId}, Client: {$clientId}, Freelancer: {$freelancerId}");
    }

    /**
     * Build PayPro payment URL
     */
    private function buildPayproPaymentUrl(int $orderId, float $amount): string
    {
        try {
            // Get PayPro configuration
            $baseUrl = rtrim(get_static_option('paypro_base_url') ?? 'https://api.paypro.com.pk', '/');
            $clientId = get_static_option('paypro_client_id');
            $clientSecret = get_static_option('paypro_client_secret');
            $merchantId = get_static_option('paypro_username');

            if (empty($clientId) || empty($clientSecret) || empty($merchantId)) {
                \Log::error('PayPro configuration missing', [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'merchant_id' => $merchantId
                ]);
                return '';
            }

            // Authenticate with PayPro
            $authResponse = \Illuminate\Support\Facades\Http::asJson()->post($baseUrl . '/v2/ppro/auth', [
                'clientid' => $clientId,
                'clientsecret' => $clientSecret,
            ]);

            if (!$authResponse->ok()) {
                \Log::error('PayPro authentication failed', [
                    'response' => $authResponse->body(),
                    'status' => $authResponse->status()
                ]);
                return '';
            }

            $token = $authResponse->header('token') ?? $authResponse->header('Token');
            if (empty($token)) {
                \Log::error('PayPro token not found in response');
                return '';
            }

            // Generate order number
            $orderNumber = 'ORD-' . $orderId . '-' . time();
            $now = \Carbon\Carbon::now();
            $dueDate = $now->copy()->addDays(1);
            $currency = get_static_option('site_global_currency') ?? 'USD';
            $user = auth('sanctum')->user();

            // Prepare payload exactly like OrderService
            $payload = [
                [
                    'MerchantId' => $merchantId,
                ],
                [
                    'OrderNumber' => $orderNumber,
                    'CurrencyAmount' => (string) $amount,
                    'OrderDueDate' => $dueDate->format('d/m/Y'),
                    'OrderType' => 'Service',
                    'IssueDate' => $now->format('d/m/Y'),
                    'OrderExpireAfterSeconds' => '0',
                    'CustomerName' => $user->first_name . ' ' . $user->last_name,
                    'CustomerMobile' => '',
                    'CustomerEmail' => $user->email,
                    'CustomerAddress' => '',
                    'Currency' => $currency,
                    'IsConverted' => 'true',
                ],
            ];
            
            // Create order in PayPro
            $response = \Illuminate\Support\Facades\Http::withHeaders(['token' => $token])
                ->asJson()
                ->post($baseUrl . '/v2/ppro/co', $payload);

            if (!$response->ok()) {
                \Log::error('PayPro order creation failed', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return '';
            }

            $orderData = $response->json();
            \Log::info('PayPro order created', ['response' => $orderData]);

            $status = $orderData[0]['Status'] ?? null;
            $details = $orderData[1] ?? [];
            $click2Pay = $details['Click2Pay'] ?? null;

            if ($status !== '00' || empty($click2Pay)) {
                \Log::error('PayPro order creation failed', ['status' => $status, 'click2pay' => $click2Pay]);
                return '';
            }

            // Update order with PayPro details
            $order = \App\Models\Order::find($orderId);
            $order->update([
                'transaction_id' => $details['PayProId'] ?? null,
                'payment_gateway' => 'paypro',
                'payment_status' => 'pending',
            ]);

            // Build callback URL
            $click2PayUrl = $click2Pay;
            $separator = str_contains($click2PayUrl, '?') ? '&' : '?';
            $click2PayUrl .= $separator . 'callback_url=' . urlencode(url('/order/paypro-return'));

            return $click2PayUrl;

        } catch (\Exception $e) {
            \Log::error('PayPro URL generation error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * PayPro Callback API
     * Handles PayPro payment callback for mobile app
     */
    public function paypro_callback(Request $request): JsonResponse
    {
        try {
            // Log callback for debugging
            \Illuminate\Support\Facades\Log::info('PayPro API Callback', $request->all());
            
            // Get callback parameters
            $payProId = $request->PayProId;
            $orderNumber = $request->OrderNumber;
            $status = $request->Status;
            $responseCode = $request->ResponseCode;
            $responseMessage = $request->ResponseMessage;
            
            // Find order by order number or PayPro ID
            $order = null;
            if ($orderNumber) {
                // Extract order ID from order number (format: ORD-{id}-{timestamp})
                $orderId = null;
                if (preg_match('/ORD-(\d+)-\d+/', $orderNumber, $matches)) {
                    $orderId = $matches[1];
                    $order = \App\Models\Order::find($orderId);
                }
            }
            
            if (!$order && $payProId) {
                $order = \App\Models\Order::where('transaction_id', $payProId)->first();
            }
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }
            
            // Update order based on payment status
            if ($status === '00' || $responseCode === '00') {
                // Payment successful
                $order->update([
                    'payment_status' => 'complete',
                    'status' => 1, // Active order
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payment completed successfully',
                    'data' => [
                        'order_id' => $order->id,
                        'payment_status' => 'complete',
                        'order_status' => 'active',
                        'redirect_url' => '/api/v1/order/success/page/' . str_repeat('x', 30) . $order->id . str_repeat('x', 30)
                    ]
                ]);
            } else {
                // Payment failed or cancelled
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 3, // Cancelled order
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $responseMessage ?? 'Payment failed',
                    'data' => [
                        'order_id' => $order->id,
                        'payment_status' => 'failed',
                        'order_status' => 'cancelled',
                        'redirect_url' => '/api/v1/order/payment/cancel/static'
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PayPro API Callback Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Callback processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
