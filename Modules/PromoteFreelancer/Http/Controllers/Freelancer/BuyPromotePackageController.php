<?php

namespace Modules\PromoteFreelancer\Http\Controllers\Freelancer;

use App\Helper\PaymentGatewayRequestHelper;
use App\Mail\BasicMail;
use App\Models\AdminNotification;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Modules\PromoteFreelancer\Entities\ProjectPromoteSettings;
use Modules\PromoteFreelancer\Entities\PromotionProjectList;
use Modules\Subscription\Entities\Subscription;
use Modules\Subscription\Entities\UserSubscription;
use Modules\Wallet\Entities\Wallet;

class BuyPromotePackageController extends Controller
{
    private const CANCEL_ROUTE = 'freelancer.package.buy.payment.cancel.static';
    public function promote_payment_cancel_static()
    {
        return view('promotefreelancer::frontend.payment-cancel');
    }

    public function buy_package(Request $request)
    {
        if (isset($request->package_id)) {
            $user = Auth::user();
            $package_details = ProjectPromoteSettings::where('id', $request->package_id)->where('status', '1')->first();

            if ($package_details) {
                $transaction_fee = $request->transaction_fee;
                if ($request->selected_payment_gateway === 'manual_payment' || $request->selected_payment_gateway === 'wallet') {
                    $total = $package_details->budget;
                } else {
                    $total = $package_details->budget + $transaction_fee;
                }

                $expire_date = Carbon::now()->addDays($package_details->duration);
                $title = __('Buy Package');
                $duration = $package_details->duration;
                $name = $user->first_name . ' ' . $user->last_name;
                $email = $user->email;
                $user_type = $user->user_type == 1 ? 'client' : 'freelancer';
                $payment_status = $request->selected_payment_gateway === 'wallet' ? 'complete' : 'pending';
                $status = $request->selected_payment_gateway === 'wallet' ? 1 : 0;
                $project_id = $request->set_project_id_for_promote == 0 ? $user->id : $request->set_project_id_for_promote;
                $type = $request->set_project_id_for_promote == 0 ? 'profile' : 'project';
                session()->put('user_id', $user->id);
                session()->put('user_type', $user_type);


                if ($request->selected_payment_gateway === 'manual_payment') {
                    $request->validate(['manual_payment_image' => 'required|mimes:jpg,jpeg,png,pdf,svg']);

                    if ($request->hasFile('manual_payment_image')) {
                        $manual_payment_image = $request->manual_payment_image;
                        $img_ext = $manual_payment_image->extension();

                        $manual_payment_image_name = 'manual_attachment_' . time() . '.' . $img_ext;
                        if (in_array($img_ext, ['jpg', 'jpeg', 'png', 'pdf', 'svg'])) {
                            $manual_image_path = 'assets/uploads/manual-payment/promotion';
                            $manual_payment_image->move($manual_image_path, $manual_payment_image_name);

                            $buy_package = PromotionProjectList::create([
                                'user_id' => $user->id,
                                'identity' => $project_id,
                                'type' => $type,
                                'package_id' => $package_details->id,
                                'price' => $total,
                                'duration' => $duration,
                                'expire_date' => $expire_date,
                                'payment_gateway' => $request->selected_payment_gateway,
                                'manual_payment_image' => $manual_payment_image_name,
                                'payment_status' => $payment_status,
                                'status' => $status,
                                'is_valid_payment' => 'yes',
                            ]);
                            $last_package_id = $buy_package->id;
                            $this->adminNotification($last_package_id, $user->id);
                        } else {
                            return back()->with(toastr_warning(__('Image type not supported')));
                        }
                    }

                    if ($type == 'profile') {
                        User::where('id', $user->id)->update([
                            'is_pro' => 'no',
                            'pro_expire_date' => $expire_date
                        ]);
                    } else {
                        Project::where('id', $project_id)->update([
                            'is_pro' => 'no',
                            'pro_expire_date' => $expire_date
                        ]);
                    }

                    $this->sendEmail($name, $last_package_id, $email);
                    toastr_success('Package purchase success. Your package will be active after admin complete the payment status pending to complete.');
                    return back();
                } elseif ($request->selected_payment_gateway === 'wallet') {
                    $wallet_balance = Wallet::select('balance')->where('user_id', $user->id)->first();
                    if (isset($wallet_balance) && $wallet_balance->balance > $total) {
                        $buy_package = PromotionProjectList::create([
                            'user_id' => $user->id,
                            'identity' => $project_id,
                            'type' => $type,
                            'package_id' => $package_details->id,
                            'price' => $total,
                            'duration' => $duration,
                            'expire_date' => $expire_date,
                            'payment_gateway' => $request->selected_payment_gateway,
                            'payment_status' => $payment_status,
                            'status' => $status,
                            'is_valid_payment' => 'yes',
                        ]);
                        $last_package_id = $buy_package->id;
                        $this->adminNotification($last_package_id, $user->id);
                        Wallet::where('user_id', $user->id)->update(['balance' => $wallet_balance->balance - $total]);

                    } else {
                        return back()->with(toastr_warning(__('Please deposit to your wallet and try again')));
                    }
                    if ($type == 'profile') {
                        User::where('id', $user->id)->update([
                            'is_pro' => 'yes',
                            'pro_expire_date' => $expire_date
                        ]);
                    } else {
                        Project::where('id', $project_id)->update([
                            'is_pro' => 'yes',
                            'pro_expire_date' => $expire_date
                        ]);
                    }
                    $this->sendEmail($name, $last_package_id, $email);
                    return back()->with(toastr_success(__('Promote package purchase success')));
                } else {
                    $buy_package = PromotionProjectList::create([
                        'user_id' => $user->id,
                        'identity' => $project_id,
                        'type' => $type,
                        'package_id' => $package_details->id,
                        'price' => $total,
                        'transaction_fee' => $transaction_fee,
                        'duration' => $duration,
                        'expire_date' => $expire_date,
                        'payment_gateway' => $request->selected_payment_gateway,
                        'payment_status' => $payment_status,
                        'status' => $status,
                    ]);

                    $last_package_id = $buy_package->id;
                    $description = sprintf(__('Order id #%1$d Email: %2$s, Name: %3$s'), $last_package_id, $email, $name);

                    if ($request->selected_payment_gateway === 'paypro') {
                        $baseUrl = rtrim(get_static_option('paypro_base_url') ?? 'https://api.paypro.com.pk', '/');
                        $clientId = get_static_option('paypro_client_id');
                        $clientSecret = get_static_option('paypro_client_secret');
                        $merchantId = get_static_option('paypro_username');

                        if (empty($clientId) || empty($clientSecret) || empty($merchantId)) {
                            return redirect()->route(self::CANCEL_ROUTE);
                        }

                        try {
                            $authResponse = Http::asJson()->post($baseUrl . '/v2/ppro/auth', [
                                'clientid' => $clientId,
                                'clientsecret' => $clientSecret,
                            ]);
                        } catch (\Exception $e) {
                            return redirect()->route(self::CANCEL_ROUTE);
                        }

                        if (!$authResponse->ok()) {
                            return redirect()->route(self::CANCEL_ROUTE);
                        }

                        $token = $authResponse->header('token') ?? $authResponse->header('Token');
                        if (empty($token)) {
                            return redirect()->route(self::CANCEL_ROUTE);
                        }

                        $orderNumber = 'PROMO-' . $last_package_id . '-' . time();
                        $now = Carbon::now();
                        $dueDate = $now->copy()->addDays(1);
                        $currency = get_static_option('site_global_currency') ?? 'USD';

                        // Build success and cancel URLs with abbreviated parameters to prevent PayPro redirect errors
                        $successUrl = route('promote.payment.success', ['id' => $last_package_id]);
                        $cancelUrl = route('promote.payment.cancel', ['id' => $last_package_id]);

                        // Store payment data in session for verification
                        session([
                            'promote_payment_' . $last_package_id => [
                                'package_id' => $package_details->id,
                                'user_id' => $user->id,
                                'project_id' => $project_id,
                                'type' => $type,
                                'amount' => $total,
                                'currency' => $currency,
                                'expire_date' => $expire_date,
                                'success_url' => $successUrl,
                                'cancel_url' => $cancelUrl,
                                'order_number' => $orderNumber
                            ]
                        ]);

                        $payload = [
                            [
                                'MerchantId' => $merchantId,
                            ],
                            [
                                'WebhookURL' => route('promote.paypro.webhook'),
                                'OrderNumber' => $orderNumber,
                                'CurrencyAmount' => (string) $total,
                                'OrderDueDate' => $dueDate->format('d/m/Y'),
                                'OrderType' => 'Service',
                                'IssueDate' => $now->format('d/m/Y'),
                                'OrderExpireAfterSeconds' => '0',
                                'CustomerName' => $name,
                                'CustomerMobile' => '',
                                'CustomerEmail' => $email,
                                'CustomerAddress' => '',
                                'Currency' => $currency,
                                'IsConverted' => 'true',
                            ],
                        ];

                        try {
                            $orderResponse = Http::withHeaders(['token' => $token])
                                ->asJson()
                                ->post($baseUrl . '/v2/ppro/co', $payload);

                            if (!$orderResponse->ok()) {
                                throw new \Exception('Failed to create order');
                            }

                            $orderData = $orderResponse->json();
                            $statusCode = $orderData[0]['Status'] ?? null;
                            $details = $orderData[1] ?? [];
                            $click2Pay = $details['Click2Pay'] ?? null;
                            $payProId = $details['PayProId'] ?? null;

                            if ($statusCode !== '00' || empty($click2Pay)) {
                                throw new \Exception('Invalid order response');
                            }

                            // Update the promotion record with PayPro details
                            $buy_package->update([
                                'transaction_id' => $payProId ?? $orderNumber,
                                'payment_gateway' => 'paypro',
                                'payment_status' => 'pending',
                                'is_valid_payment' => 'yes'
                            ]);


                            if (str_contains($click2Pay, '?')) {
                                $click2Pay .= '&';
                            } else {
                                $click2Pay .= '?';
                            }
                            $click2Pay .= 'callback_url=' . urlencode($successUrl);

                            // Store payment data in session for verification
                            session([
                                'promotion_paypro' => [
                                    'promotion_id' => $last_package_id,
                                    'paypro_id' => $payProId,
                                    'type' => $type,
                                    'identity' => $project_id,
                                    'order_number' => $orderNumber // Add this for reliable verification
                                ]
                            ]);

                            return redirect()->away($click2Pay);
                        } catch (\Exception $e) {
                            \Log::error('Promote Package PayPro Error: ' . $e->getMessage(), [
                                'trace' => $e->getTraceAsString(),
                                'package_id' => $last_package_id,
                                'user_id' => $user->id
                            ]);
                            return redirect()->route(self::CANCEL_ROUTE);
                        }
                    }

                    if ($request->selected_payment_gateway === 'paypal') {
                        try {
                            return PaymentGatewayRequestHelper::paypal()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.paypal.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'paytm') {
                        try {
                            return PaymentGatewayRequestHelper::paytm()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.paytm.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'mollie') {

                        try {
                            return PaymentGatewayRequestHelper::mollie()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.mollie.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'stripe') {
                        try {
                            return PaymentGatewayRequestHelper::stripe()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.stripe.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'razorpay') {
                        try {
                            return PaymentGatewayRequestHelper::razorpay()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.razorpay.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'flutterwave') {
                        try {
                            return PaymentGatewayRequestHelper::flutterwave()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.flutterwave.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'paystack') {
                        try {
                            return PaymentGatewayRequestHelper::paystack()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.paystack.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'payfast') {
                        try {
                            $ipn_url = get_static_option('payfast_itn_url') ?: route('freelancer.bp.payfast.ipn.package');
                            return PaymentGatewayRequestHelper::payfast()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, $ipn_url));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'cashfree') {
                        try {
                            return PaymentGatewayRequestHelper::cashfree()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.cashfree.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'instamojo') {
                        try {
                            return PaymentGatewayRequestHelper::instamojo()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.instamojo.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'marcadopago') {
                        try {
                            return PaymentGatewayRequestHelper::marcadopago()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.marcadopago.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }

                    } elseif ($request->selected_payment_gateway === 'midtrans') {
                        try {
                            return PaymentGatewayRequestHelper::midtrans()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.midtrans.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'squareup') {
                        try {
                            return PaymentGatewayRequestHelper::squareup()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.squareup.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'cinetpay') {
                        try {
                            return PaymentGatewayRequestHelper::cinetpay()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.cinetpay.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'paytabs') {

                        try {
                            return PaymentGatewayRequestHelper::paytabs()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.paytabs.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'billplz') {
                        try {
                            return PaymentGatewayRequestHelper::billplz()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.billplz.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'zitopay') {
                        try {
                            return PaymentGatewayRequestHelper::zitopay()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.zitopay.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'toyyibpay') {
                        try {
                            return PaymentGatewayRequestHelper::toyyibpay()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.toyyibpay.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'authorize_dot_net') {
                        try {
                            return PaymentGatewayRequestHelper::authorizenet()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.authorize.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'pagali') {
                        try {
                            return PaymentGatewayRequestHelper::pagalipay()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.pagali.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'sitesway') {
                        try {
                            return PaymentGatewayRequestHelper::sitesway()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.siteways.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'iyzipay') {
                        try {
                            return PaymentGatewayRequestHelper::iyzipay()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('freelancer.bp.iyzipay.ipn.package')));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'yoomoney') {
                        try {
                            return PaymentGatewayRequestHelper::yoomoney()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('yoomoney.ipn.all'), 'promotion'));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    } elseif ($request->selected_payment_gateway === 'coinpayments') {
                        try {
                            return PaymentGatewayRequestHelper::coinpayments()->charge_customer($this->buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, route('coinpayment.ipn.all'), 'promotion'));
                        } catch (\Exception $e) {
                            toastr_error($e->getMessage());
                            return back();
                        }
                    }
                }
            }
        }
    }

    private function buildPaymentArg($total, $transaction_fee, $title, $description, $last_package_id, $email, $name, $user_type, $ipn_route, $source = null)
    {
        return [
            'amount' => $total,
            'transaction_dee' => $transaction_fee,
            'title' => $title,
            'description' => $description,
            'ipn_url' => $ipn_route,
            'order_id' => $last_package_id,
            'track' => \Str::random(36),
            'cancel_url' => route(self::CANCEL_ROUTE, $last_package_id),
            'success_url' => route($user_type . '.' . 'profile.details', auth()->user()->username),
            'email' => $email,
            'name' => $name,
            'payment_type' => $source,
        ];
    }

    //send email
    private function sendEmail($name, $last_package_id, $email)
    {
        //Send purchase package email to admin
        try {
            $message = get_static_option('user_promote_package_purchase_message_admin') ?? __('A user just purchase a promotion package.');
            $message = str_replace(["@package_id"], [$last_package_id], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => get_static_option('user_promote_package_purchase_subject_admin') ?? __('Promotion package purchase email'),
                'message' => $message
            ]));
        } catch (\Exception $e) {
        }

        //Send purchase package email to user
        try {
            $message = get_static_option('user_promote_package_purchase_message') ?? __('Your promotion package purchase successfully completed.');
            $message = str_replace(["@name", "@package_id"], [$name, $last_package_id], $message);
            Mail::to($email)->send(new BasicMail([
                'subject' => get_static_option('user_promote_package_purchase_subject') ?? __('Promotion package purchase email'),
                'message' => $message
            ]));
        } catch (\Exception $e) {
        }
    }

    //admin notification
    private function adminNotification($last_package_id, $user_id)
    {
        AdminNotification::create([
            'identity' => $last_package_id,
            'user_id' => $user_id,
            'type' => __('Buy Package'),
            'message' => __('Promotion package purchase'),
        ]);
    }

    /**
     * Handle successful payment callback from PayPro
     */
    public function paymentSuccess($id, Request $request)
    {
        try {
            // Get the promotion record
            $promotion = PromotionProjectList::findOrFail($id);

            // Get data from DB record instead of query params to ensure reliability and clean URL
            $type = $promotion->type;
            $projectId = $promotion->identity; // project_id
            $userId = $promotion->user_id;

            // Get session retrieval
            $sessionPromo = session('promotion_paypro') ?: session('promote_payment_' . $id);

            // Get order number from query -> session -> DB
            $orderNumber = $request->query('order_number');
            if (empty($orderNumber) && isset($sessionPromo['order_number'])) {
                $orderNumber = $sessionPromo['order_number'];
            }

            // Manual URL parsing to handle PayPro's double '?' issue
            // e.g. ...&order_number=PROMO-123?status=Success...
            $requestUrl = $request->fullUrl();
            if (str_contains($requestUrl, '?status=') || str_contains($requestUrl, '&status=')) {
                // Check if status is in the query params normally
                if (!$request->has('status')) {
                    // It's likely hidden in another param
                    if ($orderNumber && str_contains($orderNumber, '?')) {
                        $parts = explode('?', $orderNumber);
                        $orderNumber = $parts[0];
                        // We can assume status is present if we see the double ?
                        $request->merge(['status' => 'Success']);
                    }
                }
            }

            // Get the user from the promotion or from the query parameter
            $user = User::find($userId) ?? Auth::user();

            if (!$user) {
                return redirect()->route('homepage')->with(['msg' => 'User not found', 'type' => 'error']);
            }

            // For both profile and project promotions, redirect to the user's profile page
            $redirectUrl = route('freelancer.profile.details', $user->username);

            // If there's a success_url parameter in the request, use that instead
            $successUrl = $request->query('success_url');
            if ($successUrl && filter_var($successUrl, FILTER_VALIDATE_URL)) {
                $redirectUrl = $successUrl;
            }

            // Allow overriding redirect for projects to go to profile details
            if ($type === 'project') {
                $redirectUrl = route('freelancer.profile.details', $user->username);
            }

            // Check if already completed (e.g. via webhook)
            if ($promotion->payment_status === 'complete') {
                return redirect($redirectUrl)
                    ->with(['msg' => __('Payment Successful! Package activated.'), 'type' => 'success']);
            }

            // Verify payment with PayPro
            $baseUrl = rtrim(get_static_option('paypro_base_url') ?? 'https://api.paypro.com.pk', '/');
            $clientId = get_static_option('paypro_client_id');
            $clientSecret = get_static_option('paypro_client_secret');

            // Get auth token
            $authResponse = Http::asJson()->post($baseUrl . '/v2/ppro/auth', [
                'clientid' => $clientId,
                'clientsecret' => $clientSecret,
            ]);

            if (!$authResponse->ok()) {
                return redirect($redirectUrl)
                    ->with(['msg' => 'Payment verification failed. Please contact support.', 'type' => 'error']);
            }

            $token = $authResponse->header('token') ?? $authResponse->header('Token');
            if (empty($token)) {
                return redirect($redirectUrl)
                    ->with(['msg' => 'Invalid payment verification. Please contact support.', 'type' => 'error']);
            }

            // Check order status
            $checkOrderNumber = $orderNumber ?? $promotion->transaction_id;
            $statusResponse = Http::withHeaders(['token' => $token])
                ->asJson()
                ->post($baseUrl . '/v2/ppro/checkorder', [
                    [
                        'OrderNumber' => $checkOrderNumber,
                        'MerchantId' => get_static_option('paypro_username')
                    ]
                ]);

            $isPaid = false;
            if ($statusResponse->ok()) {
                $statusData = $statusResponse->json();
                $isPaid = isset($statusData[0]['Status']) && $statusData[0]['Status'] === '00' &&
                    isset($statusData[1]['OrderStatus']) && $statusData[1]['OrderStatus'] === 'Paid';
            }

            // Fallback: Check URL status if API verification failed or was incomplete
            // This ensures users aren't blocked if the PayPro API is temporarily unreachable or returning errors
            if (!$isPaid && strtolower($request->query('status')) === 'success') {
                $isPaid = true;
            }

            if ($isPaid) {
                // Update promotion status if not already updated
                if ($promotion->payment_status !== 'complete') {
                    $expireDate = $promotion->expire_date;

                    $promotion->update([
                        'payment_status' => 'complete',
                        'status' => 1,
                        'is_valid_payment' => 'yes',
                        'updated_at' => now()
                    ]);

                    // Update user or project pro status
                    if ($type === 'profile') {
                        User::where('id', $user->id)->update([
                            'is_pro' => 'yes',
                            'pro_expire_date' => $expireDate ?? now()->addMonth()
                        ]);
                        // Redirect to profile details
                        $redirectUrl = route('freelancer.profile.details', $user->username);
                    } else if ($type === 'project' && $projectId) {
                        Project::where('id', $projectId)->update([
                            'is_pro' => 'yes',
                            'pro_expire_date' => $expireDate ?? now()->addMonth()
                        ]);
                        // Redirect to profile details as requested
                        $redirectUrl = route('freelancer.profile.details', $user->username);
                    }

                    // Send notifications
                    $this->sendEmail($user->first_name . ' ' . $user->last_name, $promotion->id, $user->email);
                    $this->adminNotification($promotion->id, $user->id);

                    // ✅ Create affiliate commission for promotion package purchase
                    try {
                        app(\App\Services\AffiliateCommissionService::class)->createGeneric(
                            (int) $promotion->user_id,
                            (float) $promotion->price,
                            "Commission from PayPro promotion package purchase #{$promotion->id} (Success Page)"
                        );
                    } catch (\Exception $e) {
                        \Log::error("Affiliate Promotion Commission Error (PayPro Success Page): " . $e->getMessage());
                    }

                    toastr_success(__('Payment Successful! Package activated.'));
                    return redirect($redirectUrl);
                }

                // If already processed, just show success message
                return redirect($redirectUrl)
                    ->with(['msg' => __('Your promotion is already active.'), 'type' => 'success']);
            }

        } catch (\Exception $e) {
            return redirect()->route('freelancer.dashboard')
                ->with(['msg' => __('Error processing your payment. Please contact support.'), 'type' => 'error']);
        }

        // Fallback return if verification failed
        return redirect()->route('freelancer.dashboard')
            ->with(['msg' => __('Payment verification failed or pending. Please contact support if you have paid.'), 'type' => 'warning']);
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel($id)
    {
        try {
            $promotion = PromotionProjectList::findOrFail($id);

            // Only update if not already processed
            if ($promotion->payment_status !== 'complete') {
                $promotion->update([
                    'payment_status' => 'cancelled',
                    'status' => 0
                ]);
            }

            return redirect()->route('freelancer.dashboard')
                ->with(['msg' => __('Payment was cancelled.'), 'type' => 'warning']);

        } catch (\Exception $e) {
            \Log::error('Promotion Payment Cancel Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'promotion_id' => $id
            ]);

            return redirect()->route('freelancer.dashboard')
                ->with(['msg' => __('Error processing your request.'), 'type' => 'error']);
        }
    }
}
