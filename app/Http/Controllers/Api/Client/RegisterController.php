<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Wallet\Entities\Wallet;

class RegisterController extends Controller
{
    //register
    public function register(Request $request)
    {
        //laravel validation
        $request->validate([
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'email' => 'required|email|unique:users|max:191',
            'username' => 'required|unique:users|max:191',
            'phone' => 'required|unique:users|max:191',
            'password' => 'required|min:6|max:191',
            'confirm_password' => 'required|min:6|max:191',
            'referral_code' => 'nullable|string|max:191',
        ]);

        //password match validation
        if ($request->password != $request->confirm_password) {
            return response()->json(['msg' => __('Password does not match')]);
        }

        $email_verify_tokn = sprintf("%d", random_int(123456, 999999));
        
        // Validate referral code if provided
        $referredBy = null;
        if ($request->filled('referral_code')) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if (!$referrer) {
                return response()->json([
                    'msg' => __('Invalid referral code'),
                    'status' => 'error'
                ]);
            }
            $referredBy = $referrer->id;
        }
        
        // Generate unique referral code for the new user
        $referralCode = $this->generateUniqueReferralCode();
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 1,
            'referral_code' => $referralCode,
            'referred_by' => $referredBy,
            'terms_condition' => 1, // Fix column name if it was terms_conditions
            'email_verify_token' => $email_verify_tokn,
        ]);

        if (!is_null($user)) {
            // ✅ Unified Referral Registration Logic
            try {
                app(\App\Services\ReferralRegistrationService::class)->bindReferral($request, $user->id);
            } catch (\Exception $e) {
                \Log::error('API Client referral binding failed: ' . $e->getMessage());
            }
            //create client wallet (no signup bonus for clients)
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'remaining_balance' => 0,
                'withdraw_amount' => 0,
                'signup_bonus' => 0,
                'status' => 1
            ]);

            //send register mail
            try {
                $message = get_static_option('user_register_message') ?? __('You have successfully registered as a client');
                    $user_type = 'client';

                    $message = strtr($message, [
                        '@name' => $user->first_name . ' ' . $user->last_name,
                        '@email' => $user->email,
                        '@username' => $user->username,
                        '@userType' => $user_type,
                        '@password' => $request->password,
                    ]);                
                    Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => get_static_option('user_register_subject') ?? __('New User Register Email'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {
            }

            //send otp mail
            try {
                Mail::to($user->email)->send(new BasicMail([
                    'subject' => __('Otp Email'),
                    'message' => __('Your otp code') . ' ' . $email_verify_tokn
                ]));
            } catch (\Exception $e) {
            }

            $token = $user->createToken(Str::slug(get_static_option('site_title', 'xilancer')) . 'api_keys')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $token,
                'status' => 'success',
            ]);
        }
    }

    //send otp
    public function resend_otp(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $otp_code = sprintf("%d", random_int(123456, 999999));
        $user_email = User::where('email', $request->email)->first();

        if (!empty($user_email)) {
            try {
                Mail::to($request->email)->send(new BasicMail([
                    'subject' => __('Otp Email'),
                    'message' => __('Your otp code') . ' ' . $otp_code
                ]));
            } catch (\Exception $e) {
                return response()->error([
                    'message' => __($e->getMessage()),
                ]);
            }
            User::where('email', $user_email->email)->update(['email_verify_token' => $otp_code]);
            return response()->json(['email' => $request->email, 'otp' => $otp_code]);
        } else {
            return response()->json(['message' => __('Email Does not Exists')]);
        }

    }

    public function email_verify(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'otp_code' => 'required|integer',
        ]);

        $user = User::where(['email_verify_token' => $request->otp_code, 'id' => $request->user_id])->first();

        if (!empty($user)) {
            User::where('id', $request->user_id)->update(['is_email_verified' => 1]);
            return response()->json(['msg' => __('Email verification success.')]);
        }
        return response()->json(['msg' => __('Your verification code is wrong.')]);
    }

    // Username availability check
    public function usernameAvailability(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:191'
        ]);

        $exists = User::where('username', $request->username)->exists();
        
        return response()->json([
            'status' => $exists ? 'not_available' : 'available',
            'message' => $exists ? __('Username is already taken') : __('Username is available'),
            'available' => !$exists
        ]);
    }

    // Email availability check
    public function emailAvailability(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:191'
        ]);

        $exists = User::where('email', $request->email)->exists();
        
        return response()->json([
            'status' => $exists ? 'not_available' : 'available',
            'message' => $exists ? __('Email is already taken') : __('Email is available'),
            'available' => !$exists
        ]);
    }

    // Phone availability check
    public function phoneAvailability(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:191'
        ]);

        $exists = User::where('phone', $request->phone)->exists();
        
        return response()->json([
            'status' => $exists ? 'not_available' : 'available',
            'message' => $exists ? __('Phone number is already taken') : __('Phone number is available'),
            'available' => !$exists
        ]);
    }

    /**
     * Generate a unique referral code
     *
     * @return string
     */
    private function generateUniqueReferralCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
