<?php

namespace Modules\Chat\Http\Controllers\Api\Freelancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatBlock;

class ChatBlockController extends Controller
{
    /**
     * Toggle chat block status for freelancer
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

        $isBlocked = ChatBlock::toggleBlock($userId, $clientId);

        return response()->json([
            'status' => 'success',
            'is_blocked' => $isBlocked,
            'message' => $isBlocked ? __('Blocked successfully') : __('Unblocked successfully')
        ]);
    }

    /**
     * Check if a chat is blocked
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

        $hasBlocked = ChatBlock::hasBlocked($userId, $clientId);
        $isBlocked = ChatBlock::isBlocked($userId, $clientId);

        return response()->json([
            'status' => 'success',
            'has_blocked' => $hasBlocked,
            'is_blocked' => $isBlocked
        ]);
    }
}
