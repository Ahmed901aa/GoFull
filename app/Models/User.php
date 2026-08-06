<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'phone_verified_at',
        'password',
        'role',
        'employee_type',
        'status',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'phone_verified_at' => 'datetime',
    ];

    // ========== Helpers ==========

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ========== Relationships ==========

    // User (provider) له profile واحد
    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class);
    }

    // User (driver) له طلبات كثيرة
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'driver_id');
    }

    // User له إشعارات كثيرة
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // السائق له مركبة واحدة
    public function vehicle()
    {
        return $this->hasOne(DriverVehicle::class, 'driver_id');
    }

    // Admin/Employee وثّق providers
    public function verifiedProviders()
    {
        return $this->hasMany(ProviderProfile::class, 'verified_by');
    }
}