<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\FuelDeliveryRequest;
use App\Http\Requests\Driver\TowingRequest;
use App\Models\Notification;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ServiceRequestController extends Controller
{
    /**
     * Get driver's request history
     */
    public function index(): JsonResponse
    {
        $requests = auth()->user()
            ->serviceRequests()
            ->with(['provider.user', 'rating'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $requests,
        ]);
    }

    /**
     * Create fuel delivery request
     */
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

        $serviceRequest = ServiceRequest::create([
            'driver_id'        => auth()->id(),
            'service_type'     => 'fuel_delivery',
            'status'           => 'pending',
            'driver_latitude'  => $data['driver_latitude'],
            'driver_longitude' => $data['driver_longitude'],
            'driver_address'   => $data['driver_address'] ?? null,
            'fuel_type'        => $data['fuel_type'],
            'fuel_quantity'    => $data['fuel_quantity'],
            'notes'            => $data['notes'] ?? null,
        ]);

        // Notify all available approved fuel providers
        $providers = ProviderProfile::where('service_type', 'fuel_delivery')
            ->where('verification_status', 'approved')
            ->where('is_available', true)
            ->with('user')
            ->get();

        foreach ($providers as $provider) {
            $this->sendNotification(
                $provider->user,
                'New Fuel Delivery Request',
                'A new fuel delivery request is available near you.',
                ['request_id' => $serviceRequest->id, 'type' => 'fuel_delivery']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Fuel delivery request created. Waiting for a provider to accept.',
            'data'    => $serviceRequest->load('driver'),
        ], 201);
    }

    /**
     * Create towing request
     */
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

        $serviceRequest = ServiceRequest::create([
            'driver_id'        => auth()->id(),
            'service_type'     => 'towing',
            'status'           => 'pending',
            'driver_latitude'  => $data['driver_latitude'],
            'driver_longitude' => $data['driver_longitude'],
            'driver_address'   => $data['driver_address'] ?? null,
            'plate_number'     => $data['plate_number'],
            'notes'            => $data['notes'] ?? null,
        ]);

        // Notify all available approved towing providers
        $providers = ProviderProfile::where('service_type', 'towing')
            ->where('verification_status', 'approved')
            ->where('is_available', true)
            ->with('user')
            ->get();

        foreach ($providers as $provider) {
            $this->sendNotification(
                $provider->user,
                'New Towing Request',
                'A new towing request is available near you.',
                ['request_id' => $serviceRequest->id, 'type' => 'towing']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Towing request created. Waiting for a provider to accept.',
            'data'    => $serviceRequest->load('driver'),
        ], 201);
    }

    /**
     * Get single request details
     */
    public function show(ServiceRequest $request): JsonResponse
    {
        if ($request->driver_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $request->load(['driver', 'provider.user', 'rating']),
        ]);
    }

    /**
     * Cancel a request
     */
    public function cancel(ServiceRequest $request): JsonResponse
    {
        if ($request->driver_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.',
            ], 404);
        }

        if (! $request->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Only active requests can be cancelled.',
            ], 422);
        }

        $request->update([
            'status'              => 'cancelled',
            'cancelled_by'        => 'driver',
            'cancelled_at'        => now(),
            'cancellation_reason' => 'Cancelled by driver.',
        ]);

        // Notify provider if one was already assigned
        if ($request->provider_id) {
            $this->sendNotification(
                $request->provider->user,
                'Request Cancelled',
                'The driver has cancelled the request.',
                ['request_id' => $request->id]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled successfully.',
        ]);
    }

    // ==========================================
    // Private Helper
    // ==========================================

    /**
     * Save notification to DB and send FCM push
     */
    private function sendNotification(User $user, string $title, string $body, array $data = []): void
    {
        // 1. Save to database
        Notification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
        ]);

        // 2. Send FCM push if token exists
        if ($user->fcm_token) {
            Http::withHeaders([
                'Authorization' => 'key=' . config('services.fcm.server_key'),
                'Content-Type'  => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to'           => $user->fcm_token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
            ]);
        }
    }
}