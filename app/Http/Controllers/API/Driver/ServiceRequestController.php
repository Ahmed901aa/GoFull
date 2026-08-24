<?php

namespace App\Http\Controllers\API\Driver;

use App\Events\NewOrderCreated;
use App\Events\OrderCancelled;
use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\FuelDeliveryRequest;
use App\Http\Requests\Driver\TowingRequest;
use App\Models\AppSetting;
use App\Models\FuelPrice;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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


    /**
     * Atomically check "no active order" and create the new one. Locks the
     * user row so a fast double-tap cannot create two active orders.
     * Returns null when an active order already exists.
     */
    private function createIfNoActive(array $attributes): ?ServiceRequest
    {
        return DB::transaction(function () use ($attributes) {
            DB::table('users')->where('id', auth()->id())->lockForUpdate()->first();

            $hasActive = auth()->user()
                ->serviceRequests()
                ->whereIn('status', ['pending', 'accepted', 'en_route', 'arrived', 'in_progress'])
                ->exists();

            return $hasActive ? null : ServiceRequest::create($attributes);
        });
    }

    public function storeFuel(FuelDeliveryRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Auto-calculate pricing from fuel_prices table. No active price row
        // means the admin disabled this fuel type — refuse rather than
        // creating a 0-priced order.
        $fuelPrice = FuelPrice::where('fuel_type', $data['fuel_type'])->active()->first();
        if (! $fuelPrice) {
            return response()->json([
                'success' => false,
                'message' => 'This fuel type is currently unavailable. Please try again later.',
            ], 422);
        }
        $pricePerLiter = $fuelPrice->price_per_liter;
        $quantity = $data['fuel_quantity'];
        $subtotal = round($pricePerLiter * $quantity, 2);
        $isEmergency = (bool) ($data['is_emergency'] ?? false);
        // Emergency surcharge is admin-configurable (app_settings key
        // 'emergency_fee', default 0 = no surcharge) and folds into the
        // service fee so income reports need no changes.
        $serviceFee = (float) AppSetting::getValue('service_fee', 15)
            + ($isEmergency ? (float) AppSetting::getValue('emergency_fee', 0) : 0);
        $total = round($subtotal + $serviceFee, 2);

        $serviceRequest = $this->createIfNoActive([
            'driver_id' => auth()->id(),
            'service_type' => 'fuel_delivery',
            'is_emergency' => $isEmergency,
            'status' => 'pending',
            'driver_latitude' => $data['driver_latitude'],
            'driver_longitude' => $data['driver_longitude'],
            'driver_address' => $data['driver_address'] ?? null,
            'fuel_type' => $data['fuel_type'],
            'fuel_quantity' => $data['fuel_quantity'],
            'price_per_liter' => $pricePerLiter,
            'subtotal' => $subtotal,
            'service_fee' => $serviceFee,
            'total' => $total,
            'notes' => $data['notes'] ?? null,
        ]);

        if (! $serviceRequest) {
            // `code` lets clients branch on the CONFLICT itself instead of
            // string-matching the human message (which may be localized).
            return response()->json([
                'success' => false,
                'code' => 'ACTIVE_ORDER_EXISTS',
                'message' => 'You already have an active order. Please wait until your current order is completed before creating a new order.',
            ], 422);
        }

        $providers = ProviderProfile::where('service_type', 'fuel_delivery')
            ->where('verification_status', 'approved')
            ->where('is_available', true)
            ->with('user')
            ->get();

        NotificationService::sendToMany(
            $providers->pluck('user')->filter(),
            $isEmergency ? '🚨 Emergency Fuel Request' : 'New Fuel Delivery Request',
            $isEmergency
                ? 'A customer ran out of fuel nearby and needs URGENT delivery.'
                : 'A new fuel delivery request is available near you.',
            ['request_id' => $serviceRequest->id, 'type' => 'fuel_delivery', 'is_emergency' => $isEmergency]
        );

        broadcast(new NewOrderCreated($serviceRequest))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Fuel delivery request created. Waiting for a provider to accept.',
            'data' => $serviceRequest->load('driver'),
        ], 201);
    }

    public function storeTowing(TowingRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Auto-calculate towing pricing from app_settings
        $towingBasePrice = (float) AppSetting::getValue('towing_base_price', 50);
        $serviceFee = (float) AppSetting::getValue('service_fee', 15);
        $total = round($towingBasePrice + $serviceFee, 2);

        $serviceRequest = $this->createIfNoActive([
            'driver_id' => auth()->id(),
            'service_type' => 'towing',
            'status' => 'pending',
            'driver_latitude' => $data['driver_latitude'],
            'driver_longitude' => $data['driver_longitude'],
            'driver_address' => $data['driver_address'] ?? null,
            'destination_latitude' => $data['destination_latitude'] ?? null,
            'destination_longitude' => $data['destination_longitude'] ?? null,
            'destination_address' => $data['destination_address'] ?? null,
            'plate_number' => $data['plate_number'],
            'car_type' => $data['car_type'] ?? null,
            'subtotal' => $towingBasePrice,
            'service_fee' => $serviceFee,
            'total' => $total,
            'notes' => $data['notes'] ?? null,
        ]);

        if (! $serviceRequest) {
            // `code` lets clients branch on the CONFLICT itself instead of
            // string-matching the human message (which may be localized).
            return response()->json([
                'success' => false,
                'code' => 'ACTIVE_ORDER_EXISTS',
                'message' => 'You already have an active order. Please wait until your current order is completed before creating a new order.',
            ], 422);
        }

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

        broadcast(new NewOrderCreated($serviceRequest))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Towing request created. Waiting for a provider to accept.',
            'data' => $serviceRequest->load('driver'),
        ], 201);
    }

    /**
     * GET /driver/requests/unrated
     * Returns the latest completed order that hasn't been rated by the customer.
     */
    public function unrated(): JsonResponse
    {
        $order = auth()->user()
            ->serviceRequests()
            ->where('status', 'completed')
            ->whereDoesntHave('rating')
            ->with('provider.user')
            ->latest('completed_at')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function show(ServiceRequest $request): JsonResponse
    {
        if ($request->driver_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $request->load(['driver', 'provider.user', 'rating']),
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
            'status' => 'cancelled',
            'cancelled_by' => 'driver',
            'cancelled_at' => now(),
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

        broadcast(new OrderCancelled($request))->toOthers();

        return response()->json(['success' => true, 'message' => 'Request cancelled successfully.']);
    }
}
