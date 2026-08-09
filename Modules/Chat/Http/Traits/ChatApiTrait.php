<?php

namespace Modules\Chat\Http\Traits;

use Modules\Chat\Entities\LiveChatMessage;
use Modules\Chat\Events\LivechatMessageStatusEvent;

trait ChatApiTrait
{
    /**
     * Mark a message as delivered
     */
    public function message_delivered($message_id)
    {
        $message = LiveChatMessage::with('liveChat')->find($message_id);
        if (!$message) {
            return response()->json(['status' => 'error', 'message' => 'Message not found'], 404);
        }

        // Only update if not already delivered or seen
        if (!$message->is_delivered && !$message->is_seen) {
            $message->update(['is_delivered' => 1]);
            
            $livechat = $message->liveChat;
            
            // Channel/Event logic based on who sent the message
            // If from_user is 1 (Client), recipient is Freelancer
            // If from_user is 2 (Freelancer), recipient is Client
            
            if ($message->from_user == 1) {
                // Client sent, Freelancer received. Notify Client that it's delivered.
                $channel = 'livechat-client-channel.' . $livechat->freelancer_id . '.' . $livechat->client_id;
                $event = 'livechat-client-status-' . $livechat->client_id;
            } else {
                // Freelancer sent, Client received. Notify Freelancer that it's delivered.
                $channel = 'livechat-freelancer-channel.' . $livechat->client_id . '.' . $livechat->freelancer_id;
                $event = 'livechat-freelancer-status-' . $livechat->freelancer_id;
            }

            event(new LivechatMessageStatusEvent($message->id, 'delivered', $channel, $event));
        }

        return response()->json(['status' => 'success', 'message' => 'Message marked as delivered']);
    }

    /**
     * Mark a message as seen/read
     */
    public function message_seen($message_id)
    {
        $message = LiveChatMessage::with('liveChat')->find($message_id);
        if (!$message) {
            return response()->json(['status' => 'error', 'message' => 'Message not found'], 404);
        }

        if (!$message->is_seen) {
            $message->update(['is_seen' => 1, 'is_delivered' => 1]);

            $livechat = $message->liveChat;

            if ($message->from_user == 1) {
                // Client sent, Freelancer seen. Notify Client.
                $channel = 'livechat-client-channel.' . $livechat->freelancer_id . '.' . $livechat->client_id;
                $event = 'livechat-client-status-' . $livechat->client_id;
            } else {
                // Freelancer sent, Client seen. Notify Freelancer.
                $channel = 'livechat-freelancer-channel.' . $livechat->client_id . '.' . $livechat->freelancer_id;
                $event = 'livechat-freelancer-status-' . $livechat->freelancer_id;
            }

            event(new LivechatMessageStatusEvent($message->id, 'seen', $channel, $event));
        }

        return response()->json(['status' => 'success', 'message' => 'Message marked as seen']);
    }
}
