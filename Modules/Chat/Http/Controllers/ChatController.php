<?php

namespace Modules\Chat\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Mail\BasicMail;
use App\Models\Order;
use App\Models\User;
use Cache;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use JetBrains\PhpStorm\NoReturn;
use Modules\Chat\Entities\LiveChat;
use Modules\Chat\Http\Requests\FetchChatRecordRequest;
use Modules\Chat\Http\Requests\MessageSendRequest;
use Modules\Chat\Entities\LiveChatMessage;
use Modules\Chat\Events\LivechatClientMessageEditedEvent;
use Modules\Chat\Services\UserChatService;
use Modules\SecurityManage\Entities\Word;

class ChatController extends Controller
{
    public function live_chat()
    {
        // Check if client's chat is disabled by admin
        $currentUser = auth('web')->user();
        if ($currentUser->freeze_chat == '1') {
            return redirect()->back()->with(toastr_error(__('Your chat has been disabled by admin')));
        }

        $filter = request()->get('filter', 'all');
        $userId = auth('web')->id();

        $client_chat_list = LiveChat::with("client","freelancer")
                                    ->whereHas('freelancer')
                                    ->withCount("client_unseen_msg","freelancer_unseen_msg")
                                    ->where("client_id", $userId)
                                    ->where('client_archived', 0);

        // Filter by favorites if requested
        if ($filter === 'favorites') {
            $favoriteFreelancerIds = \App\Models\ChatFavorite::where('user_id', $userId)
                ->where('user_type', 'client')
                ->pluck('chat_with_user_id')
                ->toArray();
            $client_chat_list = $client_chat_list->whereIn('freelancer_id', $favoriteFreelancerIds);
        }

        $client_chat_list = $client_chat_list->orderByDesc('client_unseen_msg_count')->orderByDesc('updated_at')->orderByDesc('id')->get();
        $arr = "";
        foreach($client_chat_list->pluck("freelancer_id") as $id){
            $arr .= "freelancer_id_". $id .": false,";
        }
        $arr = rtrim($arr,",");
        return view("chat::client.index",compact('client_chat_list','arr'));
    }

    public function fetch_chat_record(FetchChatRecordRequest $request){
        $data = $request->validated();
        $data = UserChatService::fetch($data["freelancer_id"],$data["client_id"],from: 1);
        $currentUserType = "freelancer";

        $body = view("chat::client.message-body", compact('data'))->render();
        $header = view("chat::client.message-header", compact('data'))->render();

        return response()->json([
            "body" => $body,
            "header" => $header,
            "allow_load_more" => $data->allow_load_more ?? false,
        ]);
    }

