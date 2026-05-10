<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ __('profile.title.bank_info') }}</title>
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
								<path d="M3 10l9-7 9 7"/>
								<path d="M9 22V12h6v10"/>
								<path d="M21 22H3"/>
							</svg>
							{{ __('profile.title.bank_info') }}
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
					<form id="bank-form" method="POST" action="{{ route('profile.bank.update') }}" class="space-y-4">
						@csrf
						<fieldset @if($viewOnly) disabled @endif>
						<div>
							<label class="block mb-1 text-sm">{{ __('profile.label.bank_name') }} <span class="text-red-600">*</span></label>
							<div class="relative">
								<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<rect x="2" y="7" width="20" height="10" rx="2"/>
										<path d="M8 11h8"/>
										<path d="M6 15h2"/>
									</svg>
								</span>
								<input type="text" name="bank_name" value="{{ old('bank_name', $info->bank_name ?? '') }}" autocomplete="off" spellcheck="false" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
							</div>
						</div>
						<div>
							<label class="block mb-1 text_sm">{{ __('profile.label.bank_number') }} <span class="text-red-600">*</span></label>
							<div class="relative">
								<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<rect x="3" y="5" width="18" height="14" rx="2"/>
										<path d="M3 10h18"/>
										<path d="M7 15h4"/>
									</svg>
								</span>
								<input type="text" name="bank_number" value="{{ old('bank_number', $info->bank_number ?? '') }}" autocomplete="off" spellcheck="false"  class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500">
							</div>
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
		const form = document.getElementById('bank-form');
		if (!form) return;
		const requiredNames = ['bank_name','bank_number'];
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
			if (value === '') { setInvalid(input); return false; }
			clearInvalid(input); return true;
		}
		requiredNames.forEach(function(name){
			const input = form.querySelector('[name="' + name + '"]');
			if (!input) return;
			input.addEventListener('input', function(){ validateField(input); });
			input.addEventListener('blur', function(){ validateField(input); });
			// Disable copy/paste/drop/context menu
			['paste','drop','copy','cut','dragover','dragstart'].forEach(function(evt){
				input.addEventListener(evt, function(e){ e.preventDefault(); });
			});
			input.addEventListener('contextmenu', function(e){ e.preventDefault(); });
		});
		form.addEventListener('submit', function(e){
			let ok = true; let firstInvalid = null;
			requiredNames.forEach(function(name){
				const input = form.querySelector('[name="' + name + '"]');
				if (!input) return;
				if (!validateField(input)) { ok = false; if (!firstInvalid) firstInvalid = input; }
			});
			if (!ok) { e.preventDefault(); firstInvalid && firstInvalid.focus(); }
		});
	})();
	</script>
</body>
</html>

