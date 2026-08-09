<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LivechatMessageStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message_id;
    public $status;
    public $channel_name;
    public $event_name;

    public function __construct(int $message_id, string $status, string $channel_name, string $event_name)
    {
        $this->message_id = $message_id;
        $this->status = $status;
        $this->channel_name = $channel_name;
        $this->event_name = $event_name;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->channel_name),
        ];
    }

    function broadcastAs(): string
    {
        return $this->event_name;
    }
}
