<?php

namespace Modules\Chat\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order_id' => 'nullable|exists:orders,id'
        ]);

        // Create report to admin
        $report = Report::create([
            'client_id' => Auth::guard('sanctum')->id(), // Reporter as client_id for admin reference
            'freelancer_id' => Auth::guard('sanctum')->id(), // Same user
            'reporter' => 'client',
            'title' => $request->title,
            'description' => $request->description,
            'order_id' => $request->order_id,
            'status' => 0 // 0 = in review
        ]);

        return response()->json([
            'msg' => __('Report submitted to admin successfully'),
            'report_id' => $report->id
        ]);
    }

    public function index()
    {
        $reports = Report::where('client_id', Auth::guard('sanctum')->id())
            ->where('reporter', 'client')
            ->where('freelancer_id', Auth::guard('sanctum')->id()) // Same user
            ->with(['order:id,order_number'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'reports' => $reports
        ]);
    }

    public function show($id)
    {
        $report = Report::where('id', $id)
            ->where('client_id', Auth::guard('sanctum')->id())
            ->where('reporter', 'client')
            ->where('freelancer_id', Auth::guard('sanctum')->id()) // Same user
            ->with(['order:id,order_number'])
            ->first();

        if (!$report) {
            return response()->json([
                'msg' => __('Report not found')
            ])->setStatusCode(404);
        }

        return response()->json([
            'report' => $report
        ]);
    }
}