    #[NoReturn]
    public function message_send(Request $request)
    {
        $order_details = Order::where('id', $request->order_id ?? 0)->first();

        // Check if client's chat is disabled by admin
        $currentUser = auth('web')->user();
        if ($currentUser->freeze_chat == 'freeze') {
            return back()->with(toastr_error(__('Your chat has been disabled by admin')));
        }

        // Check if freelancer's chat is disabled by admin
        $freelancer = User::find($request->freelancer_id);
        if ($freelancer && $freelancer->freeze_chat == 'freeze') {
            return back()->with(toastr_error(__('This freelancer\'s chat has been disabled by admin')));
        }

        // check livechat configuration
        if (
            empty(env("PUSHER_APP_ID")) &&
            empty(env("PUSHER_APP_KEY")) &&
            empty(env("PUSHER_APP_SECRET")) &&
            empty(env("PUSHER_HOST"))
        ) {
            return back()->with(toastr_error(__("Please configure your pusher credentials")));
        }

        // prevent restricted word for chat
        if (\Modules\Chat\Services\ProfanityFilterService::containsBadWords($request->message)) {
            return response()->json(['message' => __('Your message contains prohibited words and cannot be sent.')], 422);
        }

        if ($order_details?->is_project_job != 'offer') {
            try {
                // send chat message
                $message_send = UserChatService::send(
                    auth('web')->id(),
                    $request->freelancer_id,
                    $request->interview_message ?? $request->message,
                    1,
                    $request->file,
                    (int) ($request->project_id ?? $request->job_id),
                    $order_details->is_project_job ?? $request->type,
                    (int) ($request->proposal_id ?? 0),
                    $request->interview_message ?? '',
                    'html'
                );


                // === SEND EMAIL NOTIFICATION IF USER OFFLINE ===
                $client = User::select('id', 'first_name', 'last_name', 'email')
                    ->where('id', $request->freelancer_id)
                    ->first();

                $freelancer = User::select('id', 'first_name', 'last_name')
                    ->where('id', auth('web')->id())
                    ->first();

                if ($client && $client->email) {
                    // check user online status
                    if (!Cache::has('user_is_online_' . $client->id)) {
                        // user is offline -> send email
                        $freelancerName = $freelancer ? $freelancer->first_name . ' ' . $freelancer->last_name : __('Freelancer');
                        $messageContent = $request->message;
                        $chat_link = route('client.live.chat', ['freelancer_id' => $client->id]);

                        $emailMessage = '
                            <div style="max-width:600px;margin:auto;background-color:#ffffff;border:1px solid #e0e0e0;border-radius:10px;padding:0;font-family:Arial,sans-serif;color:#333;">
                                <div style="padding:30px;">
                                    <div style="border-bottom:1px solid #ddd;padding-bottom:20px;margin-bottom:20px;">
                                        <h2 style="color:#309400;margin:0;">📩 New Message on RightFreelancer</h2>
                                    </div>
                                    <p style="font-size:16px;line-height:1.6;">
                                        Hello <strong>' . e($client->first_name . ' ' . $client->last_name) . '</strong>,
                                    </p>
                                    <p style="font-size:16px;line-height:1.6;">
                                        You have received a new message from <strong>' . e($freelancerName) . '</strong>.
                                    </p>
                                    <p style="font-size:16px;line-height:1.6;">
                                        <strong>Message:</strong><br>
                                        ' . nl2br(e($messageContent)) . '
                                    </p>
                                
                                    <p style="margin-top:12px;font-size:15px;line-height:1.6;">
                                        Best regards,<br>
                                        <strong>The RightFreelancer Team</strong>
                                    </p>
                                </div>
                            </div>
                        ';

                        try {
                            Mail::to($client->email)->queue(new \App\Mail\BasicMail([
                                'subject' => __('📩 New Chat Message - RightFreelancer'),
                                'message' => $emailMessage
                            ]));
                        } catch (\Exception $e) {
                            \Log::error("Chat Email failed for user_id {$client->id}: " . $e->getMessage());
                        }
                    }
                }

                if ($request->from === 'chatbox') {
                    // If freelancer had archived this chat, unarchive it now that client sent a message
                    LiveChat::where('client_id', auth('web')->id())
                        ->where('freelancer_id', $request->freelancer_id)
                        ->where('freelancer_archived', 1)
                        ->update(['freelancer_archived' => 0]);

                    return $message_send;
                }
            } catch (\RuntimeException $e) {
                return back()->with(toastr_warning($e->getMessage()));
            }
        }

        return redirect()->route('client.live.chat', [
            'freelancer_id' => $request->freelancer_id
        ]);
    }



    public function message_update(Request $request)
    {
        $request->validate([
            'message_id' => 'required|integer',
            'message' => 'required|string|max:5000',
        ]);

        $message = LiveChatMessage::with('liveChat')
            ->where('id', $request->message_id)
            ->firstOrFail();

        // ensure the authenticated client owns this message and it is sent by client
        if (($message->from_user ?? 0) !== 1 || ($message->liveChat?->client_id !== auth('web')->id())) {
            abort(403);
        }

        //prevent restricted word for chat
        if(moduleExists('SecurityManage')) {
            $text = $request->message;
            $restrictedWords = Word::where('status', 'active')->pluck('word')->toArray();
            $matchedWords = array_filter($restrictedWords, function($word) use ($text) {
                return strpos($text, $word) !== false;
            });
            if (count($matchedWords) > 0) {
                return response()->json(['status' => 'restricted_words', 'words' => array_values($matchedWords)], 422);
            }
        }

        $payload = $message->message;
        if (!is_array($payload)) {
            $payload = [ 'message' => (string) $payload, 'project' => null ];
        }
        $payload['message'] = $request->message;
        $message->message = $payload;
        $message->save();

        // broadcast to freelancer to update their view
        event(new LivechatClientMessageEditedEvent(
            $message->liveChat->client_id,
            $message->liveChat->freelancer_id,
            $message->id,
            $payload['message'],
            $message->updated_at?->toIso8601String()
        ));

        return response()->json([
            'status' => 'ok',
            'message_id' => $message->id,
            'message' => $payload['message'],
            'updated_at' => $message->updated_at?->diffForHumans(),
        ]);
    }
    public function message_mark_delivered(Request $request)
    {
        $message_id = $request->message_id;
        $message = LiveChatMessage::find($message_id);
        if($message && $message->is_delivered == 0){
            $message->is_delivered = 1;
            $message->save();

            // Client received a message from Freelancer. So we notify the Freelancer.
            event(new \Modules\Chat\Events\LivechatMessageStatusEvent(
                $message_id,
                'delivered',
                'livechat-client-channel.' . $message->liveChat->freelancer_id . '.' . auth('web')->id(),
                'livechat-client-status-'. auth('web')->id()
            ));
        }
        return response()->json(['status' => 'ok']);
    }

