<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isActive()) {
            auth()->logout();
            
            // For API requests, return JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Your account is inactive'
                ], 403);
            }

            // For web requests, redirect to login
            return redirect('/login')->with('error', 'Your account is inactive');
        }

        return $next($request);
    }
}