<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BioSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show bio settings page
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('frontend.bio.settings.index', compact('user'));
    }

    /**
     * Update bio settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bio_description' => 'nullable|string|max:500',
            'bio_theme' => ['required', 'string', Rule::in(['default', 'dark', 'minimal', 'colorful'])],
            'bio_enabled' => 'boolean',
            'bio_avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        
        // Handle avatar upload
        if ($request->hasFile('bio_avatar')) {
            $avatar = $request->file('bio_avatar');
            $avatarName = 'bio-avatar-' . $user->id . '-' . time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = $avatar->storeAs('bio-avatars', $avatarName, 'public');
            
            // Delete old avatar if exists
            if ($user->bio_avatar) {
                $oldAvatarPath = str_replace('/storage/', '', $user->bio_avatar);
                if (Storage::disk('public')->exists($oldAvatarPath)) {
                    Storage::disk('public')->delete($oldAvatarPath);
                }
            }
            
            $user->bio_avatar = '/storage/' . $avatarPath;
        }

        $user->update([
            'bio_description' => $request->bio_description,
            'bio_theme' => $request->bio_theme,
            'bio_enabled' => $request->boolean('bio_enabled', true),
        ]);

        return redirect()
            ->route('bio.settings.index')
            ->with('success', 'Bio settings updated successfully!');
    }

    /**
     * Remove bio avatar
     */
    public function removeAvatar()
    {
        $user = Auth::user();
        
        if ($user->bio_avatar) {
            $avatarPath = str_replace('/storage/', '', $user->bio_avatar);
            if (Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }
            
            $user->update(['bio_avatar' => null]);
        }

        return redirect()
            ->route('bio.settings.index')
            ->with('success', 'Avatar removed successfully!');
    }

    /**
     * Get bio preview data
     */
    public function preview()
    {
        $user = Auth::user();
        
        // Get user's active bio links
        $featuredLinks = $user->featuredBioLinks;
        $regularLinks = $user->activeBioLinks()->where('is_featured', false)->get();

        // Get user's active projects/services
        $projects = $user->projects()
            ->where('project_on_off', 1)
            ->where('status', 1)
            ->take(6)
            ->get();

        return response()->json([
            'user' => $user,
            'featuredLinks' => $featuredLinks,
            'regularLinks' => $regularLinks,
            'projects' => $projects,
            'bio_url' => $user->bio_url
        ]);
    }
}
