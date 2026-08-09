<?php

namespace App\Models;

use App\Support\PolicyPageDefaults;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceShippingPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public static function defaults(): array
    {
        return PolicyPageDefaults::serviceShipping();
    }

    public static function bootstrap(): self
    {
        $policy = static::query()->first();
        $defaults = static::defaults();

        if (!$policy) {
            return static::create($defaults);
        }

        $updates = [];

        foreach (['title', 'meta_title', 'meta_description', 'heading', 'short_description', 'content', 'faq_content'] as $field) {
            if (blank($policy->{$field}) && array_key_exists($field, $defaults)) {
                $updates[$field] = $defaults[$field];
            }
        }

        if ((empty($policy->faqs) || !is_array($policy->faqs)) && !empty($defaults['faqs'])) {
            $updates['faqs'] = $defaults['faqs'];
        }

        if (!empty($updates)) {
            $policy->fill($updates);
            $policy->save();
            $policy->refresh();
        }

        return $policy;
    }
}
