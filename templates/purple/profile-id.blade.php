<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ __('profile.title.id_verification') }}</title>
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
								<rect x="3" y="4" width="18" height="16" rx="2"/>
								<line x1="7" y1="8" x2="13" y2="8"/>
								<circle cx="7.5" cy="13" r="2"/>
								<path d="M11 15h6"/>
							</svg>
							{{ __('profile.title.id_verification') }}
						</span>
					</h1>
					@if($viewOnly)
						<div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
							<div>
								<div class="text-xs font-semibold text-slate-700 mb-1">{{ __('profile.label.id_number') }}</div>
								<div class="text-sm text-slate-800 bg-slate-50 border rounded px-3 py-2">{{ $info->id_card_number ?? '—' }}</div>
							</div>
							<div class="border rounded-lg p-3 bg-slate-50">
								<div class="text-xs font-semibold text-slate-700 mb-2">{{ __('profile.label.id_front') }}</div>
								@if(!empty($info?->id_card_front))
									<img src="{{ asset('storage/'.ltrim($info->id_card_front, '/')) }}" alt="ID Front" class="w-full h-40 object-cover rounded">
								@else
									<div class="text-xs text-slate-500">—</div>
								@endif
							</div>
							<div class="border rounded-lg p-3 bg-slate-50">
								<div class="text-xs font-semibold text-slate-700 mb-2">{{ __('profile.label.id_back') }}</div>
								@if(!empty($info?->id_card_back))
									<img src="{{ asset('storage/'.ltrim($info->id_card_back, '/')) }}" alt="ID Back" class="w-full h-40 object-cover rounded">
								@else
									<div class="text-xs text-slate-500">—</div>
								@endif
							</div>
							<div class="border rounded-lg p-3 bg-slate-50">
								<div class="text-xs font-semibold text-slate-700 mb-2">{{ __('profile.label.id_selfie') }}</div>
								@if(!empty($info?->id_card_selfie))
									<img src="{{ asset('storage/'.ltrim($info->id_card_selfie, '/')) }}" alt="ID Selfie" class="w-full h-40 object-cover rounded">
								@else
									<div class="text-xs text-slate-500">—</div>
								@endif
							</div>
						</div>
					@elseif(session('success'))
						<div class="text-sm text-green-700 bg-green-100 rounded px-3 py-2">{{ session('success') }}</div>
						<a href="{{ route('profile') }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/>
                                <path d="m12 5 7 7-7 7"/>
                            </svg>
                            {{ __('profile.button.continue_profile') }}
                        </a>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-1 gap-4">
							<div class="border rounded-lg p-3 bg-slate-50">
								<div class="text-xs font-semibold text-slate-700 mb-2">{{ __('profile.label.id_front') }}</div>
								@if(!empty($info?->id_card_front))
									<img src="{{ asset('storage/'.ltrim($info->id_card_front, '/')) }}" alt="ID Front" class="w-full h-40 object-cover rounded">
								@else
									<div class="text-xs text-slate-500">—</div>
								@endif
							</div>
							<div class="border rounded-lg p-3 bg-slate-50">
								<div class="text-xs font-semibold text-slate-700 mb-2">{{ __('profile.label.id_back') }}</div>
								@if(!empty($info?->id_card_back))
									<img src="{{ asset('storage/'.ltrim($info->id_card_back, '/')) }}" alt="ID Back" class="w-full h-40 object-cover rounded">
								@else
									<div class="text-xs text-slate-500">—</div>
								@endif
							</div>
							<div class="border rounded-lg p-3 bg-slate-50">
								<div class="text-xs font-semibold text-slate-700 mb-2">{{ __('profile.label.id_selfie') }}</div>
								@if(!empty($info?->id_card_selfie))
									<img src="{{ asset('storage/'.ltrim($info->id_card_selfie, '/')) }}" alt="ID Selfie" class="w-full h-40 object-cover rounded">
								@else
									<div class="text-xs text-slate-500">—</div>
								@endif
							</div>
						</div>
                        
                        @else
					<form id="id-form" method="POST" action="{{ route('profile.id.update') }}" enctype="multipart/form-data" class="space-y-4">
						@csrf
						<div>
							<label class="block mb-1 text-sm">{{ __('profile.label.id_number') }} <span class="text-red-600">*</span></label>
							<div class="relative">
								<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<rect x="3" y="5" width="18" height="14" rx="2"/>
										<path d="M7 9h6"/>
										<path d="M7 13h10"/>
									</svg>
								</span>
								<input type="text" name="id_card_number" value="{{ old('id_card_number', $info->id_card_number ?? '') }}" autocomplete="off" spellcheck="false"  class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
							</div>
						</div>
						<div>
							<label class="block mb-1 text-sm">{{ __('profile.label.id_front') }} <span class="text-red-600">*</span></label>
							<div class="relative">
								<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<rect x="3" y="4" width="18" height="14" rx="2"/>
										<path d="M8 12h8"/>
										<path d="M8 8h5"/>
									</svg>
								</span>
								<input type="file" name="id_card_front" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
							</div>
						</div>
						<div>
							<label class="block mb-1 text-sm">{{ __('profile.label.id_back') }} <span class="text-red-600">*</span></label>
							<div class="relative">
								<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<rect x="3" y="4" width="18" height="14" rx="2"/>
										<path d="M8 12h8"/>
										<path d="M8 8h5"/>
									</svg>
								</span>
								<input type="file" name="id_card_back" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
							</div>
						</div>
						<div>
							<label class="block mb-1 text-sm">{{ __('profile.label.id_selfie') }} <span class="text-red-600">*</span></label>
							<div class="relative">
								<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<rect x="3" y="4" width="18" height="14" rx="2"/>
										<path d="M8 12h8"/>
										<path d="M8 8h5"/>
									</svg>
								</span>
								<input type="file" name="id_card_selfie" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
							</div>
						</div>
						<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
								<polyline points="17 21 17 13 7 13 7 21"/>
								<polyline points="7 3 7 8 15 8"/>
							</svg>
							{{ __('profile.button.save') }}
						</button>
					</form>
					@endif
				</div>
			</main>
			@include('partials.footer', ['active' => 'profile'])
		</div>
	</div>

	<script>
	(function() {
		const form = document.getElementById('id-form');
		if (!form) return;
		const requiredNames = ['id_card_number','id_card_front','id_card_back','id_card_selfie'];
		const MAX_EACH = 10 * 1024 * 1024; // 5MB per file
		const MAX_TOTAL = 30 * 1024 * 1024; // 12MB total (avoid post_max_size resets)
		// Disable copy/paste/drop/context menu for ID number to force manual input
		const idNum = form.querySelector('[name="id_card_number"]');
		if (idNum) {
			['paste','drop','copy','cut','dragover','dragstart'].forEach(function(evt){
				idNum.addEventListener(evt, function(e){ e.preventDefault(); });
			});
			idNum.addEventListener('contextmenu', function(e){ e.preventDefault(); });
		}
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
			let has = false;
			if (input.type === 'file') {
				has = input.files && input.files.length > 0;
				if (has) {
					const f = input.files[0];
					if (f.size > MAX_EACH) {
						setInvalid(input, 'File too large. Max 5MB.');
						return false;
					}
				}
			} else {
				has = (input.value || '').trim() !== '';
			}
			if (!has) { setInvalid(input); return false; }
			clearInvalid(input); return true;
		}
		requiredNames.forEach(function(name) {
			const input = form.querySelector('[name="' + name + '"]');
			if (!input) return;
			input.addEventListener('input', function(){ validateField(input); });
			input.addEventListener('change', function(){ validateField(input); });
			input.addEventListener('blur', function(){ validateField(input); });
		});
		form.addEventListener('submit', function(e) {
			let ok = true; let firstInvalid = null;
			requiredNames.forEach(function(name){
				const input = form.querySelector('[name="' + name + '"]');
				if (!input) return;
				if (!validateField(input)) { ok = false; if (!firstInvalid) firstInvalid = input; }
			});
			// Total size guard
			let total = 0;
			['id_card_front','id_card_back','id_card_selfie'].forEach(function(name){
				const input = form.querySelector('[name="' + name + '"]');
				if (input && input.files && input.files[0]) total += input.files[0].size;
			});
			if (total > MAX_TOTAL) {
				ok = false;
				alert('Total size of images is too large. Please upload smaller images (<= 12MB total).');
			}
			if (!ok) { e.preventDefault(); firstInvalid && firstInvalid.focus(); }
		});
	})();
	</script>
</body>
</html>

