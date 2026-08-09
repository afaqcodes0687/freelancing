<?php

namespace Modules\Chat\Http\Controllers\Api\Client;

use App\Jobs\SendEmailJob;
use App\Mail\BasicMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\Chat\Entities\LiveChat;
use Modules\Chat\Entities\LiveChatMessage;
use Modules\Chat\Services\UserChatService;

use Modules\Chat\Http\Traits\ChatApiTrait;

class ChatController extends Controller
{
    use ChatApiTrait;
    public function freelancer_list(Request $request)
    {
        \Carbon\Carbon::setLocale('en');
        app()->setLocale('en');
        
        $filter = $request->get('filter', 'all');

        // Check if client's chat is disabled by admin
        $currentUser = auth('sanctum')->user();
        if ($currentUser->freeze_chat == 'freeze') {
            return response()->json([
                'msg' => __('Your chat has been disabled by admin. Please contact your administrator.')
            ])->setStatusCode(403);
        }
        
        $query = LiveChat::with("freelancer:id,first_name,last_name,image,check_online_status,load_from")
            ->withCount("client_unseen_msg","freelancer_unseen_msg")
            ->with(['lastMessage' => function($q) {
                $q->select('id', 'live_chat_id', 'message', 'from_user', 'created_at', 'file');
            }])
            ->where("client_id", $currentUser->id);

        if ($filter === 'archived') {
            $query->where('client_archived', 1);
        } else {
            $query->where('client_archived', 0);
        }

        if ($filter === 'favorites') {
            $favoriteFreelancerIds = \App\Models\ChatFavorite::where('user_id', $currentUser->id)
                ->where('user_type', 'client')
                ->pluck('chat_with_user_id')
                ->toArray();
            $query->whereIn('freelancer_id', $favoriteFreelancerIds);
        }

        $client_chat_list = $query->orderByDesc('client_unseen_msg_count')
            ->paginate(10)->withQueryString();

        $profile_image_path = asset('assets/uploads/profile/');

        //check user active inactive
        $active_users = [];
        foreach($client_chat_list->pluck("freelancer_id") as $id){
            if(Cache::has('user_is_online_'.$id)){
                $active_users[] = $id;
            }
        }

        //check user activity
        $activity_check = [];
        foreach($client_chat_list as $list){
            if($list->freelancer) {
                $activity_check[$list->freelancer->id] =  $list->freelancer?->check_online_status?->diffForHumans();
            }
        }

        if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
            $client_chat_list->transform(function ($list) {
                if($list->freelancer) {
                    $list->freelancer->cloud_link = render_frontend_cloud_image_if_module_exists('profile/'.$list?->freelancer->image, load_from: $list?->freelancer->load_from);
                    $list->freelancer->profile_image_url = $list->freelancer->cloud_link;
                }
                return $list;
            });
        } else {
            $client_chat_list->transform(function ($list) use ($profile_image_path) {
                if($list->freelancer) {
                    $list->freelancer->profile_image_url = $list->freelancer->image
                        ? $profile_image_path . $list->freelancer->image
                        : null;
                }
                return $list;
            });
        }

        return response()->json([
            'chat_list'=> $client_chat_list,
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

        LiveChatMessage::where('from_user',2)->where('live_chat_id',$live_chat_id)->update(['is_seen'=>1]);

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
        $order_details = Order::where('id',$request->order_id ?? 0)->first();

        # check livechat configuration value are exist or not
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
        if ($request->freelancer_id && \App\Models\ChatBlock::isBlocked(auth('sanctum')->id(), $request->freelancer_id)) {
            return response()->json([
                'msg' => __('You cannot send messages as the chat is blocked.')
            ], 422);
        }

        if($order_details?->is_project_job != 'offer'){
            //: send message
            $find_freelancer = User::where('id',$request->freelancer_id)->where('user_type',2)->first();
            if(empty($find_freelancer)){
                return response()->json(['msg'=> __('User not found')])->setStatusCode('422');
            }

            $message_send = UserChatService::send(
                auth('sanctum')->id(),
                $request->freelancer_id,
                $request->message,1,
                $request->file,
                (int) ($request->project_id ?? $request->job_id),
                $request->type,
                $request->proposal_id ?? '',
                $request->interview_message ?? '',
            );

            if(get_static_option('chat_email_enable_disable') == 'enable'){
                if($request->freelancer_id){
                    if (!Cache::has('user_is_online_' . $request->freelancer_id)){
                        $user = User::select('id', 'email', 'check_online_status')->where('id', $request->freelancer_id)->first();
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

            if($request->from === 'chatbox'){
                return $message_send;
            }
        }

        return response()->json([
            'status'=>'Message successfully send',
        ]);

//        return redirect()->route('client.live.chat',[
//            'freelancer_id'=>$request->freelancer_id
//        ]);

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
        $message = User::select('id')->withCount(['client_unseen_message' => function($q){
            $q->where('is_seen',0)->where('from_user',2);
        }])->where('id', auth('sanctum')->user()->id)->first();

        return response()->json([
            'unseen_message' => $message,
        ]);
    }
}
