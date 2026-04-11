<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_type',
        'license_plate',
    ];

    // ========== Relationships ==========

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
