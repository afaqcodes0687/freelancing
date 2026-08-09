<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AffiliateLoginController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {

            $email_or_username = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $request->validate([
                'email' => 'required|string',
                'password' => 'required|min:6',
            ], [
                'email.required' => sprintf(__('%s required'), $email_or_username),
                'password.required' => __('Password is required'),
            ]);

            $affiliate = AffiliateProgram::where($email_or_username, $request->email)->first();

            if ($affiliate && Hash::check($request->password, $affiliate->password)) {

                if ($affiliate->is_email_verified !== 1) {
                    return response()->json([
                        'msg' => __('Please verify your email before logging in.'),
                        'type' => 'warning',
                        'status' => 'email-not-verified',
                    ]);
                }


                // ✅ Set affiliate session for navbar detection
                session([
                    'logged_in_affiliate_id' => $affiliate->id,
                    'affiliate_user' => $affiliate,
                ]);

                return response()->json([
                    'msg' => __('Login successful. Redirecting...'),
                    'type' => 'success',
                    'status' => 'affiliate-login',
                    'redirect_url' => route('affiliate.dashboard'),
                ]);
            }

            // ❌ Invalid credentials
            return response()->json([
                'msg' => sprintf(__('Your %s or Password is wrong !!'), $email_or_username),
                'type' => 'danger',
                'status' => 'not_ok',
            ]);
        }

        return view('frontend.affiliate.affiliate-login');
    }

    public function logout(Request $request)
    {
        // ✅ Clear affiliate session
        Session::forget(['logged_in_affiliate_id', 'affiliate_user']);
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('affiliate.login');
    }
}
