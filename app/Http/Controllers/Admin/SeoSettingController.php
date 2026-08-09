<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;

class SeoSettingController extends Controller
{
    public function index()
    {
        $pageTitle = 'SEO Settings';
        $seoSettings = SeoSetting::orderBy('id', 'desc')->paginate(20);
        return view('backend.seo.index', compact('pageTitle', 'seoSettings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_name' => 'required|string|unique:seo_settings,route_name',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        SeoSetting::create($request->all());

        return back()->with(toastr_success(__('SEO settings saved successfully.')));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'route_name' => 'required|string|unique:seo_settings,route_name,' . $id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $seo = SeoSetting::findOrFail($id);
        $seo->update($request->all());

        return back()->with(toastr_success(__('SEO settings updated successfully.')));
    }

    public function delete($id)
    {
        $seo = SeoSetting::findOrFail($id);
        $seo->delete();

        return back()->with(toastr_success(__('SEO settings deleted successfully.')));
    }
}
