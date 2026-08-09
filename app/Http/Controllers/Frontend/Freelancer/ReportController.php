<?php

namespace App\Http\Controllers\Frontend\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function all()
    {
        $reports = Report::where('freelancer_id',Auth::guard('web')->id())
            ->where('reporter','freelancer')
            ->where('client_id',Auth::guard('web')->id())
            ->with('order:id,order_number')
            ->latest()
            ->paginate(20);
        return view('frontend.user.freelancer.report.all', compact('reports'));
    }

    public function create()
    {
        return view('frontend.user.freelancer.report.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order_id' => 'nullable|exists:orders,id'
        ]);

        // Create report to admin
        $report = Report::create([
            'client_id' => Auth::guard('web')->id(),
            'freelancer_id' => Auth::guard('web')->id(),
            'reporter' => 'freelancer',
            'title' => $request->title,
            'description' => $request->description,
            'order_id' => $request->order_id,
            'status' => 0
        ]);

        return redirect()->route('freelancer.reports.all')->with(toastr_success(__('Report submitted to admin successfully')));
    }

    public function show($id)
    {
        $report = Report::where('id', $id)
            ->where('freelancer_id', Auth::guard('web')->id())
            ->where('reporter', 'freelancer')
            ->where('client_id', Auth::guard('web')->id())
            ->with('order:id,order_number')
            ->first();

        if (!$report) {
            return redirect()->back()->with(toastr_error(__('Report not found')));
        }

        return view('frontend.user.freelancer.report.show', compact('report'));
    }
}
