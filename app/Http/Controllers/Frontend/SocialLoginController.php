<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function facebook_redirect()
    {
        if (request()->has('type')) {
            session(['social_login_user_type' => request()->type]);
        }
        return Socialite::driver('facebook')->redirect();
    }

    public function facebook_callback()
    {
        try {
            $user_fb_details = Socialite::driver('facebook')->user();
            $user_details = User::where('email', $user_fb_details->getEmail())->first();

            if ($user_details) {
                Auth::guard('web')->login($user_details);
                if ($user_details->user_type == 1) {
                    return redirect()->to('client/dashboard/info');
                } else {
                    return redirect()->to('freelancer/dashboard/info');
                }
            } else {
                $user_type = session('social_login_user_type') ?? 1;
                $full_name = $user_fb_details->getName();
                $name_parts = explode(' ', $full_name, 2);
                $first_name = $name_parts[0] ?? $full_name;
                $last_name = $name_parts[1] ?? '';

                $base_username = explode('@', $user_fb_details->getEmail())[0];
                $username = $base_username;
                $i = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $base_username . $i++;
                }

                $new_user = User::create([
                    'username' => $username,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $user_fb_details->getEmail(),
                    'user_type' => $user_type,
                    'is_email_verified' => 1,
                    'facebook_id' => $user_fb_details->getId(),
                    'password' => Hash::make(\Illuminate\Support\Str::random(8))
                ]);

                // Replicate logic from RegisterController
                try {
                    if ($user_type == 2) {
                        $signup_bonus_enable = (bool) (get_static_option('signup_bonus_enable') ?? true);
                        $signup_bonus_amount = (float) (get_static_option('signup_bonus_amount') ?? 10);
                        $initial_balance = $signup_bonus_enable ? max(0, $signup_bonus_amount) : 0;

                        \Modules\Wallet\Entities\Wallet::create([
                            'user_id' => $new_user->id,
                            'balance' => $initial_balance,
                            'remaining_balance' => $initial_balance,
                            'withdraw_amount' => 0,
                            'signup_bonus' => $initial_balance,
                            'status' => 1
                        ]);

                        $subscription_details = \Modules\Subscription\Entities\Subscription::with('subscription_type:id,validity')
                            ->select(['id', 'subscription_type_id', 'price', 'limit'])
                            ->where('id', get_static_option('register_subscription'))
                            ->where('status', '1')->first();

                        if ($subscription_details) {
                            $expire_date = \Carbon\Carbon::now()->addDays($subscription_details?->subscription_type?->validity);
                            \Modules\Subscription\Entities\UserSubscription::create([
                                'user_id' => $new_user->id,
                                'subscription_id' => $subscription_details->id,
                                'price' => $subscription_details->price,
                                'limit' => $subscription_details->limit,
                                'expire_date' => $expire_date,
                                'payment_gateway' => 'Trial',
                                'manual_payment_payment' => '',
                                'payment_status' => 'complete',
                                'status' => 1,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Social login extra logic failed: ' . $e->getMessage());
                }

                Auth::guard('web')->login($new_user);
                
                // Send welcome email like regular registration
                try {
                    $user_type = $new_user->user_type == 1 ? 'client' : 'freelancer';
                    
                    $message = get_static_option('user_register_welcome_message') ?? __('Your registration has been successfully completed.');
                    $message = str_replace(
                        ["@name", "@email", "@username", "@password", "@userType"],
                        [
                            $new_user->first_name . ' ' . $new_user->last_name,
                            $new_user->email,
                            $new_user->username,
                            '*****',
                            $user_type
                        ],
                        $message
                    );
                    
                    \Illuminate\Support\Facades\Mail::to($new_user->email)->send(new \App\Mail\BasicMail([
                        'subject' => get_static_option('user_register_welcome_subject') ?? __('Welcome to Our Platform'),
                        'message' => $message
                    ]));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Facebook login welcome email failed: ' . $e->getMessage());
                }
                
                // Send admin notification like regular registration
                try {
                    $admin_message = get_static_option('user_register_message') ?? __('A new user has registered.');
                    $admin_message = str_replace(
                        ["@name", "@email", "@username", "@userType", "@password"],
                        [$new_user->first_name . ' ' . $new_user->last_name, $new_user->email, $new_user->username, $user_type, 'Facebook Login'],
                        $admin_message
                    );
                    \Illuminate\Support\Facades\Mail::to(get_static_option('site_global_email'))->send(new \App\Mail\BasicMail([
                        'subject' => get_static_option('user_register_subject') ?? __('New User Registration via Facebook'),
                        'message' => $admin_message
                    ]));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Facebook login admin email failed: ' . $e->getMessage());
                }
                
                return $user_type == 1 ? redirect()->to('client/profile/settings') : redirect()->to('freelancer/profile/settings');
            }
        } catch (\Exception $e) {
            return redirect()->to('login')->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
    }

    public function google_redirect()
    {
        if (request()->has('type')) {
            session(['social_login_user_type' => request()->type]);
        }
        return Socialite::driver('google')->redirect();
    }

    public function google_callback()
    {
        try {
            $user_go_details = Socialite::driver('google')->user();
            $user_details = User::where('email', $user_go_details->getEmail())->first();

            if ($user_details) {
                Auth::guard('web')->login($user_details);
                if ($user_details->user_type == 1) {
                    return redirect()->to('client/dashboard/info');
                } else {
                    return redirect()->to('freelancer/dashboard/info');
                }
            } else {
                $user_type = session('social_login_user_type') ?? 1;
                $full_name = $user_go_details->getName();
                $name_parts = explode(' ', $full_name, 2);
                $first_name = $name_parts[0] ?? $full_name;
                $last_name = $name_parts[1] ?? '';

                $base_username = explode('@', $user_go_details->getEmail())[0];
                $username = $base_username;
                $i = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $base_username . $i++;
                }

                // Attempt to get phone number if available (though Socialite standard doesn't provide it)
                $phone = $user_go_details->user['phone'] ?? $user_go_details->user['mobile'] ?? null;

                $new_user = User::create([
                    'username' => $username,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $user_go_details->getEmail(),
                    'phone' => $phone,
                    'user_type' => $user_type,
                    'is_email_verified' => 1,
                    'google_id' => $user_go_details->getId(),
                    'password' => Hash::make(\Illuminate\Support\Str::random(8))
                ]);

                // Replicate logic from RegisterController
                try {
                    if ($user_type == 2) {
                        $signup_bonus_enable = (bool) (get_static_option('signup_bonus_enable') ?? true);
                        $signup_bonus_amount = (float) (get_static_option('signup_bonus_amount') ?? 10);
                        $initial_balance = $signup_bonus_enable ? max(0, $signup_bonus_amount) : 0;

                        \Modules\Wallet\Entities\Wallet::create([
                            'user_id' => $new_user->id,
                            'balance' => $initial_balance,
                            'remaining_balance' => $initial_balance,
                            'withdraw_amount' => 0,
                            'signup_bonus' => $initial_balance,
                            'status' => 1
                        ]);

                        $subscription_details = \Modules\Subscription\Entities\Subscription::with('subscription_type:id,validity')
                            ->select(['id', 'subscription_type_id', 'price', 'limit'])
                            ->where('id', get_static_option('register_subscription'))
                            ->where('status', '1')->first();

                        if ($subscription_details) {
                            $expire_date = \Carbon\Carbon::now()->addDays($subscription_details?->subscription_type?->validity);
                            \Modules\Subscription\Entities\UserSubscription::create([
                                'user_id' => $new_user->id,
                                'subscription_id' => $subscription_details->id,
                                'price' => $subscription_details->price,
                                'limit' => $subscription_details->limit,
                                'expire_date' => $expire_date,
                                'payment_gateway' => 'Trial',
                                'manual_payment_payment' => '',
                                'payment_status' => 'complete',
                                'status' => 1,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Social login extra logic failed: ' . $e->getMessage());
                }

                Auth::guard('web')->login($new_user);
                
                // Send welcome email like regular registration
                try {
                    $user_type = $new_user->user_type == 1 ? 'client' : 'freelancer';
                    
                    $message = get_static_option('user_register_welcome_message') ?? __('Your registration has been successfully completed.');
                    $message = str_replace(
                        ["@name", "@email", "@username", "@password", "@userType"],
                        [
                            $new_user->first_name . ' ' . $new_user->last_name,
                            $new_user->email,
                            $new_user->username,
                            '*****',
                            $user_type
                        ],
                        $message
                    );
                    
                    \Illuminate\Support\Facades\Mail::to($new_user->email)->send(new \App\Mail\BasicMail([
                        'subject' => get_static_option('user_register_welcome_subject') ?? __('Welcome to Our Platform'),
                        'message' => $message
                    ]));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Google login welcome email failed: ' . $e->getMessage());
                }
                
                // Send admin notification like regular registration
                try {
                    $admin_message = get_static_option('user_register_message') ?? __('A new user has registered.');
                    $admin_message = str_replace(
                        ["@name", "@email", "@username", "@userType", "@password"],
                        [$new_user->first_name . ' ' . $new_user->last_name, $new_user->email, $new_user->username, $user_type, 'Google Login'],
                        $admin_message
                    );
                    \Illuminate\Support\Facades\Mail::to(get_static_option('site_global_email'))->send(new \App\Mail\BasicMail([
                        'subject' => get_static_option('user_register_subject') ?? __('New User Registration via Google'),
                        'message' => $admin_message
                    ]));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Google login admin email failed: ' . $e->getMessage());
                }
                
                return $user_type == 1 ? redirect()->to('client/profile/settings') : redirect()->to('freelancer/profile/settings');
            }
        } catch (\Exception $e) {
            return redirect()->to('login')->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
    }
}
