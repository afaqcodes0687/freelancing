<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;

    public const PLATFORM_ANDROID = 'android';
    public const PLATFORM_IOS = 'ios';

    protected $fillable = [
        'version',
        'version_name',
        'platform',
        'release_notes',
        'download_url',
        'file_size',
        'min_supported_version',
        'is_active',
        'force_update',
        'checksum',
        'signature',
        'release_date'
    ];

    protected $casts = [
        'release_notes' => 'array',
        'is_active' => 'boolean',
        'force_update' => 'boolean',
        'release_date' => 'datetime',
        'file_size' => 'integer'
    ];

    protected $appends = [
        'formatted_file_size',
        'release_notes_text',
    ];

    /**
     * Scope to get active versions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get versions by platform
     */
    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get latest version for a platform
     */
    public function scopeLatestForPlatform($query, string $platform)
    {
        return $query->byPlatform($platform)->active()->orderByDesc('release_date')->orderByDesc('id');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function getReleaseNotesTextAttribute(): string
    {
        if (empty($this->release_notes) || !is_array($this->release_notes)) {
            return '';
        }

        return implode(PHP_EOL, $this->release_notes);
    }

    public static function platforms(): array
    {
        return [
            self::PLATFORM_ANDROID => 'Android',
            self::PLATFORM_IOS => 'iOS',
        ];
    }
}
