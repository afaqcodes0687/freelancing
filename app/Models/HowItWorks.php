<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HowItWorks extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'banner_title',
        
        // Hiring Tab Content
        'hiring_content_title',
        'hiring_content_subtitle',
        'hiring_main_content',
        'hiring_faqs',
        'hiring_side_image',
        
        // Hiring Progress Section
        'hiring_progress_title',
        'hiring_progress_subtitle',
        'hiring_progress_content',
        'hiring_progress_faqs',
        'hiring_progress_image',
        
        // Hiring Payment Section
        'hiring_payment_title',
        'hiring_payment_subtitle',
        'hiring_payment_content',
        'hiring_payment_faqs',
        'hiring_payment_image',
        
        // Talents Tab Content
        'talents_content_title',
        'talents_content_subtitle',
        'talents_main_content',
        'talents_faqs',
        'talents_side_image',
        
        // Talents Payment Section
        'talents_payment_title',
        'talents_payment_subtitle',
        'talents_payment_content',
        'talents_payment_faqs',
        'talents_payment_image',
        
        // FAQ Tab Content
        'faq_content_title',
        'faq_content_subtitle',
        'faq_main_content',
        'faq_faqs',
        'faq_side_image',
        
        // Projects Tab Content
        'projects_content_title',
        'projects_content_subtitle',
        'projects_main_content',
        'projects_faqs',
        'projects_side_image',
    ];

    protected $casts = [
        'hiring_faqs' => 'array',
        'hiring_progress_faqs' => 'array',
        'hiring_payment_faqs' => 'array',
        'talents_faqs' => 'array',
        'talents_payment_faqs' => 'array',
        'faq_faqs' => 'array',
        'projects_faqs' => 'array',
    ];
}
