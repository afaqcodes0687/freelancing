<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $table = 'affiliate_commissions';

    protected $fillable = [
        'affiliate_id',
        'user_id',
        'referrer_user_id',
        'commission_amount',
        'commission_rate',
        'status',
        'description',
        'order_id',
        'order_amount',
        'approval_token',
        'level', // ✅ Add this to track hierarchy level
    ];

    public function affiliate()
    {
        return $this->belongsTo(AffiliateProgram::class, 'affiliate_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referrer_user()
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }
}
