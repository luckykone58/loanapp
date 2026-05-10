<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProfileGate
{
    /**
     * Handle an incoming request.
     * Enforces the rule: User MUST complete profile info before accessing loan application.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Check if user_info exists
        if (!$user->info) {
            // Redirect to profile creation/edit with an error flash message
            return redirect()->route('profile.edit')->with('error', 'Your profile must be completed before applying for a loan.');
        }

        $userInfo = $user->info;

        // 2. Check for required fields (Customize this list based on what is strictly required)
        $requiredFields = [
            'full_name',
            'id_card_number',
            'id_card_front',
            'id_card_back',
            'id_card_selfie',
            'address',
            'bank_number',
        ];

        foreach ($requiredFields as $field) {
            // Check if the field is null or an empty string
            if (empty($userInfo->$field)) {
                return redirect()->route('profile.edit')->with('error', 'Please complete all required identity and bank information before applying for a loan.');
            }
        }

        return $next($request);
    }
}