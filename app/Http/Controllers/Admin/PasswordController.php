<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Support\ActivityLog;

class PasswordController extends Controller
{
	public function edit()
	{
		return view('admin.auth.change-password');
	}

	public function update(Request $request)
	{
		$request->validate([
			'current_password' => ['required', 'current_password:admin'],
			'password' => ['required', 'confirmed', PasswordRule::defaults()],
		]);

		$user = Auth::guard('admin')->user();
		$user->password = Hash::make($request->password);
		$user->save();

		// Regenerate session to prevent fixation
		$request->session()->regenerate();

		try {
			ActivityLog::forAdmin($user, 'change password', [
				'ip' => $request->ip(),
				'user_agent' => (string) $request->userAgent(),
			]);
		} catch (\Throwable $e) {}

		return back()->with('success', 'Password updated successfully.');
	}
}



