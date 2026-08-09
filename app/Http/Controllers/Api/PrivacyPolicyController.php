<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;

class PrivacyPolicyController extends Controller
{
    public function getAppPrivacyPolicy()
    {
        $policy = PrivacyPolicy::where('type', 'app')->first();

        if (!$policy) {
            return response()->json([
                'success' => false,
                'message' => 'App privacy policy not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $policy->id,
                'title' => $policy->title,
                'meta_title' => $policy->meta_title,
                'meta_description' => $policy->meta_description,
                'heading' => $policy->heading,
                'short_description' => $policy->short_description,
                'content' => $policy->content,
                'faq_content' => $policy->faq_content,
                'faqs' => $policy->faqs,
                'updated_at' => $policy->updated_at
            ]
        ]);
    }

    public function getWebsitePrivacyPolicy()
    {
        $policy = PrivacyPolicy::where('type', 'website')->first();

        if (!$policy) {
            return response()->json([
                'success' => false,
                'message' => 'Website privacy policy not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $policy->id,
                'title' => $policy->title,
                'meta_title' => $policy->meta_title,
                'meta_description' => $policy->meta_description,
                'heading' => $policy->heading,
                'short_description' => $policy->short_description,
                'content' => $policy->content,
                'faq_content' => $policy->faq_content,
                'faqs' => $policy->faqs,
                'updated_at' => $policy->updated_at
            ]
        ]);
    }
}
