<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // ========== Helpers ==========

    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }

    // ========== Relationships ==========

    // Rating ينتمي لـ ServiceRequest
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    // نصل للـ Driver عبر الـ Request
    public function driver()
    {
        return $this->hasOneThrough(
            User::class,
            ServiceRequest::class,
            'id',         // FK في service_requests
            'id',         // FK في users
            'request_id', // local key في ratings
            'driver_id'   // local key في service_requests
        );
    }

    // نصل للـ Provider عبر الـ Request
    public function provider()
    {
        return $this->hasOneThrough(
            ProviderProfile::class,
            ServiceRequest::class,
            'id',          // FK في service_requests
            'id',          // FK في provider_profiles
            'request_id',  // local key في ratings
            'provider_id'  // local key في service_requests
        );
    }
}