<?php

namespace Modules\Chat\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatBlock;

class ChatBlockController extends Controller
{
    /**
     * Toggle chat block status for client
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

        $isBlocked = ChatBlock::toggleBlock($userId, $freelancerId);

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
            'freelancer_id' => 'required|exists:users,id',
        ]);

        $userId = auth('sanctum')->id();
        $freelancerId = $request->freelancer_id;

        $hasBlocked = ChatBlock::hasBlocked($userId, $freelancerId);
        $isBlocked = ChatBlock::isBlocked($userId, $freelancerId);

        return response()->json([
            'status' => 'success',
            'has_blocked' => $hasBlocked,
            'is_blocked' => $isBlocked
        ]);
    }
}
