<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatePayout extends Model
{
    use HasFactory;

    protected $table = 'affiliate_payouts';

    protected $fillable = [
        'affiliate_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'account_details',
    ];

    public function affiliate()
    {
        return $this->belongsTo(AffiliateProgram::class, 'affiliate_id');
    }
}
