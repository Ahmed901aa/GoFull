<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    /**
     * POST /api/auth/otp/send
     * Sends a 6-digit code via SMS (iSend).
     * Rate-limited by route middleware + OtpService's own anti-spam window.
     */
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'purpose' => ['nullable', 'in:registration,password_reset'],
        ]);

        $purpose = $data['purpose'] ?? 'registration';

        // Registration: phone must NOT exist yet. Password reset: it must.
        $exists = User::where('phone', $data['phone'])->exists();

        if ($purpose === 'registration' && $exists) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is already registered.',
            ], 422);
        }

        if ($purpose === 'password_reset' && ! $exists) {
            return response()->json([
                'success' => false,
                'message' => 'No account found for this phone number.',
            ], 404);
        }

        $result = OtpService::send($data['phone'], $purpose);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * POST /api/auth/otp/verify
     * Standalone verification (used by the password-reset flow).
     * Registration verifies inline via /auth/register instead.
     */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'digits:6'],
            'purpose' => ['nullable', 'in:registration,password_reset'],
        ]);

        $result = OtpService::verify($data['phone'], $data['code'], $data['purpose'] ?? 'registration');

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
