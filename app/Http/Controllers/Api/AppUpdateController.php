<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use App\Models\AppInstallation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AppUpdateController extends Controller
{
    /**
     * Check for app updates
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkUpdate(Request $request): JsonResponse
    {
        try {
            // Validate request
            $request->validate([
                'current_version' => 'required|string',
                'platform' => 'required|in:android,ios'
            ]);

            // Get current app version from request
            $currentVersion = $request->get('current_version');
            $platform = $request->get('platform', 'android'); // android or ios

            if (!$this->isUpdateEnabled()) {
                return response()->json([
                    'success' => true,
                    'message' => $this->getDisabledMessage(),
                    'data' => [
                        'update_available' => false,
                        'current_version' => $currentVersion,
                        'latest_version' => null,
                        'force_update' => false,
                        'update_info' => null
                    ]
                ]);
            }
            
            // Validate platform
            if (!in_array($platform, ['android', 'ios'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('Invalid platform specified'),
                    'data' => null
                ], 400);
            }

            // Get latest app version info from database or config
            $latestVersion = $this->getLatestVersionInfo($platform);
            
            if (!$latestVersion) {
                return response()->json([
                    'success' => true,
                    'message' => __('No updates available'),
                    'data' => [
                        'update_available' => false,
                        'current_version' => $currentVersion,
                        'latest_version' => null,
                        'force_update' => false,
                        'update_info' => null
                    ]
                ]);
            }

            // Compare versions
            $updateAvailable = $this->compareVersions($currentVersion, $latestVersion['version']);
            $forceUpdate = $this->shouldForceUpdate(
                $currentVersion,
                $latestVersion['version'],
                $latestVersion['min_supported_version'],
                (bool) ($latestVersion['force_update'] ?? false)
            );

            return response()->json([
                'success' => true,
                'message' => $updateAvailable ? __('Update available') : __('App is up to date'),
                'data' => [
                    'update_available' => $updateAvailable,
                    'current_version' => $currentVersion,
                    'latest_version' => $latestVersion['version'],
                    'force_update' => $forceUpdate,
                    'update_info' => $updateAvailable ? [
                        'version' => $latestVersion['version'],
                        'version_name' => $latestVersion['version_name'] ?? "Version {$latestVersion['version']}",
                        'release_date' => $latestVersion['release_date'],
                        'download_url' => $latestVersion['download_url'],
                        'file_size' => $latestVersion['file_size'] ?? 0,
                        'file_size_formatted' => $this->formatFileSize($latestVersion['file_size'] ?? 0),
                        'release_notes' => $latestVersion['release_notes'] ?? [],
                        'min_supported_version' => $latestVersion['min_supported_version'] ?? null,
                        'platform' => $platform,
                        'mandatory' => $forceUpdate,
                        'checksum' => $latestVersion['checksum'] ?? null,
                        'signature' => $latestVersion['signature'] ?? null,
                        'update_message' => $this->getUpdateMessage($platform),
                        'support_text' => get_static_option('app_update_support_text'),
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('App update check failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Failed to check for updates'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get update download URL
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDownloadUrl(Request $request): JsonResponse
    {
        try {
            $platform = $request->get('platform', 'android');
            $version = $request->get('version');
            
            if (!in_array($platform, ['android', 'ios'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('Invalid platform specified'),
                    'data' => null
                ], 400);
            }

            $versionInfo = $this->getLatestVersionInfo($platform);
            
            if (!$versionInfo || ($version && $version !== $versionInfo['version'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('Update not found'),
                    'data' => null
                ], 404);
            }

            // Generate signed download URL (valid for 1 hour)
            $downloadUrl = $this->generateSignedDownloadUrl($versionInfo['download_url']);

            return response()->json([
                'success' => true,
                'message' => __('Download URL generated'),
                'data' => [
                    'download_url' => $downloadUrl,
                    'expires_at' => now()->addHour()->toISOString(),
                    'file_size' => $versionInfo['file_size'] ?? 0,
                    'file_size_formatted' => $this->formatFileSize($versionInfo['file_size'] ?? 0),
                    'checksum' => $versionInfo['checksum'] ?? null,
                    'signature' => $versionInfo['signature'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Download URL generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Failed to generate download URL'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Record app installation/update
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function recordInstallation(Request $request): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();
            
            $request->validate([
                'version' => 'required|string',
                'platform' => 'required|in:android,ios',
                'device_id' => 'required|string',
                'previous_version' => 'nullable|string'
            ]);

            // Record installation/update in database
            $installation = AppInstallation::create([
                'user_id' => $user?->id,
                'version' => $request->get('version'),
                'platform' => $request->get('platform'),
                'device_id' => $request->get('device_id'),
                'previous_version' => $request->get('previous_version'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'installed_at' => now()
            ]);

            Log::info('App installation recorded', [
                'installation_id' => $installation->id,
                'user_id' => $user?->id,
                'version' => $request->get('version'),
                'platform' => $request->get('platform')
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Installation recorded successfully'),
                'data' => [
                    'installation_id' => $installation->id,
                    'recorded_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Installation recording failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('Failed to record installation'),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get latest version information
     * 
     * @param string $platform
     * @return array|null
     */
    private function getLatestVersionInfo(string $platform): ?array
    {
        $version = AppVersion::latestForPlatform($platform)->first();
        
        if (!$version) {
            return null;
        }

        return [
            'version' => $version->version,
            'version_name' => $version->version_name,
            'release_date' => $version->release_date->format('Y-m-d'),
            'download_url' => $this->resolveDownloadUrl($version),
            'file_size' => $version->file_size,
            'min_supported_version' => $version->min_supported_version,
            'release_notes' => $version->release_notes,
            'checksum' => $version->checksum,
            'signature' => $version->signature,
            'force_update' => $version->force_update
        ];
    }

    /**
     * Compare two version strings
     * 
     * @param string $currentVersion
     * @param string $latestVersion
     * @return bool
     */
    private function compareVersions(string $currentVersion, string $latestVersion): bool
    {
        return version_compare($currentVersion, $latestVersion, '<');
    }

    /**
     * Check if force update is required
     * 
     * @param string $currentVersion
     * @param string $latestVersion
     * @param string|null $minSupportedVersion
     * @return bool
     */
    private function shouldForceUpdate(
        string $currentVersion,
        string $latestVersion,
        ?string $minSupportedVersion,
        bool $forceUpdateFlag = false
    ): bool
    {
        if ($minSupportedVersion && version_compare($currentVersion, $minSupportedVersion, '<')) {
            return true;
        }

        return $forceUpdateFlag && version_compare($currentVersion, $latestVersion, '<');
    }

    /**
     * Format file size in human readable format
     * 
     * @param int $bytes
     * @return string
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Generate signed download URL
     * 
     * @param string $url
     * @return string
     */
    private function generateSignedDownloadUrl(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // For Play Store URLs, return clean URL (no signature needed)
        if (str_contains($url, 'play.google.com') || str_contains($url, 'apps.apple.com')) {
            return $url;
        }
        
        // Generate temporary signed URL for secure downloads
        // This is for direct file downloads (not Play Store)
        $expires = now()->addHour()->timestamp;
        $signature = hash_hmac('sha256', $url . $expires, config('app.key'));
        
        return $url . '?expires=' . $expires . '&signature=' . $signature;
    }

    private function isUpdateEnabled(): bool
    {
        return (string) get_static_option('app_update_enabled', '1') === '1';
    }

    private function getDisabledMessage(): string
    {
        return get_static_option('app_update_disabled_message') ?: __('App updates are currently unavailable');
    }

    private function getUpdateMessage(string $platform): ?string
    {
        return get_static_option("app_update_{$platform}_message")
            ?: get_static_option('app_update_default_message');
    }

    private function resolveDownloadUrl(AppVersion $version): string
    {
        if (!empty($version->download_url)) {
            return $version->download_url;
        }

        return get_static_option("app_update_{$version->platform}_fallback_url", '');
    }
}
