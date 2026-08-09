<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BasicMail;

class AdManageController extends Controller
{
    public function index()
    {
        $all_ads = Ad::with('user')->latest()->paginate(20);
        return view('backend.ads.index', compact('all_ads'));
    }

    public function change_status(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ads,id',
            'status' => 'required|in:active,pending,rejected',
            'feedback' => 'nullable|string|max:500' // Added feedback validation
        ]);

        $ad = Ad::findOrFail($request->id);
        $ad->status = $request->status;
        $ad->save();

        // Notify User
        $status_text = $request->status === 'active' ? 'approved' : $request->status;
        $msg = "Your advertisement '{$ad->title}' has been " . $status_text;

        $feedback = $request->feedback ? "<br><strong>Admin Feedback:</strong> " . nl2br($request->feedback) : "";
        $msg_with_feedback = $msg . $feedback;

        if ($ad->user_id) {
            $user = $ad->user;
            if ($user) {
                try {
                    if ($user->user_type == 1) { // Client
                        if (function_exists('client_notification')) {
                            client_notification('ad_status', $ad->user_id, 'ad', $msg);
                        }
                    } else { // Freelancer
                        if (function_exists('freelancer_notification')) {
                            freelancer_notification('ad_status', $ad->user_id, 'ad', $msg);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Ad Status Notification Failed: ' . $e->getMessage());
                }

                // Send Email
                try {
                    $data = [
                        'subject' => __('Ad Status Update'),
                        'message' => __('Hello') . ' ' . $user->name . ',<br><br>' . $msg_with_feedback,
                    ];
                    Mail::to($user->email)->send(new BasicMail($data));
                } catch (\Exception $e) {
                    \Log::error('Ad Status Email Failed: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['status' => 'success', 'msg' => __('Ad status changed successfully')]);
    }

    public function update_stats(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ads,id',
            'quantity' => 'required|integer|min:0',
            'clicks' => 'required|integer|min:0',
            'impressions' => 'required|integer|min:0',
        ]);

        $ad = Ad::findOrFail($request->id);
        $ad->quantity = $request->quantity;
        $ad->clicks = $request->clicks;
        $ad->impressions = $request->impressions;
        $ad->save();

        return response()->json(['status' => 'success', 'msg' => __('Ad stats updated successfully')]);
    }

    public function destroy($id)
    {
        Ad::findOrFail($id)->delete();
        return back()->with(['msg' => __('Ad deleted successfully'), 'type' => 'danger']);
    }
}
