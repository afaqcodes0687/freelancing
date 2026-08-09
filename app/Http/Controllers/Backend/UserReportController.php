<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Mail\ReportMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\NewsLetter\Entities\NewsLetter;

class UserReportController extends Controller
{
    public function all_reports()
    {
        $all_reports = Report::with(['client:id,first_name,last_name', 'freelancer:id,first_name,last_name'])->latest()->paginate(10);
        return view ('backend.pages.reports.all-reports',compact('all_reports'));
    }

    public function report_update(Request $request)
    {
        $request->validate([
            'status' => 'required',
            'note' => 'required'
        ]);

        Report::where('id',$request->report_id)->update([
            'status' => $request->status,
            'note' => $request->note,
        ]);

        $report = Report::where('id',$request->report_id)->first();

        if($request->status == 0){
            $status_text = __('in review');
        }
        if($request->status == 1){
            $status_text = __('closed');
        }
        if($request->status == 2){
            $status_text = __('rejected');
        }

        if($report->reporter == 'client'){
            client_notification($request->report_id,$report->client_id,'Report', __('Your report status changed to') .' '. $status_text);
            
            // Send email to client
            $client = \App\Models\User::find($report->client_id);
            if($client && $client->email){
                Mail::to($client->email)->send(new ReportMail([
                    'subject' => __('Report Status Update'),
                    'report_id' => $report->id,
                    'title' => $report->title,
                    'status' => $status_text,
                    'note' => $request->note
                ]));
            }
        }
        if($report->reporter == 'freelancer'){
            freelancer_notification($request->report_id,$report->freelancer_id,'Report', __('Your report status changed to') .' '. $status_text);
            
            // Send email to freelancer
            $freelancer = \App\Models\User::find($report->freelancer_id);
            if($freelancer && $freelancer->email){
                Mail::to($freelancer->email)->send(new ReportMail([
                    'subject' => __('Report Status Update'),
                    'report_id' => $report->id,
                    'title' => $report->title,
                    'status' => $status_text,
                    'note' => $request->note
                ]));
            }
        }
        return back()->with(toastr_success(__('Status Successfully Updated')));
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_reports = Report::with(['client:id,first_name,last_name', 'freelancer:id,first_name,last_name'])->latest()->paginate(10);
            return view('backend.pages.reports.search-result',compact('all_reports'))->render();
        }
    }

    // delete report
    public function delete_report($id)
    {
        $report = Report::find($id);
        if($report){
            $report->delete();
            return back()->with(toastr_success(__('Report deleted successfully')));
        }
        return back()->with(toastr_error(__('Report not found')));
    }
}
