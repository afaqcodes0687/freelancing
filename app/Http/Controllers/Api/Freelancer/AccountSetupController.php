<?php

namespace App\Http\Controllers\Api\Freelancer;

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
use Modules\Service\Entities\Category;
use Modules\Service\Entities\SubCategory;

class AccountSetupController extends Controller
{
    // Get introduction
    public function get_introduction()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $introduction = UserIntroduction::where('user_id', $user->id)->first();
        
        return response()->json([
            'status' => 'success',
            'data' => $introduction ?? null
        ]);
    }

    // Add/Update introduction
    public function add_introduction(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'github_link' => 'nullable|url',
            'stackoverflow_link' => 'nullable|url',
        ]);

        $user_id = auth('sanctum')->user()->id;
        
        $introduction = UserIntroduction::updateOrCreate(
            ['user_id' => $user_id],
            [
                'title' => $request->title,
                'description' => $request->description,
                'github_link' => $request->github_link,
                'stackoverflow_link' => $request->stackoverflow_link,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Introduction updated successfully',
            'data' => $introduction
        ]);
    }

    // Get all experiences
    public function get_experiences()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $experiences = UserExperience::where('user_id', $user->id)
            ->with(['country:id,country', 'state:id,state'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $experiences
        ]);
    }

    // Add experience
    public function add_experience(Request $request)
    {
        $request->validate([
            'experience_title' => 'required|max:255',
            'short_description' => 'required',
            'organization' => 'required|max:255',
            'address' => 'required|max:500',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $user_id = auth('sanctum')->user()->id;
        
        $experience = UserExperience::create([
            'user_id' => $user_id,
            'title' => $request->experience_title,
            'short_description' => $request->short_description,
            'organization' => $request->organization,
            'address' => $request->address,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Experience added successfully',
            'data' => $experience
        ]);
    }

    // Update experience
    public function update_experience(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:user_experiences,id',
            'experience_title' => 'required|max:255',
            'short_description' => 'required',
            'organization' => 'required|max:255',
            'address' => 'required|max:500',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $user_id = auth('sanctum')->user()->id;
        
        $experience = UserExperience::where('id', $request->id)
            ->where('user_id', $user_id)
            ->first();

        if (!$experience) {
            return response()->json([
                'status' => 'error',
                'message' => 'Experience not found'
            ], 404);
        }

        $experience->update([
            'title' => $request->experience_title,
            'short_description' => $request->short_description,
            'organization' => $request->organization,
            'address' => $request->address,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Experience updated successfully',
            'data' => $experience
        ]);
    }

    // Delete experience
    public function delete_experience($id)
    {
        $user_id = auth('sanctum')->user()->id;
        
        $experience = UserExperience::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$experience) {
            return response()->json([
                'status' => 'error',
                'message' => 'Experience not found'
            ], 404);
        }

        $experience->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Experience deleted successfully'
        ]);
    }

    // Get all educations
    public function get_educations()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $educations = UserEducation::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $educations
        ]);
    }

    // Add education
    public function add_education(Request $request)
    {
        $request->validate([
            'institution' => 'required|max:255',
            'degree' => 'required|max:255',
            'subject' => 'required|max:255',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $user_id = auth('sanctum')->user()->id;
        
        $education = UserEducation::create([
            'user_id' => $user_id,
            'institution' => $request->institution,
            'degree' => $request->degree,
            'subject' => $request->subject,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Education added successfully',
            'data' => $education
        ]);
    }

    // Update education
    public function update_education(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:user_educations,id',
            'institution' => 'required|max:255',
            'degree' => 'required|max:255',
            'subject' => 'required|max:255',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $user_id = auth('sanctum')->user()->id;
        
        $education = UserEducation::where('id', $request->id)
            ->where('user_id', $user_id)
            ->first();

        if (!$education) {
            return response()->json([
                'status' => 'error',
                'message' => 'Education not found'
            ], 404);
        }

        $education->update([
            'institution' => $request->institution,
            'degree' => $request->degree,
            'subject' => $request->subject,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Education updated successfully',
            'data' => $education
        ]);
    }

    // Delete education
    public function delete_education($id)
    {
        $user_id = auth('sanctum')->user()->id;
        
        $education = UserEducation::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$education) {
            return response()->json([
                'status' => 'error',
                'message' => 'Education not found'
            ], 404);
        }

        $education->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Education deleted successfully'
        ]);
    }

    // Get user selected categories and subcategories
    public function get_selected_categories()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }
        
        $userCategories = \DB::table('user_work_subcategories')
            ->where('user_id', $user->id)
            ->join('categories', 'categories.id', '=', 'user_work_subcategories.category_id')
            ->join('sub_categories', 'sub_categories.id', '=', 'user_work_subcategories.sub_category_id')
            ->select(
                'categories.id as category_id',
                'categories.category as category_name',
                'sub_categories.id as subcategory_id',
                'sub_categories.sub_category as subcategory_name'
            )
            ->get()
            ->groupBy('category_id');

        $result = [];
        foreach ($userCategories as $categoryId => $subcategories) {
            $result[] = [
                'category_id' => $categoryId,
                'category_name' => $subcategories->first()->category_name,
                'subcategories' => $subcategories->map(function($item) {
                    return [
                        'subcategory_id' => $item->subcategory_id,
                        'subcategory_name' => $item->subcategory_name,
                        'selected' => true
                    ];
                })->toArray()
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    // Add/Update work categories and subcategories
    public function add_work(Request $request)
    {
        $request->validate([
            'subcategories' => 'required|json',
        ]);

        $user_id = auth('sanctum')->user()->id;
        $data = json_decode($request->subcategories, true);

        if (!$data || !is_array($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please choose at least one category & subcategory!'
            ], 400);
        }

        // Delete existing selections
        UserWorkSubcategory::where('user_id', $user_id)->delete();

        // Add new selections
        foreach ($data as $catId => $subcatIds) {
            if (!is_array($subcatIds)) continue;

            foreach ($subcatIds as $subcat_id) {
                $subcategory = SubCategory::find($subcat_id);

                if ($subcategory) {
                    UserWorkSubcategory::create([
                        'user_id' => $user_id,
                        'category_id' => $subcategory->category_id,
                        'sub_category_id' => $subcategory->id,
                    ]);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Work categories updated successfully'
        ]);
    }

    // Get user skills
    public function get_skills()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $skills = UserSkill::where('user_id', $user->id)->first();
        
        return response()->json([
            'status' => 'success',
            'data' => $skills ? $skills->skill : ''
        ]);
    }

    // Add/Update skills
    public function add_skill(Request $request)
    {
        $request->validate([
            'skill' => 'required|max:1000',
        ]);

        $user_id = auth('sanctum')->user()->id;
        
        // Clean and format skills
        $skills = array_map('trim', explode(',', $request->skill));
        $skills = implode(',', $skills);

        $userSkill = UserSkill::updateOrCreate(
            ['user_id' => $user_id],
            [
                'user_id' => $user_id,
                'skill' => $skills,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Skills updated successfully',
            'data' => $userSkill
        ]);
    }

    // Get skills by subcategory
    public function get_skills_by_subcategory($subcategory_id)
    {
        $skills = Skill::where('sub_category_id', $subcategory_id)
            ->where('status', 1)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $skills
        ]);
    }

    // Get current hourly rate
    public function get_hourly_rate()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'hourly_rate' => $user->hourly_rate
            ]
        ]);
    }

    // Update hourly rate
    public function update_hourly_rate(Request $request)
    {
        $request->validate([
            'hourly_rate' => 'required|numeric|min:1',
        ]);

        $user_id = auth('sanctum')->user()->id;
        
        User::where('id', $user_id)->update([
            'hourly_rate' => $request->hourly_rate,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Hourly rate updated successfully',
            'data' => [
                'hourly_rate' => $request->hourly_rate
            ]
        ]);
    }

    // Get setup progress status
    public function get_setup_status()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        // Step 1: Basic info
        $step1Complete = $user->first_name && $user->last_name && $user->country_id && $user->experience_level;

        // Step 2: Detailed profile
        $user_introduction = UserIntroduction::where('user_id', $user->id)->first();
        $experiences = UserExperience::where('user_id', $user->id)->get();
        $educations = UserEducation::where('user_id', $user->id)->get();
        $userSubcategories = UserWorkSubcategory::where('user_id', $user->id)->get();
        $userSkills = UserSkill::where('user_id', $user->id)->first();

        $step2Complete = $user_introduction && 
                         $experiences->count() > 0 && 
                         $educations->count() > 0 && 
                         $userSubcategories->count() > 0 && 
                         $userSkills && 
                         $user->hourly_rate;

        $missing_fields = [];
        if (!$user_introduction) $missing_fields[] = 'Introduction';
        if ($experiences->count() == 0) $missing_fields[] = 'Experience';
        if ($educations->count() == 0) $missing_fields[] = 'Education';
        if ($userSubcategories->count() == 0) $missing_fields[] = 'Categories';
        if (!$userSkills) $missing_fields[] = 'Skills';
        if (!$user->hourly_rate) $missing_fields[] = 'Hourly Rate';

        $overall_completion = 0;
        if ($step1Complete) $overall_completion += 25;
        if ($step2Complete) $overall_completion += 75;

        return response()->json([
            'status' => 'success',
            'data' => [
                'step1_complete' => (bool)$step1Complete,
                'step2_complete' => (bool)$step2Complete,
                'overall_completion' => $overall_completion,
                'missing_fields' => $missing_fields
            ]
        ]);
    }
}
