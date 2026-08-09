<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Modules\PromoteFreelancer\Entities\PromotionProjectList;
use Modules\Service\Entities\SubCategory;

class SubcategoryProjectController extends Controller
{
    public function __construct()
    {
        $this->current_date = \Carbon\Carbon::now()->toDateTimeString();
    }

    public function sub_category_projects($category_slug, $slug)
    {
        $is_pro = 0;
        $subcategory = SubCategory::with('category','child_categories')->where('slug',$slug)->firstOrFail();
        
        $faqs = \Modules\Faq\Entities\QuestionAnswer::where('sub_category_id', $subcategory->id)
            ->orWhere(function($q) use ($subcategory) {
                $q->where('category_id', $subcategory->category_id)->whereNull('sub_category_id');
            })->take(12)->get();

        $projects = $subcategory->projects()
            ->with('project_creator')
            ->whereHas('project_creator')
            ->where('project_on_off','1')
            ->where('status','1')
            ->latest()
            ->paginate(10);

        return view('frontend.pages.subcategory-projects.landing',compact('subcategory','projects','is_pro', 'faqs'));
    }

    public function child_category_projects($category_slug, $sub_slug, $child_slug)
    {
        $is_pro = 0;
        $subcategory = SubCategory::with('category','child_categories')->where('slug',$sub_slug)->firstOrFail();
        
        $child_category = \Modules\Service\Entities\ChildCategory::where('slug', $child_slug)
            ->where('sub_category_id', $subcategory->id)
            ->firstOrFail();

        $faqs = \Modules\Faq\Entities\QuestionAnswer::where('child_category_id', $child_category->id)
            ->orWhere(function($q) use ($subcategory) {
                $q->where('sub_category_id', $subcategory->id)->whereNull('child_category_id');
            })->take(12)->get();

        $projects = $child_category->projects()
            ->with('project_creator')
            ->whereHas('project_creator')
            ->where('project_on_off','1')
            ->where('status','1')
            ->latest()
            ->paginate(10);

        return view('frontend.pages.childcategory-projects.landing',compact('subcategory','projects','is_pro', 'child_category', 'faqs'));
    }

    public function legacy_subcategory_redirect(Request $request, $slug)
    {
        $subcategory = SubCategory::with('category')->where('slug', $slug)->firstOrFail();
        $category_slug = $subcategory->category->slug ?? 'category';
        
        if ($request->has('child')) {
            return redirect()->to(route('child_category.landing', ['category_slug' => $category_slug, 'sub_slug' => $slug, 'child_slug' => $request->child]), 301);
        }
        
        return redirect()->to(route('subcategory.landing', ['category_slug' => $category_slug, 'sub_slug' => $slug]), 301);
    }

    public function legacy_child_redirect($sub_slug, $child_slug)
    {
        $subcategory = SubCategory::with('category')->where('slug', $sub_slug)->firstOrFail();
        $category_slug = $subcategory->category->slug ?? 'category';
        
        return redirect()->to(route('child_category.landing', ['category_slug' => $category_slug, 'sub_slug' => $sub_slug, 'child_slug' => $child_slug]), 301);
    }

    public function sub_category_project_filter(Request $request)
    {
        if($request->ajax()){
            $subcategory = SubCategory::select('id','sub_category')->where('id',$request->subcategory_id)->first();
            
            if (!$subcategory) {
                return response()->json(['status'=>__('nothing')]);
            }
            
            if (!empty($request->child_category_id)) {
                $child_category = \Modules\Service\Entities\ChildCategory::find($request->child_category_id);
                $projects = $child_category ? $child_category->projects() : $subcategory->projects();
            } else {
                $projects = $subcategory->projects();
            }

            $projects = $projects->with('project_creator')
                ->whereHas('project_creator')
                ->where('project_on_off','1')
                ->latest()
                ->where('status','1');

            if(!empty($request->country)){
                $projects = $projects->WhereHas('project_creator',function($q) use($request){
                    $q->where('country_id',$request->country);
                });
            }

            if(!empty($request->level)){
                $projects = $projects->WhereHas('project_creator',function($q) use($request){
                    $q->where('experience_level',$request->level);
                });
            }

            if(!empty($request->min_price) && !empty($request->max_price)){
                $projects = $projects->whereBetween('basic_regular_charge',[$request->min_price,$request->max_price]);
            }

            if(!empty($request->delivery_day)){
                $projects = $projects->where('basic_delivery',$request->delivery_day);
            }

            if(!empty($request->rating)){
                $projects = $projects->withAvg(['ratings' => function ($query){
                    $query->where('sender_id', 1);
                }],'rating')
                    ->having('ratings_avg_rating',">", $request->rating -1)
                    ->having('ratings_avg_rating',"<=", $request->rating);
            }

            $projects = $projects->paginate(10);
            return $projects->total() >= 1 ? view('frontend.pages.subcategory-projects.search-subcategory-result', compact('projects'))->render() : response()->json(['status'=>__('nothing')]);
        }
    }

