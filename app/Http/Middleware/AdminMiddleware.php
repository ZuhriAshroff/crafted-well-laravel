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
            // For admin routes, redirect directly to admin login (not user login)
            if ($request->is('admin*')) {
                return redirect()->route('admin.login')->with('info', 'Please login to access the admin panel');
            }
            
            // For API requests, return JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required',
                    'redirect' => route('admin.login')
                ], 401);
            }
            
            // Default: redirect to admin login
            return redirect()->route('admin.login')->with('info', 'Please login to access the admin panel');
        }

        $user = auth()->user();

        // Check if user account is active
        if (!$user->isActive()) {
            auth()->logout();
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Account Inactive',
                    'message' => 'Your account is inactive. Please contact support.',
                    'redirect' => route('admin.login')
                ], 403);
            }
            
            return redirect()->route('admin.login')
                ->with('error', 'Your account is inactive. Please contact support.');
        }

        // Check if user has admin role
        if (!$user->isAdmin()) {
            // Don't logout the user - they might be a valid regular user
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Access Denied',
                    'message' => 'Admin access required',
                    'redirect' => route('admin.login')
                ], 403);
            }
            
            // Redirect to admin login with a clear message
            return redirect()->route('admin.login')
                ->with('warning', 'Admin privileges required. Please login with an admin account.');
        }

        return $next($request);
    }
}