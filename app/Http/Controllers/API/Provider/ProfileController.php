<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $completedOrders = $profile->serviceRequests()->where('status', 'completed')->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $profile->id,
                'user_id'             => $user->id,
                'name'                => $user->name,
                'phone'               => $user->phone,
                'role'                => $user->role,
                'service_type'        => $profile->service_type,
                'vehicle_make'        => $profile->vehicle_make,
                'vehicle_model'       => $profile->vehicle_model,
                'vehicle_year'        => $profile->vehicle_year,
                'vehicle_plate'       => $profile->vehicle_plate,
                'vehicle_color'       => $profile->vehicle_color,
                'is_available'        => $profile->is_available,
                'verification_status' => $profile->verification_status,
                'average_rating'      => $profile->average_rating,
                'total_ratings'       => $profile->total_ratings,
                'completed_orders'    => $completedOrders,
                'created_at'          => $profile->created_at,
                'documents'           => $profile->documents->map(fn ($doc) => [
                    'id'   => $doc->id,
                    'type' => $doc->document_type,
                    'path' => $doc->document_path,
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
            'message' => 'Availability updated to ' . ($request->is_available ? 'Online' : 'Offline') . '.',
            'data'    => ['is_available' => $profile->is_available],
        ]);
    }

    public function updateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'latitude'  => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $profile = auth()->user()->providerProfile;

        if (! $profile) {
            return response()->json(['success' => false, 'message' => 'Provider profile not found.'], 404);
        }

        $profile->update([
            'current_latitude'    => $request->latitude,
            'current_longitude'   => $request->longitude,
            'location_updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Location updated.']);
    }
}