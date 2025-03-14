<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'objective',
        'challenge',
        'referral_source',
        'custom_objective',
        'custom_challenge',
        'custom_referral_source'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}