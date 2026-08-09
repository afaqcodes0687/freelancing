<?php

namespace Modules\Chat\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Chat\Entities\LiveChat;

class ChatArchiveController extends Controller
{
    /**
     * Archive (End) a conversation for the client.
     * Only hides it from client's side.
     * If the other party sends a new message, it will be unarchived automatically.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function archive(Request $request)
    {
        $request->validate([
            'freelancer_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();

        $chat = LiveChat::where('client_id', $userId)
            ->where('freelancer_id', $request->freelancer_id)
            ->first();

        if (!$chat) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Conversation not found or you do not have permission.'),
            ], 404);
        }

        $chat->update([
            'client_archived' => 1,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('Conversation archived successfully.'),
        ]);
    }

    /**
     * End a conversation for the client.
     * Hides it from client's side and updates ended_at timestamp.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function end_conversation(Request $request)
    {
        $request->validate([
            'freelancer_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();

        $chat = LiveChat::where('client_id', $userId)
            ->where('freelancer_id', $request->freelancer_id)
            ->first();

        if (!$chat) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Conversation not found or you do not have permission.'),
            ], 404);
        }

        $chat->update([
            'client_archived' => 1,
            'ended_at'        => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('Conversation ended successfully.'),
        ]);
    }

    /**
     * Restore (Unarchive) a previously archived conversation for the client.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request)
    {
        $request->validate([
            'freelancer_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();

        $chat = LiveChat::where('client_id', $userId)
            ->where('freelancer_id', $request->freelancer_id)
            ->first();

        if (!$chat) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Conversation not found or you do not have permission.'),
            ], 404);
        }

        $chat->update([
            'client_archived' => 0,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => __('Conversation restored successfully.'),
        ]);
    }

    /**
     * Get all archived conversations for the client.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = auth('sanctum')->id();

        $archived = LiveChat::with(['freelancer:id,first_name,last_name,username,image,load_from'])
            ->where('client_id', $userId)
            ->where('client_archived', 1)
            ->orderByDesc('ended_at')
            ->get()
            ->map(function ($chat) {
                return [
                    'chat_id'     => $chat->id,
                    'archived_at' => $chat->ended_at,
                    'freelancer'  => $chat->freelancer ? [
                        'id'       => $chat->freelancer->id,
                        'name'     => $chat->freelancer->first_name . ' ' . $chat->freelancer->last_name,
                        'username' => $chat->freelancer->username,
                        'image'    => $chat->freelancer->image,
                    ] : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $archived,
        ]);
    }
}