    public function message_mark_seen(Request $request)
    {
        $message_id = $request->message_id;
        $message = LiveChatMessage::find($message_id);
        if($message && $message->is_seen == 0){
            $message->is_seen = 1;
            // Also mark as delivered if not already
            $message->is_delivered = 1;
            $message->save();

            // Client seen a message from Freelancer. Notify the Freelancer.
            event(new \Modules\Chat\Events\LivechatMessageStatusEvent(
                $message_id,
                'seen',
                'livechat-client-channel.' . $message->liveChat->freelancer_id . '.' . auth('web')->id(),
                'livechat-client-status-'. auth('web')->id()
            ));
        }
        return response()->json(['status' => 'ok']);
    }

    public function toggle_favorite(Request $request)
    {
        $request->validate([
            'freelancer_id' => 'required|exists:users,id',
        ]);

        $userId = auth('web')->id();
        $freelancerId = $request->freelancer_id;
        $userType = 'client';

        $isFavorited = \App\Models\ChatFavorite::toggleFavorite($userId, $freelancerId, $userType);

        return response()->json([
            'status' => 'ok',
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? __('Added to favorites') : __('Removed from favorites')
        ]);
    }

    public function filter_chats(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $userId = auth('web')->id();

        $client_chat_list = LiveChat::with("client","freelancer")
                                    ->whereHas('freelancer')
                                    ->withCount("client_unseen_msg","freelancer_unseen_msg")
                                    ->where("client_id", $userId);

        if ($filter === 'archived') {
            $client_chat_list = $client_chat_list->where('client_archived', 1);
        } else {
            $client_chat_list = $client_chat_list->where('client_archived', 0);
        }

        // Filter by favorites if requested
        if ($filter === 'favorites') {
            $favoriteFreelancerIds = \App\Models\ChatFavorite::where('user_id', $userId)
                ->where('user_type', 'client')
                ->pluck('chat_with_user_id')
                ->toArray();
            $client_chat_list = $client_chat_list->whereIn('freelancer_id', $favoriteFreelancerIds);
        }

        $client_chat_list = $client_chat_list->orderByDesc('client_unseen_msg_count')->orderByDesc('updated_at')->orderByDesc('id')->get();        $html = '';
        foreach($client_chat_list as $client_chat) {
            $html .= view('chat::components.client.freelancer-list', ['clientChat' => $client_chat])->render();
        }

        return response()->json([
            'status' => 'ok',
            'html' => $html,
            'count' => $client_chat_list->count()
        ]);
    }

    public function end_conversation(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:live_chats,id',
        ]);

        $userId = auth('web')->id();
        $chat = LiveChat::where('id', $request->chat_id)
            ->where('client_id', $userId)
            ->firstOrFail();

        $chat->update([
            'client_archived' => 1,
            'ended_at'        => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('Conversation ended successfully'),
        ]);
    }

    public function toggle_archive(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:live_chats,id',
        ]);

        $userId = auth('web')->id();
        $chat = LiveChat::where('id', $request->chat_id)
            ->where('client_id', $userId)
            ->firstOrFail();

        $isArchived = $chat->client_archived ? 0 : 1;
        
        $chat->update([
            'client_archived' => $isArchived,
        ]);

        return response()->json([
            'status' => 'success',
            'is_archived' => $isArchived,
            'message' => $isArchived ? __('Chat archived successfully') : __('Chat unarchived successfully'),
        ]);
    }
}
