<?php

namespace App\Http\Controllers\Frontend\Client;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\Order;
use App\Models\User;
use App\Models\Skill;
use App\Models\Ad;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Wallet\Entities\Wallet;
use Illuminate\Support\Facades\Mail;
use App\Models\Invitation;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user_id = Auth::guard('web')->user()->id;
        $wallet_balance = Wallet::where('user_id', $user_id)->first();
        $total_wallet_balance = $wallet_balance->balance ?? 0;
        $total_jobs = JobPost::where('user_id', $user_id)->count();
        $complete_order = Order::where('status', 3)->whereHas('freelancer')->where('user_id', $user_id)->count();
        $active_order = Order::where('status', 1)->whereHas('freelancer')->where('user_id', $user_id)->count();

        if (get_static_option('project_enable_disable') != 'disable') {
            $latest_orders = Order::whereHas('freelancer')
                ->where(function ($query) use ($user_id) {
                    $query->where(function ($query) {
                        $query->where('payment_status', 'complete');
                    })->orWhere(function ($query) {
                        $query->where("payment_gateway", "manual_payment")
                            ->whereIn("payment_status", ["pending", "complete"]);
                    });
                })
                ->where('user_id', $user_id)
                ->latest()
                ->take(5)
                ->get();
        } else {
            $latest_orders = Order::where('user_id', $user_id)
                ->where('is_project_job', '!=', 'project')
                ->where(function ($query) {
                    $query->whereHas('freelancer', function ($q) {
                        $q->where('payment_status', 'complete');
                    })
                        ->orWhere(function ($query) {
                            $query->where('payment_gateway', 'manual_payment')
                                ->whereIn('payment_status', ['pending', 'complete']);
                        });
                })
                ->latest()
                ->take(5)
                ->get();
        }

        // ---- Client Jobs ----
        $my_jobs = JobPost::with('job_skills')
            ->where('user_id', $user_id)
            ->whereDoesntHave('orders', function ($q) {
                $q->where('status', 3);
            })
            ->latest()
            ->take(5)
            ->get();

        $talents = collect(); // default empty

        if ($total_jobs > 0) {

            $jobSkillIds = \DB::table('job_post_skills')
                ->whereIn('job_post_id', $my_jobs->pluck('id'))
                ->pluck('skill_id')
                ->unique();

            if ($jobSkillIds->count()) {

                $jobSkillNames = Skill::whereIn('id', $jobSkillIds)->pluck('skill')->toArray();

                $subQuery = User::select(
                    'users.id',
                    'username',
                    'first_name',
                    'last_name',
                    'image',
                    'hourly_rate',
                    'country_id',
                    'state_id',
                    'user_verified_status',
                    'load_from',
                    DB::raw('AVG(ratings.rating) as average_rating'),
                    DB::raw('COUNT(DISTINCT CASE WHEN orders.status = 3 THEN orders.id END) as completed_jobs')
                )
                    ->leftJoin('orders', 'users.id', '=', 'orders.freelancer_id')
                    ->leftJoin('ratings', 'orders.id', '=', 'ratings.order_id')
                    ->where('user_type', 2)
                    ->where('is_email_verified', 1)
                    ->where('is_suspend', 0)

                    ->whereHas('freelancer_skill', function ($q) use ($jobSkillNames) {
                        $q->where(function ($q2) use ($jobSkillNames) {
                            foreach ($jobSkillNames as $skillName) {
                                $q2->orWhere('skill', 'like', '%' . $skillName . '%');
                            }
                        });
                    })
                    ->groupBy(
                        'users.id',
                        'username',
                        'first_name',
                        'last_name',
                        'image',
                        'hourly_rate',
                        'country_id',
                        'state_id',
                        'user_verified_status',
                        'load_from'
                    );

                $rawTalents = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
                    ->mergeBindings($subQuery->getQuery())
                    ->orderByDesc(DB::raw('average_rating >= 4.5'))
                    ->orderByDesc('id')
                    ->paginate(15);

                $talents = User::hydrate($rawTalents->items());

                $talents = new \Illuminate\Pagination\LengthAwarePaginator(
                    $talents,
                    $rawTalents->total(),
                    $rawTalents->perPage(),
                    $rawTalents->currentPage(),
                    ['path' => request()->url(), 'query' => request()->query()]
                );

                $talents->load([
                    'user_introduction',
                    'freelancer_category',
                    'freelancer_ratings',
                    'freelancer_skill',
                    'promotionalProfiles',
                    'freelancer_orders' => function ($query) use ($user_id) {
                        $query->where('user_id', $user_id)->where('status', 3);
                    }
                ]);
            }
        }

        $ads = Ad::active()->inRandomOrder()->limit(2)->get();
        $ads = ['sidebar' => $ads];
        $clientName = Auth::user()->first_name . ' ' . Auth::user()->last_name;

        return view(
            'frontend.user.client.dashboard.dashboard',
            compact(['total_wallet_balance', 'total_jobs', 'complete_order', 'active_order', 'latest_orders', 'my_jobs', 'talents', 'ads', 'clientName'])
        );
    }



    public function sendInvitation(Request $request)
    {
        $request->validate([
            'freelancer_id' => 'required|exists:users,id',
            'job_id' => 'required|exists:job_posts,id',
            'message' => 'nullable|string|max:1000',
        ]);

        $clientId = Auth::id();
        $freelancer = User::findOrFail($request->freelancer_id);
        $job = JobPost::where('user_id', $clientId)->findOrFail($request->job_id);

        try {
            // ✅ Job Link Banaya (Route ke hisaab se)
            $jobUrl = route('job.details', [
                'username' => $job->job_creator?->username,
                'slug' => $job->slug
            ]);

            // ✅ Email Message with Clickable Link
            $inviteMessage = "
                Hi {$freelancer->first_name} {$freelancer->last_name},<br><br>
                A client invited you to their job: 
                <a href='{$jobUrl}' style='color:#309400; text-decoration:underline; font-weight:bold;'>{$job->title}</a>.<br><br>
                Message:<br>{$request->message}
            ";

            // ✅ Send Email
            Mail::to($freelancer->email)->send(new \App\Mail\BasicMail([
                'subject' => 'Job Invitation - ' . $job->title,
                'message' => $inviteMessage,
            ]));

            // ✅ Save Invitation in DB
            Invitation::create([
                'client_id' => $clientId,
                'freelancer_id' => $freelancer->id,
                'job_id' => $job->id,
                'message' => $request->message,
            ]);

            toastr_success(__('Invitation sent successfully!'));
            return back();

        } catch (\Exception $e) {
            toastr_error(__('Failed to send invitation. Please try again.'));
            return back();
        }
    }

}
