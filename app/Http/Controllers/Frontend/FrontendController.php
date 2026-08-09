<?php

namespace App\Http\Controllers\Frontend;

use App\Blog;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\StaticOption;
use App\Models\User;
use App\Models\Order;
use App\Exceptions\WalletInsufficientBalance;
use App\Exceptions\WalletNotFoundException;
use App\Services\AdService;
use Carbon\Carbon;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Modules\Pages\Entities\Page;
use Illuminate\Support\Facades\File;
use App\Models\OneRupeeDraw;


class FrontendController extends Controller
{
    public function home_page()
    {
        $home_page_id = get_static_option('home_page');
        
        // Cache page details for 1 hour
        $page_details = Cache::remember('home_page_details_' . $home_page_id, 3600, function() use ($home_page_id) {
            return Page::find($home_page_id);
        });
        
        if (empty($page_details)) {
        // show any notice or
        }

        // Cache top freelancers for 30 minutes
        $topFreelancers = Cache::remember('top_freelancers_home', 1800, function() {
            return Order::where('payment_status', 'complete')
                ->selectRaw('freelancer_id, COUNT(*) as total_orders')
                ->groupBy('freelancer_id')
                ->orderByDesc('total_orders')
                ->take(6)
                ->with('freelancer:id,first_name,last_name,image,username')
                ->get()
                ->pluck('freelancer');
        });


        return view('frontend.pages.frontend-home-3', compact('page_details', 'topFreelancers'));
    }

