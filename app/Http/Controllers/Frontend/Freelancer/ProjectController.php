<?php

namespace App\Http\Controllers\Frontend\Freelancer;

use App\Helper\LogActivity;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\AdminNotification;
use App\Models\Project;
use App\Models\ProjectAttribute;
use App\Models\ProjectSubCategory;
use App\Models\ProjectHistory;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\Service\Entities\SubCategory;

class ProjectController extends Controller
{
    // project create
    public function create_project(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->merge([
                'meta_title' => Str::slug($request->project_title),
                'meta_description' => Str::limit($request->project_description),
            ]);

            $request->validate([
                'category' => 'required',
                'project_title' => 'required|min:20|max:255',
                'project_description' => 'required|min:50',
                'slug' => 'required|max:191|unique:projects,slug',
                'image' => 'required|array|max:5',
                'image.*' => 'file|mimes:jpg,jpeg,png,svg,webp,pdf,doc,docx,xls,xlsx,csv,txt,mp4,avi,mov,wmv,mkv|max:102400',
                'basic_title' => 'required|max:191',
                'basic_regular_charge' => 'required|numeric|integer',
                'checkbox_or_numeric_title' => 'required|array|max:191',
                'meta_title' => 'nullable|max:255',
                'meta_description' => 'nullable',
                'basic_details' => 'nullable|string|max:150',
                'standard_details' => 'nullable|string|max:150',
                'premium_details' => 'nullable|string|max:150',


            ]);

            if (get_static_option('project_auto_approval') == 'yes') {
                $project_auto_approval = 1;
                $project_approve_request = 1;
            } else {
                $project_auto_approval = 0;
                $project_approve_request = 0;
            }

            $standard_title = null;
            $premium_title = null;
            $standard_regular_charge = null;
            $standard_discount_charge = null;
            $premium_regular_charge = null;
            $premium_discount_charge = null;

            if ($request->offer_packages_available_or_not == 1) {
                $standard_title = $request->standard_title;
                $premium_title = $request->premium_title;
                $standard_regular_charge = $request->standard_regular_charge;
                $standard_discount_charge = $request->standard_discount_charge;
                $premium_regular_charge = $request->premium_regular_charge;
                $premium_discount_charge = $request->premium_discount_charge;
            }

            $user_id = Auth::guard('web')->user()->id;
            $slug = !empty($request->slug) ? $request->slug : $request->project_title;

            // Define $storage_driver before use
            $storage_driver = Storage::getDefaultDriver();

            DB::beginTransaction();
            try {
                $imageNames = [];

                if ($request->hasFile('image')) {
                    foreach ($request->file('image') as $file) {
                        $maxFileSize = 100 * 1024 * 1024; // 100MB limit
                        if ($file->getSize() > $maxFileSize) {
                            toastr_error(__('Upload failed: max file size is 100MB'));
                            return back();
                        }

                        // Always generate a truly unique filename
                        $imageName = uniqid('', true) . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                        $uploadPath = 'assets/uploads/project/' . $imageName;
                        $mime = $file->getMimeType();
                        $isImage = str_starts_with($mime, 'image/');
                        $isSvg = strtolower($file->getClientOriginalExtension()) === 'svg' || $mime === 'image/svg+xml';

                        if (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
                            add_frontend_cloud_image_if_module_exists('project', $file, $imageName, 'public');
                        } else {
                            if ($isImage && !$isSvg) {
                                $image = Image::make($file)
                                    ->orientate() // fix rotation
                                    ->encode($file->getClientOriginalExtension(), 90);

                                $image->save(public_path($uploadPath));
                            } else {
                                $file->move(public_path('assets/uploads/project/'), $imageName);
                            }
                        }

                        $imageNames[] = $imageName;
                    }
                }
                // Save all file names as JSON
                $project = Project::create([
                    'user_id' => $user_id,
                    'category_id' => $request->category,
                    'child_category_id' => $request->child_category_id,
                    'title' => $request->project_title,
                    'slug' => Str::slug(purify_html($slug), '-', null),
                    'description' => $request->project_description,
                    'image' => json_encode($imageNames), // Save as JSON
                    'basic_title' => $request->basic_title,
                    'standard_title' => $standard_title,
                    'premium_title' => $premium_title,
                    'basic_revision' => $request->basic_revision,
                    'standard_revision' => $request->standard_revision,
                    'premium_revision' => $request->premium_revision,
                    'basic_delivery' => $request->basic_delivery,
                    'standard_delivery' => $request->standard_delivery,
                    'premium_delivery' => $request->premium_delivery,
                    'basic_details' => $request->basic_details,
                    'standard_details' => $request->standard_details,
                    'premium_details' => $request->premium_details,
                    'basic_regular_charge' => $request->basic_regular_charge,
                    'basic_discount_charge' => $request->basic_discount_charge,
                    'standard_regular_charge' => $standard_regular_charge,
                    'standard_discount_charge' => $standard_discount_charge,
                    'premium_regular_charge' => $premium_regular_charge,
                    'premium_discount_charge' => $premium_discount_charge,
                    'project_on_off' => 1,
                    'status' => $project_auto_approval,
                    'project_approve_request' => $project_approve_request,
                    'offer_packages_available_or_not' => $request->offer_packages_available_or_not,
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'load_from' => in_array($storage_driver, ['CustomUploader']) ? 0 : 1, //added for cloud storage 0=local 1=cloud
                ]);
                $project->project_sub_categories()->attach($request->subcategory);

                $arr = [];
                foreach ($request->checkbox_or_numeric_title as $key => $attr) {
                    // Trim and validate
                    $trimmedAttr = trim($attr);
                    if (strlen($trimmedAttr) > 100) {
                        DB::rollBack();
                        toastr_error(__('Error: Each attribute title must be 100 characters or less.'));
                        return back()->withInput();
                    }

                    $attr_value = str_replace(" ", "_", strtolower($trimmedAttr));
                    $fallback_value = $request->checkbox_or_numeric_select[$key] == 'checkbox' ? "off" : 0;

                    $arr[] = [
                        'user_id' => $user_id,
                        'create_project_id' => $project->id,
                        'check_numeric_title' => $trimmedAttr,
                        'standard_check_numeric' => $request->$attr_value["standard"] ?? $fallback_value,
                        'premium_check_numeric' => $request->$attr_value["premium"] ?? $fallback_value,
                        'type' => $request->checkbox_or_numeric_select[$key] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                ProjectAttribute::insert($arr);

                //security manage
                if (moduleExists('SecurityManage')) {
                    LogActivity::addToLog('Project create', 'Freelancer');
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

                if ($request->file('image')) {
                    // Safely delete uploaded images only if $project is already set
                    if (isset($project) && $project->image) {
                        $uploadedImages = json_decode($project->image, true);
                        if (is_array($uploadedImages)) {
                            foreach ($uploadedImages as $img) {
                                File::delete('assets/uploads/project/' . $img);
                            }
                        }
                    }
                }

                \Log::error('Project Create Error: ' . $e->getMessage());

                if (app()->environment('local')) {
                    toastr_error(__('Error: ') . $e->getMessage());
                } else {
                    toastr_error(__('Something went wrong while creating the project. Please try again.'));
                }

                return back()->withInput();
            }


            // try {
            //     $message = get_static_option('project_create_email_message') ?? __('A new project is just created.');
            //     $message = str_replace(["@project_id"],[$project->id], $message);
            //     Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
            //         'subject' => get_static_option('project_create_email_subject') ?? __('Project Create Email'),
            //         'message' => $message
            //     ]));
            // }catch (\Exception $e) {}
            try {
                $project_link = route('project.details', [
                    'username' => $project->project_creator->username,
                    'slug' => $project->slug,
                ]);

                $user = Auth::guard('web')->user();
                $posterName = e($user->first_name . ' ' . $user->last_name);
                $projectTitle = e($project->title);

                // Admin Email Message
                $adminMessage = '
                        <div style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 0; font-family: Arial, sans-serif; color: #333;">
                            <div style="padding: 30px;">
                                <div style="border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px;">
                                    <h2 style="color: #309400; margin: 0;">🚀 New Project Alert from RightFreelancer</h2>
                                </div>
                                <p style="font-size: 16px; line-height: 1.6;">
                                    A new project has been posted by <strong>' . $posterName . '</strong>.
                                </p>
                                <p style="font-size: 16px; line-height: 1.6;">
                                    Project Title:
                                    <a href="' . $project_link . '" style="color: #309400; text-decoration: underline; font-weight: bold;">' . $projectTitle . '</a>
                                </p>
                                <p style="margin-top: 16px; font-size: 15px; line-height: 1.6;">
                                    Please review the project for approval if needed.
                                </p>
                                <p style="margin-top: 12px; font-size: 15px; line-height: 1.6;">
                                    Best regards,<br>
                                    <strong>The RightFreelancer System</strong>
                                </p>
                            </div>
                        </div>
                    ';

                // User Email Message
                $userMessage = '
                        <div style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 0; font-family: Arial, sans-serif; color: #333;">
                            <div style="padding: 30px;">
                                <div style="border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px;">
                                    <h2 style="color: #309400; margin: 0;">✅ Project Successfully Created</h2>
                                </div>
                                <p style="font-size: 16px; line-height: 1.6;">
                                    Hi <strong>' . $posterName . '</strong>,
                                </p>
                                <p style="font-size: 16px; line-height: 1.6;">
                                    Your project 
                                    <a href="' . $project_link . '" style="color: #309400; text-decoration: underline; font-weight: bold;">' . $projectTitle . '</a> 
                                    has been successfully submitted on <strong>RightFreelancer</strong>.
                                </p>
                                <p style="font-size: 15px; line-height: 1.6; margin-top: 16px;">
                                    We\'re excited to help you connect with the right talent for your project!
                                </p>
                                <p style="margin-top: 12px; font-size: 15px; line-height: 1.6;">
                                    Best regards,<br>
                                    <strong>The RightFreelancer Team</strong>
                                </p>
                            </div>
                        </div>
                    ';

                // Send to Admin
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => get_static_option('project_create_email_subject') ?? __('New Project Created'),
                    'message' => $adminMessage,
                ]));

                // Send to User
                Mail::to($user->email)->send(new BasicMail([
                    'subject' => __('Your Project Post Confirmation'),
                    'message' => $userMessage,
                ]));
            } catch (\Exception $e) {
                // Optionally log the error or ignore silently
            }

            //create project notification to admin
            AdminNotification::create([
                'identity' => $project->id,
                'user_id' => $user_id,
                'type' => 'Create Project',
                'message' => __('A new project has been created'),
            ]);
            toastr_success(__('Project Successfully Created'));
            return redirect()->route('freelancer.profile.details', Auth::guard('web')->user()->username);
        }

