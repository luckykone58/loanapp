<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $currentDomain->name ?? config('app.name') }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="/css/main.css">
	<script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 15px rgba(124, 58, 237, 0.3)',
                    }
                }
            }
        }
    </script>
	<style>
		* { box-sizing: border-box; }
		body {
			margin: 0;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}
		.carousel-slide { display: none; }
		.carousel-slide.active { display: block; }
		.carousel-dot { transition: all 0.3s ease; }
		.cursor-pointer { cursor: pointer; }
	</style>
</head>
<body class="bg-gray-50 text-gray-800">
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-br from-blue-800 via-brand-700 to-brand-500 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')
			
			<main class="flex-1 pb-0 relative pb-8 px-6">
				<div class="relative z-10 bg-white rounded-3xl shadow-lg p-4 space-y-6 top-[30px] pb-40">
					

					@php
						$reqs = \App\Support\Settings::getJson('loan_requirements', []);
						$info = auth()->user()->info;
						$hasPersonal = !empty($info?->full_name);
						$needId = (bool)($reqs['id_number'] ?? false) || (bool)($reqs['id_front'] ?? false) || (bool)($reqs['id_back'] ?? false) || (bool)($reqs['id_selfie'] ?? false);
						$needBank = (bool)($reqs['bank_name'] ?? false) || (bool)($reqs['bank_account'] ?? false);
						$needSignature = (bool)($reqs['signature'] ?? false);
						$hasId = !empty($info?->id_card_number) || !empty($info?->id_card_front) || !empty($info?->id_card_back) || !empty($info?->id_card_selfie);
						$hasBank = !empty($info?->bank_name) || !empty($info?->bank_number);
						$hasSignature = !empty($info?->signature);
						$stepsOrder = ['personal'];
						if ($needId) $stepsOrder[] = 'id';
						if ($needBank) $stepsOrder[] = 'bank';
						if ($needSignature) $stepsOrder[] = 'signature';
						$completedMap = ['personal'=>$hasPersonal,'id'=>$hasId,'bank'=>$hasBank,'signature'=>$hasSignature];
						$currentKey = null;
						foreach ($stepsOrder as $k) { if (!($completedMap[$k] ?? false)) { $currentKey = $k; break; } }
						$indexMap = []; $__i=1; foreach ($stepsOrder as $k) { $indexMap[$k]=$__i++; }
						$total = count($stepsOrder);
						$done = 0; foreach ($stepsOrder as $k) { if ($completedMap[$k] ?? false) $done++; }
						$percent = (int) round(($total > 0 ? ($done / $total) : 0) * 100);
						$completed = ($done >= $total && $total > 0);
					@endphp

					@php
						// Determine progress bar height by current step
						$stepIndex = 1;
						if (($currentKey ?? null) === null) {
							$stepIndex = count($stepsOrder ?? []) ?: 1; // completed -> last step
						} else {
							$pos = array_search(($currentKey ?? ''), ($stepsOrder ?? []), true);
							$stepIndex = $pos === false ? 1 : ($pos + 1);
						}
						// Map to heights: step1=h-16, step2=h-[90px], step3=h-[180px], step4/complete=h-60
						$progressH = 'h-16';
						if ($stepIndex === 2) $progressH = 'h-[90px]';
						elseif ($stepIndex === 3) $progressH = 'h-[180px]';
						elseif ($stepIndex >= 4) $progressH = 'h-[270px]';
					@endphp

					<!-- CHECKLIST / APPLICATION PROGRESS CARD -->
        <div class="bg-white mb-8 relative">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-blue-500">{{ __('profile.title.application_status') }}</h2>
                    <p class="text-sm text-gray-500">{{ __('profile.text.complete_steps') }}</p>
                </div>
                <span class="text-xs font-bold bg-blue-50 text-blue-600 px-3 py-1 rounded-full border border-blue-100">{{ __('profile.text.progress_done', ['percent' => $percent]) }}</span>
            </div>

            <div class="relative px-2">
                <!-- Vertical Lines -->
                <!-- Gray background line for the full height -->
                <div class="absolute left-4 top-3 bottom-8 w-0.5 bg-gray-100"></div>
                <!-- Green progress line for the first segment -->
                <div class="absolute left-4 top-3 w-0.5 bg-green-500 {{ $progressH }}"></div>

                <!-- STEP 1: Personal Information -->
                <div class="relative flex gap-5 pb-8 group">
                    <div class="{{ $currentKey==='personal' ? 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white ring-4 ring-blue-50 z-10 shadow-lg shadow-blue-200' : 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full '.($hasPersonal ? 'bg-green-500 text-white' : 'bg-gray-50 text-gray-400 border-2 border-gray-200').' ring-4 ring-white z-10 shadow-sm' }}">
                        @if($hasPersonal && $currentKey!=='personal')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        @else
                        <span class="text-xs font-bold">{{ $indexMap['personal'] ?? 1 }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col pt-0.5">
                        <span class="text-sm font-bold text-gray-900">{{ __('profile.steps.personal_info') }}</span>
                        <span class="text-xs {{ $hasPersonal ? 'text-green-600' : 'text-gray-500' }} font-medium">
							{{ $hasPersonal ? __('profile.status.completed') : __('profile.status.incomplete') }}
						</span>
						@if($currentKey==='personal')
						<a href="{{ route('profile.personal') }}" class="mt-2 inline-flex items-center gap-2 text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded">
							{{ __('profile.button.update_info') }}
						</a>
						@endif
                    </div>
                </div>

                <!-- STEP 2: ID Verification -->
                @if($needId)
                <div class="relative flex gap-5 pb-8 {{ ($currentKey!=='id' && !$hasId) ? 'opacity-60' : '' }}">
                    @if($currentKey==='id')
                    <span class="absolute left-0 top-0 h-8 w-8 rounded-full bg-blue-400 opacity-20 animate-ping"></span>
                    @endif
                    <div class="{{ $currentKey==='id' ? 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white ring-4 ring-blue-50 z-10 shadow-lg shadow-blue-200' : 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full '.($hasId ? 'bg-green-500 text-white' : 'bg-gray-50 text-gray-400 border-2 border-gray-200').' ring-4 ring-white z-10' }}">
                        <span class="text-xs font-bold">{{ $indexMap['id'] ?? 2 }}</span>
                    </div>
                    <div class="flex flex-col pt-0.5 w-full">
                        <span class="text-sm font-bold text-gray-900">{{ __('profile.steps.id_verification') }}</span>
                        <p class="text-xs text-gray-500 mb-3 mt-1">{{ __('profile.text.id_verification_hint') }}</p>
                        <span class="text-xs {{ $hasId ? 'text-green-600' : 'text-gray-500' }} font-medium">
							{{ $hasId ? __('profile.status.completed') : __('profile.status.incomplete') }}
						</span>
                        @if($currentKey==='id')
                        <a href="{{ route('profile.id') }}" class="text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg w-fit transition-all shadow-sm hover:shadow active:scale-95 flex items-center gap-2">
                            {{ __('profile.button.start_verification') }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                @if($needBank)
                <!-- STEP 3: Bank Information -->
                <div class="relative flex gap-5 pb-8 {{ ($currentKey!=='bank' && !$hasBank) ? 'opacity-60' : '' }}">
                    @if($currentKey==='bank')
                    <span class="absolute left-0 top-0 h-8 w-8 rounded-full bg-blue-400 opacity-20 animate-ping"></span>
                    @endif
                    <div class="{{ $currentKey==='bank' ? 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white ring-4 ring-blue-50 z-10 shadow-lg shadow-blue-200' : 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full '.($hasBank ? 'bg-green-500 text-white' : 'bg-gray-50 text-gray-400 border-2 border-gray-200').' ring-4 ring-white z-10' }}">
                        <span class="text-xs font-bold">{{ $indexMap['bank'] ?? 3 }}</span>
                    </div>
                    <div class="flex flex-col pt-1">
                        <span class="text-sm font-bold text-gray-900">{{ __('profile.steps.bank_info') }}</span>
                        <span class="text-xs text-gray-500">{{ __('profile.text.add_disbursement_account') }}</span>
                        <span class="text-xs {{ $hasBank ? 'text-green-600' : 'text-gray-500' }} font-medium">
							{{ $hasBank ? __('profile.status.completed') : __('profile.status.incomplete') }}
						</span>
                        @if($currentKey==='bank')
                        <a href="{{ route('profile.bank') }}" class="mt-2 inline-flex items-center gap-2 text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded">
                            {{ __('profile.button.start_verification') }}
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                @if($needSignature)
                <!-- STEP 4: Signature -->
                <div class="relative flex gap-5 {{ ($currentKey!=='signature' && !$hasSignature) ? 'opacity-60' : '' }}">
                    @if($currentKey==='signature')
                    <span class="absolute left-0 top-0 h-8 w-8 rounded-full bg-blue-400 opacity-20 animate-ping"></span>
                    @endif
                    <div class="{{ $currentKey==='signature' ? 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white ring-4 ring-blue-50 z-10 shadow-lg shadow-blue-200' : 'flex h-8 w-8 shrink-0 items-center justify-center rounded-full '.($hasSignature ? 'bg-green-500 text-white' : 'bg-gray-50 text-gray-400 border-2 border-gray-200').' ring-4 ring-white z-10' }}">
                        <span class="text-xs font-bold">{{ $indexMap['signature'] ?? 4 }}</span>
                    </div>
                    <div class="flex flex-col pt-1">
                        <span class="text-sm font-bold text-gray-900">{{ __('profile.steps.signature') }}</span>
                        <span class="text-xs text-gray-500">{{ __('profile.text.sign_agreement') }}</span>
                        <span class="text-xs {{ $hasSignature ? 'text-green-600' : 'text-gray-500' }} font-medium">
							{{ $hasSignature ? __('profile.status.completed') : __('profile.status.incomplete') }}
						</span>
                        @if($currentKey==='signature')
                        <a href="{{ route('profile.signature') }}" class="mt-2 inline-flex items-center gap-2 text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded">
                            {{ __('profile.button.start_verification') }}
                        </a>
                        @endif
                    </div>
                </div>
                @endif 
            </div>
        </div>
					
					@if($completed)
					<div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-sm">
						<div class="flex items-center justify-between mb-3">
							<div>
								<h3 class="text-sm font-bold text-blue-500">{{ __('profile.title.view_submitted_info') }}</h3>
								<p class="text-xs text-gray-500">{{ __('profile.text.view_submitted_info_hint') }}</p>
							</div>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
							<div class="flex">
								<a href="{{ route('profile.personal', ['view' => 1]) }}" class="w-full inline-flex items-center justify-center gap-2 text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-2 rounded">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/></svg>
									{{ __('profile.steps.personal_info') }}
								</a>
							</div>
							@if($needId)
							<div class="flex">
								<a href="{{ route('profile.id', ['view' => 1]) }}" class="w-full inline-flex items-center justify-center gap-2 text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-2 rounded">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="13" y2="8"/><path d="M11 15h6"/></svg>
									{{ __('profile.steps.id_verification') }}
								</a>
							</div>
							@endif
							@if($needBank)
							<div class="flex">
								<a href="{{ route('profile.bank', ['view' => 1]) }}" class="w-full inline-flex items-center justify-center gap-2 text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-2 rounded">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10l9-7 9 7"/><path d="M9 22V12h6v10"/><path d="M21 22H3"/></svg>
									{{ __('profile.steps.bank_info') }}
								</a>
							</div>
							@endif
							@if($needSignature)
							<div class="flex">
								<a href="{{ route('profile.signature', ['view' => 1]) }}" class="w-full inline-flex items-center justify-center gap-2 text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-2 rounded">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
									{{ __('profile.steps.signature') }}
								</a>
							</div>
							@endif
						</div>
					</div>
					@endif
					
				</div>
			</main>
			
			@include('partials.footer', ['active' => 'profile'])
		</div>
	</div>
	
</body>
</html>

