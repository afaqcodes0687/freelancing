<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AffiliateProgram extends Authenticatable
{
    use HasFactory;

    protected $table = 'affiliates_programs';

    protected $fillable = [
        'user_id',
        'parent_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'username',
        'password',
        'country_id',
        'state_id',
        'city_id',
        'is_email_verified',
        'account_display_name',
        'company_website',
        'referral_code',
        'email_verify_token',
        'balance',
        'total_earned',
        'status',
        'order_amount',
        'commission_rate'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verify_token',
    ];

    // ✅ Parent (the affiliate who referred this user)
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // ✅ Children (affiliates referred by this user)
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // ✅ Direct commissions earned by this affiliate
    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_id');
    }

    // ✅ All referral clicks
    public function clicks()
    {
        return $this->hasMany(AffiliateClick::class, 'affiliate_id');
    }

    // ✅ All payouts (withdrawals)
    public function payouts()
    {
        return $this->hasMany(AffiliatePayout::class, 'affiliate_id');
    }

    // ✅ Get total earnings
    public function getTotalEarningsAttribute()
    {
        return $this->commissions()->sum('commission_amount');
    }

    // ✅ Recursive relationship helper (optional)
    public function allParents()
    {
        return $this->parent ? $this->parent->allParents()->merge([$this->parent]) : collect([]);
    }
}
