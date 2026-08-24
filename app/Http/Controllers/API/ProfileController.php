<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Rating;
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
            $profile = $user->providerProfile;
            $data['provider_profile'] = $profile?->load('documents');

            if ($profile) {
                $completedOrders = $profile->serviceRequests()->where('status', 'completed')->count();

                // حساب متوسط التقييم الذي حصل عليه مزود الخدمة من السائقين
                $ratingStats = Rating::where('rated_by', 'driver')->whereHas('serviceRequest', function ($q) use ($profile) {
                    $q->where('provider_id', $profile->id)
                        ->where('status', 'completed');
                })->selectRaw('ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as total_ratings')
                    ->first();

                $data['completed_orders'] = $completedOrders;
                $data['average_rating'] = $ratingStats->avg_rating ?? 0;
                $data['total_ratings'] = $ratingStats->total_ratings ?? 0;
                $data['service_type'] = $profile->service_type;
                $data['vehicle_make'] = $profile->vehicle_make;
                $data['vehicle_model'] = $profile->vehicle_model;
                $data['vehicle_plate'] = $profile->vehicle_plate;
                $data['is_available'] = $profile->is_available;
                $data['verification_status'] = $profile->verification_status;
            }
        }

        if ($user->role === 'driver') {
            $completedOrders = $user->serviceRequests()->where('status', 'completed')->count();

            // حساب متوسط التقييم الذي حصل عليه السائق من مزودي الخدمة
            $ratingStats = Rating::where('rated_by', 'provider')->whereHas('serviceRequest', function ($q) use ($user) {
                $q->where('driver_id', $user->id)
                    ->where('status', 'completed');
            })->selectRaw('ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as total_ratings')
                ->first();

            $data['completed_orders'] = $completedOrders;
            $data['average_rating'] = $ratingStats->avg_rating ?? 0;
            $data['total_ratings'] = $ratingStats->total_ratings ?? 0;
            $data['vehicle'] = $user->vehicle;
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
            'phone' => 'sometimes|string|unique:users,phone,'.$request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $request->user()->fresh(),
        ]);
    }
}
