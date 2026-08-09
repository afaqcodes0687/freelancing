<?php

namespace Modules\Meeting\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserGoogleAccount extends Model
{
    protected $fillable = [
        'user_id',
        'google_id',
        'email',
        'access_token',
        'refresh_token',
        'expires_in',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
