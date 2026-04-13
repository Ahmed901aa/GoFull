<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\UpdateStatusRequest;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class RequestController extends Controller
{
    public function index(): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        $requests = ServiceRequest::where('status', 'pending')
            ->where('service_type', $provider->service_type)
            ->with('driver')
            ->latest()
            ->paginate(15);

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
            'data'    => $request,
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

        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'This request is no longer available.'], 422);
        }

        $request->update([
            'provider_id' => $provider->id,
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);

        NotificationService::send(
            $request->driver,
            'Request Accepted',
            'A provider has accepted your request and is on the way.',
            ['request_id' => $request->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Request accepted successfully.',
            'data'    => $request->load(['driver', 'provider.user']),
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
            'data'    => $activeRequest,
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
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $rating = $serviceRequest->rating()->updateOrCreate(
            ['request_id' => $serviceRequest->id],
            [
                'rating'  => request('rating'),
                'comment' => request('comment'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully.',
            'data'    => $rating,
        ]);
    }

    public function reject(ServiceRequest $request): JsonResponse
    {
        if (! $request->isPending()) {
            return response()->json(['success' => false, 'message' => 'Only pending requests can be rejected.'], 422);
        }

        $request->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => 'provider',
            'cancellation_reason' => 'تم الرفض من قبل مزود الخدمة',
        ]);

        NotificationService::send(
            $request->driver,
            'Request Rejected',
            'A provider has declined your request. We are looking for another provider.',
            ['request_id' => $request->id, 'status' => 'cancelled']
        );

        return response()->json(['success' => true, 'message' => 'Request rejected successfully.']);
    }

    public function updateStatus(UpdateStatusRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if ($serviceRequest->provider_id !== $provider->id) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }

        $newStatus  = $request->status;
        $timestamps = [
            'arrived'   => ['arrived_at'   => now()],
            'completed' => ['completed_at' => now()],
        ];

        $serviceRequest->update(array_merge(
            ['status' => $newStatus],
            $timestamps[$newStatus] ?? []
        ));

        $messages = [
            'en_route'    => ['title' => 'Provider En Route',   'body' => 'Your provider is on the way.'],
            'arrived'     => ['title' => 'Provider Arrived',    'body' => 'Your provider has arrived at your location.'],
            'in_progress' => ['title' => 'Service In Progress', 'body' => 'Your service is now in progress.'],
            'completed'   => ['title' => 'Service Completed',   'body' => 'Service complete. Please rate your experience.'],
        ];

        if (isset($messages[$newStatus])) {
            NotificationService::send(
                $serviceRequest->driver,
                $messages[$newStatus]['title'],
                $messages[$newStatus]['body'],
                ['request_id' => $serviceRequest->id, 'status' => $newStatus]
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Status updated to '{$newStatus}' successfully.",
            'data'    => $serviceRequest->fresh(['driver', 'provider']),
        ]);
    }
}