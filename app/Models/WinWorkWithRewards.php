<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WinWorkWithRewards extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'banner_title',
        
        'main_title',
        'main_subtitle',
        'clients_count',
        'clients_text',
        'freelancers_count',
        'freelancers_text',
        'orders_count',
        'orders_text',
        'main_image',
        
        'solutions_title',
        'solutions_subtitle',
        
        'boosted_profile_title',
        'boosted_profile_subtitle',
        'boosted_profile_content',
        'boosted_profile_image',
        
        'availability_badge_title',
        'availability_badge_subtitle',
        'availability_badge_content',
        'availability_badge_image',
        
        'enhanced_proposals_title',
        'enhanced_proposals_subtitle',
        'enhanced_proposals_content',
        'enhanced_proposals_image',
        
        'payment_title',
        'payment_subtitle',
        'payment_content',
        
        'why_use_title',
        'why_use_content',
        
        'getting_started_title',
        'getting_started_content',
        
        'place_bid_title',
        'place_bid_content',
        
        'advertising_options_title',
        'advertising_options',
        
        'helpful_resources_title',
        'helpful_resources_content',
        
        'ads_guide_title',
        'ads_guide_content',
        
        'master_ads_title',
        'master_ads_content',
        
        'cta_title',
        'cta_button_text',
    ];

    protected $casts = [
        'advertising_options' => 'array',
    ];
}
