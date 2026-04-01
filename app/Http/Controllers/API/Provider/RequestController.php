<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\UpdateStatusRequest;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class RequestController extends Controller
{
    /**
     * Get pending requests matching provider's service type
     */
    public function index(): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        $requests = ServiceRequest::where('service_type', $provider->service_type)
            ->where('status', 'pending')
            ->with('driver')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $requests,
        ]);
    }

    /**
     * Accept a request
     */
    public function accept(ServiceRequest $request): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if (! $provider->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not verified yet.',
            ], 403);
        }

        if (! $provider->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'You are currently set as unavailable.',
            ], 422);
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
            return response()->json([
                'success' => false,
                'message' => 'This request is no longer available.',
            ], 422);
        }

        if ($request->service_type !== $provider->service_type) {
            return response()->json([
                'success' => false,
                'message' => 'This request does not match your service type.',
            ], 422);
        }

        $request->update([
            'provider_id' => $provider->id,
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);

        // Notify driver
        $this->sendNotification(
            $request->driver,
            'Request Accepted',
            'A provider has accepted your request and is on the way.',
            ['request_id' => $request->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Request accepted successfully.',
            'data'    => $request->load(['driver', 'provider']),
        ]);
    }

    /**
     * Reject a request — leave it pending for others
     */
    public function reject(ServiceRequest $request): JsonResponse
    {
        if (! $request->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be rejected.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request rejected.',
        ]);
    }

    /**
     * Update request status — fixed chain
     */
    public function updateStatus(UpdateStatusRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $provider = auth()->user()->providerProfile;

        if ($serviceRequest->provider_id !== $provider->id) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.',
            ], 404);
        }

        $newStatus = $request->status;

        $timestamps = [
            'arrived'   => ['arrived_at'   => now()],
            'completed' => ['completed_at' => now()],
        ];

        $serviceRequest->update(array_merge(
            ['status' => $newStatus],
            $timestamps[$newStatus] ?? []
        ));

        // Notification messages per status
        $messages = [
            'en_route'    => ['title' => 'Provider En Route',  'body' => 'Your provider is on the way to your location.'],
            'arrived'     => ['title' => 'Provider Arrived',   'body' => 'Your provider has arrived at your location.'],
            'in_progress' => ['title' => 'Service In Progress','body' => 'Your service is now in progress.'],
            'completed'   => ['title' => 'Service Completed',  'body' => 'Your service is complete. Please rate your experience.'],
        ];

        if (isset($messages[$newStatus])) {
            $this->sendNotification(
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

    // ==========================================
    // Private Helper
    // ==========================================

    private function sendNotification(User $user, string $title, string $body, array $data = []): void
    {
        Notification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
        ]);

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