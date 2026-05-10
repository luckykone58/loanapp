@extends('admin.layouts.app')

@section('title', 'Settings')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Settings</li>
@endsection

@section('content')
@if(session('success'))
	<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-2 xl:grid-cols-2 gap-6">
	<!-- 1/ Layout Configuration -->
	<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 form-surface">
		<h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
			<span class="inline-flex items-center gap-2">
				<i data-lucide="layout-dashboard" class="h-5 w-5 text-[#459699]"></i>
				<span>Layout Configuration</span>
			</span>
			<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10" onclick="toggleSection('section-layout-body', this)">Show</button>
		</h3>
		<div id="section-layout-body" class="hidden">
		<form method="POST" action="{{ route('admin.settings.save.layout', [], false) }}" enctype="multipart/form-data" class="space-y-4">
			@csrf
			<div>
				<label class="block mb-1">Select Theme</label>
				<div class="relative">
					<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
						</svg>
					</span>
					<select name="theme" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
						@foreach($availableThemes as $theme)
							<option value="{{ $theme }}" @selected(($settings['theme'] ?? '')===$theme)>{{ ucfirst($theme) }}</option>
						@endforeach
					</select>
				</div>
				@error('theme') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
			<div>
				<label class="block mb-1">Default Language</label>
				<div class="relative">
					<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 0 1 0 20"/>
						</svg>
					</span>
					<select name="default_locale" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
						@php $currentLocale = old('default_locale', $settings['default_locale'] ?? config('app.locale')); @endphp
						@foreach($availableLocales as $loc)
							<option value="{{ $loc }}" @selected($currentLocale === $loc)>{{ strtoupper($loc) }}</option>
						@endforeach
					</select>
				</div>
				@error('default_locale') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
			<div>
			</div>
			<div class="flex items-center gap-3">
				<label class="inline-flex items-center">
					<input type="checkbox" name="show_logo_login" value="1" @checked(old('show_logo_login', ($settings['show_logo_login'] ?? '1') === '1'))>
					<span class="ml-2">Show Logo on Login Page</span>
				</label>
			</div>
			<div class="flex items-center gap-3">
				<label class="inline-flex items_center">
					<input type="checkbox" name="show_logo_signup" value="1" @checked(old('show_logo_signup', ($settings['show_logo_signup'] ?? '1') === '1'))>
					<span class="ml-2">Show Logo on Signup Page</span>
				</label>
			</div>
			<div>
				<label class="block mb-1">Upload Logo</label>
				<input type="file" name="logo" accept="image/*" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
				@error('logo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				@if(!empty($settings['logo_path']))
					<div class="mt-2">
						<img src="{{ asset('storage/'.ltrim($settings['logo_path'], '/')) }}" alt="Logo" class="h-10">
					</div>
				@endif
			</div>
			<div class="pt-4">
				<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">Save Layout</button>
			</div>
		</form>
		</div>
	</div>

	<!-- 6/ Welcome Message -->
	<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 xl:col-span-2 form-surface">
		<h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
			<span class="inline-flex items-center gap-2">
				<i data-lucide="sparkles" class="h-5 w-5 text-[#459699]"></i>
				<span>Welcome Message</span>
			</span>
			<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10" onclick="toggleSection('section-welcome-body', this)">Show</button>
		</h3>
		<div id="section-welcome-body" class="hidden">
		<form method="POST" action="{{ route('admin.settings.save.welcome', [], false) }}" class="space-y-4">
			@csrf
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<label class="block mb-1">Title</label>
					<input type="text" name="welcome_title" value="{{ old('welcome_title', $settings['welcome_title']) }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
					@error('welcome_title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Message</label>
					<input type="text" name="welcome_message" value="{{ old('welcome_message', $settings['welcome_message']) }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
					@error('welcome_message') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
			</div>
			<div>
				<label class="block mb-1">Sub-message</label>
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
							</svg>
						</span>
						<textarea name="welcome_sub_message" rows="3" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">{{ old('welcome_sub_message', $settings['welcome_sub_message']) }}</textarea>
					</div>
				@error('welcome_sub_message') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
			<div class="pt-2">
				<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">Save Welcome</button>
			</div>
		</form>
		</div>
	</div>

	<!-- 2/ Home Sliders -->
	<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 form-surface">
		<h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
			<span class="inline-flex items-center gap-2">
				<i data-lucide="image" class="h-5 w-5 text-[#459699]"></i>
				<span>Home Sliders</span>
			</span>
			<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10" onclick="toggleSection('section-sliders-body', this)">Show</button>
		</h3>
		<div id="section-sliders-body" class="hidden">
		@php
			$slides = is_array($settings['home_sliders']) ? $settings['home_sliders'] : (json_decode($settings['home_sliders'] ?? '[]', true) ?: []);
		@endphp
		@if(!empty($slides))
			<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-3">
				@foreach($slides as $path)
					<div class="border rounded p-2 bg-slate-50 dark:bg-slate-800">
						<div class="aspect-[16/9] overflow-hidden rounded mb-2 bg-slate-100 dark:bg-slate-700">
							<img src="{{ asset('storage/'.ltrim($path, '/')) }}" alt="slide" class="w-full h-full object-cover">
						</div>
						<form method="POST" action="{{ route('admin.settings.delete.slider', [], false) }}">
							@csrf
							@method('DELETE')
							<input type="hidden" name="path" value="{{ $path }}">
							<button type="submit" class="w-full text-center px-2 py-1 text-xs text-red-600 border rounded hover:bg-red-50">
								Remove
							</button>
						</form>
					</div>
				@endforeach
			</div>
		@endif
		<form method="POST" action="{{ route('admin.settings.save.slider', [], false) }}" enctype="multipart/form-data" class="space-y-3">
			@csrf
			<div id="slidesInputs" class="space-y-2">
				<div class="flex items-center gap-2">
					<input type="file" name="slides[]" accept="image/*" class="flex-1 border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
					<button type="button" class="px-2 py-1 text-sm border rounded hover:bg-[#459699]/10"
						onclick="addSlideInput()">Add</button>
					<button type="button" class="px-2 py-1 text-sm border rounded hover:bg-red-50 text-red-600"
						onclick="removeSlideInput(this)">Remove</button>
				</div>
			</div>
			@if ($errors->has('slides') || $errors->has('slides.*'))
				<div class="text-sm text-red-600 space-y-1">
					@foreach ($errors->get('slides') as $err)
						<div>{{ $err }}</div>
					@endforeach
					@foreach ($errors->getMessages() as $key => $msgs)
						@if (\Illuminate\Support\Str::startsWith($key, 'slides.'))
							@foreach ($msgs as $m)
								<div>{{ $key }}: {{ $m }}</div>
							@endforeach
						@endif
					@endforeach
				</div>
			@endif
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">Upload Slides</button>
			<p class="text-xs text-slate-500">Add/remove inputs to upload multiple images. Recommended aspect ratio 16:9.</p>
		</form>
		</div>
	</div>

	<!-- 2/ Loan Configuration -->
	<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 form-surface">
		<h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
			<span class="inline-flex items-center gap-2">
				<i data-lucide="credit-card" class="h-5 w-5 text-[#459699]"></i>
				<span>Loan Configuration</span>
			</span>
			<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10" onclick="toggleSection('section-loan-body', this)">Show</button>
		</h3>
		<div id="section-loan-body" class="hidden">
		<form method="POST" action="{{ route('admin.settings.save.loan', [], false) }}" class="grid grid-cols-1 gap-4">
			@csrf
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div class="md:col-span-2">
					<label class="block mb-1">Currency</label>
					<input type="text" name="currency_symbol" maxlength="8" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}">
					@error('currency_symbol') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Minimum Loan</label>
					<input type="number" step="0.01" name="loan_min" value="{{ old('loan_min', $settings['loan_min']) }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					@error('loan_min') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Maximum Loan</label>
					<input type="number" step="0.01" name="loan_max" value="{{ old('loan_max', $settings['loan_max']) }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					@error('loan_max') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Interest Rate (%)</label>
					<input type="number" step="0.01" name="loan_interest_rate" value="{{ old('loan_interest_rate', $settings['loan_interest_rate'] ?? '0.5') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					@error('loan_interest_rate') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Loan Terms (months)</label>
					@php
						$terms = is_array($settings['loan_terms']) ? $settings['loan_terms'] : (json_decode($settings['loan_terms'] ?? '[]', true) ?: []);
					@endphp
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M3 13h8l-1 8L21 3H9l1 10z"/>
							</svg>
						</span>
						<select name="loan_terms[]" multiple class="w-full border rounded pl-10 pr-3 py-2 h-32 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
							@foreach([6,12,24,36,48,60] as $m)
								<option value="{{ $m }}" @selected(in_array($m, old('loan_terms', $terms)))>{{ $m }}</option>
							@endforeach
						</select>
					</div>
					<p class="text-xs text-slate-500 mt-1">Hold Ctrl/Cmd to select multiple.</p>
					@error('loan_terms') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
			</div>
			<div class="pt-2">
				<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">Save Loan</button>
			</div>
		</form>
		</div>
	</div>

	<!-- 3/ Completed Before Apply Loan -->
	<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 form-surface">
		<h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
			<span class="inline-flex items-center gap-2">
				<i data-lucide="clipboard-check" class="h-5 w-5 text-[#459699]"></i>
				<span>Completed Before Apply Loan</span>
			</span>
			<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10" onclick="toggleSection('section-reqs-body', this)">Show</button>
		</h3>
		<div id="section-reqs-body" class="hidden">
		@php
			$reqs = is_array($settings['loan_requirements']) ? $settings['loan_requirements'] : (json_decode($settings['loan_requirements'] ?? '{}', true) ?: []);
			$fields = ['full_name'=>'Full Name','id_number'=>'ID Number','id_front'=>'ID Front','id_back'=>'ID Back','id_selfie'=>'ID Selfie','signature'=>'Signature','bank_name'=>'Bank Name','bank_account'=>'Bank Account','relative_1'=>'Relative 1','relative_2'=>'Relative 2'];
		@endphp
		<form method="POST" action="{{ route('admin.settings.save.requirements', [], false) }}" class="space-y-3">
			@csrf
			<div class="grid grid-cols-2 md:grid-cols-3 gap-3">
				@foreach($fields as $key => $label)
					<label class="inline-flex items-center gap-2">
						<input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $reqs[$key] ?? false))>
						<span>{{ $label }}</span>
					</label>
				@endforeach
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-2">
				<div>
					<label class="block mb-1">Default Credit Score</label>
					<input type="number" name="default_credit_score" min="0" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ old('default_credit_score', $settings['default_credit_score'] ?? 30) }}">
					@error('default_credit_score') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
			</div>
			<div class="pt-2">
				<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">Save Requirements</button>
			</div>
		</form>
		</div>
	</div>

	<!-- 4/ Pages -->
	<div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
		<h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
            <span class="inline-flex items-center gap-2">
                <i data-lucide="file-text" class="h-5 w-5 text-[#459699]"></i>
                <span>Pages</span>
            </span>
			<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10" onclick="toggleSection('section-pages-body', this)">Show</button>
        </h3>
		<div id="section-pages-body" class="hidden">
		<form method="POST" action="{{ route('admin.settings.save.pages', [], false) }}" class="space-y-4">
			@csrf
			<div>
				<div class="flex items-center justify-between mb-2">
					<label class="block mb-1 font-medium">Contact Details</label>
					<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10"
						onclick="togglePane('contact-pane', this)">Show Editor</button>
				</div>
				<div id="contact-pane" class="hidden">
					<textarea name="contact_details" rows="10" class="w-full border rounded px-3 py-2 wysiwyg">{{ old('contact_details', $settings['contact_details']) }}</textarea>
				</div>
			</div>
			<div>
				<div class="flex items-center justify-between mb-2">
					<label class="block mb-1 font-medium">About Us</label>
					<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10"
						onclick="togglePane('about-pane', this)">Show Editor</button>
				</div>
				<div id="about-pane" class="hidden">
					<textarea name="about_us" rows="10" class="w-full border rounded px-3 py-2 wysiwyg">{{ old('about_us', $settings['about_us']) }}</textarea>
					<p class="text-xs text-slate-500 mt-1">Rich text supported.</p>
				</div>
			</div>
			<div>
				<div class="flex items-center justify-between mb-2">
					<label class="block mb-1 font-medium">Contract Page</label>
					<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10"
						onclick="togglePane('contract-pane', this)">Show Editor</button>
				</div>
				<div id="contract-pane" class="hidden">
					<textarea name="contract_page" rows="10" class="w-full border rounded px-3 py-2 wysiwyg">{{ old('contract_page', $settings['contract_page']) }}</textarea>
					<p class="text-xs text-slate-500 mt-1">Use this content as the contract template shown to users.</p>
				</div>
			</div>
			<div>
				<div class="flex items-center justify-between mb-2">
					<label class="block mb-1 font-medium">Agreement Page</label>
					<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10"
						onclick="togglePane('agreement-pane', this)">Show Editor</button>
				</div>
				<div id="agreement-pane" class="hidden">
					<textarea name="agreement_page" rows="10" class="w-full border rounded px-3 py-2 wysiwyg">{{ old('agreement_page', $settings['agreement_page']) }}</textarea>
					<p class="text-xs text-slate-500 mt-1">Content for the agreement page.</p>
				</div>
			</div>
			<div class="pt-2">
				<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">Save Pages</button>
			</div>
		</form>
		</div>
	</div>

	<!-- 5/ FAQs -->
	<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 xl:col-span-2 form-surface">
		<h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
			<span class="inline-flex items-center gap-2">
				<i data-lucide="help-circle" class="h-5 w-5 text-[#459699]"></i>
				<span>FAQs</span>
			</span>
			<button type="button" class="px-3 py-1.5 text-sm rounded border hover:bg-[#459699]/10" onclick="toggleSection('section-faqs-body', this)">Show</button>
		</h3>
		<div id="section-faqs-body" class="hidden">
		@php
			$faqs = is_array($settings['faqs_json']) ? $settings['faqs_json'] : (json_decode($settings['faqs_json'] ?? '[]', true) ?: []);
		@endphp
		<form method="POST" action="{{ route('admin.settings.save.faqs', [], false) }}" id="faqsForm">
			@csrf
			<div id="faqsList" class="space-y-4">
				@foreach($faqs as $i => $item)
					<div class="p-4 border rounded bg-slate-50 dark:bg-slate-700" data-faq-item>
						<div class="flex items-center justify-between mb-2" data-faq-drag draggable="true">
							<div class="text-sm font-medium text-slate-700 dark:text-slate-200">Question {{ $i + 1 }}</div>
							<div class="flex items-center gap-2">
								<button type="button" class="px-2 py-1 text-xs rounded border hover:bg-[#459699]/10"
									onclick="togglePane('faq-body-{{ $i }}', this)">Show</button>
								<button type="button" class="px-2 py-1 text-xs text-red-600 border rounded"
									onclick="this.closest('[draggable=true]').remove()">Remove</button>
							</div>
						</div>
						<div id="faq-body-{{ $i }}" class="hidden space-y-3">
							<div>
								<label class="block mb-1 text-sm">Question</label>
								<div class="relative">
									<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h10"/>
										</svg>
									</span>
									<input type="text" name="faqs[{{ $i }}][q]" value="{{ $item['q'] ?? '' }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
								</div>
							</div>
							<div>
								<label class="block mb-1 text-sm">Answer</label>
								<div class="relative">
									<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
										</svg>
									</span>
									<textarea name="faqs[{{ $i }}][a]" rows="3" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>{{ $item['a'] ?? '' }}</textarea>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			<div class="mt-3 flex items-center gap-3">
				<button type="button" class="px-3 py-2 border rounded" onclick="addFaq()">Add FAQ</button>
				<span class="text-xs text-slate-500">Max 10 items. Drag blocks to reorder.</span>
			</div>
			<div class="pt-4">
				<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">Save FAQs</button>
			</div>
		</form>
		</div>
	</div>

	
