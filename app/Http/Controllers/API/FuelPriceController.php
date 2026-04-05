<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;

class FuelPriceController extends Controller
{
    /**
     * GET /api/fuel/prices
     * Returns all active fuel types with prices.
     */
    public function index()
    {
        $prices = FuelPrice::active()->get(['id', 'fuel_type', 'name_ar', 'price_per_liter']);

        return response()->json([
            'success' => true,
            'data' => $prices,
        ]);
    }
}
