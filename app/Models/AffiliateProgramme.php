<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateProgramme extends Model
{
    protected $fillable = [
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'banner_title',
        
        'hero_title',
        'hero_subtitle',
        'hero_button_text',
        'hero_image',
        
        'trusted_by_title',
        'easy_start_title',
        
        'step1_title',
        'step1_subtitle',
        'step1_image',
        
        'step2_title',
        'step2_subtitle',
        'step2_image',
        
        'step3_title',
        'step3_subtitle',
        'step3_image',
        
        'benefits_title',
        'commission_title',
        'commission_content',
        'commission_image',
        
        'support_title',
        'support_content',
        'support_image',
        
        'resources_title',
        'resources_content',
        'resources_image',
        
        'why_title',
        'why_subtitle',
        'why_content',
        'why_image',
        
        'promote_title',
        'promote_content',
        'promote_image',
        'promote_avatar',
        'promote_name',
        'promote_profession',
        'promote_subtitle',
        'promote_reviews',
        
        'jobs_title',
        'jobs_content',
        'jobs_image',
        
        'faq_title',
        
        'cta_title',
        'cta_button_text',
        'cta_image',
        
        'stats1_number',
        'stats1_text',
        'stats2_number',
        'stats2_text',
        'stats3_number',
        'stats3_text',
    ];

    protected $casts = [
        'commission_content' => 'string',
        'support_content' => 'string',
        'resources_content' => 'string',
        'why_content' => 'string',
        'promote_content' => 'string',
        'jobs_content' => 'string',
    ];
}