    public function dynamic_single_page($slug)
    {
        $page_post = Page::where('slug', $slug)->first();

        $user_details = User::where(['user_type' => 0, 'username' => $slug])->first();
        $preserved_pages = [
            'home_page',
            'service_list_page',
            'blog_page',
        ];

        $static_option = StaticOption::whereIn('option_name', $preserved_pages)->get()->mapWithKeys(function ($item) {
            return [$item->option_name => $item->option_value];
        })->toArray();

        $pages_id_slugs = Page::whereIn('id', array_values($static_option))->get()->mapWithKeys(function ($item) {
            return [$item->id => $item->slug];
        })->toArray();

        if (in_array($slug, $pages_id_slugs) && $slug === $pages_id_slugs[$static_option['home_page']]) {
            return redirect()->route('homepage');
        }
        elseif (in_array($slug, $pages_id_slugs) && $slug === $pages_id_slugs[$static_option['blog_page']]) {
            $all_blogs = Blog::where('status', 'publish')->orderBy('id', 'desc')->paginate(6);
            return view('frontend.pages.blog.blog-static', [
                'all_blogs' => $all_blogs,
                'page_post' => $page_post,
            ]);
        }
        elseif (in_array($slug, $pages_id_slugs) && $slug === $pages_id_slugs[$static_option['service_list_page']]) {
            $all_services = Service::with('reviews')->where(['status' => 1, 'is_service_on' => 1])->orderBy('id', 'desc')->paginate(6);
            return view('frontend.pages.services.service-static', [
                'all_services' => $all_services,
                'page_post' => $page_post,
            ]);
        }
        elseif (!is_null($user_details)) {
            // dd('sdfsad');
            return $this->_user_profile($user_details);
        }

        $page_type = 'page';
        if (!is_null($page_post)) {
            return view('frontend.pages.dynamic.dynamic-single', compact(['page_post', 'page_type']));
        }

        abort(404);
    }
    public function adNew(Request $request)
    {
        // 1 dollar for 50 quantity
        $ppq = 1 / 50; // 0.02

        $request->merge([
            'user_id' => auth()->id(),
            'ppq' => number_format($ppq, 2),
        ]);

        $request->validate([
            'user_id' => ['required'],
            'company' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'cover_image' => ['required', 'image', 'mimes:jpeg,png,jpg'],
            'optimize_for' => ['required', Rule::in(['click', 'impression'])],
            'quantity' => ['required', 'integer', 'min:50'],
            'ppq' => ['required', 'numeric'],
            'selected_payment_gateway' => ['nullable', 'string']
        ]);

        // upload image
        $attachment = $request->file('cover_image');
        $attachment_name = time() . '-' . uniqid() . '.' . $attachment->getClientOriginalExtension();
        $attachment->move('assets/uploads/ads/', $attachment_name);

        $form = $request->only(['company', 'title', 'url', 'description', 'optimize_for', 'quantity', 'ppq']);
        $form['user_id'] = Auth::id();
        $form['cover_image'] = $attachment_name;
        $form['status'] = 'pending';
        $form['is_paid'] = false;

        // 👇 calculate total budget
        $form['budget'] = $request->quantity * $request->ppq;

        DB::beginTransaction();
        try {
            $ad = Ad::create($form);
            $gateway = $request->input('selected_payment_gateway', 'wallet');

            if ($gateway === 'wallet') {
                $request->merge(['id' => $ad->id]);
                (new AdService())->payByWallet($request);
                DB::commit();
                return redirect()->route('ad.create')->with('success', 'Advertisement added and paid via wallet. Waiting for admin approval!');
            }

            if ($gateway === 'paypro') {
                $baseUrl = rtrim(get_static_option('paypro_base_url') ?? 'https://api.paypro.com.pk', '/');
                $clientId = get_static_option('paypro_client_id');
                $clientSecret = get_static_option('paypro_client_secret');
                $merchantId = get_static_option('paypro_username');

                if (empty($clientId) || empty($clientSecret) || empty($merchantId)) {
                    DB::rollBack();
                    \Log::error('Ad PayPro: Missing configuration');
                    return back()->with('error', __('Payment gateway not configured'));
                }

                $authResponse = Http::asJson()->post($baseUrl . '/v2/ppro/auth', [
                    'clientid' => $clientId,
                    'clientsecret' => $clientSecret,
                ]);

                if (!$authResponse->ok()) {
                    DB::rollBack();
                    return back()->with('error', __('Unable to initialize payment'));
                }

                $token = $authResponse->header('token') ?? $authResponse->header('Token');
                $amountFormatted = number_format($form['budget'], 2, '.', '');
                $user = Auth::user();
                $customerName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                if ($customerName === '') {
                    $customerName = $user->email ?? 'Customer';
                }
                $orderNumber = 'AD-C-' . $ad->id . '-' . time();
                $now = Carbon::now();
                $dueDate = $now->copy()->addDays(1);
                $currency = get_static_option('site_global_currency') ?? 'USD';

                $payload = [
                    ['MerchantId' => $merchantId],
                    [
                        'OrderNumber' => $orderNumber,
                        'CurrencyAmount' => (string)$amountFormatted,
                        'OrderDueDate' => $dueDate->format('d/m/Y'),
                        'OrderType' => 'Service',
                        'IssueDate' => $now->format('d/m/Y'),
                        'OrderExpireAfterSeconds' => '0',
                        'CustomerName' => $customerName,
                        'CustomerMobile' => $user->phone ?? '',
                        'CustomerEmail' => $user->email,
                        'CustomerAddress' => '',
                        'Currency' => $currency,
                        'IsConverted' => 'true',
                    ],
                ];

                $orderResponse = Http::withHeaders(['token' => $token])->asJson()->post($baseUrl . '/v2/ppro/co', $payload);

                if (!$orderResponse->ok()) {
                    DB::rollBack();
                    \Log::error('Ad PayPro: Order request failed', ['code' => $orderResponse->status(), 'body' => $orderResponse->json()]);
                    return back()->with('error', __('PayPro request failed'));
                }

                $orderData = $orderResponse->json();
                $statusCode = $orderData[0]['Status'] ?? null;
                $details = $orderData[1] ?? [];
                $click2Pay = $details['Click2Pay'] ?? null;

                if (empty($click2Pay)) {
                    DB::rollBack();
                    \Log::warning('Ad PayPro: Click2Pay missing from response', ['response' => $orderData]);
                    $msg = $details['Message'] ?? __('Unexpected PayPro status');
                    return back()->with('error', __('PayPro error (Status ') . $statusCode . '): ' . $msg);
                }

                $ad->update(['gateway_slug' => 'paypro']);
                DB::commit();

                // 🔹 Set session for return verification (used by AdController@paypro_return)
                session()->put('ad_paypro', [
                    'ad_id' => $ad->id,
                    'order_number' => $orderNumber
                ]);

                $callbackUrl = Auth::user()->user_type == 1 ? route('client.ad.paypro.return') : route('freelancer.ad.paypro.return');
                $redirectUrl = $click2Pay . (str_contains($click2Pay, '?') ? '&' : '?') . 'callback_url=' . urlencode($callbackUrl);
                return redirect()->away($redirectUrl);
            }
            if ($gateway === 'payfast') {
                $amount = $form['budget'];
                $user = Auth::user();

                $callbackUrl = Auth::user()->user_type == 1 ? route('client.ad.payfast.return') : route('freelancer.ad.payfast.return');
                $cancelUrl = Auth::user()->user_type == 1 ? route('client.ad.manage') : route('freelancer.ad.manage');

                session(['ad_payfast' => [
                    'ad_id' => $ad->id,
                    'role' => Auth::user()->user_type == 1 ? 'client' : 'freelancer'
                ]]);

                $ad->update(['gateway_slug' => 'payfast']);
                DB::commit();

                $payfast = new \App\Helper\GoPayFast();
                $payfast->setMerchantId(get_static_option('payfast_merchant_id') ?: '14833');
                $payfast->setMerchantKey(get_static_option('payfast_merchant_key') ?: 'rPcy4T7GQkSCFsHBLdn26s');
                $payfast->setStoreId(get_static_option('payfast_store_id') ?? '');
                $payfast->setEnv(get_static_option('payfast_test_mode') == 'on');

                return $payfast->charge_customer([
                    'amount' => $amount,
                    'order_id' => $ad->id,
                    'email' => $user->email ?? 'test@test.com',
                    'name' => 'Ad Payment',
                    'description' => 'Payment for Ad #'.$ad->id,
                    'success_url' => $callbackUrl,
                    'cancel_url' => $cancelUrl
                ]);
            }

            DB::commit();
            return redirect()->route('ad.create')->with('success', 'Advertisement added successfully! Waiting for payment/approval.');

        }
        catch (WalletNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Wallet not found');
        }
        catch (WalletInsufficientBalance $e) {
            DB::rollBack();
            return back()->with('error', 'Insufficient wallet balance');
        }
        catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ad creation failed: An unexpected error occurred', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'user_id' => Auth::id()]);
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function adUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ads,id',
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'optimize_for' => ['required', Rule::in(['click', 'impression'])],
            'quantity' => ['required', 'integer'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
        ]);