</div>

<script>
	// WYSIWYG is initialized by resources/js/admin-wysiwyg.js via Vite
	function togglePane(id, btn) {
		const el = document.getElementById(id);
		if (!el) return;
		const hidden = el.classList.toggle('hidden');
		if (btn) {
			btn.textContent = hidden ? 'Show Editor' : 'Hide Editor';
		}
		// If we just showed it and TinyMCE is loaded, ensure editor is rendered
		if (!hidden && window.tinymce && typeof window.tinymce.execCommand === 'function') {
			// Re-init in case it was hidden at initial load
			if (window.initWysiwyg) window.initWysiwyg();
		}
	}

	// Section toggle (show/hide section content)
	function toggleSection(id, btn) {
		const el = document.getElementById(id);
		if (!el) return;
		const hidden = el.classList.toggle('hidden');
		if (btn) btn.textContent = hidden ? 'Show' : 'Hide';
	}
	// Dynamic add/remove file inputs for sliders
	function addSlideInput() {
		const wrap = document.getElementById('slidesInputs');
		if (!wrap) return;
		const row = document.createElement('div');
		row.className = 'flex items-center gap-2';
		row.innerHTML = `
			<input type="file" name="slides[]" accept="image/*" class="flex-1 border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			<button type="button" class="px-2 py-1 text-sm border rounded hover:bg-[#459699]/10" onclick="addSlideInput()">Add</button>
			<button type="button" class="px-2 py-1 text-sm border rounded hover:bg-red-50 text-red-600" onclick="removeSlideInput(this)">Remove</button>
		`;
		wrap.appendChild(row);
	}
	function removeSlideInput(btn) {
		const row = btn?.closest('.flex');
		const wrap = document.getElementById('slidesInputs');
		if (row && wrap && wrap.children.length > 1) {
			wrap.removeChild(row);
		}
	}

		function addFaq() {
		const list = document.getElementById('faqsList');
		const count = list.children.length;
		if (count >= 10) return alert('Maximum 10 FAQs.');
		const idx = count;
		const container = document.createElement('div');
		container.className = 'p-4 border rounded bg-slate-50 dark:bg-slate-700';
		container.setAttribute('data-faq-item', '');
		const bodyId = `faq-body-${idx}`;
		container.innerHTML = `
			<div class="flex items-center justify-between mb-2" data-faq-drag draggable="true">
				<div class="text-sm font-medium text-slate-700 dark:text-slate-200">Question ${idx + 1}</div>
				<div class="flex items-center gap-2">
					<button type="button" class="px-2 py-1 text-xs rounded border hover:bg-[#459699]/10"
						onclick="togglePane('${bodyId}', this)">Show</button>
					<button type="button" class="px-2 py-1 text-xs text-red-600 border rounded"
						onclick="this.closest('[draggable=true]').remove()">Remove</button>
				</div>
			</div>
			<div id="${bodyId}" class="hidden space-y-3">
				<div>
					<label class="block mb-1 text-sm">Question</label>
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h10"/>
							</svg>
						</span>
						<input type="text" name="faqs[${idx}][q]" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					</div>
				</div>
				<div>
					<label class="block mb-1 text-sm">Answer</label>
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
							</svg>
						</span>
						<textarea name="faqs[${idx}][a]" rows="3" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required></textarea>
					</div>
				</div>
			</div>
		`;
		list.appendChild(container);
		}

	// Basic drag & drop reordering (drag using header handle only)
	let dragSrc;
	document.addEventListener('dragstart', (e) => {
		const handle = e.target.closest('[data-faq-drag]');
		if (!handle) return;
		const item = handle.closest('[data-faq-item]');
		if (!item) return;
		dragSrc = item;
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData('text/plain', '');
	});
	document.addEventListener('dragover', (e) => {
		if (e.target.closest('#faqsList [data-faq-item]')) {
			e.preventDefault();
		}
	});
	document.addEventListener('drop', (e) => {
		const list = document.getElementById('faqsList');
		const target = e.target.closest('#faqsList [data-faq-item]');
		if (dragSrc && target && dragSrc !== target) {
			const children = Array.from(list.children);
			const srcIndex = children.indexOf(dragSrc);
			const tgtIndex = children.indexOf(target);
			if (srcIndex < tgtIndex) {
				list.insertBefore(dragSrc, target.nextSibling);
			} else {
				list.insertBefore(dragSrc, target);
			}
			// Reindex names
			Array.from(list.children).forEach((el, idx) => {
				el.querySelectorAll('input[name^="faqs["], textarea[name^="faqs["]').forEach((field) => {
					const isQ = field.name.endsWith('[q]');
					field.name = `faqs[${idx}][${isQ ? 'q' : 'a'}]`;
				});
			});
		}
	});
</script>
@endsection