    public function pagination(Request $request)
    {
        if($request->ajax()){
            $is_pro = $request->get_pro_projects ?? 0;
            $subcategory = SubCategory::select('id','sub_category')->where('id',$request->subcategory_id)->first();

            if (!$subcategory) {
                return response()->json(['status'=>__('nothing')]);
            }

            if (!empty($request->child_category_id)) {
                $child_category = \Modules\Service\Entities\ChildCategory::find($request->child_category_id);
                $baseQuery = $child_category ? $child_category->projects() : $subcategory->projects();
            } else {
                $baseQuery = $subcategory->projects();
            }

            if($request->get_pro_projects == 1){
                $projects = $baseQuery
                    ->with('project_creator')
                    ->whereHas('project_creator')
                    ->where('project_on_off','1')
                    ->where('status','1')
                    ->where('pro_expire_date','>',$this->current_date)
                    ->where('is_pro','yes')
                    ->inRandomOrder();
            }else{
                $projects = $baseQuery
                    ->with('project_creator')
                    ->whereHas('project_creator')
                    ->where('project_on_off','1')
                    ->where('status','1')
                    ->latest();
            }

            if (isset($request->country) && !empty($request->country)) {
                $projects = $projects->WhereHas('project_creator', function ($q) use ($request) {
                    $q->where('country_id', $request->country);
                });
            }

            if (isset($request->level) && !empty($request->level)) {
                $projects = $projects->WhereHas('project_creator', function ($q) use ($request) {
                    $q->where('experience_level', $request->level);
                });
            }

            if (isset($request->min_price) && isset($request->max_price) && !empty($request->min_price) && !empty($request->max_price)) {
                $projects = $projects->whereBetween('basic_regular_charge', [$request->min_price, $request->max_price]);
            }

            if (isset($request->delivery_day) && !empty($request->delivery_day)) {
                $projects = $projects->where('basic_delivery', $request->delivery_day);
            }

            if(!empty($request->rating)){
                $projects = $projects->withAvg(['ratings' => function ($query){
                    $query->where('sender_type', 1);
                }],'rating')
                    ->having('ratings_avg_rating',">", $request->rating -1)
                    ->having('ratings_avg_rating',"<=", $request->rating);
            }

            $projects = $projects->paginate(10);

            //pro project impression count
            if(moduleExists('PromoteFreelancer')){
                if($projects->total() >=1 && $is_pro == 1) {
                    foreach ($projects as $project) {
                        $find_package = PromotionProjectList::where('identity',$project->id)
                            ->where('type','project')
                            ->where('expire_date','>=',$this->current_date)
                            ->first();
                        if($find_package){
                            PromotionProjectList::where('id',$find_package->id)->update(['impression'=>$find_package->impression + 1]);
                        }
                    }
                }
            }
            return $projects->total() >= 1 ? view('frontend.pages.subcategory-projects.search-subcategory-result', compact('projects','is_pro'))->render() : response()->json(['status'=>__('nothing')]);
        }
    }

    //reset jobs filter
    public function reset(Request $request)
    {
        $subcategory = SubCategory::select('id','sub_category')->where('id',$request->subcategory_id)->first();
        
        if (!$subcategory) {
            return response()->json(['status'=>__('nothing')]);
        }
        
        $projects = $subcategory->projects()
            ->with('project_creator')
            ->whereHas('project_creator')
            ->where('project_on_off','1')
            ->where('status','1')
            ->latest()
            ->paginate(10);
        return $projects->total() >= 1 ? view('frontend.pages.subcategory-projects.search-subcategory-result',compact('projects'))->render() : response()->json(['status'=>__('nothing')]);
    }
}
