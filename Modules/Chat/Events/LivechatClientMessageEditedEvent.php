<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LivechatClientMessageEditedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $client_id;
    public int $freelancer_id;
    public int $message_id;
    public string $message;
    public ?string $updated_at;

    public function __construct(int $client_id, int $freelancer_id, int $message_id, string $message, ?string $updated_at)
    {
        $this->client_id = $client_id;
        $this->freelancer_id = $freelancer_id;
        $this->message_id = $message_id;
        $this->message = $message;
        $this->updated_at = $updated_at;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('livechat-client-channel.' . $this->freelancer_id . '.' . $this->client_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'livechat-client-edited-' . $this->client_id;
    }
}


