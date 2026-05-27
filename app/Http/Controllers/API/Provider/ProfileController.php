<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
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

        $profile->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude,
            'location_updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Location updated.']);
    }
}
