<?php

namespace Modules\Chat\Http\Controllers\Api\Freelancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Chat\Entities\LiveChat;

class ChatArchiveController extends Controller
{
    /**
     * Archive (End) a conversation for the freelancer.
     * Only hides it from freelancer's side.
     * If the other party sends a new message, it will be unarchived automatically.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function archive(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();

        $chat = LiveChat::where('client_id', $request->client_id)
            ->where('freelancer_id', $userId)
            ->first();

        if (!$chat) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Conversation not found or you do not have permission.'),
            ], 404);
        }

        $chat->update([
            'freelancer_archived' => 1,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('Conversation archived successfully.'),
        ]);
    }

    /**
     * End a conversation for the freelancer.
     * Hides it from freelancer's side and updates ended_at timestamp.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function end_conversation(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();

        $chat = LiveChat::where('client_id', $request->client_id)
            ->where('freelancer_id', $userId)
            ->first();

        if (!$chat) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Conversation not found or you do not have permission.'),
            ], 404);
        }

        $chat->update([
            'freelancer_archived' => 1,
            'ended_at'            => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('Conversation ended successfully.'),
        ]);
    }

    /**
     * Restore (Unarchive) a previously archived conversation for the freelancer.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();

        $chat = LiveChat::where('client_id', $request->client_id)
            ->where('freelancer_id', $userId)
            ->first();

        if (!$chat) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Conversation not found or you do not have permission.'),
            ], 404);
        }

        $chat->update([
            'freelancer_archived' => 0,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('Conversation restored successfully.'),
        ]);
    }

    /**
     * Get all archived conversations for the freelancer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = auth('sanctum')->id();

        $archived = LiveChat::with(['client:id,first_name,last_name,username,image,load_from'])
            ->where('freelancer_id', $userId)
            ->where('freelancer_archived', 1)
            ->orderByDesc('ended_at')
            ->get()
            ->map(function ($chat) {
                return [
                    'chat_id'     => $chat->id,
                    'archived_at' => $chat->ended_at,
                    'client'      => $chat->client ? [
                        'id'       => $chat->client->id,
                        'name'     => $chat->client->first_name . ' ' . $chat->client->last_name,
                        'username' => $chat->client->username,
                        'image'    => $chat->client->image,
                    ] : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $archived,
        ]);
    }
}
