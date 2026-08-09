<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppVersion;
use Illuminate\Support\Facades\DB;

class AppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing versions
        DB::table('app_versions')->delete();

        // Android versions
        AppVersion::create([
            'version' => '1.2.0',
            'version_name' => 'RightFreelancer v1.2.0 - Enhanced Gaming Experience',
            'platform' => 'android',
            'release_notes' => [
                '🎮 New Features:',
                '• Enhanced One Dollar Game sharing system',
                '• Improved withdrawal process for game earnings',
                '• Real-time referral earnings tracking',
                '• New game profile cards',
                '',
                '🔧 Technical Updates:',
                '• Updated game API endpoints',
                '• Enhanced security features',
                '• Performance improvements',
                '• Bug fixes and stability improvements',
                '',
                '📱 UI/UX Improvements:',
                '• Better game history display',
                '• Enhanced referral stats dashboard',
                '• Improved withdrawal interface'
            ],
            'download_url' => 'https://play.google.com/store/apps/details?id=com.right.freelancer',
            'file_size' => 15728640, // 15MB
            'min_supported_version' => '1.0.0',
            'is_active' => true,
            'force_update' => false,
            'checksum' => 'sha256:a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456',
            'signature' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1234567890abcdef',
            'release_date' => now(),
        ]);

        AppVersion::create([
            'version' => '1.1.0',
            'version_name' => 'RightFreelancer v1.1.0 - Game Features',
            'platform' => 'android',
            'release_notes' => [
                '🎮 Game Features:',
                '• One Dollar Game implementation',
                '• Referral commission system',
                '• Game history tracking',
                '• Withdrawal functionality',
                '',
                '🔧 Technical Updates:',
                '• New API endpoints',
                '• Database optimizations',
                '• Security enhancements'
            ],
            'download_url' => 'https://play.google.com/store/apps/details?id=com.right.freelancer',
            'file_size' => 14680064, // 14MB
            'min_supported_version' => '1.0.0',
            'is_active' => false, // Not the latest version
            'force_update' => false,
            'checksum' => 'sha256:b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef1234567',
            'signature' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2345678901abcdef',
            'release_date' => now()->subMonth(),
        ]);

        AppVersion::create([
            'version' => '1.0.0',
            'version_name' => 'RightFreelancer v1.0.0 - Initial Release',
            'platform' => 'android',
            'release_notes' => [
                '🚀 Initial Release:',
                '• Complete freelancer marketplace',
                '• User authentication system',
                '• Profile management',
                '• Job posting and bidding',
                '• Wallet system',
                '• Payment processing',
                '',
                '📱 Core Features:',
                '• Responsive design',
                '• Real-time notifications',
                '• Secure messaging',
                '• File sharing',
                '• Rating system'
            ],
            'download_url' => 'https://play.google.com/store/apps/details?id=com.right.freelancer',
            'file_size' => 13631488, // 13MB
            'min_supported_version' => null,
            'is_active' => false,
            'force_update' => false,
            'checksum' => 'sha256:c3d4e5f6789012345678901234567890abcdef1234567890abcdef12345678',
            'signature' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3456789012bcdef',
            'release_date' => now()->subMonths(2),
        ]);

        // iOS versions
        AppVersion::create([
            'version' => '1.2.0',
            'version_name' => 'RightFreelancer v1.2.0 - Enhanced Gaming Experience',
            'platform' => 'ios',
            'release_notes' => [
                '🎮 New Features:',
                '• Enhanced One Dollar Game sharing system',
                '• Improved withdrawal process for game earnings',
                '• Real-time referral earnings tracking',
                '• New game profile cards',
                '',
                '🔧 Technical Updates:',
                '• Updated game API endpoints',
                '• Enhanced security features',
                '• Performance improvements',
                '• Bug fixes and stability improvements',
                '',
                '📱 UI/UX Improvements:',
                '• Better game history display',
                '• Enhanced referral stats dashboard',
                '• Improved withdrawal interface'
            ],
            'download_url' => 'https://apps.apple.com/app/rightfreelancer/id123456789',
            'file_size' => 20971520, // 20MB
            'min_supported_version' => '1.0.0',
            'is_active' => true,
            'force_update' => false,
            'checksum' => 'sha256:d4e5f6789012345678901234567890abcdef1234567890abcdef123456789',
            'signature' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4567890123cdef',
            'release_date' => now(),
        ]);

        AppVersion::create([
            'version' => '1.1.0',
            'version_name' => 'RightFreelancer v1.1.0 - Game Features',
            'platform' => 'ios',
            'release_notes' => [
                '🎮 Game Features:',
                '• One Dollar Game implementation',
                '• Referral commission system',
                '• Game history tracking',
                '• Withdrawal functionality',
                '',
                '🔧 Technical Updates:',
                '• New API endpoints',
                '• Database optimizations',
                '• Security enhancements'
            ],
            'download_url' => 'https://apps.apple.com/app/rightfreelancer/id123456789',
            'file_size' => 19922944, // 19MB
            'min_supported_version' => '1.0.0',
            'is_active' => false,
            'force_update' => false,
            'checksum' => 'sha256:e5f6789012345678901234567890abcdef1234567890abcdef1234567890',
            'signature' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5678901234def',
            'release_date' => now()->subMonth(),
        ]);

        AppVersion::create([
            'version' => '1.0.0',
            'version_name' => 'RightFreelancer v1.0.0 - Initial Release',
            'platform' => 'ios',
            'release_notes' => [
                '🚀 Initial Release:',
                '• Complete freelancer marketplace',
                '• User authentication system',
                '• Profile management',
                '• Job posting and bidding',
                '• Wallet system',
                '• Payment processing',
                '',
                '📱 Core Features:',
                '• Responsive design',
                '• Real-time notifications',
                '• Secure messaging',
                '• File sharing',
                '• Rating system'
            ],
            'download_url' => 'https://apps.apple.com/app/rightfreelancer/id123456789',
            'file_size' => 18874368, // 18MB
            'min_supported_version' => null,
            'is_active' => false,
            'force_update' => false,
            'checksum' => 'sha256:f6789012345678901234567890abcdef1234567890abcdef12345678901',
            'signature' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6789012345ef',
            'release_date' => now()->subMonths(2),
        ]);

        $this->command->info('App versions seeded successfully!');
    }
}
