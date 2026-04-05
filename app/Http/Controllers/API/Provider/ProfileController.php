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

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $user->id,
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
}