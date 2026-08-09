<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateRegistration extends Model
{
    use HasFactory;

    protected $table = 'affiliate_registrations';

    protected $fillable = [
        'affiliate_id',
        'user_id',
    ];

    public function affiliate()
    {
        return $this->belongsTo(AffiliateProgram::class, 'affiliate_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
