<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
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