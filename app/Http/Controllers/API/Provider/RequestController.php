<?php

namespace App\Http\Controllers\API\Provider;

use App\Events\OrderCancelled;
use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\UpdateStatusRequest;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    /** Max distance (km) a pending request can be from the provider. */
    private const DISPATCH_RADIUS_KM = 30;

    public function index(): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        $query = ServiceRequest::where('status', 'pending')
            ->where('service_type', $provider->service_type)
            // Hide requests this provider already rejected
            ->whereDoesntHave('rejections', fn ($q) => $q->where('provider_id', $provider->id))
            ->with('driver');

        // Radius filter + nearest-first ordering (Haversine, km).
        // Only applied when the provider has a known location.
        // (SQL math functions require MySQL/MariaDB — skipped on SQLite dev.)
        $supportsHaversine = in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb']);

        if ($supportsHaversine && $provider->current_latitude !== null && $provider->current_longitude !== null) {
            $haversine = '(6371 * acos(least(1.0, cos(radians(?)) * cos(radians(driver_latitude)) '
                .'* cos(radians(driver_longitude) - radians(?)) '
                .'+ sin(radians(?)) * sin(radians(driver_latitude)))))';

            $query->select('*')
                ->selectRaw("{$haversine} AS distance_km", [
                    $provider->current_latitude,
                    $provider->current_longitude,
                    $provider->current_latitude,
                ])
                ->whereRaw("{$haversine} <= ?", [
                    $provider->current_latitude,
                    $provider->current_longitude,
                    $provider->current_latitude,
                    self::DISPATCH_RADIUS_KM,
                ])
                ->orderBy('distance_km');
        } else {
            $query->latest();
        }

        $requests = $query->paginate(15);

        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function show(ServiceRequest $request): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if ($request->provider_id !== $provider->id) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        $request->load(['driver', 'rating']);

        return response()->json([
            'success' => true,
            'data' => $request,
        ]);
    }

    public function accept(ServiceRequest $request): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if (! $provider->isApproved()) {
            return response()->json(['success' => false, 'message' => 'Your account is not verified yet.'], 403);
        }

        if (! $provider->is_available) {
            return response()->json(['success' => false, 'message' => 'You are currently set as unavailable.'], 422);
        }

        if ($request->service_type !== $provider->service_type) {
            return response()->json(['success' => false, 'message' => 'This request does not match your service type.'], 422);
        }

        $hasActive = ServiceRequest::where('provider_id', $provider->id)
            ->whereIn('status', ['accepted', 'en_route', 'arrived', 'in_progress'])
            ->exists();

        if ($hasActive) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active request in progress.',
            ], 422);
        }

        // ── Atomic accept: conditional UPDATE prevents two providers
        //    claiming the same request simultaneously (race condition). ──
        $claimed = DB::transaction(function () use ($request, $provider) {
            $affected = ServiceRequest::where('id', $request->id)
                ->where('status', 'pending')
                ->whereNull('provider_id')
                ->update([
                    'provider_id' => $provider->id,
                    'status' => 'accepted',
                    'accepted_at' => now(),
                ]);

            if ($affected === 0) {
                return false;
            }

            // ── Auto-status: save previous availability, force active ──
            $provider->update([
                'was_available_before_order' => $provider->is_available,
                'is_available' => true,
            ]);

            return true;
        });

        if (! $claimed) {
            return response()->json(['success' => false, 'message' => 'This request is no longer available.'], 422);
        }

        $request->refresh();

        NotificationService::send(
            $request->driver,
            'Request Accepted',
            'A provider has accepted your request and is on the way.',
            ['request_id' => $request->id]
        );

        broadcast(new OrderStatusUpdated($request))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Request accepted successfully.',
            'data' => $request->load(['driver', 'provider.user']),
        ]);
    }

    public function getActive(): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        $activeRequest = ServiceRequest::where('provider_id', $provider->id)
            ->whereIn('status', ['accepted', 'en_route', 'arrived', 'in_progress'])
            ->with(['driver', 'provider.user'])
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => $activeRequest,
        ]);
    }

    public function history(): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        $requests = ServiceRequest::where('provider_id', $provider->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['driver', 'rating'])
            ->latest()
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function rateCustomer(ServiceRequest $serviceRequest): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if ($serviceRequest->provider_id !== $provider->id) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        if (! $serviceRequest->isCompleted()) {
            return response()->json(['success' => false, 'message' => 'Can only rate completed requests.'], 422);
        }

        request()->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $rating = $serviceRequest->rating()->updateOrCreate(
            ['request_id' => $serviceRequest->id],
            [
                'rating' => request('rating'),
                'comment' => request('comment'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully.',
            'data' => $rating,
        ]);
    }

    public function reject(ServiceRequest $request): JsonResponse
    {
        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Only pending requests can be rejected.'], 422);
        }

        $provider = auth()->user()->providerProfile;

        // ── Per-provider rejection: the request stays PENDING so other
        //    providers can still accept it. It is only hidden from
        //    this provider's pending list. ──
        $request->rejections()->firstOrCreate(['provider_id' => $provider->id]);

        return response()->json(['success' => true, 'message' => 'Request rejected successfully.']);
    }

    public function cancel(ServiceRequest $request): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if ($request->provider_id !== $provider->id) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        $cancellableStatuses = ['accepted', 'en_route', 'arrived', 'in_progress'];
        if (! in_array($request->status, $cancellableStatuses)) {
            return response()->json(['success' => false, 'message' => 'Only active requests can be cancelled.'], 422);
        }

        $reason = request('reason', 'تم الإلغاء من قبل مزود الخدمة');

        $request->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => 'provider',
            'cancellation_reason' => $reason,
        ]);

        // ── Auto-status: restore previous availability ──
        $provider->update([
            'is_available' => $provider->was_available_before_order,
        ]);

        NotificationService::send(
            $request->driver,
            'Request Cancelled',
            'The provider has cancelled your request. We are looking for another provider.',
            ['request_id' => $request->id, 'status' => 'cancelled']
        );

        broadcast(new OrderCancelled($request))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled successfully.',
        ]);
    }

    public function updateStatus(UpdateStatusRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if ($serviceRequest->provider_id !== $provider->id) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        $newStatus = $request->status;
        $timestamps = [
            'arrived' => ['arrived_at' => now()],
            'completed' => ['completed_at' => now()],
        ];

        $serviceRequest->update(array_merge(
            ['status' => $newStatus],
            $timestamps[$newStatus] ?? []
        ));

        // ── Auto-status: restore previous availability on completion ──
        if ($newStatus === 'completed') {
            $provider->update([
                'is_available' => $provider->was_available_before_order,
            ]);
        }

        $messages = [
            'en_route' => ['title' => 'Provider En Route',   'body' => 'Your provider is on the way.'],
            'arrived' => ['title' => 'Provider Arrived',    'body' => 'Your provider has arrived at your location.'],
            'in_progress' => ['title' => 'Service In Progress', 'body' => 'Your service is now in progress.'],
            'completed' => ['title' => 'Service Completed',   'body' => 'Service complete. Please rate your experience.'],
        ];

        if (isset($messages[$newStatus])) {
            NotificationService::send(
                $serviceRequest->driver,
                $messages[$newStatus]['title'],
                $messages[$newStatus]['body'],
                ['request_id' => $serviceRequest->id, 'status' => $newStatus]
            );
        }

        broadcast(new OrderStatusUpdated($serviceRequest))->toOthers();

        return response()->json([
            'success' => true,
            'message' => "Status updated to '{$newStatus}' successfully.",
            'data' => $serviceRequest->fresh(['driver', 'provider']),
        ]);
    }
}
