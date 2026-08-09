<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AboutUsController extends Controller
{
    public function index()
    {
        $aboutUs = AboutUs::first();
        return view('backend.pages.about-us.index', compact('aboutUs'));
    }

    public function create()
    {
        return view('backend.pages.about-us.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ceo_name' => 'nullable|string|max:255',
            'ceo_title' => 'nullable|string|max:255',
            'ceo_description' => 'nullable|string',
            'ceo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'main_title' => 'nullable|string|max:255',
            'main_description' => 'nullable|string',
            'opportunity_text' => 'nullable|string',
            'clients_count' => 'nullable|string|max:50',
            'freelancers_count' => 'nullable|string|max:50',
            'orders_count' => 'nullable|string|max:50',
            'jobs_handled' => 'nullable|string|max:50',
            'earned_amount' => 'nullable|string|max:50',
            'awards_count' => 'nullable|string|max:50',
            'video_title' => 'nullable|string|max:255',
            'video_description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'video_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'what_we_do_title' => 'nullable|string|max:255',
            'what_we_do_description' => 'nullable|string',
            'certifications_title' => 'nullable|string|max:255',
            'certifications_description' => 'nullable|string',
            'team_title' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['ceo_image', 'video_thumbnail', 'team_members', 'certifications']);

        // Handle CEO Image
        if ($request->hasFile('ceo_image')) {
            $image = $request->file('ceo_image');
            $imageName = time() . '_ceo.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/uploads/about-us'), $imageName);
            $data['ceo_image'] = $imageName;
        }

        // Handle Video Thumbnail
        if ($request->hasFile('video_thumbnail')) {
            $image = $request->file('video_thumbnail');
            $imageName = time() . '_video.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/uploads/about-us'), $imageName);
            $data['video_thumbnail'] = $imageName;
        }

        // Handle Team Members
        if ($request->has('team_members')) {
            $teamMembers = [];
            if ($request->has('team_member_names')) {
                foreach ($request->team_member_names as $key => $name) {
                    if (!empty($name)) {
                        $member = [
                            'name' => $name,
                            'position' => $request->team_member_positions[$key] ?? '',
                            'image' => ''
                        ];

                        // Handle team member image
                        if ($request->hasFile("team_member_images.$key")) {
                            $image = $request->file("team_member_images.$key");
                            $imageName = time() . '_team_' . $key . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('assets/uploads/about-us'), $imageName);
                            $member['image'] = $imageName;
                        }

                        $teamMembers[] = $member;
                    }
                }
            }
            $data['team_members'] = json_encode($teamMembers);
        }

        // Handle Certifications
        if ($request->has('certifications')) {
            $certifications = [];
            if ($request->has('certification_titles')) {
                foreach ($request->certification_titles as $key => $title) {
                    if (!empty($title)) {
                        $cert = [
                            'title' => $title,
                            'link' => $request->certification_links[$key] ?? '',
                            'image' => ''
                        ];

                        // Handle certification image
                        if ($request->hasFile("certification_images.$key")) {
                            $image = $request->file("certification_images.$key");
                            $imageName = time() . '_cert_' . $key . '.' . $image->getClientOriginalExtension();
                            $image->move(public_path('assets/uploads/about-us'), $imageName);
                            $cert['image'] = $imageName;
                        }

                        $certifications[] = $cert;
                    }
                }
            }
            $data['certifications'] = json_encode($certifications);
        }

        AboutUs::create($data);

        return redirect()->route('admin.about-us.index')
            ->with(toastr_success('About Us page created successfully!'));
    }

    public function show(string $id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        return view('backend.pages.about-us.show', compact('aboutUs'));
    }

    public function edit(string $id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        return view('backend.pages.about-us.edit', compact('aboutUs'));
    }

    public function testEdit(string $id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        return view('backend.pages.about-us.test', compact('aboutUs'));
    }

    public function update(Request $request, string $id)
    {
        $aboutUs = AboutUs::findOrFail($id);

        // Debug: Log incoming request data
        \Log::info('About Us Update Request Data:', $request->all());

        $request->validate([
            'ceo_name' => 'nullable|string|max:255',
            'ceo_title' => 'nullable|string|max:255',
            'ceo_description' => 'nullable|string',
            'ceo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'main_title' => 'nullable|string|max:255',
            'main_description' => 'nullable|string',
            'opportunity_text' => 'nullable|string',
            'clients_count' => 'nullable|string|max:50',
            'freelancers_count' => 'nullable|string|max:50',
            'orders_count' => 'nullable|string|max:50',
            'jobs_handled' => 'nullable|string|max:50',
            'earned_amount' => 'nullable|string|max:50',
            'awards_count' => 'nullable|string|max:50',
            'video_title' => 'nullable|string|max:255',
            'video_description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'video_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'what_we_do_title' => 'nullable|string|max:255',
            'what_we_do_description' => 'nullable|string',
            'certifications_title' => 'nullable|string|max:255',
            'certifications_description' => 'nullable|string',
            'team_title' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['ceo_image', 'video_thumbnail', 'team_members', 'certifications']);

        // Debug: Log data after filtering
        \Log::info('About Us Data after filtering:', $data);

        // Handle CEO Image
        if ($request->hasFile('ceo_image')) {
            // Delete old image
            if ($aboutUs->ceo_image && file_exists(public_path('assets/frontend/img/' . $aboutUs->ceo_image))) {
                unlink(public_path('assets/frontend/img/' . $aboutUs->ceo_image));
            }
            
            $image = $request->file('ceo_image');
            $imageName = time() . '_ceo.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/frontend/img'), $imageName);
            $data['ceo_image'] = $imageName;
        }

        // Handle Video Thumbnail
        if ($request->hasFile('video_thumbnail')) {
            // Delete old image
            if ($aboutUs->video_thumbnail && file_exists(public_path('assets/frontend/img/' . $aboutUs->video_thumbnail))) {
                unlink(public_path('assets/frontend/img/' . $aboutUs->video_thumbnail));
            }
            
            $image = $request->file('video_thumbnail');
            $imageName = time() . '_video.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/frontend/img'), $imageName);
            $data['video_thumbnail'] = $imageName;
        }

        // Handle Team Members
        $teamMembers = [];
        if ($request->has('team_member_names')) {
            // First, handle deleted items
            $deletedTeamMembers = $request->get('deleted_team_members', []);
            
            foreach ($request->team_member_names as $key => $name) {
                // Skip if this item was marked for deletion
                if (isset($deletedTeamMembers[$key]) && $deletedTeamMembers[$key] === 'deleted') {
                    continue;
                }
                
                if (!empty($name)) {
                    $member = [
                        'name' => $name,
                        'position' => $request->team_member_positions[$key] ?? '',
                        'image' => ''
                    ];

                    // Handle team member image
                    if ($request->hasFile("team_member_images.$key")) {
                        $image = $request->file("team_member_images.$key");
                        $imageName = time() . '_team_' . $key . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('assets/frontend/img'), $imageName);
                        $member['image'] = $imageName;
                    } else {
                        // Try to find existing image by name match
                        $existingTeamMembers = json_decode($aboutUs->team_members, true) ?? [];
                        foreach ($existingTeamMembers as $existingMember) {
                            if ($existingMember['name'] === $name && isset($existingMember['image'])) {
                                $member['image'] = $existingMember['image'];
                                break;
                            }
                        }
                    }

                    $teamMembers[] = $member;
                }
            }
        }
        $data['team_members'] = json_encode($teamMembers);

        // Handle Certifications
        $certifications = [];
        if ($request->has('certification_titles')) {
            // First, handle deleted items
            $deletedCertifications = $request->get('deleted_certifications', []);
            
            foreach ($request->certification_titles as $key => $title) {
                // Skip if this item was marked for deletion
                if (isset($deletedCertifications[$key]) && $deletedCertifications[$key] === 'deleted') {
                    continue;
                }
                
                if (!empty($title)) {
                    $cert = [
                        'title' => $title,
                        'link' => $request->certification_links[$key] ?? '',
                        'image' => ''
                    ];

                    // Handle certification image
                    if ($request->hasFile("certification_images.$key")) {
                        $image = $request->file("certification_images.$key");
                        $imageName = time() . '_cert_' . $key . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('assets/frontend/img'), $imageName);
                        $cert['image'] = $imageName;
                    } else {
                        // Try to find existing image by title match
                        $existingCertifications = json_decode($aboutUs->certifications, true) ?? [];
                        foreach ($existingCertifications as $existingCert) {
                            if ($existingCert['title'] === $title && isset($existingCert['image'])) {
                                $cert['image'] = $existingCert['image'];
                                break;
                            }
                        }
                    }

                    $certifications[] = $cert;
                }
            }
        }
        $data['certifications'] = json_encode($certifications);

        // Handle image deletions
        if ($request->has('delete_team_member_images')) {
            $teamMembers = json_decode($data['team_members'], true);
            foreach ($request->delete_team_member_images as $memberName) {
                foreach ($teamMembers as $key => $member) {
                    if ($member['name'] === $memberName && isset($member['image'])) {
                        // Delete physical file
                        if (file_exists(public_path('assets/frontend/img/' . $member['image']))) {
                            unlink(public_path('assets/frontend/img/' . $member['image']));
                        }
                        // Remove image from data
                        $teamMembers[$key]['image'] = '';
                        break;
                    }
                }
            }
            $data['team_members'] = json_encode($teamMembers);
        }

        if ($request->has('delete_certification_images')) {
            $certifications = json_decode($data['certifications'], true);
            foreach ($request->delete_certification_images as $certTitle) {
                foreach ($certifications as $key => $cert) {
                    if ($cert['title'] === $certTitle && isset($cert['image'])) {
                        // Delete physical file
                        if (file_exists(public_path('assets/frontend/img/' . $cert['image']))) {
                            unlink(public_path('assets/frontend/img/' . $cert['image']));
                        }
                        // Remove image from data
                        $certifications[$key]['image'] = '';
                        break;
                    }
                }
            }
            $data['certifications'] = json_encode($certifications);
        }

        // Debug: Log final data before update
        \Log::info('About Us Final data before update:', $data);

        $result = $aboutUs->update($data);

        // Debug: Log update result
        \Log::info('About Us Update result:', ['success' => $result]);

        return redirect()->route('admin.about-us.index')
            ->with(toastr_success('About Us page updated successfully!'));
    }

    public function destroy(string $id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        
        // Delete images
        if ($aboutUs->ceo_image && file_exists(public_path('assets/uploads/about-us/' . $aboutUs->ceo_image))) {
            unlink(public_path('assets/uploads/about-us/' . $aboutUs->ceo_image));
        }
        
        if ($aboutUs->video_thumbnail && file_exists(public_path('assets/uploads/about-us/' . $aboutUs->video_thumbnail))) {
            unlink(public_path('assets/uploads/about-us/' . $aboutUs->video_thumbnail));
        }

        // Delete team member images
        if ($aboutUs->team_members) {
            foreach ($aboutUs->team_members as $member) {
                if (isset($member['image']) && file_exists(public_path('assets/uploads/about-us/' . $member['image']))) {
                    unlink(public_path('assets/uploads/about-us/' . $member['image']));
                }
            }
        }

        // Delete certification images
        if ($aboutUs->certifications) {
            foreach ($aboutUs->certifications as $cert) {
                if (isset($cert['image']) && file_exists(public_path('assets/uploads/about-us/' . $cert['image']))) {
                    unlink(public_path('assets/uploads/about-us/' . $cert['image']));
                }
            }
        }

        $aboutUs->delete();

        return redirect()->route('admin.about-us.index')
            ->with(toastr_success('About Us page deleted successfully!'));
    }
}
