<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BioLink;
use App\Models\LinkClick;
use App\Models\Project;
use App\Services\ReferralTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

class BioController extends Controller
{
    protected $referralTrackingService;

    public function __construct(ReferralTrackingService $referralTrackingService)
    {
        $this->referralTrackingService = $referralTrackingService;
    }

    /**
     * Display the public bio page for a user
     */
    public function show($username)
    {
        $user = User::where('username', $username)
            ->where('bio_enabled', true)
            ->firstOrFail();

        // Track referral using existing service
        if ($user->referral_code) {
            $this->referralTrackingService->trackClick(request());
        }

        // Increment bio views
        $user->incrementBioViews();

        // Get user's active bio links
        $featuredLinks = $user->featuredBioLinks;
        $regularLinks = $user->activeBioLinks()->where('is_featured', false)->get();

        // Get user's active projects/services
        $projects = $user->projects()
            ->where('project_on_off', 1)
            ->where('status', 1)
            ->take(6)
            ->get();

        // Generate QR code URL
        $qrCodeUrl = $this->generateQrCode($user->bio_url);

        return view('frontend.bio.show', compact(
            'user',
            'featuredLinks', 
            'regularLinks',
            'projects',
            'qrCodeUrl'
        ));
    }

    /**
     * Redirect to link URL and track click
     */
    public function redirectLink($username, $linkId)
    {
        $user = User::where('username', $username)->firstOrFail();
        $link = BioLink::where('id', $linkId)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Track the click
        $this->trackClick($link, $user);

        // Get URL with referral if affiliate
        $redirectUrl = $link->url_with_referral;

        return redirect($redirectUrl);
    }

    /**
     * Download QR code for user's bio page
     */
    public function downloadQrCode($username, $format = 'png')
    {
        $user = User::where('username', $username)
            ->where('bio_enabled', true)
            ->firstOrFail();

        $qrCode = QrCode::create($user->bio_url)
            ->setSize(300)
            ->setMargin(2);

        if ($format === 'svg') {
            $writer = new SvgWriter();
            $result = $writer->write($qrCode);
            $filename = "qr-{$username}-bio.svg";
            
            return response($result->getString())
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } else {
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $filename = "qr-{$username}-bio.png";
            
            return response($result->getString())
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        }
    }

    /**
     * Track link click
     */
    private function trackClick(BioLink $link, User $user)
    {
        $clickData = [
            'bio_link_id' => $link->id,
            'user_id' => $user->id,
            'visitor_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
        ];

        // Get location data (optional - you can integrate with a geolocation service)
        $locationData = $this->getLocationData(request()->ip());
        $clickData = array_merge($clickData, $locationData);

        LinkClick::create($clickData);
    }

    /**
     * Get location data from IP (basic implementation)
     */
    private function getLocationData($ip)
    {
        // This is a basic implementation. You can integrate with services like:
        // - GeoIP2
        // - ipstack
        // - ipapi
        // For now, return empty data
        return [
            'country' => null,
            'city' => null,
        ];
    }

    /**
     * Generate QR code for bio URL
     */
    private function generateQrCode($url)
    {
        // Cache QR code for performance
        $cacheKey = 'qr-code-' . md5($url);
        
        return Cache::remember($cacheKey, 3600, function () use ($url) {
            $qrCode = QrCode::create($url)
                ->setSize(200)
                ->setMargin(1);
            
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            return 'data:image/png;base64,' . base64_encode($result->getString());
        });
    }

    /**
     * API endpoint to get bio analytics (for user dashboard)
     */
    public function getAnalytics($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Authorization check - only user can see their own analytics
        if (auth()->id() !== $user->id) {
            abort(403);
        }

        $analytics = LinkClick::getAnalyticsForUser($user->id, 30);

        return response()->json($analytics);
    }
}
