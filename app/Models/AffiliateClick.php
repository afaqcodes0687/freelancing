<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{
    use HasFactory;

    protected $table = 'affiliate_clicks';

    protected $fillable = [
        'affiliate_id',
        'user_referrer_id',
        'ip_address',
        'user_agent',
        'referer',
        'country',
        'clicked_at',
    ];

    public $timestamps = true;

    public function affiliate()
    {
        return $this->belongsTo(AffiliateProgram::class, 'affiliate_id');
    }

    public function user_referrer()
    {
        return $this->belongsTo(User::class, 'user_referrer_id');
    }

    public function registeredUser()
    {
        return $this->belongsTo(\App\Models\AffiliateProgram::class, 'registered_user_id');
    }


}
