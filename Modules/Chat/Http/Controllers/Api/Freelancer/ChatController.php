<?php

namespace Modules\Chat\Http\Controllers\Api\Freelancer;

use App\Mail\BasicMail;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\Chat\Entities\LiveChat;
use Modules\Chat\Entities\LiveChatMessage;
use Modules\Chat\Http\Requests\FetchChatRecordRequest;
use Modules\Chat\Services\UserChatService;
use Modules\SupportTicket\Entities\ChatMessage;

use Modules\Chat\Http\Traits\ChatApiTrait;

class ChatController extends Controller
{
    use ChatApiTrait;
    public function client_list(Request $request)
    {
        \Carbon\Carbon::setLocale('en');
        app()->setLocale('en');
        
        $filter = $request->get('filter', 'all');

        // Check if freelancer's chat is disabled by admin
        $currentUser = auth('sanctum')->user();
        if ($currentUser->freeze_chat == 'freeze') {
            return response()->json([
                'msg' => __('Your chat has been disabled by admin. Please contact your administrator.')
            ])->setStatusCode(403);
        }
        
        $query = LiveChat::with("client:id,first_name,last_name,image,check_online_status,load_from")
            ->withCount("client_unseen_msg","freelancer_unseen_msg")
            ->with(['lastMessage' => function($q) {
                $q->select('id', 'live_chat_id', 'message', 'from_user', 'created_at', 'file');
            }])
            ->where("freelancer_id", $currentUser->id);

        if ($filter === 'archived') {
            $query->where('freelancer_archived', 1);
        } else {
            $query->where('freelancer_archived', 0);
        }

        if ($filter === 'favorites') {
            $favoriteClientIds = \App\Models\ChatFavorite::where('user_id', $currentUser->id)
                ->where('user_type', 'freelancer')
                ->pluck('chat_with_user_id')
                ->toArray();
            $query->whereIn('client_id', $favoriteClientIds);
        }

        $freelancer_chat_list = $query->orderByDesc('freelancer_unseen_msg_count')
            ->paginate(10)->withQueryString();

        $profile_image_path = asset('assets/uploads/profile/');

        //check user active inactive
        $active_users = [];
        foreach($freelancer_chat_list->pluck("client_id") as $id){
            if(Cache::has('user_is_online_'.$id)){
                $active_users[] = $id;
            }
        }

        //check user activity
        $activity_check = [];
        foreach($freelancer_chat_list as $list){
            $activity_check[$list->client?->id] =  $list->client?->check_online_status?->diffForHumans();
        }

        if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
            $freelancer_chat_list->transform(function ($list) {
                if($list->client) {
                    $list->client->cloud_link = render_frontend_cloud_image_if_module_exists('profile/'.$list?->client->image, load_from: $list?->client->load_from);
                    $list->client->profile_image_url = $list->client->cloud_link;
                }
                return $list;
            });
        } else {
            $freelancer_chat_list->transform(function ($list) use ($profile_image_path) {
                if($list->client) {
                    $list->client->profile_image_url = $list->client->image
                        ? $profile_image_path . $list->client->image
                        : null;
                }
                return $list;
            });
        }

