<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\User;
use App\Scopes\DomainScope;
use App\Support\ActivityLog;

class AdminAuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        if (Auth::guard('admin')->check()) {
            // Already logged in as admin, go to dashboard
            return redirect()->to(route('admin.dashboard', absolute: false));
        }
        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Multi-tenant: locate admin across domains, then switch domain context
        $user = User::query()
            ->withoutGlobalScope(DomainScope::class)
            ->where('username', $credentials['username'])
            ->whereIn('role', ['admin', 'SuperAdmin'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }
        
        // Enforce domain host match for Admins (SuperAdmin can access any domain)
        $currentDomain = app()->bound('currentDomain') ? app('currentDomain') : null;
        if ($user->role !== 'SuperAdmin') {
            if (!$currentDomain || (int) $user->domain_id !== (int) $currentDomain->id) {
                throw ValidationException::withMessages([
                    'username' => trans('auth.failed'),
                ]);
            }
        }

        // Persist the user's domain to session so DomainScope resolves properly
        if ($user->domain_id) {
            $request->session()->put('domain_id', $user->domain_id);
        }

        Auth::guard('admin')->login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        try {
            ActivityLog::forAdmin($user, 'login', [
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}




