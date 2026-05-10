<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
	public function handle(Request $request, Closure $next): Response
	{
		$user = Auth::guard('admin')->user();
		if (!$user || $user->role !== 'SuperAdmin') {
			abort(403, 'Only SuperAdmin can access this area.');
		}
		return $next($request);
	}
}



