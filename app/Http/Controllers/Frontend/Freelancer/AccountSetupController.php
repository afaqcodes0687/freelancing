<?php

namespace App\Http\Controllers\Frontend\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserIntroduction;
use App\Models\UserSkill;
use App\Models\UserWorkSubcategory;

use App\Models\UserWork;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\Service\Entities\Category;
use Modules\Service\Entities\SubCategory;
use App\Models\IdentityVerification;
use Modules\Wallet\Entities\BankAccount;

class AccountSetupController extends Controller
{
    //account setup main page
    public function account_setup()
    {
        $user = Auth::user(); 
        $user_id = $user->id;

        // Step 1
        $step1Complete = $user->first_name && $user->last_name && $user->country_id && $user->experience_level;

        // Related Data
        $user_introduction = UserIntroduction::where('user_id', $user_id)->first();
        $experiences = UserExperience::where('user_id', $user_id)->latest()->get();
        $educations = UserEducation::where('user_id', $user_id)->latest()->get();

        // Categories
        $categories = Category::where('status', 1)->with('sub_categories')->get();
        $count = Category::count();
        $more_categories = Category::select(['id', 'category', 'slug', 'image'])
            ->where('status', 1)
            ->skip(5)
            ->take($count - 5)
            ->get();

        // Already selected subcategories
        $userSubcategories = \DB::table('user_work_subcategories')
            ->where('user_id', $user_id)
            ->pluck('sub_category_id')
            ->toArray();

        // User selected skills
        $skillsArray = $user->skills()->pluck('skill')->toArray();

        // Category -> Subcategory -> Skills mapping
        $categoriesWithSkills = [];

        if (!empty($userSubcategories)) {
            $userCategories = \DB::table('user_work_subcategories')
                ->where('user_id', $user_id)
                ->join('categories', 'categories.id', '=', 'user_work_subcategories.category_id')
                ->join('sub_categories', 'sub_categories.id', '=', 'user_work_subcategories.sub_category_id')
                ->select(
                    'categories.id as category_id',
                    'categories.category as category_name',
                    'sub_categories.id as subcategory_id',
                    'sub_categories.sub_category as subcategory_name'
                )
                ->get();

            foreach ($userCategories as $item) {
                $skills = \App\Models\Skill::where('sub_category_id', $item->subcategory_id)
                    ->pluck('skill')
                    ->toArray();

                $categoriesWithSkills[$item->category_id]['category_name'] = $item->category_name;
                $categoriesWithSkills[$item->category_id]['subcategories'][$item->subcategory_id] = [
                    'subcategory_name' => $item->subcategory_name,
                    'skills' => $skills,
                    'selected' => in_array($item->subcategory_id, $userSubcategories),
                ];
            }
        }

        // Step 2: Account Setup check
        $step2Complete = $user_introduction && $experiences->count() > 0  && $educations->count() > 0 && count($userSubcategories) > 0  && $user->skills()->count() > 0 && $user->hourly_rate;   

        // Step 3: Wallet
        $bank_account = BankAccount::where('user_id', $user->id)->first();
        $step3Complete = $bank_account !== null && $bank_account->account_title && $bank_account->bank_name && (
            $bank_account->swis_code || $bank_account->iban_number || $bank_account->account_number
        );

        // Step 4: Identity Verification
        $identity_verification = IdentityVerification::where('user_id', $user->id)->first();
        $step4Complete = $identity_verification && $identity_verification->status === 1;

        return view('frontend.user.freelancer.account.account-setup', compact(
            'user_introduction',
            'experiences',
            'educations',
            'categories',
            'more_categories',
            'step1Complete',
            'step2Complete',
            'step3Complete',
            'step4Complete',
            'userSubcategories',
            'categoriesWithSkills',
            'skillsArray'
        ));
    }

