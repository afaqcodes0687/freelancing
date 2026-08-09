<?php

namespace Modules\Meeting\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Chat\Entities\LiveChat;

class Meeting extends Model
{
    protected $fillable = [
        'live_chat_id',
        'sender_id',
        'receiver_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'meeting_link',
        'google_event_id',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function liveChat()
    {
        return $this->belongsTo(LiveChat::class, 'live_chat_id');
    }
}
