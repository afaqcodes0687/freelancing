<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','skill'];
    
     protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at'
    ];
}
