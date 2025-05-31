<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        if (!$user->isActive()) {
            auth()->logout();
            return response()->json(['error' => 'Account is inactive'], 403);
        }

        return $next($request);
    }
}