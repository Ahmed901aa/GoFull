<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'vehicle_plate',
        'vehicle_color',
        'is_available',
        'verification_status',
        'appointment_date',
        'appointment_notes',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'average_rating',
        'total_ratings',
    ];

    protected $casts = [
        'is_available'     => 'boolean',
        'appointment_date' => 'datetime',
        'verified_at'      => 'datetime',
        'average_rating'   => 'decimal:2',
    ];

    // ========== Helpers ==========

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    public function isFuelProvider(): bool
    {
        return $this->service_type === 'fuel_delivery';
    }

    public function isTowingProvider(): bool
    {
        return $this->service_type === 'towing';
    }

    // ========== Relationships ==========

    // ProviderProfile ينتمي لـ User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ProviderProfile له وثائق كثيرة
    public function documents()
    {
        return $this->hasMany(ProviderDocument::class, 'provider_id');
    }

    // ProviderProfile له طلبات كثيرة
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'provider_id');
    }

    // ProviderProfile له تقييمات كثيرة عبر الطلبات
    public function ratings()
    {
        return $this->hasManyThrough(
            Rating::class,
            ServiceRequest::class,
            'provider_id',  // FK في service_requests
            'request_id',   // FK في ratings
        );
    }

    // من وثّق هذا الـ provider (admin أو employee)
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}