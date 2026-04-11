<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;

class FuelPriceController extends Controller
{
    /**
     * GET /api/fuel/prices
     * Returns all active fuel types with prices and tax info.
     */
    public function index()
    {
        $prices = FuelPrice::active()
            ->get(['id', 'fuel_type', 'name_ar', 'price_per_liter', 'tax_type', 'tax_value'])
            ->map(fn (FuelPrice $price) => [
                'id'              => $price->id,
                'fuel_type'       => $price->fuel_type,
                'name_ar'         => $price->name_ar,
                'price_per_liter' => $price->price_per_liter,
                'tax_type'        => $price->tax_type,
                'tax_value'       => $price->tax_value,
                'tax_amount'      => $price->calculateTax(),
                'price_with_tax'  => $price->price_with_tax,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $prices,
        ]);
    }
}
