<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AffiliateProgram;

class AffiliateAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ Check if affiliate session is active
        $affiliateId = session('logged_in_affiliate_id');

        if (!$affiliateId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'msg' => 'Please login to access affiliate dashboard.']);
            }
            return redirect()->route('affiliate.login')
                ->with('error', 'Please login to access affiliate dashboard.');
        }

        // ✅ Check if affiliate exists in database
        $affiliate = AffiliateProgram::find($affiliateId);

        if (!$affiliate) {
            session()->forget('logged_in_affiliate_id');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'msg' => 'Invalid affiliate account. Please login again.']);
            }
            return redirect()->route('affiliate.login')
                ->with('error', 'Invalid affiliate account. Please login again.');
        }

        // ✅ Allow request to continue
        return $next($request);
    }
}
