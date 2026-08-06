<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    /** Max wrong verification attempts before the code is invalidated. */
    public const MAX_ATTEMPTS = 5;

    protected $fillable = [
        'phone',
        'code',
        'purpose',
        'attempts',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'attempts' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function hasTooManyAttempts(): bool
    {
        return $this->attempts >= self::MAX_ATTEMPTS;
    }
}
