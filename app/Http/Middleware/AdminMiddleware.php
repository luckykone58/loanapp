<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Checks if the authenticated user has the 'admin' role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if admin guard is authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }

        $user = Auth::guard('admin')->user();

        // 2. Check if user has an admin-capable role
        if (!in_array($user->role, ['admin', 'SuperAdmin'], true)) {
            // Deny access and redirect, or abort with a 403 Forbidden
            return response()->view('errors.403', ['message' => 'Unauthorized access. You do not have administrator privileges.'], 403);
        }

        return $next($request);
    }
}