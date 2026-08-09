<?php

namespace Modules\Service\Entities;

use App\Models\JobPost;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChildCategory extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug', 'sub_category_id', 'short_description', 'meta_title', 'meta_description', 'image', 'status'];
    protected $casts = ['status' => 'integer'];

    protected static function newFactory()
    {
        return \Modules\Service\Database\factories\ChildCategoryFactory::new();
    }

    public static function all_child_categories()
    {
        return self::select(['id', 'name', 'status', 'image'])->where('status', 1)->get();
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function category()
    {
        return $this->hasOneThrough(Category::class, SubCategory::class, 'id', 'id', 'sub_category_id', 'category_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'child_category_id', 'id');
    }

    public function jobs()
    {
        return $this->hasMany(JobPost::class, 'child_category_id', 'id')->select(['id', 'child_category_id', 'slug'])->where(['on_off' => '1', 'status' => '1']);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class, 'child_category_id', 'id');
    }
}
