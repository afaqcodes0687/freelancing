<?php

namespace App\Http\Controllers\Frontend\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserIntroduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PortfolioController extends Controller
{
    //add portfolio
    public function add_portfolio(Request $request)
    {
        $request->validate(
            [
                'media' => 'required|mimes:jpg,png,jpeg,gif,svg,webp,mp4,mov,avi,webm,pdf,doc,docx,xls,xlsx|max:102400',
                'portfolio_title' => 'required|string|min:10|max:100|unique:portfolios,title',
                'portfolio_description' => 'required|string|min:50',
            ],
            [
                'media.required' => 'Portfolio image, video, or document is required',
                'portfolio_title.required' => 'Portfolio title is required',
                'portfolio_description.required' => 'Portfolio description is required',
            ]
        );

        if ($request->ajax()) {
            $fileName = '';
            $media = $request->file('media');

            if ($media) {
                $fileName = time() . '-' . uniqid() . '.' . $media->getClientOriginalExtension();
                $upload_folder = 'portfolio';
                $storage_driver = Storage::getDefaultDriver();

                $isImage = in_array(strtolower($media->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                $isVideo = in_array(strtolower($media->getClientOriginalExtension()), ['mp4', 'mov', 'avi', 'webm']);
                $isDocument = in_array(strtolower($media->getClientOriginalExtension()), ['pdf', 'doc', 'docx', 'xls', 'xlsx']);

                if (cloudStorageExist() && in_array($storage_driver, ['s3', 'cloudFlareR2', 'wasabi'])) {
                    add_frontend_cloud_image_if_module_exists($upload_folder, $media, $fileName, 'public');
                } else {
                    if ($isImage) {
                        // Only move SVG, do not resize
                        if (strtolower($media->getClientOriginalExtension()) === 'svg') {
                            $media->move(public_path('assets/uploads/portfolio'), $fileName);
                        } else {
                            $resize_image = Image::make($media)->resize(590, 440);
                            $resize_image->save('assets/uploads/portfolio/' . $fileName);
                        }
                    } elseif ($isVideo || $isDocument) {
                        $media->move(public_path('assets/uploads/portfolio'), $fileName);
                    } else {
                        // fallback for any other allowed type
                        $media->move(public_path('assets/uploads/portfolio'), $fileName);
                    }
                }
            }

            Portfolio::create([
                'user_id' => Auth::guard('web')->user()->id,
                'username' => Auth::guard('web')->user()->username,
                'title' => $request->portfolio_title,
                'description' => $request->portfolio_description,
                'image' => $fileName,
                'load_from' => in_array($storage_driver, ['CustomUploader']) ? 0 : 1,
            ]);

            return response()->json(['status' => 'success']);
        }
    }

    //edit portfolio
    public function edit_portfolio(Request $request)
    {
        $request->validate([
            'edit_portfolio_title' => 'required|string|min:10|max:100|unique:portfolios,title,' . $request->edit_portfolio_id,
            'edit_portfolio_description' => 'required|string|min:50',
        ], [
            'edit_portfolio_title.required' => 'Portfolio title is required',
            'edit_portfolio_description.required' => 'Portfolio description is required',
        ]);

        $portfolio = Portfolio::findOrFail($request->edit_portfolio_id);
        $oldFile = public_path('assets/uploads/portfolio/' . $portfolio->image);
        $fileName = $portfolio->image;
        $upload_folder = 'portfolio';
        $storage_driver = Storage::getDefaultDriver();

        if ($request->ajax()) {
            $media = $request->file('edit_image');

            if ($media) {
                $request->validate([
                    'edit_image' => 'mimes:jpg,jpeg,png,gif,svg,webp,mp4,mov,avi,webm,pdf,doc,docx,xls,xlsx|max:102400',
                ], [
                    'edit_image.mimes' => 'The file must be a valid media format (JPG, PNG, MP4, PDF, etc.)',
                ]);

                $fileName = time() . '-' . uniqid() . '.' . $media->getClientOriginalExtension();
                $ext = strtolower($media->getClientOriginalExtension());

                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                $isDoc = in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);

                // Delete old file
                if (cloudStorageExist() && in_array($storage_driver, ['s3', 'cloudFlareR2', 'wasabi'])) {
                    if (!empty($portfolio->image)) {
                        delete_frontend_cloud_image_if_module_exists($upload_folder . '/' . $portfolio->image);
                    }
                    add_frontend_cloud_image_if_module_exists($upload_folder, $media, $fileName, 'public');
                } else {
                    if (file_exists($oldFile)) {
                        File::delete($oldFile);
                    }

                    if ($isImage) {
                        if ($ext === 'svg') {
                            $media->move(public_path('assets/uploads/portfolio'), $fileName);
                        } else {
                            $resize = Image::make($media)->resize(590, 440);
                            $resize->save(public_path('assets/uploads/portfolio/' . $fileName));
                        }
                    } elseif ($isVideo || $isDoc) {
                        $media->move(public_path('assets/uploads/portfolio'), $fileName);
                    } else {
                        // fallback
                        $media->move(public_path('assets/uploads/portfolio'), $fileName);
                    }
                }
            }

            // Update portfolio
            $portfolio->update([
                'title' => $request->edit_portfolio_title,
                'description' => $request->edit_portfolio_description,
                'image' => $fileName,
            ]);

            return response()->json(['status' => 'success']);
        }
    }

    //delete portfolio
    public function delete_portfolio(Request $request)
    {
        if ($request->ajax()) {
            $portfolio = Portfolio::find($request->id);
            if ($portfolio) {
                if (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
                    // Get the current image path from the database
                    $currentImagePath = $portfolio->image;
                    // Delete the old image if it exists
                    if ($currentImagePath) {
                        delete_frontend_cloud_image_if_module_exists('portfolio/' . $currentImagePath);
                    }
                } else {
                    $delete_old_img = 'assets/uploads/portfolio/' . $portfolio->image;
                    if (file_exists($delete_old_img)) {
                        File::delete($delete_old_img);
                    }
                }
                $portfolio->delete();
            }
            return response()->json([
                'status' => 'success',
            ]);
        }
    }

    //delete education
    public function delete_education(Request $request)
    {
        if ($request->ajax()) {
            UserEducation::find($request->id)->delete();
            return response()->json([
                'status' => 'success',
            ]);
        }
    }

    //delete experience
    public function delete_experience(Request $request)
    {
        if ($request->ajax()) {
            UserExperience::find($request->id)->delete();
            return response()->json([
                'status' => 'success',
            ]);
        }
    }

    //change project availability status
    public function availability_status(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->project_on_off == 1 ? 0 : 1;
            Project::where('id', $request->id)->update([
                'project_on_off' => $status,
            ]);
            return response()->json([
                'status' => 'success',
            ]);
        }
    }

