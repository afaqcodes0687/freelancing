<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Project;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\User;
use Modules\Service\Entities\Category;
use App\Models\UserEarning;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserSkill;
use App\Models\UserWork;
use App\Models\UserIntroduction;
use App\Models\IdentityVerification;
use App\Models\OneRupeeDraw;
use Modules\Wallet\Entities\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\PromoteFreelancer\Entities\PromotionProjectList;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\CountryManage\Entities\City;

class ProfileDetailsController extends Controller
{
    //freelancer profile details
    public function profile_details($username)
    {

        $user = User::with('user_introduction', 'freelancer_ratings')
            ->select(['id', 'username', 'image', 'hourly_rate', 'first_name', 'last_name', 'country_id', 'state_id', 'city_id', 'check_work_availability', 'user_verified_status', 'load_from', 'experience_level'])
            ->where('username', $username)
            ->first();

        if ($user) {
            $countries = Country::all();
            $states = State::where('country_id', old('country', $user->country_id))->get();
            $cities = City::where('state_id', old('state', $user->state_id))->get();
            $user_work = UserWork::where('user_id', $user->id)->first();
            $total_earning = UserEarning::where('user_id', $user->id)->first();
            $complete_orders_in_total = Order::whereHas('user')->where('freelancer_id', $user->id)->where('status', 3)->count();
            $active_orders_count = Order::where('freelancer_id', $user->id)->whereHas('user')->where('status', 1)->count();
            $one_dollar_game_winnings = OneRupeeDraw::where('winner_user_id', $user->id)->count();

            // Get total reviews count
            $total_reviews_count = \App\Models\Rating::whereHas('order', function($query) use ($user) {
                $query->where('freelancer_id', $user->id)->where('status', 3);
            })->where('sender_type', 1)->count();
            
            // Get total completed jobs count
            $total_completed_jobs = Order::where('freelancer_id', $user->id)->where('status', 3)->count();
            
            // Get only 5 latest reviews for display
            $latest_reviews = Order::select('id', 'identity', 'status', 'freelancer_id')
                ->whereHas('user')
                ->whereHas('rating', function($query) {
                    $query->where('sender_type', 1);
                })
                ->where('freelancer_id', $user->id)
                ->where('status', 3)
                ->latest()
                ->take(5)
                ->get();

            $skills_according_to_category = collect();

            // Get all user's category/subcategory pairs from user_work_subcategories
            $userWorkSubcats = \DB::table('user_work_subcategories')
                ->where('user_id', $user->id)
                ->select('category_id', 'sub_category_id')
                ->get();

            if ($userWorkSubcats->isNotEmpty()) {
                // Get unique category IDs and subcategory IDs
                $categoryIds = $userWorkSubcats->pluck('category_id')->unique()->toArray();
                $subCategoryIds = $userWorkSubcats->pluck('sub_category_id')->unique()->toArray();

                // Fetch categories → subcategories → skills
                $skills_according_to_category = Category::with([
                    'sub_categories' => function ($q) use ($subCategoryIds) {
                        $q->whereIn('id', $subCategoryIds)
                            ->with([
                                'skills' => function ($query) {
                                    $query->select('id', 'skill', 'sub_category_id', 'category_id')
                                        ->where('status', 1);
                                }
                            ]);
                    }
                ])
                    ->whereIn('id', $categoryIds)
                    ->select('id', 'category')
                    ->get();
            }


            $skills = UserSkill::select('skill')->where('user_id', $user->id)->first()->skill ?? '';
            $portfolios = Portfolio::where('username', $username)->latest()->get();
            $educations = UserEducation::where('user_id', $user->id)->latest()->get();
            $experiences = UserExperience::where('user_id', $user->id)->latest()->get();
            $projects = Project::with('project_history')->whereHas('project_creator')->where('user_id', $user->id)->withCount('orders')->latest()->get();
            $average_rating = round($user->freelancer_ratings->avg('rating') ?? 0, 1);

            // Step completion logic for verification badge
            $user_introduction = UserIntroduction::where('user_id', $user->id)->first();
            $experiences = UserExperience::where('user_id', $user->id)->latest()->get();
            $educations = UserEducation::where('user_id', $user->id)->latest()->get();
            $user_work_exists = \DB::table('user_work_subcategories')->where('user_id', $user->id)->exists();

            // Step 1: Personal Information (basic info)
            $step1Complete = $user->first_name && $user->last_name && $user->country_id && $user->image;

            // Step 2: Account Setup (introduction, experience, education, work, hourly rate)
            $step2Complete = $user_introduction && $experiences->count() > 0 && $educations->count() > 0 && $user_work_exists && $user->hourly_rate;

            // Step 3: Wallet (Bank Account) - simplified check
            $bank_account = BankAccount::where('user_id', $user->id)->first();
            $step3Complete = $bank_account !== null && $bank_account->account_title && $bank_account->bank_name;

            // Step 4: Identity Verification
            $identity_verification = IdentityVerification::where('user_id', $user->id)->first();
            $step4Complete = $identity_verification && $identity_verification->status === 1;

            // Check if all steps are complete for verification badge
            $isProfileVerified = $step1Complete && $step2Complete && $step3Complete && $step4Complete;

            //pro profile view count
            if (moduleExists('PromoteFreelancer')) {
                if (Session::has('is_pro')) {
                    $current_date = \Carbon\Carbon::now()->toDateTimeString();
                    $find_package = PromotionProjectList::where('identity', $user->id)
                        ->where('type', 'profile')
                        ->where('expire_date', '>=', $current_date)
                        ->first();
                    if ($find_package) {
                        PromotionProjectList::where('id', $find_package->id)->update(['click' => $find_package->click + 1]);
                        Session::forget('is_pro');
                    }
                }
            }

            // Connects info (only for freelancers)
            $freelancer_total_connects = 0;
            if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2) {
                $freelancer_id = Auth::guard('web')->user()->id;
                $freelancer_total_connects = \Modules\Subscription\Entities\UserSubscription::where('user_id', $freelancer_id)
                    ->where('payment_status', 'complete')
                    ->where('status', 1)
                    ->where(function($query) {
                        $query->whereNull('expire_date')
                              ->orWhereDate('expire_date', '>', \Carbon\Carbon::now());
                    })
                    ->sum('limit');
            }

            return view('frontend.profile-details.profile-details', compact([
                'username',
                'skills_according_to_category',
                'portfolios',
                'skills',
                'educations',
                'experiences',
                'projects',
                'user',
                'total_earning',
                'complete_orders_in_total',
                'active_orders_count',
                'one_dollar_game_winnings',
                'total_reviews_count',
                'latest_reviews',
                'total_completed_jobs',
                'countries',
                'states',
                'cities',
                'average_rating',
                'isProfileVerified',
                'step1Complete',
                'step2Complete',
                'step3Complete',
                'step4Complete',
                'freelancer_total_connects',
            ]));
        } else {
            return back();
        }
    }
    //freelancer public profile view
    public function social_image($username)
    {
        $user = User::where('username', $username)->first();
        if (!$user)
            abort(404);

        $total_earning = UserEarning::where('user_id', $user->id)->first();
        $complete_orders_in_total = Order::where('freelancer_id', $user->id)->where('status', 3)->count();
        $active_orders_count = Order::where('freelancer_id', $user->id)->where('status', 1)->count();
        $average_rating = round($user->freelancer_ratings->avg('rating') ?? 0, 1);

        $hourlyRate = amount_with_currency_symbol($user->hourly_rate);
        $totalEarned = amount_with_currency_symbol($total_earning->total_earning ?? 0);

        // Professional Font Path
        $fontPath = base_path('vendor/endroid/qr-code/assets/open_sans.ttf');
        $width = 1200;
        $height = 630;

        $img = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        $blue = imagecolorallocate($img, 0, 119, 181);
        $brandGreen = imagecolorallocate($img, 48, 148, 0); // #309400
        $dark = imagecolorallocate($img, 33, 37, 41);
        $grey = imagecolorallocate($img, 108, 117, 125);
        $lightGrey = imagecolorallocate($img, 230, 230, 230);
        $green = imagecolorallocate($img, 40, 167, 69);

        imagefill($img, 0, 0, $white);

        // Sidebar Design (Now using Brand Green)
        imagefilledrectangle($img, 0, 0, 450, 630, $brandGreen);

        // LOAD PROFILE IMAGE
        $src = null;
        $profile_img_data = null;

        // 1. Try Cloud Storage
        if ($user->image && cloudStorageExist()) {
            $driver = get_static_option('storage_driver');
            if (in_array($driver, ['wasabi', 's3', 'cloudFlareR2'])) {
                try {
                    $profile_img_data = \Storage::disk($driver)->get('profile/' . $user->image);
                } catch (\Exception $e) {
                }
            }
        }

        // 2. Try Local File (CORRECTED PATH)
        if (!$profile_img_data && $user->image) {
            $local_path = public_path('assets/uploads/profile/' . $user->image);
            if (file_exists($local_path)) {
                $profile_img_data = @file_get_contents($local_path);
            }
        }

        // 3. Last Resort: Default Image
        if (!$profile_img_data) {
            $default_path = public_path('assets/static/img/author/author.jpg'); // Adjusting to public_path too
            if (file_exists($default_path)) {
                $profile_img_data = @file_get_contents($default_path);
            }
        }

        if ($profile_img_data) {
            $src = @imagecreatefromstring($profile_img_data);
            if ($src) {
                $sw = imagesx($src);
                $sh = imagesy($src);
                $size = min($sw, $sh);
                $x = ($sw - $size) / 2;
                $y = ($sh - $size) / 2;

                $dest_w = 380;
                $dest_h = 380;
                $dest_x = 35;
                $dest_y = 125;

                // White Border for Image
                imagefilledrectangle($img, $dest_x - 5, $dest_y - 5, $dest_x + $dest_w + 5, $dest_y + $dest_h + 5, $white);
                imagecopyresampled($img, $src, $dest_x, $dest_y, $x, $y, $dest_w, $dest_h, $size, $size);
                imagedestroy($src);
            }
        }

        // DRAW TEXT 
        $name = strtoupper($user->first_name . ' ' . $user->last_name);
        $title = optional($user->user_introduction)->title ?? 'Professional Freelancer';

        if (file_exists($fontPath)) {
            // PRO TYPOGRAPHY
            imagettftext($img, 32, 0, 500, 100, $dark, $fontPath, $name);
            imagettftext($img, 18, 0, 500, 150, $brandGreen, $fontPath, $title);

            imageline($img, 500, 180, 1100, 180, $lightGrey);

            // Stats
            $y = 260;
            $items = [
                "Hourly Rate: $hourlyRate",
                "Total Earned: $totalEarned",
                "Completed Jobs: $complete_orders_in_total",
                "Active Jobs: $active_orders_count",
                "Rating Score: $average_rating / 5.0"
            ];

            foreach ($items as $item) {
                imagettftext($img, 20, 0, 500, $y, $dark, $fontPath, $item);
                $y += 60;
            }

            imagettftext($img, 14, 0, 500, 580, $grey, $fontPath, "PRO PROFILE | RIGHTFREELANCER.COM");
        } else {
            // Fallback to basic fonts if TTF not found
            imagestring($img, 5, 500, 80, $name, $dark);
            imagestring($img, 5, 500, 110, "-----------------------------", $grey);
            imagestring($img, 4, 500, 140, $title, $brandGreen);
            imagestring($img, 5, 500, 220, "Hourly Rate:     $hourlyRate", $dark);
            imagestring($img, 5, 500, 270, "Total Earned:    $totalEarned", $dark);
            imagestring($img, 5, 500, 320, "Completed Jobs:  $complete_orders_in_total", $dark);
            imagestring($img, 5, 500, 370, "Active Jobs:     $active_orders_count", $dark);
            imagestring($img, 5, 500, 420, "Rating Score:    $average_rating / 5.0", $brandGreen);
        }

        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
        exit;
    }

    //freelancer portfolio details
    public function portfolio_details(Request $request)
    {
        $portfolioDetails = \App\Models\Portfolio::where('id', $request->id)->first();
        $username = \App\Models\User::select('username')->where('id', $portfolioDetails->user_id)->first();
        $username = $username->username;
        return view('frontend.profile-details.portfolio-details', compact('portfolioDetails', 'username'))->render();
    }
}
