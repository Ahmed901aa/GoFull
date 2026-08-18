<?php

namespace App\Http\Controllers\Web\Admin;

use App\Events\HomeDataUpdated;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\FuelPrice;
use Illuminate\Http\Request;

class FuelPriceController extends Controller
{
    public function index()
    {
        $prices = FuelPrice::orderBy('id')->get();
        $openStations = (int) AppSetting::getValue('open_stations_count', 0);

        return view('admin.fuel_prices.index', compact('prices', 'openStations'));
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

        // Push the fresh snapshot to every connected customer app
        event(new HomeDataUpdated);

        return back()->with('success', 'تم تحديث السعر بنجاح وإرساله للتطبيق مباشرة.');
    }

    /**
     * Update the "open stations right now" counter shown on the app home page.
     */
    public function updateOpenStations(Request $request)
    {
        $data = $request->validate([
            'open_stations_count' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        AppSetting::setValue('open_stations_count', (string) $data['open_stations_count']);

        event(new HomeDataUpdated);

        return back()->with('success', 'تم تحديث عدد المحطات المفتوحة وإرساله للتطبيق مباشرة.');
    }
}
