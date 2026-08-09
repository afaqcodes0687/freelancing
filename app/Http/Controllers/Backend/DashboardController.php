<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Wallet\Entities\WalletHistory;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $total_job = JobPost::whereHas('job_creator')->count();
        $total_client = User::where('user_type',1)->count();
        $total_freelancer = User::where('user_type',2)->count();
        $total_revenue_orders = Order::where('status',3)->sum('commission_amount');
        $total_revenue_games = WalletHistory::where(function($q){
                $q->where('payment_gateway','game')
                  ->orWhere('payment_gateway','paypal')
                  ->orWhere('payment_gateway','stripe');
            })
            ->where('transaction_id','like','game\_%')
            ->where('payment_status','complete')
            ->sum('amount');
        $total_revenue = $total_revenue_orders + $total_revenue_games;

        $orders = Order::whereHas('user')->whereHas('freelancer')->latest()->take(10)->get();

        for($i=11; $i>=0;$i--){
            $month_list[] = Carbon::now()->subMonth($i)->format('M');
            $orders_month = Order::where('status',3)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonth($i))
                ->sum('commission_amount');

            $games_month = WalletHistory::where(function($q){
                    $q->where('payment_gateway','game')
                      ->orWhere('payment_gateway','paypal')
                      ->orWhere('payment_gateway','stripe');
                })
                ->where('transaction_id','like','game\_%')
                ->where('payment_status','complete')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonth($i))
                ->sum('amount');

            $monthly_income[] = $orders_month + $games_month;

        }
        return view('backend.pages.dashboard.dashboard',compact([
            'total_job',
            'total_client',
            'total_freelancer',
            'total_revenue',
            'orders',
            'month_list',
            'monthly_income',
        ]));
    }
}
