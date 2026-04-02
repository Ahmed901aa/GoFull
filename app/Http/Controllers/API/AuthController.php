<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ProviderDocument;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'status'   => 'active',
        ]);

        if ($data['role'] === 'provider') {
            $profile = ProviderProfile::create([
                'user_id'       => $user->id,
                'service_type'  => $data['service_type'],
                'vehicle_make'  => $data['vehicle_make'],
                'vehicle_model' => $data['vehicle_model'],
                'vehicle_year'  => $data['vehicle_year'],
                'vehicle_plate' => $data['vehicle_plate'],
                'vehicle_color' => $data['vehicle_color'] ?? null,
            ]);

            foreach ($data['documents'] as $doc) {
                $path = $doc['file']->store('documents', 'public');
                ProviderDocument::create([
                    'provider_id'   => $profile->id,
                    'document_type' => $doc['type'],
                    'document_path' => $path,
                ]);
            }
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'phone'  => $user->phone,
                    'role'   => $user->role,
                    'status' => $user->status,
                ],
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number or password.',
            ], 401);
        }

        if (! $user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been suspended. Please contact support.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        $responseData = [
            'token' => $token,
            'user'  => [
                'id'     => $user->id,
                'name'   => $user->name,
                'phone'  => $user->phone,
                'role'   => $user->role,
                'status' => $user->status,
            ],
        ];

        if ($user->isProvider()) {
            $responseData['provider_profile'] = $user->providerProfile;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => $responseData,
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    // ─── Change Password (مسجّل دخول) ─────────────────────────────
public function changePassword(ChangePasswordRequest $request): JsonResponse
{
    $user = auth()->user();

    if (! Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Current password is incorrect.',
        ], 422);
    }

    if (Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'New password must be different from current password.',
        ], 422);
    }

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    // إلغاء كل الـ tokens القديمة وإنشاء token جديد
    $user->tokens()->delete();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Password changed successfully.',
        'data'    => ['token' => $token],
    ]);
}

}