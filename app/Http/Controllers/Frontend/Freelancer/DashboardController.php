<?php

namespace App\Http\Controllers\Frontend\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Project;
use App\Models\JobPost;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user_id = Auth::guard('web')->user()->id;
        $wallet_balance = Wallet::where('user_id',$user_id)->first();
        $total_wallet_balance = $wallet_balance->balance ?? 0;
        $total_project = Project::where('user_id',$user_id)->count();
        $complete_order = Order::where('status',3)->whereHas('user')->where('freelancer_id',$user_id)->count();
        $active_order = Order::where('status',1)->whereHas('user')->where('freelancer_id',$user_id)->count();

        if(get_static_option('project_enable_disable') != 'disable'){
            $latest_orders = Order::where('freelancer_id',$user_id)->whereHas('user')->where('payment_status','complete')->latest()->take(5)->get();
        }else{
            $latest_orders = Order::where('freelancer_id',$user_id)->where('is_project_job', '!=', 'project')->whereHas('user')->where('payment_status','complete')->latest()->take(5)->get();
        }
        $my_projects = Project::select('id','title','slug')->where('user_id',$user_id)->latest()->take(5)->get();

        $freelancer_skill_names = Auth::user()->skills()
            ->pluck('skill') 
            ->flatMap(function($s) {
                return array_map('trim', explode(',', strtolower($s)));
            })->toArray();

        $freelancer_skill_ids = Skill::whereIn(\DB::raw('LOWER(skill)'), $freelancer_skill_names)
            ->pluck('id')
            ->toArray();

        $jobs_query = JobPost::where('status', 1)
            ->where('on_off', '1')
            ->where('job_approve_request', '1')
            ->whereHas('job_skills', function($query) use ($freelancer_skill_ids) {
                $query->whereIn('skill_id', $freelancer_skill_ids);
            })
            ->whereDoesntHave('orders', function($q) {
                $q->where('status', 3); 
            });

        if (!moduleExists('HourlyJob')) {
            $jobs_query->where('type', 'fixed');
        }

        $jobs = $jobs_query->with(['job_creator', 'job_skills', 'orders'])
            ->latest()
            ->get();

        return view('frontend.user.freelancer.dashboard.dashboard',compact(['total_wallet_balance','total_project','complete_order','active_order','latest_orders','my_projects','jobs', 'freelancer_skill_names']));
    }
}
