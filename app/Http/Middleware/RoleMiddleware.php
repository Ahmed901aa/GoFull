<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (! auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401)
                : redirect()->route('admin.login');
        }

        if (! in_array(auth()->user()->role, $roles)) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthorized.'], 403)
                : abort(403);
        }

        return $next($request);
    }
}