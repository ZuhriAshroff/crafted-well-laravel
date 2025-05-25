<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return $this->unauthorized($request, 'Authentication required');
        }

        $user = auth()->user();

        // Check if user account is active
        if (!$user->isActive()) {
            auth()->logout();
            return $this->unauthorized($request, 'Account is inactive');
        }

        // Check if user has admin role
        if (!$user->isAdmin()) {
            return $this->unauthorized($request, 'Admin access required');
        }

        return $next($request);
    }

    /**
     * Handle unauthorized access
     */
    private function unauthorized(Request $request, string $message): Response
    {
        // For API requests, return JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $message
            ], 403);
        }

        // For web requests, redirect or abort
        if ($request->is('admin/*')) {
            return redirect('/login')->with('error', $message);
        }

        abort(403, $message);
    }
}