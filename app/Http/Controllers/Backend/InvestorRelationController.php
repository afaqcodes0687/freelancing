<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvestorRelation;

class InvestorRelationController extends Controller
{
    public function edit()
    {
        $policy = InvestorRelation::first();

        if (!$policy) {
            $policy = InvestorRelation::create([
                'title' => 'Investor Relations',
                'meta_title' => 'Investor Relation - Financial Insights and Business Strategy | Right Freelancer',
                'meta_description' => 'Explore investor relations at Right Freelancer. Get financial reports, company strategy, and investment opportunities all in one place.',
                'content' => '',
            ]);
        }

        return view('backend.investor_relation.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
        ]);

        $policy = InvestorRelation::first();

        if (!$policy) {
            $policy = InvestorRelation::create([
                'title' => $request->title,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'content' => $request->content,
            ]);
        } else {
            $policy->fill([
                'title' => $request->title,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'content' => $request->content,
            ])->save();
        }

        return back()->with(toastr_success('Investor Relations page updated successfully!'));
    }
}
