<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = ['title', 'type', 'action', 'subtitle', 'image_url', 'discount_code', 'color_hex', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['full_image_url'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Returns a fully-qualified image URL.
     * Handles: full URLs (https://…), absolute paths (/images/…), storage paths (banners/img.jpg), and null.
     */
    public function getFullImageUrlAttribute(): ?string
    {
        if (! $this->image_url) {
            return null;
        }

        // Already a full URL
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }

        // Absolute public path (e.g. /images/logo.png)
        if (str_starts_with($this->image_url, '/')) {
            return rtrim(config('app.url'), '/').$this->image_url;
        }

        // Relative storage path → build full URL from public storage disk
        return Storage::disk('public')->url($this->image_url);
    }
}
