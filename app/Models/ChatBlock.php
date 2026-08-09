<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'blocker_id',
        'blocked_id',
    ];

    /**
     * Check if one user has blocked the other.
     */
    public static function isBlocked($userId, $otherUserId)
    {
        return self::where(function($query) use ($userId, $otherUserId) {
            $query->where('blocker_id', $userId)->where('blocked_id', $otherUserId);
        })->orWhere(function($query) use ($userId, $otherUserId) {
            $query->where('blocker_id', $otherUserId)->where('blocked_id', $userId);
        })->exists();
    }

    /**
     * Check if specifically blocker has blocked blocked user.
     */
    public static function hasBlocked($blockerId, $blockedId)
    {
        return self::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->exists();
    }
    
    /**
     * Toggle block status.
     */
    public static function toggleBlock($blockerId, $blockedId)
    {
        $existing = self::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->first();

        if ($existing) {
            $existing->delete();
            return false; // Unblocked
        } else {
            self::create([
                'blocker_id' => $blockerId,
                'blocked_id' => $blockedId,
            ]);
            return true; // Blocked
        }
    }
}
