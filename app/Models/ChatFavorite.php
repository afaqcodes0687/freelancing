<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chat_with_user_id',
        'user_type',
    ];

    /**
     * Get the user who favorited the chat.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user with whom the chat is favorited.
     */
    public function chatWithUser()
    {
        return $this->belongsTo(User::class, 'chat_with_user_id');
    }

    /**
     * Check if a chat is favorited by a user.
     */
    public static function isFavorited($userId, $chatWithUserId, $userType)
    {
        return self::where('user_id', $userId)
            ->where('chat_with_user_id', $chatWithUserId)
            ->where('user_type', $userType)
            ->exists();
    }

    /**
     * Toggle favorite status.
     */
    public static function toggleFavorite($userId, $chatWithUserId, $userType)
    {
        $existing = self::where('user_id', $userId)
            ->where('chat_with_user_id', $chatWithUserId)
            ->where('user_type', $userType)
            ->first();

        if ($existing) {
            $existing->delete();
            return false; // Removed from favorites
        } else {
            self::create([
                'user_id' => $userId,
                'chat_with_user_id' => $chatWithUserId,
                'user_type' => $userType,
            ]);
            return true; // Added to favorites
        }
    }
}
