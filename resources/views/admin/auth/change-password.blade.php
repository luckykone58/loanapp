@extends('admin.layouts.app')

@section('title', 'Change Password')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Change Password</li>
@endsection

@section('content')
<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 max-w-xl form-surface">
	@if(session('success'))
		<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
	@endif
	<form method="POST" action="{{ route('admin.password.update') }}">
		@csrf
		@method('PUT')
		<div class="mb-4">
			<label class="block mb-1" for="current_password">Current Password</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
						<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
					</svg>
				</span>
				<input id="current_password" name="current_password" type="password" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required />
			</div>
			@error('current_password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="mb-4">
			<label class="block mb-1" for="password">New Password</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
						<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
					</svg>
				</span>
				<input id="password" name="password" type="password" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required />
			</div>
			@error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="mb-4">
			<label class="block mb-1" for="password_confirmation">Confirm New Password</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
						<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
					</svg>
				</span>
				<input id="password_confirmation" name="password_confirmation" type="password" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required />
			</div>
		</div>
		<div class="flex gap-2">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="save" class="h-4 w-4"></i><span>Update Password</span>
			</button>
			<a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-cancel">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Cancel</span>
			</a>
		</div>
	</form>
	</div>
@endsection


