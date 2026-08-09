<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'ceo_name',
        'ceo_title',
        'ceo_description',
        'ceo_image',
        'main_title',
        'main_description',
        'opportunity_text',
        'clients_count',
        'freelancers_count',
        'orders_count',
        'jobs_handled',
        'earned_amount',
        'awards_count',
        'video_title',
        'video_description',
        'video_url',
        'video_thumbnail',
        'what_we_do_title',
        'what_we_do_description',
        'certifications_title',
        'certifications_description',
        'certifications',
        'team_title',
        'team_members',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'certifications' => 'array',
        'team_members' => 'array',
    ];
}
