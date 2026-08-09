<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'meta_title',
        'meta_description',
        'heading',
        'short_description',
        'content',
        'faq_content',
        'faqs'
    ];

    protected $casts = [
        'faqs' => 'array'
    ];
}
