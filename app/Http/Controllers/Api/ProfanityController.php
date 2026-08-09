<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Chat\Services\ProfanityFilterService;

class ProfanityController extends Controller
{
    /**
     * Check if a given message contains profanity/bad words.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkProfanity(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $containsBadWords = ProfanityFilterService::containsBadWords($request->message);

        if ($containsBadWords) {
            return response()->json([
                'status' => 'error',
                'contains_profanity' => true,
                'msg' => __('Your message contains prohibited words and cannot be sent.')
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'contains_profanity' => false,
            'msg' => __('Message is clean.')
        ], 200);
    }
}
