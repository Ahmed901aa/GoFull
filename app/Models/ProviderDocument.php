<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProviderDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'document_type',
        'document_path',
        'status',
    ];

    protected $appends = ['document_url'];

    /**
     * Full URL for the document file.
     */
    public function getDocumentUrlAttribute(): ?string
    {
        if (! $this->document_path) {
            return null;
        }

        // Request-host based (APP_URL drifts in dev; see Banner model).
        return url('/storage/'.ltrim($this->document_path, '/'));
    }

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
