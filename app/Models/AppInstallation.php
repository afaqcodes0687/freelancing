<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppInstallation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'version',
        'platform',
        'device_id',
        'previous_version',
        'ip_address',
        'user_agent',
        'device_info',
        'installed_at'
    ];

    protected $casts = [
        'device_info' => 'array',
        'installed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the installation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get installations by platform
     */
    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get installations by version
     */
    public function scopeByVersion($query, string $version)
    {
        return $query->where('version', $version);
    }

    /**
     * Scope to get recent installations
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('installed_at', '>=', now()->subDays($days));
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('installed_at')->orderByDesc('id');
    }
}
