<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelPrice extends Model
{
    protected $fillable = ['fuel_type', 'name_ar', 'price_per_liter', 'tax_type', 'tax_value', 'is_active'];

    protected $casts = [
        'price_per_liter' => 'decimal:2',
        'tax_value'       => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate the tax amount for a given base amount (defaults to price_per_liter).
     */
    public function calculateTax(float $amount = null): float
    {
        $amount ??= (float) $this->price_per_liter;

        return $this->tax_type === 'percentage'
            ? round($amount * ((float) $this->tax_value / 100), 2)
            : (float) $this->tax_value;
    }

    /**
     * Price per liter including tax.
     */
    public function getPriceWithTaxAttribute(): float
    {
        return round((float) $this->price_per_liter + $this->calculateTax(), 2);
    }

    /**
     * Formatted tax label for display.
     */
    public function getTaxLabelAttribute(): string
    {
        if ((float) $this->tax_value <= 0) {
            return 'بدون ضريبة';
        }

        return $this->tax_type === 'percentage'
            ? number_format($this->tax_value, 2) . '%'
            : number_format($this->tax_value, 2) . ' د.ل';
    }
}
