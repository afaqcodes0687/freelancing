<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Chat\Entities\LiveChat;
use Modules\Chat\Entities\LiveChatMessage;
use Modules\CountryManage\Entities\City;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\Wallet\Entities\Wallet;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\PromoteFreelancer\Entities\PromotionProjectList;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'hourly_rate',
        'experience_level',
        'email',
        'referral_code',
        'phone',
        'username',
        'password',
        'user_type',
        'country_id',
        'email_verify_token',
        'is_email_verified',
        'google_2fa_secret',
        'google_2fa_enable_disable_disable',
        'google_id',
        'facebook_id',
        'apple_id',
        'load_from',
        'is_synced',
        'referred_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'check_online_status'=>'datetime',
        'user_type'=>'integer',
        'check_work_availability'=>'integer',
        'user_active_inactive_status'=>'integer',
        'user_verified_status'=>'integer',
        'is_suspend'=>'integer',
        'google_2fa_enable_disable_disable'=>'integer',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Generate referral code when user is created
        static::creating(function ($user) {
            if (!$user->referral_code) {
                $user->referral_code = $user->generateReferralCode();
            }
        });
    }

    public function hasVerifiedPayment(): bool
    {
        return $this->user_complete_orders()->exists();
    }

    /**
     * Get total spent amount (raw number)
     */
    public function totalSpent(): float
    {
        return (float) $this->user_complete_orders()->sum('payable_amount');
    }

    /**
     * Get formatted spent amount (with currency)
     */
    public function totalSpentFormatted(): string
    {
        return float_amount_with_currency_symbol($this->totalSpent());
    }


    //get user full name
    public function getFullnameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    //google 2fa
    protected function google2faSecret(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  decrypt($value),
            set: fn ($value) =>  encrypt($value),
        );
    }

    public function user_country()
    {
        return $this->belongsTo(Country::class,'country_id')->select('id','country','status');
    }
    public function user_state()
    {
        return $this->belongsTo(State::class,'state_id');
    }
    public function user_city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
    public function user_introduction()
    {
        return $this->hasOne(UserIntroduction::class,'user_id');
    }
    public function identity_verify()
    {
        return $this->hasOne(IdentityVerification::class,'user_id','id');
    }

    public function user_jobs()
    {
        return $this->hasMany(JobPost::class,'user_id','id');
    }

    public function user_complete_orders()
    {
        return $this->hasMany(Order::class,'user_id','id')->where('status',3);
    }

    public function projects()
    {
        return $this->hasMany(Projects::class,'user_id','id');
    }

    public function user_wallet()
    {
        return $this->hasOne(\Modules\Wallet\Entities\Wallet::class,'user_id','id');
    }

    public function admin_commission()
    {
        return $this->hasOne(IndividualCommissionSetting::class,'user_id','id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class,'user_id','id');
    }

    public function freelancer_orders()
    {
        return $this->hasMany(Order::class,'freelancer_id','id');
    }

    public function freelancer_category()
    {
        return $this->hasMany(UserWork::class,'user_id','id');
    }

    public function freelancer_skill()
    {
        return $this->hasMany(UserSkill::class,'user_id','id');
    }

    public function freelancer_ratings(): HasManyThrough
    {
        return $this->hasManyThrough(Rating::class, Order::class,'freelancer_id','order_id');
    }
    public function ratingsFromClients(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(\App\Models\Rating::class, \App\Models\Order::class,
            'freelancer_id', 'order_id', 'id', 'id'            
            
        );
    }


    public function freelancer_unseen_message(): HasManyThrough
    {
        return $this->hasManyThrough(LiveChatMessage::class, LiveChat::class,'freelancer_id','live_chat_id');
    }

    public function client_unseen_message(): HasManyThrough
    {
        return $this->hasManyThrough(LiveChatMessage::class, LiveChat::class,'client_id','live_chat_id');
    }

    // Referral relationships
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function old_referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function old_referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }
    public function works()
    {
        return $this->hasMany(UserWork::class);
    }

    /**
     * Generate unique referral code for user
     */
    public function generateReferralCode()
    {
        if (!$this->referral_code) {
            do {
                $code = strtoupper(\Illuminate\Support\Str::random(8));
            } while (self::where('referral_code', $code)->exists());
            
            $this->referral_code = $code;
        }
        
        return $this->referral_code;
    }

    /**
     * Get total referral earnings
     */
    public function getTotalReferralEarnings()
    {
        return $this->old_referrals()
            ->where('status', 'completed')
            ->sum('reward_amount');
    }

    /**
     * Get remaining earning potential
     */
    public function getRemainingReferralPotential()
    {
        $totalEarned = $this->getTotalReferralEarnings();
        return max(0, 500 - $totalEarned);
    }

    /**
     * Check if user has reached referral limit
     */
    public function hasReachedReferralLimit()
    {
        return $this->getTotalReferralEarnings() >= 500;
    }

    /**
     * Get pending referrals count
     */
    public function getPendingReferralsCount()
    {
        return $this->old_referrals()
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Get completed referrals count
     */
    public function getCompletedReferralsCount()
    {
        return $this->old_referrals()
            ->where('status', 'completed')
            ->count();
    }

        public function isProActive()
    {
        return $this->is_pro === 'yes' && $this->pro_expire_date && $this->pro_expire_date >= now();
    }
    public function skills()
    {
        return $this->hasMany(UserSkill::class, 'user_id', 'id');
    }
    public function promotionalProfiles()
    {
        return $this->hasMany(\Modules\PromoteFreelancer\Entities\PromotionProjectList::class, 'user_id', 'id')
            ->where('type', 'profile')
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                ->orWhere('expire_date', '>=', now()); // ✅ only active profiles
            });
    }

    /**
     * Get bio links for the user
     */
    public function bioLinks()
    {
        return $this->hasMany(BioLink::class)->ordered();
    }

    /**
     * Get active bio links only
     */
    public function activeBioLinks()
    {
        return $this->hasMany(BioLink::class)->active()->ordered();
    }

    /**
     * Get featured bio links only
     */
    public function featuredBioLinks()
    {
        return $this->hasMany(BioLink::class)->active()->featured()->ordered();
    }

    /**
     * Get link clicks for the user's links
     */
    public function linkClicks()
    {
        return $this->hasManyThrough(LinkClick::class, BioLink::class);
    }

    /**
     * Get bio page URL
     */
    public function getBioUrlAttribute()
    {
        return url('/u/' . $this->username);
    }

    /**
     * Get bio page views count
     */
    public function incrementBioViews()
    {
        $this->increment('bio_views');
    }

    /**
     * Get total clicks on all bio links
     */
    public function getTotalLinkClicksAttribute()
    {
        return $this->linkClicks()->count();
    }

    /**
     * Get unique clicks on all bio links
     */
    public function getUniqueLinkClicksAttribute()
    {
        return $this->linkClicks()->distinct('ip_address')->count();
    }

}
