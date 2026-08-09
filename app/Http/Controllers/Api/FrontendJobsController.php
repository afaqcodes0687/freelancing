<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\JobPost;
use App\Models\JobProposal;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FrontendJobsController extends Controller
{
    /**
     * Get all jobs with pagination and filters
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = JobPost::with([
            'job_creator.user_complete_orders',
            'job_skills',
            'job_category',
            'job_sub_categories'
        ])
            ->whereHas('job_creator')
            ->where('on_off', '1')
            ->withCount('job_proposals')
            ->where('status', '1')
            ->where('job_approve_request', '1')
            ->latest();

        // Apply filters
        $this->applyFilters($query, $request);

        // Get pagination limit
        $limit = $request->get('limit', 10);

        // Filter by job type if HourlyJob module doesn't exist
        if (!moduleExists('HourlyJob')) {
            $query->where('type', 'fixed');
        }

        $jobs = $query->paginate($limit);

        // Get ads if requested
        $ads = [];
        if ($request->get('include_ads', false)) {
            $adData = Ad::active()->inRandomOrder()->limit(2)->get();
            $ads = [
                'sidebar' => $adData
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'jobs' => $jobs->items(),
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
                    'from' => $jobs->firstItem(),
                    'to' => $jobs->lastItem()
                ]
            ],
            'ads' => $ads,
            'message' => __('Jobs retrieved successfully')
        ]);
    }

    /**
     * Get job details by ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $job = JobPost::with([
            'job_creator.user_complete_orders',
            'job_skills',
            'job_category',
            'job_sub_categories',
            'job_proposals.freelancer'
        ])
            ->withCount('job_proposals')
            ->whereHas('job_creator')
            ->where('on_off', '1')
            ->where('status', '1')
            ->where('job_approve_request', '1')
            ->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => __('Job not found')
            ], 404);
        }

        $completed_order = Order::with('freelancer')
            ->where('identity', $job->id)
            ->where('is_project_job', 'job') 
            ->where('status', 3) // 3 = complete
            ->latest()
            ->first();

        $is_job_completed = !is_null($completed_order);
        $is_job_hired = JobProposal::where('job_id', $job->id)->where('is_hired', 1)->exists();

        $job_status = 'open';
        if ($is_job_completed) {
            $job_status = 'complete';
        } elseif ($is_job_hired) {
            $job_status = 'in progress';
        }

        return response()->json([
            'success' => true,
            'data' => $job,
            'is_job_completed' => $is_job_completed,
            'is_job_hired' => $is_job_hired,
            'job_status' => $job_status,
            'message' => __('Job details retrieved successfully')
        ]);
    }

    /**
     * Search jobs with filters
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = JobPost::with([
            'job_creator.user_complete_orders',
            'job_skills',
            'job_category',
            'job_sub_categories'
        ])
            ->whereHas('job_creator')
            ->where('on_off', '1')
            ->where('status', '1')
            ->where('job_approve_request', '1');

        // Apply search term
        if ($request->get('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('tags', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Apply filters
        $this->applyFilters($query, $request);

        // Filter by job type if HourlyJob module doesn't exist
        if (!moduleExists('HourlyJob')) {
            $query->where('type', 'fixed');
        }

        $limit = $request->get('limit', 10);
        $jobs = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [
                'jobs' => $jobs->items(),
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
                    'from' => $jobs->firstItem(),
                    'to' => $jobs->lastItem()
                ]
            ],
            'message' => __('Jobs search completed successfully')
        ]);
    }

    /**
     * Get jobs by category
     * 
     * @param int $categoryId
     * @param Request $request
     * @return JsonResponse
     */
    public function getByCategory(int $categoryId, Request $request): JsonResponse
    {
        $query = JobPost::with([
            'job_creator.user_complete_orders',
            'job_skills',
            'job_category',
            'job_sub_categories'
        ])
            ->whereHas('job_creator')
            ->where('on_off', '1')
            ->where('status', '1')
            ->where('job_approve_request', '1')
            ->where('category', $categoryId);

        // Apply additional filters
        $this->applyFilters($query, $request);

        // Filter by job type if HourlyJob module doesn't exist
        if (!moduleExists('HourlyJob')) {
            $query->where('type', 'fixed');
        }

        $limit = $request->get('limit', 10);
        $jobs = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => [
                'jobs' => $jobs->items(),
                'pagination' => [
                    'current_page' => $jobs->currentPage(),
                    'last_page' => $jobs->lastPage(),
                    'per_page' => $jobs->perPage(),
                    'total' => $jobs->total(),
                    'from' => $jobs->firstItem(),
                    'to' => $jobs->lastItem()
                ]
            ],
            'message' => __('Category jobs retrieved successfully')
        ]);
    }

    /**
     * Get featured/recommended jobs
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $query = JobPost::with([
            'job_creator.user_complete_orders',
            'job_skills',
            'job_category',
            'job_sub_categories'
        ])
            ->whereHas('job_creator')
            ->where('on_off', '1')
            ->withCount('job_proposals')
            ->where('status', '1')
            ->where('job_approve_request', '1')
            ->orderBy('created_at', 'desc')
            ->take(10);

        // Filter by job type if HourlyJob module doesn't exist
        if (!moduleExists('HourlyJob')) {
            $query->where('type', 'fixed');
        }

        $jobs = $query->get();

        return response()->json([
            'success' => true,
            'data' => $jobs,
            'message' => __('Featured jobs retrieved successfully')
        ]);
    }

    /**
     * Apply common filters to job query
     * 
     * @param $query
     * @param Request $request
     * @return void
     */
    private function applyFilters($query, Request $request): void
    {
        // Filter by country
        if ($request->get('country')) {
            $query->whereHas('job_creator', function ($q) use ($request) {
                $q->where('country_id', $request->get('country'));
            });
        }

        // Filter by job type
        if ($request->get('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by freelancer level
        if ($request->get('level')) {
            $query->whereHas('job_creator', function ($q) use ($request) {
                $q->where('level', $request->get('level'));
            });
        }

        // Filter by budget range
        if ($request->get('min_price') && $request->get('max_price')) {
            $query->whereBetween('budget', [
                $request->get('min_price'),
                $request->get('max_price')
            ]);
        }

        // Filter by duration
        if ($request->get('duration')) {
            $query->where('duration', $request->get('duration'));
        }

        // Filter by skills
        if ($request->get('skills')) {
            $skills = is_array($request->get('skills')) 
                ? $request->get('skills') 
                : explode(',', $request->get('skills'));
            
            $query->whereHas('job_skills', function ($q) use ($skills) {
                $q->whereIn('skill_id', $skills);
            });
        }

        // Filter by tags
        if ($request->get('tags')) {
            $tags = is_array($request->get('tags')) 
                ? $request->get('tags') 
                : explode(',', $request->get('tags'));
            
            foreach ($tags as $tag) {
                $query->where('tags', 'LIKE', "%{$tag}%");
            }
        }
    }
}
