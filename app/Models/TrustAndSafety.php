<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustAndSafety extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meta_title',
        'meta_description',
        'banner_title',
        'content_title',
        'introduction',
        'top_rated_program',
        'communication_importance',
        'escrow_system',
        'customer_support',
        'dispute_resolution',
        'freelancer_profiles',
        'project_guidelines',
        'scam_protection_title',
        'scam_protection_points',
        'contact_info',
    ];

    protected $casts = [
        'scam_protection_points' => 'array',
    ];
}
