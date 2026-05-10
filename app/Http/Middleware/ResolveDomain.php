<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResolveDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        // Resolve by domain_id preference (no host matching)
        $domainId = null;
        if (Auth::guard('admin')->check()) {
            $domainId = Auth::guard('admin')->user()->domain_id;
        } elseif (Auth::check()) {
            $domainId = Auth::user()->domain_id;
        } 

        $domain = null;
        if ($domainId) {
            $domain = Domain::find($domainId);
        }

        if (!$domain) {
            // Resolve by real host from the incoming request
            $host = $request->getHost();
            $domain = Domain::query()
                ->where('host', $host)
                ->where('status', 'Active')
                ->first();
            if (!$domain) {
                return response()->view('404', [], 404);
            }
            // Persist selected domain_id to session for guest flows
            $request->session()->put('domain_id', $domain->id);
        }

        if ($domain->status !== 'Active') {
            return response()->view('404', [], 404);
        }

        app()->instance('currentDomain', $domain);
        // Also share to views
        view()->share('currentDomain', $domain);

        return $next($request);
    }
}


