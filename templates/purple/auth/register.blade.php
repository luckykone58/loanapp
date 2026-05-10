<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ __('register.title.title', ['domain' => ($currentDomain->name ?? config('app.name'))]) }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
					colors: {
						brand: {
							50: '#f5f3ff',
							100: '#ede9fe',
							500: '#8b5cf6',
							600: '#7c3aed',
							700: '#6d28d9',
							800: '#5b21b6',
							900: '#4c1d95',
						},
						// Back-compat for existing classes below
						'primary-teal': '#7c3aed',
						'primary-dark': '#6d28d9',
						'bg-main': '#F9FAFB',
					}
				}
			}
		}
	</script>
	<style>
		body { font-family: 'Inter', ui-sans-serif, system-ui; }
	</style>
</head>
<body class="min-h-screen flex items-center justify-center p-0 sm:p-6 bg-gradient-to-br from-blue-800 via-brand-700 to-brand-500 px-2 py-2">
	<div class="w-full min-h-screen bg-white p-8 rounded-3xl border border-gray-200 shadow-xl sm:p-10  sm:min-h-0 sm:max-w-md sm:rounded-3xl sm:border sm:border-gray-200 sm:shadow-xl">
        @php($showSignupLogo = \App\Support\Settings::get('show_logo_signup', '1'))
		@php($logoPath = \App\Support\Settings::get('logo_path'))
		@php($logoUrl = $logoPath ? asset('storage/'.ltrim($logoPath, '/')) : null)
		@if($showSignupLogo === '1')
		<div class="text-center mb-10 mt-12 sm:mt-0">
			@if($logoUrl)
				<img src="{{ $logoUrl }}" alt="{{ $currentDomain->name ?? config('app.name') }}" class="h-12 mx-auto object-contain">
			@else
				<svg class="w-12 h-12 text-primary-teal mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
				</svg>
				<h1 class="text-3xl font-extrabold text-gray-900 mt-4 tracking-tight">{{ __('register.title.heading') }}</h1>
			@endif
            <p class="text-sm text-gray-500 mt-1">{{ __('register.text.subtitle') }}</p>
		</div>
		@else
		<div class="text-center mb-10 mt-12 sm:mt-0">
			<svg class="w-12 h-12 text-primary-teal mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
			</svg>
			<h1 class="text-3xl font-extrabold text-gray-900 mt-4 tracking-tight">{{ __('register.title.heading') }}</h1>
			<p class="text-sm text-gray-500 mt-1">{{ __('register.text.subtitle') }}</p>
		</div>
		@endif

		@if ($errors->any())
			<div class="mb-4 text-sm text-red-700 bg-red-100 rounded-lg px-4 py-2">
				{{ $errors->first() }}
			</div>
		@endif

		<form method="POST" action="{{ route('register') }}" class="space-y-5">
			@csrf

			<div>
				<label for="username" class="block text-sm font-medium text-gray-700 mb-2">{{ __('register.label.username') }}</label>
				<div class="relative flex items-center">
					<div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
						<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"></path>
						</svg>
					</div>
					<input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username"
						class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-teal/30 focus:border-primary-teal transition duration-150 placeholder-gray-400 text-gray-900 text-base hover:border-gray-300"
						placeholder="{{ __('register.placeholder.username') }}">
				</div>
			</div>

			<div>
				<label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('register.label.password') }}</label>
				<div class="relative flex items-center">
					<div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
						<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9V7a3 3 0 00-3-3H6a3 3 0 00-3 3v2"></path>
						</svg>
					</div>
					<input id="password" name="password" type="password" required autocomplete="new-password"
						class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-teal/30 focus:border-primary-teal transition duration-150 placeholder-gray-400 text-gray-900 text-base hover:border-gray-300"
						placeholder="{{ __('register.placeholder.password') }}">
				</div>
			</div>

			<div>
				<label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('register.label.password_confirmation') }}</label>
				<input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
					class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:outline-none focus:ring-primary-teal/30 focus:border-primary-teal transition duration-150 placeholder-gray-400 text-gray-900 text-base hover:border-gray-300"
					placeholder="{{ __('register.placeholder.password_confirmation') }}">
			</div>

			<div>
				<button type="submit" class="w-full flex justify-center items-center py-3 px-4 rounded-xl text-base font-bold text-white bg-primary-teal hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-primary-teal/50 transition duration-300 shadow-md hover:shadow-lg">
					{{ __('register.button.sign_up') }}
				</button>
			</div>
		</form>

		<p class="text-center text-sm text-gray-500 mt-12">
			{{ __('register.text.have_account') }}
			<a href="{{ route('login') }}" class="font-bold text-primary-teal hover:text-primary-dark transition duration-150">{{ __('register.link.login') }}</a>
		</p>
	</div>
</body>
</html>

