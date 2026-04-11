<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Models\DriverVehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * عرض بيانات مركبة السائق
     */
    public function show(): JsonResponse
    {
        $vehicle = auth()->user()->vehicle;

        if (! $vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم تسجيل مركبة بعد',
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $vehicle]);
    }

    /**
     * تسجيل أو تحديث بيانات المركبة
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_type'  => ['required', 'string', 'max:100'],
            'license_plate' => ['required', 'string', 'max:20'],
        ]);

        $vehicle = DriverVehicle::updateOrCreate(
            ['driver_id' => auth()->id()],
            $validated,
        );

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ بيانات المركبة بنجاح',
            'data'    => $vehicle,
        ]);
    }
}