    // add introduction
    public function add_introduction(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'description'=>'required',
        ]);

        if($request->ajax()){
            $user_id = Auth::user()->id;
            UserIntroduction::updateOrCreate(['user_id'=>$user_id],
                [
                'user_id'=>$user_id,
                'title'=>$request->title,
                'description'=>$request->description,
            ]);
            return response()->json([
                'status'=>'ok',
            ]);
        }
    }

  // add experience
    public function add_experience(Request $request)
    {
        $request->validate([
            'experience_title'=>'required',
            'short_description'=>'required',
            'organization'=>'required',
            'address'=>'required',
            'start_date'=>'required',
        ]);
        if($request->ajax()){
            $user_id = Auth::user()->id;
            UserExperience::create(
                [
                    'user_id'=>$user_id,
                    'title'=>$request->experience_title,
                    'short_description'=>$request->short_description,
                    'organization'=>$request->organization,
                    'address'=>$request->address,
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
                    'start_date'=>$request->start_date,
                    'end_date'=>$request->end_date ?? null,
                ]);
            return response()->json([
                'status'=>'ok',
            ]);
        }
    }

    // edit experience
    public function update_experience(Request $request)
    {
        $request->validate([
            'experience_title'=>'required',
            'short_description'=>'required',
            'organization'=>'required',
            'address'=>'required',
            'start_date'=>'required',
        ]);
        if($request->ajax()){
            $user_id = Auth::user()->id;
            UserExperience::where('id',$request->id)->update(
                [
                    'user_id'=>$user_id,
                    'title'=>$request->experience_title,
                    'short_description'=>$request->short_description,
                    'organization'=>$request->organization,
                    'address'=>$request->address,
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
                    'start_date'=>Carbon::parse($request->start_date),
                    'end_date'=> !empty($request->end_date) ? Carbon::parse($request->end_date) : null,
                ]);
            return response()->json([
                'status'=>'ok',
            ]);
        }
    }

    // add education
    public function add_education(Request $request)
    {
        $request->validate([
            'institution'=>'required',
            'degree'=>'required',
            'subject'=>'required',
            'start_date'=>'required',
        ]);
        if($request->ajax()){
            $user_id = Auth::user()->id;
            UserEducation::create(
                [
                    'user_id'=>$user_id,
                    'institution'=>$request->institution,
                    'degree'=>$request->degree,
                    'subject'=>$request->subject,
                    'start_date'=>$request->start_date,
                    'end_date'=>$request->end_date ?? null,
                ]);
            return response()->json([
                'status'=>'ok',
            ]);
        }
    }

    // edit education
    public function update_education(Request $request)
    {
        $request->validate([
            'institution'=>'required',
            'subject'=>'required',
            'degree'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
        ]);
        if($request->ajax()){
            $user_id = Auth::user()->id;
            UserEducation::where('id',$request->id)->update(
                [
                    'user_id'=>$user_id,
                    'institution'=>$request->institution,
                    'subject'=>$request->subject,
                    'degree'=>$request->degree,
                    'start_date'=>Carbon::parse($request->start_date),
                    'end_date'=>Carbon::parse($request->end_date),
                ]);
            return response()->json([
                'status'=>'ok',
            ]);
        }
    }

    // search category
    public function search_category(Request $request)
    {
        $more_categories = Category::where('status',1)->where('category', 'LIKE', "%". strip_tags($request->string_search) ."%")->get();

        if($more_categories->count() >= 1){
            return view('frontend.user.freelancer.account.work.search-categories', compact('more_categories'))->render();
        }else{
            return response()->json([
                'status'=>__('nothing')
            ]);
        }
    }
    public function add_work(Request $request)
    {
        $request->validate([
            'subcategories' => 'required|string'
        ]);

        $user_id = auth()->id();

        // JSON decode (expected format: {"2":[12,13],"3":[21]})
        $data = json_decode($request->subcategories, true);

        if (!$data || !is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please choose at least one category & subcategory !'
            ], 400);
        }

        foreach ($data as $catId => $subcatIds) {
            if (!is_array($subcatIds)) continue;

            foreach ($subcatIds as $subcat_id) {
                $subcategory = SubCategory::find($subcat_id);

                if ($subcategory) {
                    // check agar already exist na ho
                    $exists = UserWorkSubcategory::where('user_id', $user_id)
                        ->where('sub_category_id', $subcategory->id)
                        ->exists();

                    if (!$exists) {
                        UserWorkSubcategory::create([
                            'user_id'        => $user_id,
                            'category_id'    => $subcategory->category_id,
                            'sub_category_id'=> $subcategory->id,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Work updated successfully'
        ]);
    }





    // add work
  public function add_skill(Request $request)
{
    $request->validate([
        'skill' => 'required|max:1000',
    ]);

    if ($request->ajax()) {
        $user_id = Auth::user()->id;

        // ✅ Extra spaces hatao
        $skills = array_map('trim', explode(',', $request->skill));
        $skills = implode(',', $skills); // dobara string banao clean version

        UserSkill::updateOrCreate(
            ['user_id' => $user_id],
            [
                'user_id' => $user_id,
                'skill'   => $skills,
            ]
        );

        return response()->json([
            'status' => 'ok',
        ]);
    }
}


    // add hourly rate
       public function add_hourly_rate(Request $request)
        {
            if (!$request->ajax()) {
                return response()->json(['status' => 'fail', 'message' => 'Invalid request'], 403);
            }

            $request->validate([
                'hourly_rate' => 'required|numeric|min:1',
            ]);

            $user_id = Auth::id();
            User::where('id', $user_id)->update([
                'hourly_rate' => $request->hourly_rate,
            ]);

            return response()->json(['status' => 'ok']);
        }

    // upload profile photo
    public function upload_profile_photo(Request $request)
    {
        $user_id = Auth::guard('web')->user()->id;
        $user_image = User::where('id',$user_id)->first();
        $delete_old_img =  'assets/uploads/profile/'.$user_image->image;

        $upload_folder = 'profile';
        $storage_driver = Storage::getDefaultDriver();

        if ($image = $request->file('profile_image')) {
            if(file_exists($delete_old_img)){
                File::delete($delete_old_img);
            }
            $imageName = time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $resize_full_image = Image::make($request->profile_image)
                ->resize(80, 80);

            if (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])) {
                if(!empty($user_image)) {
                    // Get the current image path from the database
                    $currentImagePath = $user_image->image;
                    // Delete the old image if it exists
                    if ($currentImagePath) {
                        delete_frontend_cloud_image_if_module_exists('profile/' . $currentImagePath);
                    }
                }
                add_frontend_cloud_image_if_module_exists($upload_folder, $image, $imageName,'public');
            }else{
                $resize_full_image->save('assets/uploads/profile' .'/'. $imageName);
            }


        }else{
            $imageName = $user_image->image;
        }

        User::where('id',$user_id)->update([
            'image'=>$imageName,
            'load_from' => in_array($storage_driver,['CustomUploader']) ? 0 : 1, //added for cloud storage 0=local 1=cloud
        ]);

        return response()->json([
            'status'=>'uploaded',
        ]);
    }

    //congrats
    public function congrats(){
        return view('frontend.user.freelancer.account.congrats');
    }

}
