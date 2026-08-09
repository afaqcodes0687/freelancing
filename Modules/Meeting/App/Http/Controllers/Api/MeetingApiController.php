<?php

namespace Modules\Meeting\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Meeting\Entities\Meeting;
use Modules\Meeting\Entities\UserGoogleAccount;
use Modules\Meeting\Services\GoogleMeetingService;
use Modules\Chat\Entities\LiveChatMessage;
use Modules\Chat\Entities\LiveChat;
use Carbon\Carbon;

class MeetingApiController extends Controller
{
    protected $googleService;

    public function __construct(GoogleMeetingService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Schedule a Jitsi Meeting
     */
    public function schedule(Request $request)
    {
        $request->validate([
            'live_chat_id' => 'required|exists:live_chats,id',
            'receiver_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:15',
        ]);

        try {
            $user = auth('sanctum')->user();
            $startTime = Carbon::parse($request->start_time);
            $endTime = (clone $startTime)->addMinutes($request->duration);

            // Jitsi Meet Logic (Instant & Free)
            $roomName = 'rf-' . substr(md5(uniqid()), 0, 10);
            $meetingLink = 'https://meet.jit.si/' . $roomName;

            $meeting = Meeting::create([
                'live_chat_id' => $request->live_chat_id,
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'meeting_link' => $meetingLink,
                'google_event_id' => 'jitsi_' . $roomName,
                'status' => 'scheduled',
            ]);

            // Save to chat message
            $livechat = LiveChat::find($request->live_chat_id);
            $messageFrom = $user->id == $livechat->client_id ? 1 : 2;
            $recipient_id = ($messageFrom == 1) ? $livechat->freelancer_id : $livechat->client_id;
            $is_delivered = \Illuminate\Support\Facades\Cache::has('user_is_online_' . $recipient_id) ? 1 : 0;

            $message = LiveChatMessage::create([
                'live_chat_id' => $request->live_chat_id,
                'from_user' => $messageFrom,
                'message' => [
                    'type' => 'meeting',
                    'meeting_id' => $meeting->id,
                    'title' => $meeting->title,
                    'start_time' => $meeting->start_time->format('Y-m-d H:i:s'),
                    'link' => $meeting->meeting_link,
                ],
                'is_delivered' => $is_delivered,
            ]);

            // Broadcast real-time events (Pusher)
            if ($messageFrom == 2) { // Freelancer sent to Client
                $messageBlade = view("chat::components.client.message", ["data" => $livechat, "message" => $message])->render();
                event(new \Modules\Chat\Events\LivechatVendorMessageEvent($messageBlade, $message, $livechat, $livechat->client_id, $livechat->freelancer_id));
            } else { // Client sent to Freelancer
                $bladeMessage = view("chat::components.freelancer.message", ["data" => $livechat, "message" => $message])->render();
                event(new \Modules\Chat\Events\LivechatUserMessageEvent($bladeMessage, $message, $livechat, $livechat->client_id, $livechat->freelancer_id));
            }

            // Send Email Notifications
            try {
                $receiver = \App\Models\User::find($request->receiver_id);
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\MeetingScheduledMail($meeting, $user->fullname ?? $user->username, $receiver->fullname ?? $receiver->username));
                \Illuminate\Support\Facades\Mail::to($receiver->email)->send(new \App\Mail\MeetingScheduledMail($meeting, $receiver->fullname ?? $receiver->username, $user->fullname ?? $user->username));
            } catch (\Exception $mailEx) {
                \Log::error('API Meeting Email Error: ' . $mailEx->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => __('Meeting scheduled successfully'),
                'meeting_link' => $meeting->meeting_link,
                'meeting' => $meeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