        return response()->json([
            'chat_list'=> $freelancer_chat_list,
            'profile_image_path'=> $profile_image_path,
            'active_users'=> $active_users,
            'activity_check'=> $activity_check,
            'storage_driver' => Storage::getDefaultDriver() ?? '',
        ]);
    }

    public function fetch_record($live_chat_id)
    {
        \Carbon\Carbon::setLocale('en');
        app()->setLocale('en');
        $all_message = LiveChatMessage::where('live_chat_id',$live_chat_id)
            ->latest()->paginate(20)->withQueryString();

        $tempAllMessage = $all_message->getCollection();

//        LiveChatMessage::where('from_user',1)->where('live_chat_id',$live_chat_id)->update(['is_seen'=>1]);

        if (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
            $tempAllMessage->transform(function ($msg) {
                // check hare for selected driver
                if(array_key_exists('project', $msg->message ?? []) ){
                    if(array_key_exists('image', $msg->message['project'] ?? [])){
                        $message = [...$msg->message];
                        $project = [...$message['project']];
                        $project['cloud_link'] = $msg->message['project']['image'] ? render_frontend_cloud_image_if_module_exists('project/' . $msg->message['project']['image'],load_from: 1) : null;

                        unset($msg->message);
                        $message['project'] = $project;
                        $msg->message = $message;
                    }
                }

                return $msg;
            });
        }

        if($all_message){
            return response()->json([
                'all_message' => $all_message,
                'attachment_path' => asset('assets/uploads/media-uploader/live-chat/'),
                'project_path' => asset('assets/uploads/project/'),
                'storage_driver' => Storage::getDefaultDriver() ?? '',
            ]);
        }
        return response()->json(['msg' => __('No message found.')]);
    }

    public function message_send(Request $request)
    {
        if(empty(env("PUSHER_APP_ID")) && empty(env("PUSHER_APP_KEY")) && empty(env("PUSHER_APP_SECRET")) && empty(env("PUSHER_HOST"))){
            return response()->json([
                'msg'=>__("Please configure your pusher credentials.")
            ]);
        }

        //find user for withdraw freeze check
        $find_user_for_chat_freeze = User::find(auth('sanctum')->id());
        if($find_user_for_chat_freeze->freeze_chat == 'freeze'){
            return response()->json([
                'msg' => __('Your chat has been freeze. Please contact your administrator.')
            ])->setStatusCode(422);
        }

        // prevent restricted word for chat
        if (\Modules\Chat\Services\ProfanityFilterService::containsBadWords($request->message)) {
            return response()->json([
                'msg' => __('Your message contains prohibited words and cannot be sent.')
            ], 422);
        }

        // Check if chat is blocked
        if ($request->client_id && \App\Models\ChatBlock::isBlocked(auth('sanctum')->id(), $request->client_id)) {
            return response()->json([
                'msg' => __('You cannot send messages as the chat is blocked.')
            ], 422);
        }


        if(empty($request->message) && empty($request->file)){
            $request->validate([
                'message'=>'required'
            ]);
        }

        if(!empty($request->file)){
            $request->validate([
                'file'=>'required|mimes:jpg,png,jpeg,gif,pdf'
            ]);
        }

        // Validate client_id
        $request->validate([
            'client_id' => 'required|exists:users,id'
        ]);

        // send message
        $message_send = UserChatService::send(
            $request->client_id,
            auth('sanctum')->id(),
            $request->message,2,
            $request->file,
            $request->project_id ?? null);

        if(get_static_option('chat_email_enable_disable') == 'enable'){
            if($request->client_id){
                if (!Cache::has('user_is_online_' . $request->client_id)){
                    $user = User::select('id', 'email', 'check_online_status')->where('id', $request->client_id)->first();
//                        dispatch(new SendEmailJob($user->email,$request->message));
                    try {
                        Mail::to($user->email)->send(new BasicMail([
                            'subject' =>  __('Chat Email'),
                            'message' => __('You have a new chat message. Please check')
                        ]));
                    }
                    catch (\Exception $e) {}
                }

            }
        }

        return response()->json([
            'status'=>'Message successfully send'
        ]);
    }

    public function credentials()
    {
        $pusher_app_id = !empty(env('PUSHER_APP_ID')) ? env('PUSHER_APP_ID') : '';
        $pusher_app_key = !empty(env('PUSHER_APP_KEY')) ? env('PUSHER_APP_KEY') : '';
        $pusher_app_secret = !empty(env('PUSHER_APP_SECRET')) ? env('PUSHER_APP_SECRET') : '';
        $pusher_app_cluster = !empty(env('PUSHER_APP_CLUSTER')) ? env('PUSHER_APP_CLUSTER') : '';

        return response()->json([
            'pusher_app_id' => $pusher_app_id,
            'pusher_app_key' => $pusher_app_key,
            'pusher_app_secret' => $pusher_app_secret,
            'pusher_app_cluster' => $pusher_app_cluster,
        ]);
    }

    //unseen message count
    public function unseen_message_count()
    {
        $message = User::select('id')->withCount(['freelancer_unseen_message' => function($q){
            $q->where('is_seen',0);
        }])->where('id', auth('sanctum')->user()->id)->first();

        return response()->json([
            'unseen_message' => $message,
        ]);
    }

}
