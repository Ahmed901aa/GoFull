<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function index()
    {
        $serviceFee = AppSetting::getValue('service_fee', 15);

        // Stats: how much total service fees collected
        $totalServiceFees = ServiceRequest::where('status', 'completed')->sum('service_fee');
        $todayServiceFees = ServiceRequest::where('status', 'completed')
            ->whereDate('completed_at', today())
            ->sum('service_fee');
        $completedOrders = ServiceRequest::where('status', 'completed')->count();

        return view('admin.settings.index', compact(
            'serviceFee',
            'totalServiceFees',
            'todayServiceFees',
            'completedOrders',
        ));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'service_fee' => ['required', 'numeric', 'min:0', 'max:9999'],
        ]);

        AppSetting::setValue('service_fee', $data['service_fee']);

        return back()->with('success', 'تم تحديث رسوم الخدمة بنجاح.');
    }
}