    //change work availability status
    public function work_availability_status(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->check_work_availability == 1 ? 0 : 1;
            User::where('id', $request->user_id)->update([
                'check_work_availability' => $status,
            ]);
            return response()->json([
                'status' => 'success',
            ]);
        }
    }

    //update profile details
    public function profile_details_update(Request $request)
    {
        $request->validate(
            [
                'first_name' => 'required|min:2|max:30',
                'last_name' => 'required|min:2|max:30',
                'title' => 'required|string|min:10|max:100',
                'description' => 'required|string|min:50',
                'country_id' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',

            ],
            [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'title.required' => 'Professional title is required',
                'description.required' => 'Professional description is required',
                'country_id.required' => 'Country is required',
            ]
        );

        if ($request->ajax()) {
            $user_id = Auth::guard('web')->user()->id;
            User::where('id', $user_id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,

            ]);

            UserIntroduction::updateOrCreate(
                ['user_id' => $user_id],
                [
                    'user_id' => $user_id,
                    'title' => $request->title,
                    'description' => $request->description,
                ]
            );

            return response()->json([
                'status' => 'success',
            ]);
        }
    }

    //update profile details linked accounts
    public function profile_details_linked_accounts_update(Request $request)
    {
        dd('ok');
        $request->validate(
            [
                'github_link' => 'nullable|url',
                'stackoverflow_link' => 'nullable|url',
            ]
        );

        if ($request->ajax()) {
            $user_id = Auth::guard('web')->user()->id;
            $github_meta = null;

            if ($request->github_link) {
                $github_meta = $this->fetch_github_data($request->github_link);
            }

            UserIntroduction::updateOrCreate(
                ['user_id' => $user_id],
                [
                    'user_id' => $user_id,
                    'github_link' => $request->github_link,
                    'stackoverflow_link' => $request->stackoverflow_link,
                    'github_meta' => $github_meta,
                ]
            );

            return response()->json([
                'status' => 'success',
            ]);
        }
    }

    public function profile_details_linked_accounts_unlink(Request $request)
    {
        if ($request->ajax()) {
            $user_id = Auth::guard('web')->user()->id;
            $type = $request->type; // 'github' or 'stackoverflow'

            $intro = UserIntroduction::where('user_id', $user_id)->first();
            if ($intro) {
                if ($type == 'github') {
                    $intro->update([
                        'github_link' => null,
                        'github_meta' => null
                    ]);
                } elseif ($type == 'stackoverflow') {
                    $intro->update([
                        'stackoverflow_link' => null,
                        'stackoverflow_meta' => null
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
            ]);
        }
    }

    private function fetch_github_data($url)
    {
        try {
            // Extract username from URL
            $path = parse_url($url, PHP_URL_PATH);
            $username = trim($path, '/');
            if (!$username)
                return null;

            $response = Http::withHeaders([
                'User-Agent' => 'Laravel'
            ])->get("https://api.github.com/users/{$username}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'name' => $data['name'] ?? $data['login'],
                    'avatar_url' => $data['avatar_url'] ?? '',
                    'followers' => $data['followers'] ?? 0,
                    'created_at' => $data['created_at'] ?? '',
                    'login' => $data['login'] ?? ''
                ];
            }
        } catch (\Exception $e) {
            Log::error('GitHub API error: ' . $e->getMessage());
        }
        return null;
    }

    //update profile details hourly rate
    public function profile_details_hourly_rate_update(Request $request)
    {
        $request->validate(
            [
                'hourly_rate' => 'required|numeric|min:1|max:300',
            ],
            [
                'hourly_rate.required' => 'Price is required',
            ]
        );

        if ($request->ajax()) {
            User::where('id', Auth::guard('web')->user()->id)->update([
                'hourly_rate' => $request->hourly_rate,
            ]);
            return response()->json([
                'status' => 'success',
            ]);
        }
    }
}
