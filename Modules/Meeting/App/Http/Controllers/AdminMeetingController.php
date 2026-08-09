<?php

namespace Modules\Meeting\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Meeting\Entities\Meeting;
use Modules\Meeting\Entities\UserGoogleAccount;
use Modules\Meeting\Services\GoogleMeetingService;
use Carbon\Carbon;

class AdminMeetingController extends Controller
{
    protected $googleService;

    public function __construct(GoogleMeetingService $googleService)
    {
        $this->middleware('auth:admin');
        $this->googleService = $googleService;
    }

    public function googleSettings()
    {
        $systemAccount = UserGoogleAccount::where('user_id', -1)->first();
        $staticLink = get_static_option('static_meeting_link');
        $preferredProvider = get_static_option('preferred_meeting_provider') ?? 'google';
        return view('meeting::admin.settings', compact('systemAccount', 'staticLink', 'preferredProvider'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'static_meeting_link' => 'nullable|url',
            'preferred_meeting_provider' => 'required|in:google,jitsi',
        ]);

        update_static_option('static_meeting_link', $request->static_meeting_link);
        update_static_option('preferred_meeting_provider', $request->preferred_meeting_provider);

        return redirect()->back()->with(['msg' => __('Meeting settings updated successfully'), 'type' => 'success']);
    }

    public function allMeetings()
    {
        $allMeetings = Meeting::with(['sender', 'receiver'])->latest()->paginate(20);
        return view('meeting::admin.all-meetings', compact('allMeetings'));
    }

    public function search_meeting(Request $request)
    {
        $string_search = strip_tags($request->string_search);
        $allMeetings = Meeting::with(['sender', 'receiver'])
            ->where(function($q) use ($string_search){
                $q->where('title', 'LIKE', "%$string_search%")
                  ->orWhere('description', 'LIKE', "%$string_search%")
                  ->orWhereHas('sender', function($q2) use ($string_search){
                      $q2->where('first_name', 'LIKE', "%$string_search%")
                         ->orWhere('last_name', 'LIKE', "%$string_search%")
                         ->orWhere('email', 'LIKE', "%$string_search%");
                  })
                  ->orWhereHas('receiver', function($q2) use ($string_search){
                      $q2->where('first_name', 'LIKE', "%$string_search%")
                         ->orWhere('last_name', 'LIKE', "%$string_search%")
                         ->orWhere('email', 'LIKE', "%$string_search%");
                  });
            })
            ->latest()
            ->paginate(20);

        return $allMeetings->total() >= 1 ? view('meeting::admin.search-result', compact('allMeetings'))->render() : response()->json(['status' => 'nothing']);
    }

    public function paginate(Request $request)
    {
        if ($request->ajax()) {
            $string_search = strip_tags($request->string_search);
            $allMeetings = Meeting::with(['sender', 'receiver'])
                ->where(function($q) use ($string_search){
                    $q->where('title', 'LIKE', "%$string_search%")
                      ->orWhere('description', 'LIKE', "%$string_search%")
                      ->orWhereHas('sender', function($q2) use ($string_search){
                          $q2->where('first_name', 'LIKE', "%$string_search%")
                             ->orWhere('last_name', 'LIKE', "%$string_search%")
                             ->orWhere('email', 'LIKE', "%$string_search%");
                      })
                      ->orWhereHas('receiver', function($q2) use ($string_search){
                          $q2->where('first_name', 'LIKE', "%$string_search%")
                             ->orWhere('last_name', 'LIKE', "%$string_search%")
                             ->orWhere('email', 'LIKE', "%$string_search%");
                      });
                })
                ->latest()
                ->paginate(20);

            return view('meeting::admin.search-result', compact('allMeetings'))->render();
        }
    }

    public function redirectToGoogle()
    {
        return redirect()->away($this->googleService->getAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('code')) {
            $token = $this->googleService->authenticate($request->code);

            UserGoogleAccount::updateOrCreate(
                ['user_id' => -1],
                [
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? null,
                    'expires_in' => $token['expires_in'] ?? 3600,
                    'expires_at' => Carbon::now()->addSeconds($token['expires_in'] ?? 3600),
                    'email' => 'system@rightfreelancer.com',
                ]
            );

            return redirect()->route('admin.meeting.google.settings')->with(['msg' => __('System Google Account connected successfully'), 'type' => 'success']);
        }

        return redirect()->route('admin.meeting.google.settings')->with(['msg' => __('Failed to connect Google Account'), 'type' => 'danger']);
    }
}
