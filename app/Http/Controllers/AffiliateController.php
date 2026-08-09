<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProgram;
use App\Mail\BasicMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\CountryManage\Entities\City;
use Illuminate\Support\Str;
use App\Models\AffiliateCommission;
use App\Models\AffiliateClick;
use DB;
use Illuminate\Support\Facades\Cookie;
class AffiliateController extends Controller
{

    public function approveCommission($token)
    {
        $commission = AffiliateCommission::where('approval_token', $token)->first();

        if (!$commission) {
            return redirect('/')->with('error', 'Invalid or expired approval link.');
        }

        if ($commission->status != 'pending') {
            return redirect('/')->with('error', 'Commission already processed.');
        }

        DB::beginTransaction();
        try {
            $affiliate = AffiliateProgram::find($commission->affiliate_id);
            $affiliate->balance = (float) $affiliate->balance + (float) $commission->commission_amount;
            $affiliate->save();

            $commission->update([
                'status' => 'approved',
                'approval_token' => null,
            ]);

            // send email
            Mail::to($affiliate->email)->send(new BasicMail([
                'subject' => 'Commission approved',
                'message' => "Your commission #{$commission->id} has been approved."
            ]));

            DB::commit();
            return redirect('/')->with('success', 'Your commission has been approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approve via token failed: ' . $e->getMessage());
            return redirect('/')->with('error', 'Something went wrong.');
        }
    }
    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'first_name' => 'required|regex:/^[a-zA-Z0-9]+$/u|max:191',
                'last_name' => 'required|regex:/^[a-zA-Z0-9]+$/u|max:191',
                'email' => 'required|email|unique:affiliates_programs,email|max:191',
                'username' => 'required|unique:affiliates_programs,username|max:191',
                'phone' => 'nullable|unique:affiliates_programs,phone|max:191',
                'password' => 'required|min:6|max:191',
                'confirm_password' => 'required|same:password',
                'account_display_name' => 'nullable|max:191',
                'company_website' => 'nullable|url',
            ]);

            if (!empty(get_static_option('site_google_captcha_enable'))) {
                $request->validate([
                    'g-recaptcha-response' => 'required|captcha',
                ]);
            }

            $email_verify_token = sprintf("%d", random_int(123456, 999999));

            $affiliate = AffiliateProgram::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'account_display_name' => $request->account_display_name,
                'company_website' => $request->company_website,
                'referral_code' => uniqid('ref_'),
                'email_verify_token' => $email_verify_token,
                'is_email_verified' => 0,
            ]);

            // ✅ Send verification email immediately after registration
            try {
                $message_body = '
                <div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
                    <h2 style="color: #333; font-size: 22px;">' . __('Email Verification') . '</h2>
                    <p style="font-size: 16px; color: #555;">' . __('Your verification code is:') . '</p>
                    <div style="margin: 10px 0;">
                        <span style="font-size: 32px; color: #309400; font-weight: bold;">' . $email_verify_token . '</span>
                    </div>
                    <p style="font-size: 14px; color: #999;">' . __('This code will expire soon. If you didn\'t request this, you can ignore this email.') . '</p>
                </div>
            ';

                Mail::to($affiliate->email)->send(new BasicMail([
                    'subject' => __('Verify your email address - Affiliate'),
                    'message' => $message_body
                ]));
            } catch (\Exception $e) {
                \Log::error('Affiliate email verification send failed: ' . $e->getMessage());
            }

            // ✅ Handle referral parent linkage only (no commission on affiliate signup)
            if (session()->has('referral_affiliate_id') || request()->cookie('ref_aid')) {
                $referrerId = session('referral_affiliate_id') ?? request()->cookie('ref_aid');
                $parentExists = AffiliateProgram::find($referrerId);

                if ($parentExists) {
                    $affiliate->parent_id = $referrerId;
                    $affiliate->save();
                } else {
                    \Log::warning("Invalid referrer ID for referral attribution: $referrerId");
                }
            }


            // ✅ Notify Admin
            try {
                $admin_message = get_static_option('user_register_message') ?? __('A new affiliate has registered.');
                $admin_message = str_replace(
                    ["@name", "@email", "@username", "@userType"],
                    [$affiliate->first_name . ' ' . $affiliate->last_name, $affiliate->email, $affiliate->username, 'affiliate'],
                    $admin_message
                );

                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => get_static_option('user_register_subject') ?? __('New Affiliate Registration'),
                    'message' => $admin_message
                ]));
            } catch (\Exception $e) {
            }

            session(['pending_affiliate_id' => $affiliate->id]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'redirect_url' => route('affiliate.email.verify'),
                    'msg' => __('Registration successful! Redirecing to verification...')
                ]);
            }

            return redirect()->route('affiliate.email.verify')
                ->with('success', __('Check your email for the verification code.'));
        }

        return view('frontend.affiliate.affiliate-register');
    }


    public function emailVerify(Request $request)
    {
        $affiliate = AffiliateProgram::find(session('pending_affiliate_id'));

        if (!$affiliate) {
            return redirect()->route('affiliate.register')->with('error', __('No pending affiliate verification found.'));
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'email_verify_token' => 'required|max:191'
            ], [
                'email_verify_token.required' => __('Verify code is required')
            ]);

            $affiliate_match = AffiliateProgram::where([
                'email_verify_token' => $request->email_verify_token,
                'email' => $affiliate->email
            ])->first();

            if ($affiliate_match) {
                $affiliate_match->is_email_verified = 1;
                $affiliate_match->email_verify_token = null;
                $affiliate_match->save();

                // ✅ Auto-login: Set session for the affiliate
                session(['logged_in_affiliate_id' => $affiliate_match->id]);
                session()->forget('pending_affiliate_id');

                try {
                    $message = __('Your affiliate registration has been successfully completed.');
                    $message = str_replace(
                        ["@name", "@email", "@referral_code"],
                        [
                            $affiliate_match->first_name . ' ' . $affiliate_match->last_name,
                            $affiliate_match->email,
                            $affiliate_match->referral_code,
                        ],
                        $message
                    );

                    Mail::to($affiliate_match->email)->send(new BasicMail([
                        'subject' => __('Welcome to Our Affiliate Program'),
                        'message' => $message,
                    ]));
                } catch (\Exception $e) {
                    // Optional: log the exception
                }

                return redirect()->route('affiliate.profile.settings')->with('success', __('Your email has been verified. Please complete your profile.'));
            }

            return back()->with('error', __('Your verification code is wrong.'));
        }

        // resend OTP if needed
        if (is_null($affiliate->email_verify_token)) {
            $verify_token = sprintf("%d", random_int(123456, 999999));
            $affiliate->email_verify_token = $verify_token;
            $affiliate->save();

            try {
                $message_body = '
                    <div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
                        <h2 style="color: #333; font-size: 22px;">' . __('Email Verification') . '</h2>
                        <p style="font-size: 16px; color: #555;">' . __('Your verification code is:') . '</p>
                        <div style="margin: 10px 0;">
                            <span style="font-size: 32px; color: #309400; font-weight: bold;">' . $verify_token . '</span>
                        </div>
                        <p style="font-size: 14px; color: #999;">' . __('This code will expire soon. If you didn\'t request this, you can ignore this email.') . '</p>
                    </div>
                ';

                Mail::to($affiliate->email)->send(new BasicMail([
                    'subject' => __('Verify your email address - Affiliate'),
                    'message' => $message_body
                ]));
            } catch (\Exception $e) {
            }
        }

        return view('frontend.affiliate.email-verify', compact('affiliate'));
    }

    public function resendCode()
    {
        $affiliate = AffiliateProgram::find(session('pending_affiliate_id'));

        if (!$affiliate) {
            return redirect()->route('affiliate.register')->with('error', __('No affiliate session found.'));
        }

        $verify_token = sprintf("%d", random_int(123456, 999999));
        $affiliate->email_verify_token = $verify_token;
        $affiliate->save();

        try {
            $message_body = '
                <div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
                    <h2 style="color: #333; font-size: 22px;">' . __('Email Verification') . '</h2>
                    <p style="font-size: 16px; color: #555;">' . __('Your new verification code is:') . '</p>
                    <div style="margin: 10px 0;">
                        <span style="font-size: 32px; color: #309400; font-weight: bold;">' . $verify_token . '</span>
                    </div>
                    <p style="font-size: 14px; color: #999;">' . __('This code will expire soon.') . '</p>
                </div>
            ';

            Mail::to($affiliate->email)->send(new BasicMail([
                'subject' => __('Verify your email address - Affiliate'),
                'message' => $message_body
            ]));
        } catch (\Exception $e) {
        }

        return redirect()->route('affiliate.email.verify')->with('success', __('A new code has been sent to your email.'));
    }
    public function userNameAvailability(Request $request)
    {
        $username = AffiliateProgram::where('username', $request->username)->first();
        if (!empty($username) && $username->username == $request->username) {
            $status = 'not_available';
            $msg = __('Sorry! Username name is not available');
        } else {
            $status = 'available';
            $msg = __('Congrats! Username name is available');
        }
        return response()->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }

    public function emailAvailability(Request $request)
    {
        $email = AffiliateProgram::where('email', $request->email)->first();
        if (!empty($email) && $email->email == $request->email) {
            $status = 'not_available';
            $msg = __('Sorry! Email has already taken');
        } else {
            $status = 'available';
            $msg = __('Congrats! Email is available');
        }
        return response()->json([
            'status' => $status,
            'msg' => $msg,
        ]);
    }
    public function phoneNumberAvailability(Request $request)
    {
        $phone = AffiliateProgram::where('phone', $request->phone)->first();
        if (!empty($phone) && $phone->phone == $request->phone) {
            $status = 'not_available';
            $msg = __('Sorry! Phone Number has already taken');
        } else {
            $status = 'available';
            $msg = __('Congrats! Phone number is available');
        }
        return response()->json([
            'status' => $status,
            'msg' => $msg,
            'phone' => $phone,
        ]);
    }
}
