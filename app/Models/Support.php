<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meta_title',
        'meta_description',
        'banner_title',
        'content_title',
        'main_content',
        'faq_title',
        'faqs',
        'side_image',
        'main_image'
    ];

    protected $casts = [
        'faqs' => 'array',
    ];
}
