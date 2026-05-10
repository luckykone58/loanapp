@extends('admin.layouts.guest')

@section('title', 'Login')

@section('content')
<form method="POST" action="{{ url('/admin/login') }}">
	@csrf
	<div class="mb-4">
		<label class="block mb-1 text-[#459699]" for="username">Username</label>
		<div class="relative">
			<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
					<circle cx="12" cy="7" r="4"/>
				</svg>
			</span>
			<input id="username" name="username" type="text" required autofocus
				class="w-full border rounded pl-10 pr-3 py-2 text-[#459699] focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent"
				value="{{ old('username') }}" />
		</div>
		@error('username')
			<p class="text-red-600 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<div class="mb-4">
		<label class="block mb-1 text-[#459699]" for="password">Password</label>
		<div class="relative">
			<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
					<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
				</svg>
			</span>
			<input id="password" name="password" type="password" required
				class="w-full border rounded pl-10 pr-3 py-2 text-[#459699] focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" />
		</div>
		@error('password')
			<p class="text-red-600 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<div class="mb-4">
		<label class="inline-flex items-center">
			<input type="checkbox" name="remember" class="rounded border-slate-300 text-[#459699] focus:ring-[#459699] focus:outline-none" />
			<span class="ml-2 text-[#459699]">Remember me</span>
		</label>
	</div>

	<button id="login-submit" type="submit" class="px-4 py-2 bg-[#459699] hover:bg-[#3d8587] text-white rounded-lg w-full inline-flex items-center justify-center gap-2 transition">
		<svg id="login-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
			<polyline points="10 17 15 12 10 7"/>
			<line x1="15" y1="12" x2="3" y2="12"/>
		</svg>
		<svg id="login-spinner" class="h-5 w-5 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
			<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
			<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
		</svg>
		<span id="login-text">Log in</span>
	</button>
</form>
<script>
(function(){
	var form = document.querySelector('form[action$="/admin/login"]') || document.querySelector('form');
	if(!form) return;
	var btn = document.getElementById('login-submit');
	var icon = document.getElementById('login-icon');
	var spinner = document.getElementById('login-spinner');
	var text = document.getElementById('login-text');
	form.addEventListener('submit', function(){
		if (spinner) spinner.classList.remove('hidden');
		if (icon) icon.classList.add('hidden');
		if (text) text.textContent = 'Signing in…';
		if (btn) {
			btn.disabled = true;
			btn.classList.add('opacity-60','cursor-not-allowed');
		}
	}, { once: true });
})();
</script>
@endsection



