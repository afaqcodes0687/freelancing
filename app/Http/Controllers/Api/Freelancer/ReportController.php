<?php

namespace App\Http\Controllers\Api\Freelancer;

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
            'client_id' => auth('sanctum')->user()->id,
            'freelancer_id' => auth('sanctum')->user()->id,
            'reporter' => 'freelancer',
            'title' => $request->title,
            'description' => $request->description,
            'order_id' => $request->order_id,
            'status' => 0
        ]);

        return response()->json([
            'msg' => 'Report submitted to admin successfully',
            'report_id' => $report->id
        ], 200);
    }

    public function all()
    {
        $reports = Report::where('freelancer_id', auth('sanctum')->user()->id)
            ->where('reporter', 'freelancer')
            ->where('client_id', auth('sanctum')->user()->id)
            ->with('order:id,order_number')
            ->latest()
            ->paginate(20);

        return response()->json([
            'reports' => $reports
        ], 200);
    }

    public function show($id)
    {
        $report = Report::where('id', $id)
            ->where('freelancer_id', auth('sanctum')->user()->id)
            ->where('reporter', 'freelancer')
            ->where('client_id', auth('sanctum')->user()->id)
            ->with('order:id,order_number')
            ->first();

        if (!$report) {
            return response()->json([
                'msg' => 'Report not found'
            ], 404);
        }

        return response()->json([
            'report' => $report
        ], 200);
    }
}
