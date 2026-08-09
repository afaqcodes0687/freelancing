<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BioLink;
use App\Models\LinkClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BioLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's bio links management page
     */
    public function index()
    {
        $user = Auth::user();
        $links = $user->bioLinks()->withCount('clicks')->get();
        
        // Get analytics data
        $analytics = LinkClick::getAnalyticsForUser($user->id, 30);
        
        return view('frontend.bio.links.index', compact('links', 'analytics'));
    }

    /**
     * Show the form for creating a new bio link
     */
    public function create()
    {
        return view('frontend.bio.links.create');
    }

    /**
     * Store a newly created bio link
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'type' => ['required', Rule::in(['social', 'affiliate', 'service', 'external'])],
            'is_featured' => 'boolean',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        
        // Get the highest sort order and increment
        $maxSortOrder = $user->bioLinks()->max('sort_order') ?? 0;
        
        $link = $user->bioLinks()->create([
            'title' => $request->title,
            'url' => $request->url,
            'type' => $request->type,
            'is_featured' => $request->boolean('is_featured', false),
            'sort_order' => $maxSortOrder + 1,
            'icon' => $request->icon,
            'color' => $request->color,
        ]);

        return redirect()
            ->route('bio.links.index')
            ->with('success', 'Link created successfully!');
    }

    /**
     * Show the form for editing the specified bio link
     */
    public function edit(BioLink $bioLink)
    {
        // Authorization check
        if ($bioLink->user_id !== Auth::id()) {
            abort(403);
        }

        return view('frontend.bio.links.edit', compact('bioLink'));
    }

    /**
     * Update the specified bio link
     */
    public function update(Request $request, BioLink $bioLink)
    {
        // Authorization check
        if ($bioLink->user_id !== Auth::id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'type' => ['required', Rule::in(['social', 'affiliate', 'service', 'external'])],
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $bioLink->update([
            'title' => $request->title,
            'url' => $request->url,
            'type' => $request->type,
            'is_featured' => $request->boolean('is_featured', false),
            'is_active' => $request->boolean('is_active', true),
            'icon' => $request->icon,
            'color' => $request->color,
        ]);

        return redirect()
            ->route('bio.links.index')
            ->with('success', 'Link updated successfully!');
    }

    /**
     * Remove the specified bio link
     */
    public function destroy(BioLink $bioLink)
    {
        // Authorization check
        if ($bioLink->user_id !== Auth::id()) {
            abort(403);
        }

        $bioLink->delete();

        return redirect()
            ->route('bio.links.index')
            ->with('success', 'Link deleted successfully!');
    }

    /**
     * Toggle link status (active/inactive)
     */
    public function toggleStatus(BioLink $bioLink)
    {
        // Authorization check
        if ($bioLink->user_id !== Auth::id()) {
            abort(403);
        }

        $bioLink->update([
            'is_active' => !$bioLink->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $bioLink->is_active
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(BioLink $bioLink)
    {
        // Authorization check
        if ($bioLink->user_id !== Auth::id()) {
            abort(403);
        }

        $bioLink->update([
            'is_featured' => !$bioLink->is_featured
        ]);

        return response()->json([
            'success' => true,
            'is_featured' => $bioLink->is_featured
        ]);
    }

    /**
     * Reorder links
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'links' => 'required|array',
            'links.*.id' => 'required|integer|exists:bio_links,id',
            'links.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        foreach ($request->links as $linkData) {
            $link = BioLink::where('id', $linkData['id'])
                ->where('user_id', $user->id)
                ->first();
                
            if ($link) {
                $link->update(['sort_order' => $linkData['sort_order']]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Display analytics dashboard
     */
    public function analytics()
    {
        $user = Auth::user();
        
        // Get analytics data
        $analytics = LinkClick::getAnalyticsForUser($user->id, 30);
        
        return view('frontend.bio.analytics', compact('analytics'));
    }

    /**
     * Get link statistics
     */
    public function statistics(BioLink $bioLink)
    {
        // Authorization check
        if ($bioLink->user_id !== Auth::id()) {
            abort(403);
        }

        $stats = [
            'total_clicks' => $bioLink->clicks()->count(),
            'today_clicks' => $bioLink->clicks()->today()->count(),
            'last_week_clicks' => $bioLink->clicks()->where('created_at', '>=', now()->subDays(7))->count(),
            'last_month_clicks' => $bioLink->clicks()->where('created_at', '>=', now()->subDays(30))->count(),
            'unique_clicks' => $bioLink->clicks()->distinct('ip_address')->count(),
            'recent_clicks' => $bioLink->clicks()
                ->with('visitor')
                ->latest()
                ->take(10)
                ->get()
        ];

        return response()->json($stats);
    }
}
