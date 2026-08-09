<?php

namespace Modules\Chat\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatFavorite;

class ChatFavoriteController extends Controller
{
    /**
     * Toggle chat favorite status for client
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'freelancer_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();
        $freelancerId = $request->freelancer_id;
        $userType = 'client';

        $isFavorited = ChatFavorite::toggleFavorite($userId, $freelancerId, $userType);

        return response()->json([
            'status' => 'success',
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? 'Added to favorites' : 'Removed from favorites'
        ]);
    }

    /**
     * Get all favorite chats for client
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = auth('sanctum')->id();

        $favorites = ChatFavorite::with(['chatWithUser:id,first_name,last_name,image,load_from'])
            ->where('user_id', $userId)
            ->where('user_type', 'client')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $favorites
        ]);
    }

    /**
     * Check if a chat is favorited
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        $request->validate([
            'freelancer_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();
        $freelancerId = $request->freelancer_id;
        $userType = 'client';

        $isFavorited = ChatFavorite::isFavorited($userId, $freelancerId, $userType);

        return response()->json([
            'status' => 'success',
            'is_favorited' => $isFavorited
        ]);
    }
}
