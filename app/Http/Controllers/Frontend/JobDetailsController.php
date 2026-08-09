<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobPost;
use App\Models\JobProposal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\Subscription\Entities\Subscription;
use Modules\Subscription\Entities\UserSubscription;
use App\Models\Order;
use App\Models\UserIntroduction;
use App\Models\UserExperience;
use App\Models\UserEducation;
use App\Models\UserWorkSubcategory;
use App\Models\UserSkill;
use App\Models\IdentityVerification;
use Modules\Wallet\Entities\BankAccount;


class JobDetailsController extends Controller
{
    public function job_details($username = null, $slug = null)
    {
        $job_details = JobPost::with(['job_creator', 'job_skills', 'job_proposals'])
            ->where('slug', $slug)
            ->first();

        if (!empty($job_details)) {
            // Job client info
            $user = User::with('user_country')
                ->where('id', $job_details->user_id)
                ->first();

            $completed_order = Order::with('freelancer')
                ->where('identity', $job_details->id)
                ->where('is_project_job', 'job') 
                ->where('status', 3) // 3 = complete
                ->latest()
                ->first();

            $is_job_completed = !is_null($completed_order);
            $completed_at_formatted = $completed_order?->updated_at?->toFormattedDateString();

            $is_job_hired = JobProposal::where('job_id', $job_details->id)->where('is_hired', 1)->exists();

            $freelancer_total_connects = 0;
            $connects_required_per_proposal = get_static_option('job_proposal_connects', 2);

            if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2) {
                $freelancer_id = Auth::guard('web')->user()->id;

                // Count connects from all active subscriptions (including free ones with no expiry)
                $freelancer_total_connects = UserSubscription::where('user_id', $freelancer_id)
                    ->where('payment_status', 'complete')
                    ->where('status', 1)
                    ->where(function($query) {
                        $query->whereNull('expire_date') // Free subscriptions with no expiry
                              ->orWhereDate('expire_date', '>', Carbon::now()); // Paid subscriptions with valid expiry
                    })
                    ->sum('limit');
            }

            $is_profile_complete = false;

            if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2) {
                $freelancer_id = Auth::guard('web')->user()->id;
                $is_profile_complete = $this->isFreelancerProfileComplete($freelancer_id);
            }

             $is_identity_verified = false;

