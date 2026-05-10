<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Notification;
use App\Support\Settings;
use Carbon\Carbon;
use App\Services\IpLocationService;
use App\Support\ActivityLog;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('users', 'username')
                    ->where(function ($q) {
                        return $q->where('domain_id', app()->bound('currentDomain') ? app('currentDomain')->id : null);
                    }),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $name = $request->input('name', $request->username);
        $ipLocation = IpLocationService::buildIpLocationLabel($request->ip());

        $user = User::create([
            'name' => $name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'ip_location' => $ipLocation,
        ]);

        event(new Registered($user));

        // Create Welcome Notification for the new user
        try {
            $welcomeTitle = (string) Settings::get('welcome_title', 'Welcome to '.config('app.name'));
            $welcomeMessage = (string) Settings::get('welcome_message', 'Your account has been created successfully.');
            $welcomeSub = (string) Settings::get('welcome_sub_message', 'Explore features and complete your profile to get started.');
            Notification::create([
                'user_id' => $user->id,
                'title' => $welcomeTitle,
                'message' => $welcomeMessage,
                'subtext' => $welcomeSub,
                'notes' => '',
                'type' => 'welcome',
                'status' => 'unread',
                'created_date' => Carbon::now()->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Failed to create welcome notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        Auth::login($user);

        try {
            ActivityLog::forUser($user, 'registered', [
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        return redirect(route('home', absolute: false));
    }
}
