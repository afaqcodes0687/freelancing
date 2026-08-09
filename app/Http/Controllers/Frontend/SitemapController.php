<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\Service\Entities\SubCategory;
use Modules\Service\Entities\ChildCategory;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate a dynamic XML sitemap that reflects the new hierarchical URL structure.
     * Includes: static pages, category hubs, subcategory landings, and child-category landings.
     */
    public function index(): Response
    {
        $now = now()->toAtomString();

        // ── Static pages ──────────────────────────────────────────────────────
        $staticUrls = [
            ['loc' => url('/'),                 'priority' => '1.00', 'changefreq' => 'daily'],
            ['loc' => url('/jobs'),             'priority' => '0.80', 'changefreq' => 'daily'],
            ['loc' => url('/talents'),          'priority' => '0.80', 'changefreq' => 'daily'],
            ['loc' => url('/projects'),         'priority' => '0.80', 'changefreq' => 'daily'],
            ['loc' => url('/packages/all'),     'priority' => '0.70', 'changefreq' => 'weekly'],
            ['loc' => url('/how-it-works'),     'priority' => '0.60', 'changefreq' => 'monthly'],
            ['loc' => url('/login'),            'priority' => '0.50', 'changefreq' => 'monthly'],
            ['loc' => url('/register'),         'priority' => '0.50', 'changefreq' => 'monthly'],
        ];

        // ── Category hubs ─────────────────────────────────────────────────────
        $categories = Category::where('status', 1)->get(['id', 'slug', 'updated_at']);

        // ── Subcategories with their parent category slug ─────────────────────
        $subcategories = SubCategory::with('category')
            ->where('status', 1)
            ->get(['id', 'slug', 'category_id', 'updated_at']);

        // ── Child categories ──────────────────────────────────────────────────
        $childCategories = ChildCategory::with('sub_category.category')
            ->get(['id', 'slug', 'sub_category_id', 'updated_at']);

        $xml = view('frontend.pages.sitemap.index', compact(
            'now', 'staticUrls', 'categories', 'subcategories', 'childCategories'
        ))->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
