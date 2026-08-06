<?php

namespace App\Http\Controllers\API\Provider;

use App\Events\ProviderLocationUpdated;
use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $user = auth()->user();
        $profile = $user->providerProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Provider profile not found.'], 404);
        }

        $profile->load('documents');

        $completedRequests = $profile->serviceRequests()->where('status', 'completed');
        $completedOrders = (clone $completedRequests)->count();
        $totalIncome = (clone $completedRequests)->sum('total');

        // Calculate ratings live from the ratings table (raw query to avoid GROUP BY issue)
        $ratingStats = DB::table('ratings')
            ->join('service_requests', 'service_requests.id', '=', 'ratings.request_id')
            ->where('service_requests.provider_id', $profile->id)
            ->selectRaw('ROUND(AVG(ratings.rating), 1) as avg_rating, COUNT(*) as total_ratings')
            ->first();

        $averageRating = (float) ($ratingStats->avg_rating ?? $profile->average_rating ?? 0);
        $totalRatings = (int) ($ratingStats->total_ratings ?? $profile->total_ratings ?? 0);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $profile->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
                'service_type' => $profile->service_type,
                'vehicle_make' => $profile->vehicle_make,
                'vehicle_model' => $profile->vehicle_model,
                'vehicle_year' => $profile->vehicle_year,
                'vehicle_plate' => $profile->vehicle_plate,
                'vehicle_color' => $profile->vehicle_color,
                'is_available' => $profile->is_available,
                'verification_status' => $profile->verification_status,
                'average_rating' => $averageRating,
                'total_ratings' => $totalRatings,
                'completed_orders' => $completedOrders,
                'total_income' => round($totalIncome, 2),
                'created_at' => $profile->created_at,
                'documents' => $profile->documents->map(fn ($doc) => [
                    'id' => $doc->id,
                    'type' => $doc->document_type,
                    'path' => $doc->document_path,
                    'url' => $doc->document_url,
                ]),
            ],
        ]);
    }

    public function updateAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'is_available' => ['required', 'boolean'],
        ]);

        $profile = auth()->user()->providerProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Provider profile not found.'], 404);
        }

        $profile->update(['is_available' => $request->is_available]);

        return response()->json([
            'success' => true,
            'message' => 'Availability updated to '.($request->is_available ? 'Online' : 'Offline').'.',
            'data' => ['is_available' => $profile->is_available],
        ]);
    }

    public function updateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $profile = auth()->user()->providerProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Provider profile not found.'], 404);
        }

        // Distance since last stored position (metres) — used to throttle
        // both the DB write and the WebSocket broadcast.
        $movedMetres = $this->distanceMetres(
            $profile->current_latitude,
            $profile->current_longitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        $staleSeconds = $profile->location_updated_at
            ? abs($profile->location_updated_at->diffInSeconds(now()))
            : PHP_INT_MAX;

        // Ignore GPS jitter: skip if moved < 15 m and updated < 30 s ago
        if ($movedMetres !== null && $movedMetres < 15 && $staleSeconds < 30) {
            return response()->json(['success' => true, 'message' => 'Location unchanged.']);
        }

        $profile->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude,
            'location_updated_at' => now(),
        ]);

        // Push the new position to the customer over Reverb (WebSocket)
        // when this provider has an active order.
        $activeOrder = ServiceRequest::where('provider_id', $profile->id)
            ->whereIn('status', ['accepted', 'en_route', 'arrived', 'in_progress'])
            ->latest()
            ->first();

        if ($activeOrder) {
            broadcast(new ProviderLocationUpdated(
                $activeOrder,
                (float) $request->latitude,
                (float) $request->longitude,
            ));
        }

        return response()->json(['success' => true, 'message' => 'Location updated.']);
    }

    /**
     * Haversine distance in metres. Null when there is no previous position.
     */
    private function distanceMetres(?float $lat1, ?float $lng1, float $lat2, float $lng2): ?float
    {
        if ($lat1 === null || $lng1 === null) {
            return null;
        }

        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
