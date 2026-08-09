<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'bio_link_id',
        'user_id',
        'visitor_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city'
    ];

    /**
     * Get the bio link that was clicked
     */
    public function bioLink()
    {
        return $this->belongsTo(BioLink::class);
    }

    /**
     * Get the user who owns the link
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the visitor who clicked (if logged in)
     */
    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }

    /**
     * Scope to get clicks by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get clicks for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope to get clicks for this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope to get clicks for this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope to get unique clicks by IP
     */
    public function scopeUniqueByIp($query)
    {
        return $query->distinct('ip_address');
    }

    /**
     * Get clicks analytics for a user
     */
    public static function getAnalyticsForUser($userId, $days = 30)
    {
        $startDate = now()->subDays($days);
        
        return [
            'total_clicks' => static::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->count(),
            'unique_clicks' => static::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->distinct('ip_address')
                ->count(),
            'top_links' => BioLink::withCount(['clicks' => function($query) use ($userId, $startDate) {
                    $query->where('user_id', $userId)
                          ->where('created_at', '>=', $startDate);
                }])
                ->where('user_id', $userId)
                ->orderBy('clicks_count', 'desc')
                ->limit(5)
                ->get(),
            'daily_clicks' => static::where('user_id', $userId)
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
                ->orderBy('date')
                ->pluck('clicks', 'date')
                ->toArray()
        ];
    }
}
