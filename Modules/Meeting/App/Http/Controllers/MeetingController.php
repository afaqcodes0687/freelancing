<?php

namespace Modules\Meeting\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Meeting\Entities\Meeting;
use Modules\Meeting\Entities\UserGoogleAccount;
use Modules\Meeting\Services\GoogleMeetingService;
use Modules\Chat\Entities\LiveChatMessage;
use Carbon\Carbon;

class MeetingController extends Controller
{
    protected $googleService;

    public function __construct(GoogleMeetingService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function index()
    {
        $user = Auth::guard('web')->user();
        $meetings = Meeting::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        $googleAccount = UserGoogleAccount::where('user_id', $user->id)->first();
        $systemAccountConnected = UserGoogleAccount::where('user_id', -1)->exists();

        return view('meeting::index', compact('meetings', 'googleAccount', 'systemAccountConnected'));
    }

    public function redirectToGoogle()
    {
        return redirect()->away($this->googleService->getAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('code')) {
            $token = $this->googleService->authenticate($request->code);
            $user = Auth::guard('web')->user();

            UserGoogleAccount::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? null,
                    'expires_in' => $token['expires_in'],
                    'expires_at' => Carbon::now()->addSeconds($token['expires_in']),
                    'email' => $user->email, // Optional: get from token if needed
                ]
            );

            $route = $user->user_type == 1 ? 'client.meeting.index' : 'freelancer.meeting.index';
            return redirect()->route($route)->with(['msg' => __('Google Calendar connected successfully'), 'type' => 'success']);
        }

        $user = Auth::guard('web')->user();
        $route = $user->user_type == 1 ? 'client.meeting.index' : 'freelancer.meeting.index';
        return redirect()->route($route)->with(['msg' => __('Failed to connect Google Calendar'), 'type' => 'danger']);
    }

    public function schedule(Request $request)
    {
        $request->validate([
            'live_chat_id' => 'required',
            'receiver_id' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:15',
        ]);

        try {
            $user = Auth::guard('web')->user();
            $startTime = Carbon::parse($request->start_time);
            $endTime = (clone $startTime)->addMinutes($request->duration);

            $receiver = \App\Models\User::find($request->receiver_id);
            if (!$receiver) {
                return response()->json(['status' => 'error', 'message' => __('Receiver not found')], 404);
            }

            $details = [
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'receiver_email' => $receiver->email,
                'sender_email' => $user->email,
            ];

            $googleResult = $this->googleService->createMeeting($user, $details);

            if (isset($googleResult['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Please connect your Google Calendar account first to schedule meetings.')
                ], 400);
            }

            $meeting = Meeting::create([
                'live_chat_id' => $request->live_chat_id,
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'meeting_link' => $googleResult['meeting_link'] ?? '',
                'google_event_id' => $googleResult['event_id'] ?? '',
                'status' => 'scheduled',
            ]);

            // Send a message in the chat
            $messageContent = [
                'type' => 'meeting',
                'meeting_id' => $meeting->id,
                'title' => $meeting->title,
                'start_time' => $meeting->start_time->format('Y-m-d H:i:s'),
                'link' => $meeting->meeting_link,
            ];

            $livechat = $meeting->liveChat;
            if (!$livechat) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Meeting scheduled successfully, but chat message could not be sent (Chat not found)'),
                    'meeting' => $meeting
                ]);
            }

            $messageFrom = $user->id == $livechat->client_id ? 1 : 2;
            $recipient_id = ($messageFrom == 1) ? $livechat->freelancer_id : $livechat->client_id;
            $is_delivered = \Illuminate\Support\Facades\Cache::has('user_is_online_' . $recipient_id) ? 1 : 0;

            $message = LiveChatMessage::create([
                'live_chat_id' => $request->live_chat_id,
                'from_user' => $messageFrom,
                'message' => $messageContent,
                'is_delivered' => $is_delivered,
            ]);

            // Trigger real-time events (Pusher)
            if ($messageFrom == 2) { // Freelancer sent to Client
                $messageBlade = view("chat::components.client.message", [
                    "data" => $livechat,
                    "message" => $message,
                ])->render();

                event(new \Modules\Chat\Events\LivechatVendorMessageEvent(
                    $messageBlade,
                    $message,
                    $livechat,
                    $livechat->client_id,
                    $livechat->freelancer_id,
                ));

                if ($is_delivered) {
                    event(new \Modules\Chat\Events\LivechatMessageStatusEvent(
                        $message->id,
                        'delivered',
                        'livechat-client-channel.' . $livechat->freelancer_id . '.' . $livechat->client_id,
                        'livechat-client-status-' . $livechat->client_id
                    ));
                }
            } else { // Client sent to Freelancer
                $bladeMessage = view("chat::components.freelancer.message", [
                    "data" => $livechat,
                    "message" => $message
                ])->render();

                event(new \Modules\Chat\Events\LivechatUserMessageEvent(
                    $bladeMessage,
                    $message,
                    $livechat,
                    $livechat->client_id,
                    $livechat->freelancer_id,
                ));

                if ($is_delivered) {
                    event(new \Modules\Chat\Events\LivechatMessageStatusEvent(
                        $message->id,
                        'delivered',
                        'livechat-freelancer-channel.' . $livechat->client_id . '.' . $livechat->freelancer_id,
                        'livechat-freelancer-status-' . $livechat->freelancer_id
                    ));
                }
            }

            // Render the message blade for the sender to show in chat immediately
            $senderBlade = '';
            if ($messageFrom == 2) { // Freelancer
                $senderBlade = view("chat::components.freelancer.message", [
                    "data" => $livechat,
                    "message" => $message,
                ])->render();
            } else { // Client
                $senderBlade = view("chat::components.client.message", [
                    "data" => $livechat,
                    "message" => $message,
                ])->render();
            }

            // Send Email Notifications to both parties
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\MeetingScheduledMail(
                    $meeting,
                    $user->fullname ?? $user->username,
                    $receiver->fullname ?? $receiver->username
                ));

                \Illuminate\Support\Facades\Mail::to($receiver->email)->send(new \App\Mail\MeetingScheduledMail(
                    $meeting,
                    $receiver->fullname ?? $receiver->username,
                    $user->fullname ?? $user->username
                ));
            } catch (\Exception $mailEx) {
                \Log::error('Meeting Email Error: ' . $mailEx->getMessage());
                // Don't fail the request if email fails, but log it
            }

            return response()->json([
                'status' => 'success',
                'message' => __('Meeting scheduled successfully'),
                'meeting' => $meeting,
                'message_html' => $senderBlade
            ]);
        } catch (\Exception $e) {
            \Log::error('Meeting Schedule Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('Something went wrong: ') . $e->getMessage()
            ], 500);
        }
    }
}
