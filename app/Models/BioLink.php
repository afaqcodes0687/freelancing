<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BioLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'url',
        'type',
        'is_featured',
        'is_active',
        'sort_order',
        'icon',
        'color'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the user that owns the bio link
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the clicks for this bio link
     */
    public function clicks()
    {
        return $this->hasMany(LinkClick::class);
    }

    /**
     * Get total clicks count
     */
    public function getTotalClicksAttribute()
    {
        return $this->clicks()->count();
    }

    /**
     * Get today's clicks count
     */
    public function getTodayClicksAttribute()
    {
        return $this->clicks()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get clicks in the last 7 days
     */
    public function getLastWeekClicksAttribute()
    {
        return $this->clicks()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    /**
     * Get clicks in the last 30 days
     */
    public function getLastMonthClicksAttribute()
    {
        return $this->clicks()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }

    /**
     * Get the URL with referral parameter if it's an affiliate link
     */
    public function getUrlWithReferralAttribute()
    {
        if ($this->type === 'affiliate' && $this->user) {
            $separator = parse_url($this->url, PHP_URL_QUERY) ? '&' : '?';
            return $this->url . $separator . 'ref=' . $this->user->referral_code;
        }
        
        return $this->url;
    }

    /**
     * Scope to get only active links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only featured links
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get links by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
