<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'provider_id',
        'service_type',
        'status',
        'driver_latitude',
        'driver_longitude',
        'driver_address',
        'destination_latitude',
        'destination_longitude',
        'destination_address',
        'notes',
        'fuel_type',
        'fuel_quantity',
        'plate_number',
        'accepted_at',
        'arrived_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'price_per_liter',
        'subtotal',
        'service_fee',
        'total',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'driver_latitude'       => 'decimal:8',
        'driver_longitude'      => 'decimal:8',
        'destination_latitude'  => 'decimal:8',
        'destination_longitude' => 'decimal:8',
        'fuel_quantity'    => 'decimal:2',
        'price_per_liter'  => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'service_fee'      => 'decimal:2',
        'total'            => 'decimal:2',
        'accepted_at'      => 'datetime',
        'arrived_at'       => 'datetime',
        'completed_at'     => 'datetime',
        'cancelled_at'     => 'datetime',
    ];

    // ========== Helpers ==========

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            'pending',
            'accepted',
            'en_route',
            'arrived',
            'in_progress',
        ]);
    }

    public function isFuelDelivery(): bool
    {
        return $this->service_type === 'fuel_delivery';
    }

    public function isTowing(): bool
    {
        return $this->service_type === 'towing';
    }

    // ========== Relationships ==========

    // الطلب ينتمي لـ Driver
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // الطلب ينتمي لـ ProviderProfile
    public function provider()
    {
        return $this->belongsTo(ProviderProfile::class, 'provider_id');
    }

    // الطلب له تقييم واحد فقط
    public function rating()
    {
        return $this->hasOne(Rating::class, 'request_id');
    }
}