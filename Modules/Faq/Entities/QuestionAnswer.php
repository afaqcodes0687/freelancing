<?php

namespace Modules\Faq\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Service\Entities\Category;

class QuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'question', 'answer'];

    protected static function newFactory()
    {
        return \Modules\Faq\Database\factories\QuestionAnswerFactory::new();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
