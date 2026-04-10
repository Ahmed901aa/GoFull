<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\FuelDeliveryRequest;
use App\Http\Requests\Driver\TowingRequest;
use App\Models\AppSetting;
use App\Models\FuelPrice;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class ServiceRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = auth()->user()
            ->serviceRequests()
            ->with(['provider.user', 'rating'])
            ->latest()
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function storeFuel(FuelDeliveryRequest $request): JsonResponse
    {
        $hasActive = auth()->user()
            ->serviceRequests()
            ->whereIn('status', ['pending', 'accepted', 'en_route', 'arrived', 'in_progress'])
            ->exists();

        if ($hasActive) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active request. Please wait for it to complete.',
            ], 422);
        }

        $data = $request->validated();

        // Auto-calculate pricing from fuel_prices table
        $fuelPrice = FuelPrice::where('fuel_type', $data['fuel_type'])->active()->first();
        $pricePerLiter = $fuelPrice ? $fuelPrice->price_per_liter : 0;
        $quantity = $data['fuel_quantity'];
        $subtotal = round($pricePerLiter * $quantity, 2);
        $serviceFee = (float) AppSetting::getValue('service_fee', 15);
        $total = round($subtotal + $serviceFee, 2);

        $serviceRequest = ServiceRequest::create([
            'driver_id'        => auth()->id(),
            'service_type'     => 'fuel_delivery',
            'status'           => 'pending',
            'driver_latitude'  => $data['driver_latitude'],
            'driver_longitude' => $data['driver_longitude'],
            'driver_address'   => $data['driver_address'] ?? null,
            'fuel_type'        => $data['fuel_type'],
            'fuel_quantity'    => $data['fuel_quantity'],
            'price_per_liter'  => $pricePerLiter,
            'subtotal'         => $subtotal,
            'service_fee'      => $serviceFee,
            'total'            => $total,
            'notes'            => $data['notes'] ?? null,
        ]);

        $providers = ProviderProfile::where('service_type', 'fuel_delivery')
            ->where('verification_status', 'approved')
            ->where('is_available', true)
            ->with('user')
            ->get();

        NotificationService::sendToMany(
            $providers->pluck('user')->filter(),
            'New Fuel Delivery Request',
            'A new fuel delivery request is available near you.',
            ['request_id' => $serviceRequest->id, 'type' => 'fuel_delivery']
        );

        return response()->json([
            'success' => true,
            'message' => 'Fuel delivery request created. Waiting for a provider to accept.',
            'data'    => $serviceRequest->load('driver'),
        ], 201);
    }

    public function storeTowing(TowingRequest $request): JsonResponse
    {
        $hasActive = auth()->user()
            ->serviceRequests()
            ->whereIn('status', ['pending', 'accepted', 'en_route', 'arrived', 'in_progress'])
            ->exists();

        if ($hasActive) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active request. Please wait for it to complete.',
            ], 422);
        }

        $data = $request->validated();

        // Auto-calculate towing pricing from app_settings
        $towingBasePrice = (float) AppSetting::getValue('towing_base_price', 50);
        $serviceFee = (float) AppSetting::getValue('service_fee', 15);
        $total = round($towingBasePrice + $serviceFee, 2);

        $serviceRequest = ServiceRequest::create([
            'driver_id'              => auth()->id(),
            'service_type'           => 'towing',
            'status'                 => 'pending',
            'driver_latitude'        => $data['driver_latitude'],
            'driver_longitude'       => $data['driver_longitude'],
            'driver_address'         => $data['driver_address'] ?? null,
            'destination_latitude'   => $data['destination_latitude'] ?? null,
            'destination_longitude'  => $data['destination_longitude'] ?? null,
            'destination_address'    => $data['destination_address'] ?? null,
            'plate_number'           => $data['plate_number'],
            'subtotal'               => $towingBasePrice,
            'service_fee'            => $serviceFee,
            'total'                  => $total,
            'notes'                  => $data['notes'] ?? null,
        ]);

        $providers = ProviderProfile::where('service_type', 'towing')
            ->where('verification_status', 'approved')
            ->where('is_available', true)
            ->with('user')
            ->get();

        NotificationService::sendToMany(
            $providers->pluck('user')->filter(),
            'New Towing Request',
            'A new towing request is available near you.',
            ['request_id' => $serviceRequest->id, 'type' => 'towing']
        );

        return response()->json([
            'success' => true,
            'message' => 'Towing request created. Waiting for a provider to accept.',
            'data'    => $serviceRequest->load('driver'),
        ], 201);
    }

    public function show(ServiceRequest $request): JsonResponse
    {
        if ($request->driver_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $request->load(['driver', 'provider.user', 'rating']),
        ]);
    }

    public function cancel(ServiceRequest $request): JsonResponse
    {
        if ($request->driver_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        // Only pending requests can be cancelled. Once a provider has
        // accepted, the customer must let the service proceed.
        if ($request->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء الطلب بعد قبوله من قبل مزود الخدمة.',
            ], 422);
        }

        $request->update([
            'status'              => 'cancelled',
            'cancelled_by'        => 'driver',
            'cancelled_at'        => now(),
            'cancellation_reason' => 'Cancelled by driver.',
        ]);

        if ($request->provider_id) {
            NotificationService::send(
                $request->provider->user,
                'Request Cancelled',
                'The driver has cancelled the request.',
                ['request_id' => $request->id]
            );
        }

        return response()->json(['success' => true, 'message' => 'Request cancelled successfully.']);
    }
}