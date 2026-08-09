<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIntroduction extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'description', 'github_link', 'stackoverflow_link', 'github_meta', 'stackoverflow_meta'];

    protected $casts = [
        'github_meta' => 'array',
        'stackoverflow_meta' => 'array',
    ];
}
