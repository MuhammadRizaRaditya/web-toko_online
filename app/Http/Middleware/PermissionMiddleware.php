<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (! Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated'], 401)
                : redirect()->guest(route('login'));
        }

        $user = Auth::user();

        if ($user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, 'Insufficient permission: requires '.implode(', ', $permissions));
    }
}
