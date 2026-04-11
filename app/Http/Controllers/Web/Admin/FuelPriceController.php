<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice;
use Illuminate\Http\Request;

class FuelPriceController extends Controller
{
    public function index()
    {
        $prices = FuelPrice::orderBy('id')->get();

        return view('admin.fuel_prices.index', compact('prices'));
    }

    public function update(Request $request, FuelPrice $fuelPrice)
    {
        $data = $request->validate([
            'price_per_liter' => ['required', 'numeric', 'min:0', 'max:9999'],
            'tax_type'        => ['required', 'in:percentage,fixed'],
            'tax_value'       => ['required', 'numeric', 'min:0', 'max:9999'],
            'is_active'       => ['nullable', 'boolean'],
        ]);

        $fuelPrice->update([
            'price_per_liter' => $data['price_per_liter'],
            'tax_type'        => $data['tax_type'],
            'tax_value'       => $data['tax_value'],
            'is_active'       => $request->has('is_active'),
        ]);

        return back()->with('success', 'تم تحديث السعر بنجاح.');
    }
}