        $ad = Ad::findOrFail($request->id);

        $data = [
            'title' => $request->title,
            'company' => $request->company,
            'url' => $request->url,
            'description' => $request->description,
            'optimize_for' => $request->optimize_for,
            'quantity' => $request->quantity,
        ];

        // Handle new image upload if provided
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($ad->cover_image && File::exists(public_path('assets/uploads/ads/' . $ad->cover_image))) {
                File::delete(public_path('assets/uploads/ads/' . $ad->cover_image));
            }

            $image = $request->file('cover_image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/uploads/ads/'), $imageName);
            $data['cover_image'] = $imageName;
        }
        else {
            // If no new image, keep the old one
            $data['cover_image'] = $ad->cover_image;
        }

        $data['status'] = 'pending';
        $ad->update($data);

        return back()->with(toastr_success('Ad updated successfully'));
    }


    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        $ad->delete();

        return back()->with(toastr_success('Ad deleted successfully'));
    }

    public function partnership()
    {
        $partnership = \App\Models\PartnershipPage::first();
        return view('frontend.pages.partnership', compact('partnership'));
    }

    public function investor_relations()
    {
        $investor_relation = \App\Models\InvestorRelation::first();
        return view('frontend.pages.investor-relations', compact('investor_relation'));
    }

}
