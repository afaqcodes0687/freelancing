<?php

namespace App\Http\Controllers\Api\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\JobProposal;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Chat\Entities\Offer;
use Modules\Subscription\Entities\UserSubscription;
use App\Models\UserIntroduction;
use App\Models\UserExperience;
use App\Models\UserEducation;
use App\Models\UserWorkSubcategory;
use App\Models\UserSkill;
use App\Models\IdentityVerification;
use Modules\Wallet\Entities\BankAccount;

class JobController extends Controller
{
    public function all_job()
    {
        $jobs = JobPost::with('job_creator:id,first_name,last_name,username,image,country_id,state_id,city_id,created_at,user_verified_status','job_skills')
            ->withCount('job_proposals')
            ->where('on_off','1')
            ->where('status','1')
            ->where('job_approve_request','1')
            ->where('type','fixed')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $arr = [];
        foreach($jobs as $key=> $job){
            $arr = $job->job_creator?->user_country?->country;
        }


        if($jobs){
            return response()->json([
                'jobs' => $jobs,
            ]);
        }
        return response()->json(['msg' => __('no jobs found.')]);
    }

    public function job_details($id = null)
    {
        $job_details = JobPost::with(['job_creator:id,first_name,last_name,username,image,country_id,state_id,city_id,created_at,user_verified_status', 'job_skills', 'job_proposals'])
            ->withCount('job_proposals')
            ->where(function($query) use ($id) {
                if (is_numeric($id)) {
                    $query->where('id', $id);
                } else {
                    $query->where('slug', $id);
                }
            })
            ->first();

        if (empty($job_details)) {
            return response()->json(['msg' => __('no job found.')], 404);
        }

        $user = User::select('id', 'first_name', 'last_name', 'username', 'image', 'country_id', 'state_id', 'city_id', 'created_at', 'user_verified_status', 'load_from')
            ->with('user_country')
            ->withCount('user_jobs')
            ->where('id', $job_details->user_id)->first();

        $total_job = JobPost::where('user_id', $job_details->user_id)->count();
        $total_order = Order::where('user_id', $job_details->user_id)
            ->where('status', 3)
            ->count();

        $hiring_rate = 0;
        if ($total_job > 0) {
            $hiring_rate = ($total_order * 100) / $total_job;
        }

        //check proposal send or not
        $check_proposal_send_or_not = 0;
        if (auth('sanctum')->check()) {
            $freelancer_id = auth('sanctum')->user()->id;
            $check_proposal_send_or_not = JobProposal::where('freelancer_id', $freelancer_id)->where('job_id', $job_details->id)->count();
        }

        if (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
            if ($job_details->attachment) {
                $job_details->cloud_link = render_frontend_cloud_image_if_module_exists('jobs/' . $job_details->attachment, load_from: $job_details->load_from);
            } else {
                $job_details->cloud_link = null;
            }

            if ($user->image) {
                $user->cloud_link = render_frontend_cloud_image_if_module_exists('profile/' . $user->image, load_from: $user->load_from);
            } else {
                $user->cloud_link = null;
            }
        }

        $completed_order = Order::with('freelancer')
            ->where('identity', $job_details->id)
            ->where('is_project_job', 'job') 
            ->where('status', 3) // 3 = complete
            ->latest()
            ->first();

        $is_job_completed = !is_null($completed_order);
        $is_job_hired = JobProposal::where('job_id', $job_details->id)->where('is_hired', 1)->exists();

        $job_status = 'open';
        if ($is_job_completed) {
            $job_status = 'complete';
        } elseif ($is_job_hired) {
            $job_status = 'in progress';
        }

        return response()->json([
            'job_details' => $job_details,
            'user' => $user,
            'image' => asset('assets/uploads/profile/' . $user?->image),
            'job_file_path' => asset('assets/uploads/jobs/'),
            'hiring_rate' => $hiring_rate,
            'check_proposal_send_or_not' => $check_proposal_send_or_not,
            'storage_driver' => Storage::getDefaultDriver() ?? '',
            'is_job_completed' => $is_job_completed,
            'is_job_hired' => $is_job_hired,
            'job_status' => $job_status,
        ]);
    }

