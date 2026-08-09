<?php

namespace Modules\Chat\Http\Controllers\Api\Freelancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatFavorite;

class ChatFavoriteController extends Controller
{
    /**
     * Toggle chat favorite status for freelancer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();
        $clientId = $request->client_id;
        $userType = 'freelancer';

        $isFavorited = ChatFavorite::toggleFavorite($userId, $clientId, $userType);

        return response()->json([
            'status' => 'success',
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? 'Added to favorites' : 'Removed from favorites'
        ]);
    }

    /**
     * Get all favorite chats for freelancer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = auth('sanctum')->id();

        $favorites = ChatFavorite::with(['chatWithUser:id,first_name,last_name,image,load_from'])
            ->where('user_id', $userId)
            ->where('user_type', 'freelancer')
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
            'client_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();
        $clientId = $request->client_id;
        $userType = 'freelancer';

        $isFavorited = ChatFavorite::isFavorited($userId, $clientId, $userType);

        return response()->json([
            'status' => 'success',
            'is_favorited' => $isFavorited
        ]);
    }
}