        return view('frontend.user.freelancer.project.create.create-project');
    }

    // project edit
    public function edit_project(Request $request, $id)
    {
        $project_details = Project::with('project_attributes')
            ->where('user_id', Auth::guard('web')->user()->id)
            ->where('id', $id)->firstOrFail();
        $get_sub_categories_from_project_category = SubCategory::where('category_id', $project_details->category_id)->get() ?? '';
        
        $get_child_categories_from_project_subcategory = [];
        if($project_details->project_sub_categories->count() > 0) {
            $first_sub_category_id = $project_details->project_sub_categories->first()->id;
            $get_child_categories_from_project_subcategory = \Modules\Service\Entities\ChildCategory::where('sub_category_id', $first_sub_category_id)->get() ?? [];
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'project_title' => 'required|min:20|max:100|unique:projects,title,' . $id,
                'project_description' => 'required|min:50',
                'slug' => 'required|max:191|unique:projects,slug,' . $id,
                'basic_title' => 'required|max:191',
                'basic_regular_charge' => 'required|numeric|integer',
                'checkbox_or_numeric_title' => 'required|array|max:191',
                'meta_title' => 'nullable|max:255',
                'meta_description' => 'nullable|max:500',
                'image' => 'nullable|array', // allow multiple files
                'image.*' => 'file|mimes:jpg,jpeg,png,svg,webp,pdf,doc,docx,xls,xlsx,csv,txt,mp4,avi,mov,wmv,mkv|max:102400',
                'basic_details' => 'nullable|string|max:150',
                'standard_details' => 'nullable|string|max:150',
                'premium_details' => 'nullable|string|max:150',
            ]);

            // Check total file count before proceeding
            $existingImages = json_decode($project_details->image ?? '[]', true);
            if (!is_array($existingImages)) {
                $existingImages = [];
            }

            // FILTER: Exclude removed files from the count check
            $removedFilesCount = 0;
            if ($request->filled('removed_files')) {
                $removedFiles = json_decode($request->removed_files, true);
                if (is_array($removedFiles)) {
                    $removedFilesCount = count($removedFiles);
                }
            }

            $currentCount = count($existingImages) - $removedFilesCount;
            // Ensure count doesn't go below zero (sanity check)
            if ($currentCount < 0) {
                $currentCount = 0;
            }

            $newImageCount = $request->hasFile('image') ? count($request->file('image')) : 0;

            if (($currentCount + $newImageCount) > 5) {
                toastr_error(__('You can have a maximum of 5 images in total.'));
                return back();
            }

            $standard_title = null;
            $premium_title = null;
            $standard_regular_charge = null;
            $standard_discount_charge = null;
            $premium_regular_charge = null;
            $premium_discount_charge = null;

            if ($request->offer_packages_available_or_not == 1) {
                $standard_title = $request->standard_title;
                $premium_title = $request->premium_title;
                $standard_regular_charge = $request->standard_regular_charge;
                $standard_discount_charge = $request->standard_discount_charge;
                $premium_regular_charge = $request->premium_regular_charge;
                $premium_discount_charge = $request->premium_discount_charge;
            }

            $user_id = Auth::guard('web')->user()->id;
            $slug = !empty($request->slug) ? $request->slug : $request->project_title;

            // Define storage driver
            $storage_driver = Storage::getDefaultDriver();

            DB::beginTransaction();
            try {
                $imageNames = $existingImages;

                // Handle removed files
                if ($request->filled('removed_files')) {
                    $removedFiles = json_decode($request->removed_files, true);
                    if (is_array($removedFiles)) {
                        foreach ($removedFiles as $fileToRemove) {
                            if (($key = array_search($fileToRemove, $imageNames)) !== false) {
                                unset($imageNames[$key]);
                                // Delete file from storage
                                delete_frontend_cloud_image_if_module_exists('project/' . $fileToRemove);
                            }
                        }
                        $imageNames = array_values($imageNames); // Reindex array
                    }
                }

                if ($request->hasFile('image')) {
                    foreach ($request->file('image') as $file) {
                        $maxFileSize = 100 * 1024 * 1024; // 100MB
                        if ($file->getSize() > $maxFileSize) {
                            toastr_error(__('Upload failed: max file size is 100MB'));
                            return back();
                        }
                        $imageName = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $uploadPath = 'assets/uploads/project/' . $imageName;
                        $mime = $file->getMimeType();
                        $isImage = str_starts_with($mime, 'image/');
                        $isSvg = strtolower($file->getClientOriginalExtension()) === 'svg' || $mime === 'image/svg+xml';

                        if (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
                            add_frontend_cloud_image_if_module_exists('project', $file, $imageName, 'public');
                        } else {
                            if ($isImage && !$isSvg) {
                                // Maintain original aspect ratio, no distortion
                                $image = Image::make($file);

                                // Limit extremely large images but preserve ratio
                                $maxWidth = 1600;
                                $maxHeight = 1600;

                                $image->resize($maxWidth, $maxHeight, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });

                                $image->save($uploadPath, 90); // 90% quality
                            } else {
                                // For non-image or SVG files
                                $file->move('assets/uploads/project/', $imageName);
                            }

                        }
                        $imageNames[] = $imageName;
                    }
                }

                // Save all file names as JSON and update load_from for cloud storage
                Project::where('id', $id)->update([
                    'user_id' => $user_id,
                    'category_id' => $request->category,
                    'child_category_id' => $request->child_category_id,
                    'title' => $request->project_title,
                    'slug' => Str::slug(purify_html($slug), '-', null),
                    'description' => $request->project_description,
                    'image' => json_encode($imageNames),
                    'basic_title' => $request->basic_title,
                    'standard_title' => $standard_title,
                    'premium_title' => $premium_title,
                    'basic_revision' => $request->basic_revision,
                    'standard_revision' => $request->standard_revision,
                    'premium_revision' => $request->premium_revision,
                    'basic_delivery' => $request->basic_delivery,
                    'standard_delivery' => $request->standard_delivery,
                    'premium_delivery' => $request->premium_delivery,
                    'basic_regular_charge' => $request->basic_regular_charge,
                    'basic_details' => $request->basic_details,
                    'standard_details' => $request->standard_details,
                    'premium_details' => $request->premium_details,
                    'basic_discount_charge' => $request->basic_discount_charge,
                    'standard_regular_charge' => $standard_regular_charge,
                    'standard_discount_charge' => $standard_discount_charge,
                    'premium_regular_charge' => $premium_regular_charge,
                    'premium_discount_charge' => $premium_discount_charge,
                    'project_on_off' => 1,
                    'project_approve_request' => $project_details->project_approve_request == 1 ? 1 : 0,
                    'offer_packages_available_or_not' => $request->offer_packages_available_or_not ?? 0,
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'load_from' => in_array($storage_driver, ['CustomUploader']) ? 0 : 1,
                ]);

                //update product pivot table data
                $project = Project::find($id);
                $project->project_sub_categories()->sync($request->subcategory);

                ProjectAttribute::where('create_project_id', $id)->delete();

                $arr = [];
                foreach ($request->checkbox_or_numeric_title as $key => $attr) {
                    // Trim and validate
                    $trimmedAttr = trim($attr);
                    if (strlen($trimmedAttr) > 100) {
                        DB::rollBack(); // cancel the whole transaction if you're already in one
                        toastr_error(__('Error: Each attribute title must be 100 characters or less.'));
                        return back()->withInput();
                    }

                    $attr_value = str_replace(" ", "_", strtolower($trimmedAttr));
                    $fallback_value = $request->checkbox_or_numeric_select[$key] == 'checkbox' ? "off" : 0;

                    $arr[] = [
                        'user_id' => $user_id,
                        'create_project_id' => $project->id,
                        'check_numeric_title' => $trimmedAttr,
                        'standard_check_numeric' => $request->$attr_value["standard"] ?? $fallback_value,
                        'premium_check_numeric' => $request->$attr_value["premium"] ?? $fallback_value,
                        'type' => $request->checkbox_or_numeric_select[$key] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }


                ProjectAttribute::insert($arr);

                $project_id_from_project_history_table = ProjectHistory::where('project_id', $id)->first();

                if (empty($project_id_from_project_history_table)) {
                    ProjectHistory::Create([
                        'project_id' => $project->id,
                        'user_id' => $project->user_id,
                        'reject_count' => 0,
                        'edit_count' => 1,
                    ]);
                } else {
                    ProjectHistory::where('project_id', $id)->update([
                        'reject_count' => $project_id_from_project_history_table->edit_count + 1
                    ]);
                }

                //security manage
                if (moduleExists('SecurityManage')) {
                    LogActivity::addToLog('Project edit', 'Freelancer');
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                if ($request->file('image')) {
                    $delete_imgs = json_decode($project->image, true);
                    foreach ($delete_imgs as $img) {
                        delete_frontend_cloud_image_if_module_exists('project/' . $img);
                    }
                }
                // Log the real error for debugging
                \Log::error('Project Edit Error: ' . $e->getMessage());
                // Show a more generic error to the user, or the real error in development
                if (app()->environment('local')) {
                    toastr_error(__('Error: ') . $e->getMessage());
                } else {
                    toastr_error(__('Something went wrong while updating the project. Please try again.'));
                }
                return back()->withInput();
            }

            try {
                $project_link = route('project.details', [
                    'username' => $project->project_creator->username,
                    'slug' => $project->slug,
                ]);

                $user = Auth::guard('web')->user();
                $posterName = e($user->first_name . ' ' . $user->last_name);
                $projectTitle = e($project->title);

                // ✅ Admin Email Message
                $adminMessage = '
                    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 0; font-family: Arial, sans-serif; color: #333;">
                        <div style="padding: 30px;">
                            <div style="border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px;">
                                <h2 style="color: #309400; margin: 0;">✏️ Project Updated on RightFreelancer</h2>
                            </div>
                            <p style="font-size: 16px; line-height: 1.6;">
                                The project has been updated by <strong>' . $posterName . '</strong>.
                            </p>
                            <p style="font-size: 16px; line-height: 1.6;">
                                Project Title:
                                <a href="' . $project_link . '" style="color: #309400; text-decoration: underline; font-weight: bold;">' . $projectTitle . '</a>
                            </p>
                            <p style="margin-top: 16px; font-size: 15px; line-height: 1.6;">
                                Please review the changes if necessary.
                            </p>
                            <p style="margin-top: 12px; font-size: 15px; line-height: 1.6;">
                                Best regards,<br>
                                <strong>The RightFreelancer System</strong>
                            </p>
                        </div>
                    </div>
                ';

                // ✅ User Email Message
                $userMessage = '
                    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 0; font-family: Arial, sans-serif; color: #333;">
                        <div style="padding: 30px;">
                            <div style="border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px;">
                                <h2 style="color: #309400; margin: 0;">🔄 Project Successfully Updated</h2>
                            </div>
                            <p style="font-size: 16px; line-height: 1.6;">
                                Hi <strong>' . $posterName . '</strong>,
                            </p>
                            <p style="font-size: 16px; line-height: 1.6;">
                                Your project 
                                <a href="' . $project_link . '" style="color: #309400; text-decoration: underline; font-weight: bold;">' . $projectTitle . '</a> 
                                has been successfully updated on <strong>RightFreelancer</strong>.
                            </p>
                            <p style="font-size: 15px; line-height: 1.6; margin-top: 16px;">
                                Your changes have been saved. Thank you for keeping your project details up to date!
                            </p>
                            <p style="margin-top: 12px; font-size: 15px; line-height: 1.6;">
                                Best regards,<br>
                                <strong>The RightFreelancer Team</strong>
                            </p>
                        </div>
                    </div>
                ';

                // ✅ Send Admin Mail
                Mail::to(get_static_option('site_global_email'))
                    ->send(new BasicMail([
                        'subject' => __('Project Updated'),
                        'message' => $adminMessage,
                    ]));

                // ✅ Send User Mail
                Mail::to($user->email)
                    ->send(new BasicMail([
                        'subject' => __('Your Project Update Confirmation'),
                        'message' => $userMessage,
                    ]));
            } catch (\Exception $e) {
                \Log::error("Edit Project Mail Error: " . $e->getMessage());
            }



            //edit project notification to admin
            AdminNotification::create([
                'identity' => $id,
                'user_id' => $user_id,
                'type' => 'Edit Project',
                'message' => __('A project has been edited.'),
            ]);

            toastr_success(__('Project Successfully Updated'));
            return redirect()->route('freelancer.profile.details', Auth::guard('web')->user()->username);
        }

        return view('frontend.user.freelancer.project.edit.edit-project', compact('project_details', 'get_sub_categories_from_project_category', 'get_child_categories_from_project_subcategory'));
    }

    // project preview
    public function project_preview()
    {
        $all_projects = Project::with('project_attributes')->where('user_id', Auth::guard('web')->user()->id)->latest()->get();
        return view('frontend.user.freelancer.project.preview.all-projects', compact('all_projects'));
    }

    // project description

    public function project_description(Request $request)
    {
        if ($request->ajax()) {
            $project_title_and_description = Project::select(['title', 'description'])->where('id', $request->project_id)->first();
            return view('frontend.user.freelancer.project.preview.project-description', compact('project_title_and_description'))->render();
        }
    }

    // project delete
    public function delete_project(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        ProjectAttribute::where('create_project_id', $project->id)->delete();
        ProjectHistory::where('project_id', $project->id)->delete();
        $project->delete();
        return response()->json(['status' => 'success']);
    }

}