    public function job_proposal_send(Request $request)
    {
        $request->validate([
            'job_id' => 'required',
            'client_id' => 'required',
            'amount' => 'required|numeric|gt:0',
            'duration' => 'required',
            'revision' => 'required|integer|min:0|max:100',
            'cover_letter' => 'required|min:10',
            'attachment' => 'nullable|mimes:png,jpg,jpeg,bmp,gif,tiff,svg,csv,txt,xlx,xls,pdf,docx,doc,mp4,avi,mov,wmv,webp|max:20480',
        ]);

        $freelancer_id = auth('sanctum')->user()->id;

        $is_job_hired = JobProposal::where('job_id', $request->job_id)->where('is_hired', 1)->exists();
        if ($is_job_hired) {
            return response()->json(['msg' => __('This job is already filled and no longer accepting proposals.')], 422);
        }

        $check_freelancer_proposal = JobProposal::where('freelancer_id', $freelancer_id)->where('job_id', $request->job_id)->first();
        if ($check_freelancer_proposal) {
            return response()->json(['msg' => __('You can not send one more proposal.')], 422);
        }

        if (!$this->isFreelancerProfileComplete($freelancer_id)) {
            return response()->json(['msg' => __('Complete your profile (100%) to send job proposals')], 422);
        }

        if (!$this->isFreelancerIdentityVerified($freelancer_id)) {
            return response()->json(['msg' => __('Your identity must be verified by admin to send job proposals')], 422);
        }

        if (!auth('sanctum')->user()->country_id) {
            return response()->json(['msg' => __('Complete your profile properly to send job proposals')], 422);
        }

        if (auth('sanctum')->user()->is_suspend == 1) {
            return response()->json(['msg' => __('You can not send job proposal because your account is suspended. please try to contact admin.')], 422);
        }

        $total_limit = UserSubscription::where('user_id', $freelancer_id)->where('payment_status', 'complete')->whereDate('expire_date', '>', Carbon::now())->sum('limit');
        $limit_settings = get_static_option('job_proposal_connects', 2);

        if (get_static_option('subscription_enable_disable') != 'disable') {
            $freelancer_subscription = UserSubscription::select(['id', 'user_id', 'limit', 'expire_date', 'created_at'])
                ->where('payment_status', 'complete')
                ->where('status', 1)
                ->where('user_id', $freelancer_id)
                ->where("limit", '>=', $limit_settings)
                ->whereDate('expire_date', '>', Carbon::now())->first();

            if ($total_limit >= $limit_settings && !empty($freelancer_subscription)) {
                $attachment_name = '';
                $upload_folder = 'jobs/proposal';
                $storage_driver = Storage::getDefaultDriver();
                $image_extensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'tiff', 'webp'];
                $svg_extensions = ['svg'];

                if (cloudStorageExist() && in_array($storage_driver, ['s3', 'cloudFlareR2', 'wasabi'])) {
                    if ($attachment = $request->file('attachment')) {
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
                        $extension = strtolower($attachment->getClientOriginalExtension());
                        $attachment_name = time() . '-' . uniqid() . '.' . $extension;
                        $attachment->move('assets/uploads/jobs/proposal', $attachment_name);
                    }
                }

                $proposal = JobProposal::create([
                    'job_id' => $request->job_id,
                    'freelancer_id' => $freelancer_id,
                    'client_id' => $request->client_id,
                    'amount' => $request->amount,
                    'freelancer_service_fee' => $request->freelancer_service_fee ?? 0,
                    'you_receive_amount' => $request->you_receive_amount ?? 0,
                    'duration' => $request->duration,
                    'revision' => $request->revision,
                    'cover_letter' => $request->cover_letter,
                    'attachment' => $attachment_name,
                    'load_from' => in_array($storage_driver, ['CustomUploader']) ? 0 : 1,
                ]);

                $this->save_milestones_api($request, $proposal->id);

                client_notification($proposal->id, $request->client_id, 'Proposal', __('You have a new job proposal'));

                UserSubscription::where('id', $freelancer_subscription->id)->update([
                    'limit' => $freelancer_subscription->limit - $limit_settings
                ]);

                return response()->json(['msg' => __('Proposal successfully send')]);
            }

            return response()->json(['msg' => __('You have not enough connect to apply.')], 422);
        } else {
            $attachment_name = '';
            if ($attachment = $request->file('attachment')) {
                $extension = strtolower($attachment->getClientOriginalExtension());
                $attachment_name = time() . '-' . uniqid() . '.' . $extension;
                $attachment->move('assets/uploads/jobs/proposal', $attachment_name);
            }

            $proposal = JobProposal::create([
                'job_id' => $request->job_id,
                'freelancer_id' => $freelancer_id,
                'client_id' => $request->client_id,
                'amount' => $request->amount,
                'freelancer_service_fee' => $request->freelancer_service_fee ?? 0,
                'you_receive_amount' => $request->you_receive_amount ?? 0,
                'duration' => $request->duration,
                'revision' => $request->revision,
                'cover_letter' => $request->cover_letter,
                'attachment' => $attachment_name,
            ]);

            $this->save_milestones_api($request, $proposal->id);

            client_notification($proposal->id, $request->client_id, 'Proposal', __('You have a new job proposal'));

            return response()->json(['msg' => __('Proposal successfully send')]);
        }
    }

    private function save_milestones_api($request, $job_proposal_id)
    {
        $milestones = $request->input('milestones');
        if (!empty($milestones)) {
            if (is_string($milestones)) {
                $milestones = json_decode($milestones, true);
            }
            if (is_array($milestones) && count($milestones) > 0) {
                foreach ($milestones as $milestone) {
                    \App\Models\JobProposalMilestone::create([
                        'job_proposal_id' => $job_proposal_id,
                        'title' => $milestone['title'] ?? '',
                        'description' => $milestone['description'] ?? '',
                        'price' => $milestone['price'] ?? 0,
                        'revision' => $milestone['revision'] ?? 0,
                        'deadline' => $milestone['deadline'] ?? ''
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

    //job filter
    public function jobs_filter(Request $request)
    {
        if(!empty($request->country) || !empty($request->type) || !empty($request->level) || !empty($request->min_price) || !empty($request->max_price) || !empty($request->duration || !empty($request->category) || !empty($request->subcategory) )){
            $jobs = JobPost::with('job_creator:id,first_name,last_name,username,image,country_id,state_id,city_id,created_at,user_verified_status','job_skills','job_sub_categories')
                ->withCount('job_proposals')
                ->where('on_off','1')
                ->where('status','1')
                ->where('job_approve_request','1')
                ->where('type','fixed')
                ->latest();

            if(!empty($request->country)){
                $jobs = $jobs->WhereHas('job_creator',function($q) use($request){
                    $q->where('country_id',$request->country);
                });
            }

            if(!empty($request->type)){
                $jobs = $jobs->where('type',$request->type);
            }

            if(!empty($request->level)){
                $jobs = $jobs->where('level',$request->level);
            }

            if(!empty($request->min_price) && !empty($request->max_price)){
                $jobs = $jobs->whereBetween('budget',[$request->min_price,$request->max_price]);
            }

            if(!empty($request->duration)){
                $jobs = $jobs->where('duration',$request->duration);
            }

            if(!empty($request->category)){
                $jobs = $jobs->where('category',$request->category);
            }

            if(!empty($request->subcategory)){
                $jobs = $jobs->WhereHas('job_sub_categories',function($q) use($request){
                    $q->where('sub_categories.id',$request->subcategory);
                });
            }

            if(!empty($request->string)){
                $jobs = $jobs->where('title','LIKE','%'.$request->string.'%');
            }

            $jobs = $jobs->paginate(10)->withQueryString();

            $arr = [];
            foreach($jobs as $key=> $job){
                $arr = $job->job_creator?->user_country?->country;
            }

            if($jobs->total() > 0){
                return response()->json([
                    'jobs' => $jobs,
                ]);
            }else{
                return response()->json(['msg' => __('no jobs found.')]);
            }

        }else{
            return response()->json(['msg' => __('no jobs found.')]);
        }
    }

    //my prposals
    public function my_proposal()
    {
        $my_proposals = JobProposal::with('job:id,user_id,title,budget')
            ->where('freelancer_id',auth('sanctum')->user()->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
            $my_proposals->transform(function ($proposal) {
                if($proposal->attachment){
                    $proposal->cloud_link = render_frontend_cloud_image_if_module_exists('jobs/proposal/'.$proposal->attachment, load_from: $proposal->load_from);
                }else{
                    $proposal->cloud_link = null;
                }
                return $proposal;
            });
        }

        return response()->json([
            'my_proposals' => $my_proposals,
            'storage_driver' => Storage::getDefaultDriver() ?? '',
        ]);
    }

    public function my_offer()
    {
        $my_offers = Offer::with('client:id,first_name,last_name,image,load_from')->where('freelancer_id',auth('sanctum')->user()->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
            $my_offers->transform(function ($offer) {
                $offer->client->cloud_link = render_frontend_cloud_image_if_module_exists('profile/' . $offer?->client->image, load_from: $offer?->client->load_from);
                return $offer;
            });
        }

        return response()->json([
            'my_offers' => $my_offers,
            'profile_image_path' => asset('assets/uploads/profile/'),
            'storage_driver' => Storage::getDefaultDriver() ?? '',
        ]);
    }

}
