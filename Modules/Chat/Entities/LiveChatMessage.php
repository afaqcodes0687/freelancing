<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Http;
use Modules\User\Entities\User;
use Modules\Vendor\Entities\Vendor;

class LiveChatMessage extends Model
{
    protected $fillable = [
        "live_chat_id",
        "from_user",
        "message",
        "file",
        'load_from',
        'is_synced',
        'is_seen',
        'is_delivered'
    ];

    protected $casts = [
        "message" => "json",
        "created_at" => "datetime",
        "updated_at" => "datetime",
        "is_seen" => "integer"
    ];

    public function liveChat(): BelongsTo
    {
        return $this->belongsTo(LiveChat::class,"live_chat_id","id");
    }

    public function client(): HasManyThrough
    {
        return $this->hasManyThrough(User::class,LiveChat::class,'live_chat_id','id','id','client_id');
    }

    public function freelancer(): HasManyThrough
    {
        return $this->hasManyThrough(User::class,LiveChat::class,'live_chat_id','id','id','freelancer_id');
    }

    //: this method will be return file path
    public function getFilePathAttribute(){
        return $this->file;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($modal){
            // first check who is the sender of this message if this is a customer, then send notification to the vendor
            // get vendor from the message
            $freelancer = $modal->liveChat->freelancer;
            $user = $modal->liveChat->client;

            $messageText = is_array($modal->message) ? (($modal->message['type'] ?? '') == 'meeting' ? __('Meeting Scheduled: ') . ($modal->message['title'] ?? '') : __('New Message')) : $modal->message;

            // send notification to the vendor
            $notificationBody = [
                'title' => $modal->from_user == 1 ? $user->first_name : $freelancer->first_name,
                'id' => $modal->id,
                'body' => $messageText,
                'file' => $modal->file,
                'description' => '',
                'type' => 'message',
                'sound' => 'default',
                'fcm_device' => '',
                'livechat' => $modal->liveChat
            ];

            // send notification to the vendor/client via background job
            $toToken = $modal->from_user == 1 ? ($freelancer->firebase_device_token ?? '') : ($user->firebase_device_token ?? '');
            if(!empty($toToken)){
                \App\Jobs\SendFcmNotificationJob::dispatch($notificationBody, $toToken);
            }
        });
    }
}
