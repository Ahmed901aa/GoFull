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
        'was_available_before_order',
        'verification_status',
        'appointment_date',
        'appointment_notes',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'average_rating',
        'total_ratings',
        'current_latitude',
        'current_longitude',
        'location_updated_at',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'was_available_before_order' => 'boolean',
        'appointment_date' => 'datetime',
        'verified_at' => 'datetime',
        'location_updated_at' => 'datetime',
        'current_latitude' => 'float',
        'current_longitude' => 'float',
        'average_rating' => 'decimal:2',
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
