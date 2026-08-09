<?php

namespace Modules\Service\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Service\Entities\ChildCategory;

class ChildCategoryController extends Controller
{
    // add child category
    public function all_child_category(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'name'=> 'required|regex:/\D+/|unique:child_categories|max:191',
                'short_description'=> 'nullable|max:191',
                'slug' => 'nullable|unique:child_categories|max:191',
                'meta_title' => 'nullable|max:250',
                'meta_description' => 'nullable|max:300',
            ]);

            $slug = !empty($request->slug) ? $request->slug : $request->name;
            ChildCategory::create([
                'name' => $request->name,
                'short_description' => $request->short_description,
                'slug' => Str::slug(purify_html($slug),'-',null),
                'sub_category_id' => $request->sub_category,
                'status' => $request->status,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'image' => $request->image,
            ]);
            toastr_success(__('New Child Category Successfully Added'));
        }
        $all_child_categories = ChildCategory::with('sub_category.category')->latest()->paginate(10);
        return view('service::child-category.all-child-category',compact('all_child_categories'));
    }

    // edit child category
    public function edit_child_category(Request $request)
    {
        $request->validate([
            'edit_name'=> 'required|max:191|regex:/\D+/|unique:child_categories,name,'.$request->edit_child_category_id,
            'edit_short_description'=> 'nullable|max:191',
            'edit_slug'=> 'required|max:191|unique:child_categories,slug,'.$request->edit_child_category_id,
            'edit_meta_title' => 'nullable|max:250',
            'edit_meta_description' => 'nullable|max:300',
        ]);

        $slug = !empty($request->edit_slug) ? $request->edit_slug : $request->edit_name;
        ChildCategory::where('id',$request->edit_child_category_id)->update([
            'name'=>$request->edit_name,
            'short_description'=>$request->edit_short_description,
            'slug' => Str::slug(purify_html($slug),'-',null),
            'sub_category_id'=>$request->edit_sub_category,
            'meta_title' => $request->edit_meta_title,
            'meta_description' => $request->edit_meta_description,
            'image' => $request->image,
        ]);
        return redirect()->back()->with(toastr_success(__('Child Category Successfully Updated')));
    }

    // change status
    public function change_status($id)
    {
        $child_category = ChildCategory::select('status')->where('id',$id)->first();
        $child_category->status==1 ? $status=0 : $status=1;
        ChildCategory::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(toastr_success(__('Status Successfully Changed')));
    }

    // delete child category
    public function delete_child_category($id)
    {
        $child_category = ChildCategory::find($id);
        $job_count = $child_category->jobs?->count();
        $project_count = $child_category->projects?->count();
        $skill_count = $child_category->skills?->count();
        return $this->filter_and_delete_child_category($child_category,$job_count,$project_count,$skill_count);
    }

    // bulk action child category
    public function bulk_action_child_category(Request $request){
        foreach($request->ids as $child_category_id){
            $child_category = ChildCategory::find($child_category_id);
            $job_count = $child_category->jobs?->count();
            $project_count = $child_category->projects?->count();
            $skill_count = $child_category->skills?->count();
            $this->filter_and_delete_child_category($child_category,$job_count,$project_count,$skill_count);
        }
        return redirect()->back()->with(toastr_error(__('Selected Child Category Successfully Deleted')));
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_child_categories = ChildCategory::latest()->paginate(10);
            return view('service::child-category.search-result', compact('all_child_categories'))->render();
        }
    }

    public function search_child_category(Request $request)
    {
        $all_child_categories = ChildCategory::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%") ->paginate(10);
        return $all_child_categories->total() >= 1 ? view('service::child-category.search-result', compact('all_child_categories'))->render() : response()->json(['status'=>__('nothing')]);

    }

    private function filter_and_delete_child_category($child_category,$job_count,$project_count,$skill_count)
    {
        if($job_count > 0){
            return back()->with(toastr_error(__('Child Category is not deletable because it is related to jobs')));
        }elseif($project_count > 0){
            return back()->with(toastr_error(__('Child Category is not deletable because it is related to projects')));
        }elseif($skill_count > 0){
            return back()->with(toastr_error(__('Child Category is not deletable because it is related to skills')));
        }else{
            $child_category->delete();
            return redirect()->back()->with(toastr_error(__('Child Category Successfully Deleted')));
        }
    }
}
