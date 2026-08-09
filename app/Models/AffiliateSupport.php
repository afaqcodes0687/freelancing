<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateSupport extends Model
{
    use HasFactory;

    protected $table = 'affiliate_supports';

    protected $fillable = [
        'affiliate_id',
        'subject',
        'message',
        'status',
    ];

    public function affiliate()
    {
        return $this->belongsTo(AffiliateProgram::class, 'affiliate_id');
    }
}
