<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'reward_amount',
        'max_reward',
        'status',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'reward_amount' => 'decimal:2',
        'max_reward' => 'decimal:2'
    ];

    /**
     * Get the user who made the referral
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the user who was referred
     */
    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Check if referrer has reached the $500 limit
     */
    public static function hasReachedLimit($referrerId)
    {
        $totalEarned = self::where('referrer_id', $referrerId)
            ->where('status', 'completed')
            ->sum('reward_amount');
        
        return $totalEarned >= 500;
    }

    /**
     * Get total earnings for a referrer
     */
    public static function getTotalEarnings($referrerId)
    {
        return self::where('referrer_id', $referrerId)
            ->where('status', 'completed')
            ->sum('reward_amount');
    }

    /**
     * Get remaining earning potential
     */
    public static function getRemainingPotential($referrerId)
    {
        $totalEarned = self::getTotalEarnings($referrerId);
        return max(0, 500 - $totalEarned);
    }

    /**
     * Get pending referrals count
     */
    public static function getPendingCount($referrerId)
    {
        return self::where('referrer_id', $referrerId)
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Get completed referrals count
     */
    public static function getCompletedCount($referrerId)
    {
        return self::where('referrer_id', $referrerId)
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get total referrals count (all statuses)
     */
    public static function getTotalReferralsCount($referrerId)
    {
        return self::where('referrer_id', $referrerId)->count();
    }

    /**
     * Get badge progress for a specific badge level
     */
    public static function getBadgeProgress($referrerId, $requiredCount)
    {
        $completedCount = self::getCompletedCount($referrerId);
        return [
            'current' => min($completedCount, $requiredCount),
            'required' => $requiredCount,
            'percentage' => min(100, ($completedCount / $requiredCount) * 100),
            'is_completed' => $completedCount >= $requiredCount
        ];
    }
} 