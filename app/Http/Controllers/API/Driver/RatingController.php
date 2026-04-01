<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\RatingRequest;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class RatingController extends Controller
{
    /**
     * Submit rating for a completed request
     */
    public function store(RatingRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        if ($serviceRequest->driver_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.',
            ], 404);
        }

        if (! $serviceRequest->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only rate a completed service.',
            ], 422);
        }

        if ($serviceRequest->rating()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You have already rated this service.',
            ], 422);
        }

        $rating = $serviceRequest->rating()->create([
            'rating'  => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update provider average_rating and total_ratings
        $provider = $serviceRequest->provider;

        if ($provider) {
            $newTotal   = $provider->total_ratings + 1;
            $newAverage = (($provider->average_rating * $provider->total_ratings) + $request->rating) / $newTotal;

            $provider->update([
                'total_ratings'  => $newTotal,
                'average_rating' => round($newAverage, 2),
            ]);

            // Notify provider
            $this->sendNotification(
                $provider->user,
                'New Rating Received',
                "You received a {$request->rating}-star rating.",
                ['request_id' => $serviceRequest->id, 'rating' => $request->rating]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully.',
            'data'    => $rating,
        ], 201);
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