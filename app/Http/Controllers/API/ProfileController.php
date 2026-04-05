<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * Returns the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status,
        ];

        if ($user->role === 'provider') {
            $data['provider_profile'] = $user->providerProfile?->load('documents');
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * PATCH /api/profile
     * Update name or phone.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $request->user()->fresh(),
        ]);
    }
}
