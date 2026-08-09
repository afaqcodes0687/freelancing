<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnershipPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meta_title',
        'meta_description',
        'escrow_title',
        'escrow_description',
        'escrow_image',
        'why_partner_title',
        'why_partner_description',
        'expand_talent_title',
        'expand_talent_description',
        'expand_talent_image',
        'foster_innovation_title',
        'foster_innovation_description',
        'foster_innovation_image',
        'market_presence_title',
        'market_presence_description',
        'market_presence_image',
        'economic_empowerment_description',
        'economic_empowerment_image',
        'opportunities',
        'process',
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'opportunities' => 'array',
        'process' => 'array',
    ];
}
