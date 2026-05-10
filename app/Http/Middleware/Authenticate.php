<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // If an admin is already authenticated, always send them to the admin dashboard
        if (Auth::guard('admin')->check()) {
            return route('admin.dashboard', absolute: false);
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return '/admin/login';
        }

        return route('login');
    }
}




