<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProviderApproved
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user    = auth()->user();
        $profile = $user?->providerProfile;

        if (! $user || ! $user->isProvider()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if (! $profile || ! $profile->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is pending verification. Please wait for admin approval.',
            ], 403);
        }

        return $next($request);
    }
}