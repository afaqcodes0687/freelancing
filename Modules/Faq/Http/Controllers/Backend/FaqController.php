<?php

namespace Modules\Faq\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Faq\Entities\QuestionAnswer;
use Modules\Service\Entities\Category;

class FaqController extends Controller
{
    // all faq
    public function faq_all(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'question'    => 'required|max:500',
                'answer'      => 'nullable',
                'category_id' => 'nullable|exists:categories,id',
            ]);
            QuestionAnswer::create([
                'category_id' => $request->category_id ?: null,
                'question'    => purify_html($request->question),
                'answer'      => purify_html($request->answer),
            ]);
            toastr_success(__('FAQ Successfully Added'));
        }

        $all_faqs   = QuestionAnswer::with('category')->latest()->paginate(10);
        $categories = Category::select('id', 'category')->where('status', 1)->get();
        return view('faq::backend.faqs.all-faqs', compact('all_faqs', 'categories'));
    }

    // edit faq
    public function edit_faq(Request $request)
    {
        $request->validate([
            'edit_question'    => 'required|max:500',
            'edit_answer'      => 'nullable',
            'edit_category_id' => 'nullable|exists:categories,id',
        ]);

        QuestionAnswer::where('id', $request->edit_faq_id)->update([
            'category_id' => $request->edit_category_id ?: null,
            'question'    => purify_html($request->edit_question),
            'answer'      => purify_html($request->edit_answer),
        ]);
        return redirect()->back()->with(toastr_success(__('FAQ Successfully Updated')));
    }

    // delete faq
    public function delete_faq($id)
    {
        QuestionAnswer::findOrFail($id)->delete();
        return redirect()->back()->with(toastr_success(__('FAQ Successfully Deleted')));
    }

    // search faq
    public function search_faq(Request $request)
    {
        $all_faqs = QuestionAnswer::with('category')
            ->where('question', 'LIKE', '%' . strip_tags($request->string_search) . '%')
            ->latest()
            ->paginate(10);

        return $all_faqs->total() >= 1
            ? view('faq::backend.faqs.search-result', compact('all_faqs'))->render()
            : response()->json(['status' => __('nothing')]);
    }

    // pagination
    public function pagination(Request $request)
    {
        if ($request->ajax()) {
            if (empty($request->string_search)) {
                $all_faqs = QuestionAnswer::with('category')->latest()->paginate(10);
                return view('faq::backend.faqs.search-result', compact('all_faqs'))->render();
            } else {
                $all_faqs = QuestionAnswer::with('category')
                    ->where('question', 'LIKE', '%' . strip_tags($request->string_search) . '%')
                    ->latest()
                    ->paginate(10);
                return $all_faqs->total() >= 1
                    ? view('faq::backend.faqs.search-result', compact('all_faqs'))->render()
                    : response()->json(['status' => __('nothing')]);
            }
        }
    }
}
