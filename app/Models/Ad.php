<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                // Check if optimize_for is clicks and clicks are less than quantity
                $q->where(function ($sub) {
                    $sub->where('optimize_for', 'click')
                        ->whereColumn('clicks', '<', 'quantity');
                })
                    // OR check if optimize_for is impressions and impressions are less than quantity
                    ->orWhere(function ($sub) {
                    $sub->where('optimize_for', 'impression')
                        ->whereColumn('impressions', '<', 'quantity');
                });
            });
    }
}
