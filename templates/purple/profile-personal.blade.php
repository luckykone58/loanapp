<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ __('profile.title.personal_info') }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="/css/main.css">
</head>
<body class="bg-gray-50 text-gray-800">
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')
			<main class="flex-1 pb-0 relative pb-8 px-6">
				<div class="relative z-10 bg-white rounded-3xl shadow-lg p-4 space-y-6 top-[30px] pb-40">
					@php($viewOnly = in_array(request()->query('view'), [1, '1', true, 'true'], true))
					<h1 class="text-xl font-bold text-blue-500 mb-2">
						<span class="inline-flex items-center gap-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600">
								<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
								<circle cx="12" cy="7" r="4"/>
							</svg>
							{{ __('profile.title.personal_info') }}
						</span>
					</h1>
					@if(session('success'))
						<div class="text-sm text-green-700 bg-green-100 rounded px-3 py-2">{{ session('success') }}</div>
						<a href="{{ route('profile') }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M5 12h14"/>
								<path d="m12 5 7 7-7 7"/>
							</svg>
							{{ __('profile.button.continue_profile') }}
						</a>
					@endif
					@php($reqs = \App\Support\Settings::getJson('loan_requirements', []))
					@php($requireRel1 = !empty($reqs['relative_1']))
					@php($requireRel2 = !empty($reqs['relative_2']))
					<form method="POST" action="{{ route('profile.personal.update') }}" class="space-y-4">
						@csrf
						<fieldset @if($viewOnly) disabled @endif>
						<div class="grid grid-cols-1 gap-4">
							<div>
								<label class="block mb-1 text-sm">{{ __('profile.label.full_name') }} <span class="text-red-600">*</span></label>
								<div class="relative">
									<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
											<circle cx="12" cy="7" r="4"/>
										</svg>
									</span>
									<input required type="text" name="full_name" value="{{ old('full_name', $info->full_name ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
								</div>
								@error('full_name') <p class="text-xs text-red-600 mt-1">{{ static::e($message) }}</p> @enderror
							</div>
							<div>
								<label class="block mb-1 text-sm">{{ __('profile.label.email') }} <span class="text-red-600">*</span></label>
								<div class="relative">
									<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
											<path d="m22 6-10 7L2 6"/>
										</svg>
									</span>
									<input required type="text" name="email" value="{{ old('email', $info->email ?? '') }}" autocomplete="email" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
								</div>
								@error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
							</div>
							<div>
								<label class="block mb-1 text-sm">{{ __('profile.label.address') }} <span class="text-red-600">*</span></label>
								<div class="relative">
									<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M3 9l9-6 9 6v10a2 2 0 0 1-2 2h-4v-6H9v6H5a2 2 0 0 1-2-2z"/>
										</svg>
									</span>
									<input required type="text" name="address" value="{{ old('address', $info->address ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
								</div>
								@error('address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.company') }} <span class="text-red-600">*</span></label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M3 21V7a2 2 0 0 1 2-2h3V3h8v2h3a2 2 0 0 1 2 2v14H3Z"/>
												<path d="M3 10h18"/>
											</svg>
										</span>
										<input required type="text" name="company" value="{{ old('company', $info->company ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('company') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.company_address') }} <span class="text-red-600">*</span></label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M3 9l9-6 9 6v10a2 2 0 0 1-2 2h-4v-6H9v6H5a2 2 0 0 1-2-2z"/>
											</svg>
										</span>
										<input required type="text" name="company_address" value="{{ old('company_address', $info->company_address ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('company_address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
							</div>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.position') }} <span class="text-red-600">*</span></label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M16 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
												<circle cx="12" cy="7" r="4"/>
											</svg>
										</span>
										<input required type="text" name="position" value="{{ old('position', $info->position ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('position') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.monthly_income') }} <span class="text-red-600">*</span></label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<rect x="3" y="5" width="18" height="14" rx="2"/>
												<path d="M3 10h18"/>
												<path d="M7 15h4"/>
											</svg>
										</span>
										<input required type="text" name="monthly_income" value="{{ old('monthly_income', $info->monthly_income ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('monthly_income') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
							</div>
							@if($requireRel1)
							<h3 class="text-sm font-semibold text-blue-500 mt-2">{{ __('profile.title.reference_1') }}</h3>
							<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.contact_1_person') }} @if($requireRel1)<span class="text-red-600">*</span>@endif</label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
												<circle cx="12" cy="7" r="4"/>
											</svg>
										</span>
										<input @if($requireRel1) required @endif type="text" name="contact_1_person" value="{{ old('contact_1_person', $info->contact_1_person ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('contact_1_person') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.contact_1_phone') }} @if($requireRel1)<span class="text-red-600">*</span>@endif</label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.09 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7"/>
											</svg>
										</span>
										<input @if($requireRel1) required @endif type="text" name="contact_1_phone" value="{{ old('contact_1_phone', $info->contact_1_phone ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue500">
									</div>
									@error('contact_1_phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.contact_1_relativity') }} @if($requireRel1)<span class="text-red-600">*</span>@endif</label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M10 13a5 5 0 1 0-7 7l7-7Z"/>
												<path d="M12 12l7-7a5 5 0 1 1 7 7l-7 7"/>
												<path d="M16 5l3 3"/>
											</svg>
										</span>
										<input @if($requireRel1) required @endif type="text" name="contact_1_relativity" value="{{ old('contact_1_relativity', $info->contact_1_relativity ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('contact_1_relativity') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
							</div>
							@endif
							@if($requireRel2)
							<h3 class="text-sm font-semibold  text-blue-500 mt-4">{{ __('profile.title.reference_2') }}</h3>
							<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.contact_2_person') }} @if($requireRel2)<span class="text-red-600">*</span>@endif</label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
												<circle cx="12" cy="7" r="4"/>
											</svg>
										</span>
										<input @if($requireRel2) required @endif type="text" name="contact_2_person" value="{{ old('contact_2_person', $info->contact_2_person ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('contact_2_person') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.contact_2_phone') }} @if($requireRel2)<span class="text-red-600">*</span>@endif</label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.09 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7"/>
											</svg>
										</span>
                                        <input @if($requireRel2) required @endif type="text" name="contact_2_phone" value="{{ old('contact_2_phone', $info->contact_2_phone ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('contact_2_phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
								<div>
									<label class="block mb-1 text-sm">{{ __('profile.label.contact_2_relativity') }} @if($requireRel2)<span class="text-red-600">*</span>@endif</label>
									<div class="relative">
										<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M10 13a5 5 0 1 0-7 7l7-7Z"/>
												<path d="M12 12l7-7a5 5 0 1 1 7 7l-7 7"/>
												<path d="M16 5l3 3"/>
											</svg>
										</span>
										<input @if($requireRel2) required @endif type="text" name="contact_2_relativity" value="{{ old('contact_2_relativity', $info->contact_2_relativity ?? '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
									</div>
									@error('contact_2_relativity') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
								</div>
							</div>
							@endif
						</div>
						</fieldset>
						@unless($viewOnly)
						<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
								<polyline points="17 21 17 13 7 13 7 21"/>
								<polyline points="7 3 7 8 15 8"/>
							</svg>
							{{ __('profile.button.save') }}
						</button>
						@endunless
					</form>
				</div>
			</main>
			@include('partials.footer', ['active' => 'profile'])
		</div>
	</div>

	<script>
	(function() {
		// Build required fields list from settings flags rendered by Blade
		const requiredNames = [
			'full_name', 'email', 'address', 'company', 'company_address', 'position', 'monthly_income'
		];
		const requireRel1 = {{ $requireRel1 ? 'true' : 'false' }};
		const requireRel2 = {{ $requireRel2 ? 'true' : 'false' }};
		if (requireRel1) requiredNames.push('contact_1_person','contact_1_phone','contact_1_relativity');
		if (requireRel2) requiredNames.push('contact_2_person','contact_2_phone','contact_2_relativity');

		function ensureErrorNode(input) {
			let node = input.nextElementSibling;
			if (!node || !node.classList || !node.classList.contains('js-error')) {
				node = document.createElement('p');
				node.className = 'js-error text-xs text-red-600 mt-1 hidden';
				input.parentNode.insertBefore(node, input.nextSibling);
			}
			return node;
		}

		function setInvalid(input, msg) {
			input.classList.add('border-red-500');
			input.setAttribute('aria-invalid', 'true');
			const node = ensureErrorNode(input);
			node.textContent = msg || 'This field is required.';
			node.classList.remove('hidden');
		}

		function clearInvalid(input) {
			input.classList.remove('border-red-500');
			input.removeAttribute('aria-invalid');
			const node = ensureErrorNode(input);
			node.textContent = '';
			node.classList.add('hidden');
		}

		function validateField(input) {
			const value = (input.value || '').trim();
			if (value === '') {
				setInvalid(input);
				return false;
			}
			clearInvalid(input);
			return true;
		}

		requiredNames.forEach(function(name) {
			const input = document.querySelector('[name="' + name + '"]');
			if (!input) return;
			input.addEventListener('input', function(){ validateField(input); });
			input.addEventListener('blur', function(){ validateField(input); });
		});

		const form = document.querySelector('form[action*="profile/personal"]') || document.querySelector('form');
		if (form) {
			form.addEventListener('submit', function(e) {
				let ok = true;
				let firstInvalid = null;
				requiredNames.forEach(function(name) {
					const input = document.querySelector('[name="' + name + '"]');
					if (!input) return;
					if (!validateField(input)) {
						ok = false;
						if (!firstInvalid) firstInvalid = input;
					}
				});
				if (!ok) {
					e.preventDefault();
					firstInvalid && firstInvalid.focus();
				}
			});
		}
	})();
	</script>
</body>
</html>