                if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2) {
                    $freelancer_id = Auth::guard('web')->user()->id;
                    $is_identity_verified = $this->isFreelancerIdentityVerified($freelancer_id);
                }

            return view('frontend.pages.job-details.job-details', compact(
                'job_details',
                'user',
                'freelancer_total_connects',
                'connects_required_per_proposal',
                'completed_order',
                'is_job_completed',
                'completed_at_formatted',
                'is_job_hired',
                'is_profile_complete',
                'is_identity_verified'
            ));
        }

        return back();
    }
    public function job_proposal_send(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'amount' => 'required|numeric|gt:0',
            'freelancer_service_fee' => 'required|numeric',
            'you_receive_amount' => 'required|numeric',
            'duration' => 'required',
            'revision' => 'required|min:0|max:100',
            'cover_letter' => 'required|min:10',
        ]);

        $freelancer_id = Auth::guard('web')->user()->id;
        $connects_required_per_proposal = get_static_option('job_proposal_connects', 2);

        $is_job_hired = JobProposal::where('job_id', $request->job_id)->where('is_hired', 1)->exists();
        if ($is_job_hired) {
            return back()->with(toastr_warning(__('This job is already filled and no longer accepting proposals.')));
        }

        $check_freelancer_proposal = JobProposal::where('freelancer_id', $freelancer_id)->where('job_id', $request->job_id)->first();

        if (!$this->isFreelancerProfileComplete($freelancer_id)) {
            return back()->with(toastr_warning(__('Complete your profile (100%) to send job proposals')));
        }


        if (!$this->isFreelancerIdentityVerified($freelancer_id)) {
            return back()->with(toastr_warning(__('Your identity must be verified by admin to send job proposals')));
        }

        if (!Auth::guard('web')->user()->country_id) {
            return back()->with(toastr_warning(__('Complete your profile properly to send job proposals')));
        }

        if ($check_freelancer_proposal) {
            return back()->with(toastr_warning(__('You can not send one more proposal.')));
        }

        if (Auth::guard('web')->user()->is_suspend == 1) {
            return back()->with(toastr_warning(__('You can not send job proposal beacuse your account is suspended. please try to contact admin')));
        }

        if (get_static_option('subscription_enable_disable') != 'disable') {
            $freelancer_subscription = UserSubscription::select(['id', 'user_id', 'limit', 'expire_date', 'created_at'])
                ->where('payment_status', 'complete')
                ->where('status', 1)
                ->where('user_id', $freelancer_id)
                ->where("limit", '>=', $connects_required_per_proposal)
                ->where(function($query) {
                    $query->whereNull('expire_date') // Free subscriptions with no expiry
                          ->orWhereDate('expire_date', '>', Carbon::now()); // Paid subscriptions with valid expiry
                })
                ->first();

            $total_limit = UserSubscription::where('user_id', $freelancer_id)
                ->where('payment_status', 'complete')
                ->where('status', 1)
                ->where(function($query) {
                    $query->whereNull('expire_date') // Free subscriptions with no expiry
                          ->orWhereDate('expire_date', '>', Carbon::now()); // Paid subscriptions with valid expiry
                })
                ->sum('limit');

            if ($total_limit >= $connects_required_per_proposal && !empty($freelancer_subscription)) {
                $attachment_name = '';
                $upload_folder = 'jobs/proposal';
                $storage_driver = Storage::getDefaultDriver();
                $image_extensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'tiff', 'webp'];
                $svg_extensions = ['svg'];

                if (cloudStorageExist() && in_array($storage_driver, ['s3', 'cloudFlareR2', 'wasabi'])) {
                    if ($attachment = $request->file('attachment')) {
                        $request->validate([
                            'attachment' => 'required|mimes:png,jpg,jpeg,bmp,gif,tiff,svg,csv,txt,xlx,xls,xlsx,pdf,docx,doc,mp4,avi,mov,wmv,webp|max:20480',
                        ]);
                        $extension = strtolower($attachment->getClientOriginalExtension());
                        $attachment_name = time() . '-' . uniqid() . '.' . $extension;

                        if (in_array($extension, array_merge($image_extensions, $svg_extensions))) {
                            add_frontend_cloud_image_if_module_exists($upload_folder, $attachment, $attachment_name, 'public');
                        } else {
                            $attachment->storeAs($upload_folder, $attachment_name, 'public');
                        }
                    }
                } else {
                    if ($attachment = $request->file('attachment')) {
                        $request->validate([
                            'attachment' => 'required|mimes:png,jpg,jpeg,bmp,gif,tiff,svg,csv,txt,xlx,xls,xlsx,pdf,docx,doc,mp4,avi,mov,wmv,webp|max:20480',
                        ]);
                        $extension = strtolower($attachment->getClientOriginalExtension());
                        $attachment_name = time() . '-' . uniqid() . '.' . $extension;

                        if (in_array($extension, $image_extensions)) {
                            $resize_full_image = Image::make($request->attachment)->resize(1000, 600);
                            $resize_full_image->save('assets/uploads/jobs/proposal' . '/' . $attachment_name);
                        } elseif (in_array($extension, $svg_extensions)) {
                            // SVG - don't resize, just move
                            $attachment->move('assets/uploads/jobs/proposal', $attachment_name);
                        } else {
                            $attachment->move('assets/uploads/jobs/proposal', $attachment_name);
                        }
                    }
                }

                $proposal = JobProposal::create([
                    'job_id' => $request->job_id,
                    'freelancer_id' => auth()->user()->id,
                    'client_id' => $request->client_id,
                    'amount' => $request->amount,
                    'freelancer_service_fee' => $request->freelancer_service_fee,
                    'you_receive_amount' => $request->you_receive_amount,
                    'duration' => $request->duration,
                    'revision' => $request->revision,
                    'cover_letter' => $request->cover_letter,
                    'attachment' => $attachment_name,
                    'load_from' => in_array($storage_driver, ['CustomUploader']) ? 0 : 1,
                ]);

                $this->save_milestones($request, $proposal->id);

                client_notification($proposal->id, $request->client_id, 'Proposal', __('You have a new job proposal'));

                UserSubscription::where('id', $freelancer_subscription->id)->update([
                    'limit' => $freelancer_subscription->limit - $connects_required_per_proposal
                ]);

                return back()->with(toastr_success(__('Proposal successfully send')));
            }

            return back()->with(toastr_warning(__('You have not enough connect to apply.')));
        } else {
            $attachment_name = '';
            if ($attachment = $request->file('attachment')) {
                $request->validate([
                    'attachment' => 'required|mimes:png,jpg,jpeg,bmp,gif,tiff,svg,csv,txt,xlx,xls,xlsx,pdf,docx,doc,mp4,avi,mov,wmv,webp|max:20480',
                ]);

                $extension = strtolower($attachment->getClientOriginalExtension());
                $attachment_name = time() . '-' . uniqid() . '.' . $extension;
                $image_extensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'tiff', 'webp'];
                $svg_extensions = ['svg'];

                if (in_array($extension, $image_extensions)) {
                    $resize_full_image = Image::make($request->attachment)->resize(1000, 600);
                    $resize_full_image->save('assets/uploads/jobs/proposal' . '/' . $attachment_name);
                } elseif (in_array($extension, $svg_extensions)) {
                    $attachment->move('assets/uploads/jobs/proposal', $attachment_name);
                } else {
                    $attachment->move('assets/uploads/jobs/proposal', $attachment_name);
                }
            }

            $proposal = JobProposal::create([
                'job_id' => $request->job_id,
                'freelancer_id' => auth()->user()->id,
                'client_id' => $request->client_id,
                'amount' => $request->amount,
                'freelancer_service_fee' => $request->freelancer_service_fee,
                'you_receive_amount' => $request->you_receive_amount,
                'duration' => $request->duration,
                'revision' => $request->revision,
                'cover_letter' => $request->cover_letter,
                'attachment' => $attachment_name,
            ]);

            $this->save_milestones($request, $proposal->id);

            client_notification($proposal->id, $request->client_id, 'Proposal', __('You have a new job proposal'));

            return back()->with(toastr_success(__('Proposal successfully send')));
        }
    }
    
    private function save_milestones($request, $proposal_id)
    {
        if ($request->pay_by_milestone === 'pay-by-milestone') {
            $titles = $request->milestone_title;
            $descriptions = $request->milestone_description;
            $prices = $request->milestone_price;
            $revisions = $request->milestone_revision;
            $deadlines = $request->milestone_deadline;

            if (is_array($titles) && count($titles) > 0) {
                foreach ($titles as $key => $title) {
                    \App\Models\JobProposalMilestone::create([
                        'job_proposal_id' => $proposal_id,
                        'title' => $title,
                        'description' => $descriptions[$key] ?? '',
                        'price' => $prices[$key] ?? 0,
                        'revision' => $revisions[$key] ?? '',
                        'deadline' => $deadlines[$key] ?? '',
                    ]);
                }
            }
        }
    }

    private function isFreelancerProfileComplete($freelancer_id)
    {
        $user = User::find($freelancer_id);
        if (!$user) {
            return false;
        }

        $step1Complete = $user->first_name && $user->last_name && $user->country_id && $user->experience_level;

        $user_introduction = UserIntroduction::where('user_id', $freelancer_id)->first();
        $experiences = UserExperience::where('user_id', $freelancer_id)->get();
        $educations = UserEducation::where('user_id', $freelancer_id)->get();
        $userSubcategories = UserWorkSubcategory::where('user_id', $freelancer_id)->pluck('sub_category_id')->toArray();
        
        $step2Complete = $user_introduction && 
                         $experiences->count() > 0 && 
                         $educations->count() > 0 && 
                         count($userSubcategories) > 0 && 
                         $user->skills()->count() > 0 && 
                         $user->hourly_rate;

        $bank_account = BankAccount::where('user_id', $freelancer_id)->first();
        $step3Complete = $bank_account !== null && 
                         $bank_account->account_title && 
                         $bank_account->bank_name && 
                         ($bank_account->swis_code || $bank_account->iban_number || $bank_account->account_number);

        return $step1Complete && $step2Complete && $step3Complete;
    }
    private function isFreelancerIdentityVerified($freelancer_id)
    {
        $identity_verification = IdentityVerification::where('user_id', $freelancer_id)->first();
        
        return $identity_verification && $identity_verification->status === 1;
    }

}
