<?php

namespace App\Http\Controllers\API\Driver;

use App\Events\ProviderRated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\RatingRequest;
use App\Models\ServiceRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class RatingController extends Controller
{
    public function store(RatingRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        if ($serviceRequest->driver_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
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
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $provider = $serviceRequest->provider;

        if ($provider) {
            $newTotal = $provider->total_ratings + 1;
            $newAverage = (($provider->average_rating * $provider->total_ratings) + $request->rating) / $newTotal;

            $provider->update([
                'total_ratings' => $newTotal,
                'average_rating' => round($newAverage, 2),
            ]);

            NotificationService::send(
                $provider->user,
                'New Rating Received',
                "You received a {$request->rating}-star rating.",
                ['request_id' => $serviceRequest->id, 'rating' => $request->rating]
            );

            broadcast(new ProviderRated($provider, $rating))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully.',
            'data' => $rating,
        ], 201);
    }
}
