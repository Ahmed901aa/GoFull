<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestRejection extends Model
{
    protected $fillable = [
        'request_id',
        'provider_id',
    ];

    public function request()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    public function provider()
    {
        return $this->belongsTo(ProviderProfile::class, 'provider_id');
    }
}
