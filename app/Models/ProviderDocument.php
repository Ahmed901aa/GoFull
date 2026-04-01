<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'document_type',
        'document_path',
        'status',
    ];

    // ========== Helpers ==========

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ========== Relationships ==========

    // Document ينتمي لـ ProviderProfile
    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class, 'provider_id');
    }
}