<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWorkSubcategory extends Model
{
    use HasFactory;

    protected $table = 'user_work_subcategories';

    protected $fillable = [
        'user_id',
        'category_id',
        'sub_category_id',
    ];
}
