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
	</style>
</head>
<body class="bg-gray-50 text-gray-800">
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-br from-blue-800 via-brand-700 to-brand-500 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')

			<main class="flex-1 pb-0 relative pb-8 px-6">
				<div class="relative z-10 bg-white rounded-3xl shadow-lg p-4 space-y-6 top-[30px] pb-40 min-h-[500px]">
				
					@php
						$minAmount = (int) \App\Support\Settings::get('loan_min', 1000);
						$maxAmount = (int) \App\Support\Settings::get('loan_max', 100000);
						$interestPercent = (float) \App\Support\Settings::get('loan_interest_rate', 0.5); // percent value e.g. 0.5 => 0.5%
						$currencySymbol = (string) \App\Support\Settings::get('currency_symbol', '$');
						$terms = \App\Support\Settings::getJson('loan_terms', []);
						$monthOptions = !empty($terms) ? array_values(array_map('intval', $terms)) : [3, 6, 12, 24];
						$defaultMonths = $monthOptions[0] ?? 6;
						$user = Auth::user();
						$info = $user?->info;
						$borrowerName = $info?->full_name ?? ($user?->name ?? $user?->username ?? '');
						$bankName = $info?->bank_name ?? '';
						$existingLoan = \App\Models\Loan::where('user_id', \Illuminate\Support\Facades\Auth::id())->orderByDesc('id')->first();
						$showDetails = $existingLoan && strtolower((string)$existingLoan->status) !== 'rejected';
					@endphp

					<div class="bg-white rich-content">
						<h1 class="text-xl font-bold text-blue-500 mb-4 flex items-center gap-2">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-blue-600">
								<rect x="2" y="5" width="20" height="14" rx="2"/>
								<path d="M2 10h20"/>
								<path d="M6 15h2"/>
								<path d="M10 15h5"/>
							</svg>
							{{ __('loan.title.apply') }}
						</h1>

						<!-- Loan Details (visible when a non-rejected loan exists or after submit) -->
						@php
							$detailsHiddenClass = $showDetails ? '' : 'hidden';
							$badgeClasses = 'px-2 py-0.5 text-xs rounded-full border';
							$st = strtolower((string)($existingLoan->status ?? ''));
							$badgeVariant = match ($st) {
								'approved' => 'bg-green-50 text-green-700 border-green-200',
								'processing' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
								'rejected' => 'bg-red-50 text-red-700 border-red-200',
								default => 'bg-gray-50 text-gray-700 border-gray-200',
							};
							$interestForDetails = $showDetails ? (float)($existingLoan->interest ?? $interestPercent) : $interestPercent;
							$amountForDetails = $showDetails ? (float)($existingLoan->amount ?? 0) : 0;
							$monthsForDetails = $showDetails ? (int)($existingLoan->period ?? 0) : 0;
							$monthlyForDetails = ($monthsForDetails > 0 && $amountForDetails > 0)
								? ($amountForDetails / $monthsForDetails) + ($amountForDetails * ($interestForDetails/100.0))
								: 0;
						@endphp
						<div id="loan-details" class="rounded-xl border border-gray-200 p-4 bg-white mb-4 {{ $detailsHiddenClass }}">
							<div id="loan-success" class="hidden mb-3 flex items-center gap-2 text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
								<span>{{ __('loan.message.submitted_success') }}</span>
							</div>
							<div class="flex items-center justify-between mb-3">
								<div class="flex items-center gap-2">
									<span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/>
										</svg>
									</span>
									<div>
										<div class="text-sm text-gray-500">{{ __('loan.label.loan_number') }}</div>
										<div id="detail-loan-number" class="text-base font-semibold text-gray-900">{{ $existingLoan->loan_number ?? '-' }}</div>
									</div>
								</div>
								<span id="detail-status-badge" class="{{ $badgeClasses }} {{ $badgeVariant }}">{{ ucfirst($existingLoan->status ?? '') }}</span>
							</div>
							<dl class="space-y-3">
								<div class="grid grid-cols-12 items-center">
									<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="M7 15h4"/></svg>
										<span>{{ __('loan.label.amount') }}</span>
									</dt>
									<dd id="detail-amount" class="col-span-6 text-right text-sm font-semibold text-gray-900">{{ $currencySymbol }} {{ number_format($amountForDetails, 2) }}</dd>
								</div>
								<div class="grid grid-cols-12 items-center">
									<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
										<span>{{ __('loan.text.repayment_plan') }}</span>
									</dt>
									<dd id="detail-plan" class="col-span-6 text-right text-sm font-semibold text-gray-900">{{ $monthsForDetails > 0 ? $monthsForDetails.' months' : '-' }}</dd>
								</div>
								<div class="grid grid-cols-12 items-center">
									<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a4 4 0 0 0-4 4v7"/><path d="M2 16h7"/><circle cx="16" cy="16" r="6"/><path d="M16 14v4"/><path d="M14 16h4"/></svg>
										<span>{{ __('loan.text.base_interest_rate') }}</span>
									</dt>
									<dd id="detail-interest" class="col-span-6 text-right text-sm font-semibold text-gray-900">{{ rtrim(rtrim(number_format($interestForDetails, 2, '.', ''), '0'), '.') }}%</dd>
								</div>
								<div class="grid grid-cols-12 items-center">
									<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12a8 8 0 1 1 8 8"/><path d="M12 6v6l4 2"/></svg>
										<span>{{ __('loan.text.recurring_payment') }}</span>
									</dt>
									<dd id="detail-recurring" class="col-span-6 text-right text-sm font-semibold text-gray-900">{{ $currencySymbol }} {{ number_format($monthlyForDetails, 2) }}</dd>
								</div>
								<div class="grid grid-cols-12 items-center">
									<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg>
										<span>{{ __('loan.label.start_date') }}</span>
									</dt>
									<dd id="detail-start-date" class="col-span-6 text-right text-sm font-semibold text-gray-900">{{ !empty($existingLoan?->start_date) ? \Carbon\Carbon::parse($existingLoan->start_date)->toDateString() : '-' }}</dd>
								</div>
							</dl>
						</div>

						@php $profileComplete = \App\Support\ProfileProgress::isComplete($user); @endphp
						<div class="space-y-6">
							@unless($profileComplete)
							<div class="w-full max-w-2xl mt-6">
								<div class="bg-white rounded-lg border-l-4 border-red-500 shadow-md p-5 flex items-start gap-4">
									<div class="text-red-500 mt-0.5">
										<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
											<line x1="12" y1="9" x2="12" y2="13"></line>
											<line x1="12" y1="17" x2="12.01" y2="17"></line>
										</svg>
									</div>
									<div class="flex-1">
										<h3 class="text-gray-900 font-semibold">{{ __('loan.title.complete_profile_required') }}</h3>
										<p class="text-gray-600 text-sm mt-1">
											{{ __('loan.message.complete_profile_required') }}
										</p>
										<div class="mt-4 flex items-center gap-4">
											<a href="{{ route('profile') }}" class="text-sm font-medium text-red-600 hover:text-red-800 flex items-center gap-1 group">
												{{ __('loan.button.complete_profile') }}
												<svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
												</svg>
											</a>
										</div>
									</div>
								</div>
							</div>
							@endunless

							@if($profileComplete && !$showDetails && ($existingLoan && strtolower((string)$existingLoan->status)==='rejected'))
							<div class="rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm">
								Your previous loan was rejected. You can reapply below.
							</div>
							@endif

							@if($profileComplete)
							<div id="loan-form-wrap" class="{{ $showDetails ? 'hidden' : '' }}">
							<!-- Amount -->
							<div>
								<label class="block text-sm text-gray-500 mb-1">{{ __('loan.label.applying_for') }}</label>
								<div class="flex items-end justify-between gap-4">
									<div class="w-full">
										<label for="loan-amount" class="block text-sm font-medium text-gray-900">{{ __('loan.label.amount') }}</label>
										<input id="loan-amount" type="number" max="{{ $maxAmount }}" step="1" value="{{ $minAmount }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" />
										<p class="text-xs text-gray-500 mt-1">{{ __('loan.text.amount_range', ['min' => number_format($minAmount), 'max' => number_format($maxAmount)]) }}</p>
										<div id="loan-amount-error" class="mt-2 hidden">
											<div class="flex items-start gap-2 rounded-md border border-red-200 bg-red-50 px-3 py-2">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
													<path fill-rule="evenodd" d="M10.29 3.86a1 1 0 0 1 1.42 0l8.47 8.47a1 1 0 0 1 0 1.42l-6.36 6.36a2 2 0 0 1-2.83 0l-6.36-6.36a1 1 0 0 1 0-1.42l6.66-6.47Zm1.21 4.64a.75.75 0 0 0-1.5 0v5a.75.75 0 0 0 1.5 0v-5Zm-.75 9.25a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
												</svg>
												<p id="loan-amount-error-text" class="text-sm text-red-700"></p>
											</div>
										</div>
									</div>
								</div>
								<input id="loan-amount-range" type="range" step="1000" min="{{ $minAmount }}" max="{{ $maxAmount }}" value="{{ $minAmount }}" class="w-full mt-3" />
							</div>

							<!-- Duration -->
							<div>
								<label class="block text-sm font-medium text-gray-900 mb-2">{{ __('loan.label.duration') }}</label>
								<div id="loan-duration-options" class="grid grid-cols-4 gap-2">
									@foreach($monthOptions as $m)
										<button type="button" data-months="{{ $m }}" class="duration-btn px-3 py-2 rounded-lg border text-sm inline-flex items-center gap-2 {{ $m===$defaultMonths ? 'bg-purple-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300' }}">
											<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<rect x="3" y="4" width="18" height="18" rx="2"/>
												<path d="M16 2v4"/>
												<path d="M8 2v4"/>
												<path d="M3 10h18"/>
											</svg>
											<span>{{ $m }}</span>
										</button>
									@endforeach
								</div>
							</div>

							<!-- Information -->
							<div class="rounded-xl border border-gray-200 p-4 mt-4 mb-4 bg-gray-50">
								<dl class="space-y-3">
									<div class="grid grid-cols-12 items-center">
										<dt class="col-span-7 flex items-center gap-2 text-sm text-gray-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M3 10h18"/>
												<path d="M7 15h4"/>
											</svg>
											<span>{{ __('loan.text.initial_loan_requested') }}</span>
										</dt>
										<dd id="info-initial" class="col-span-5 text-right text-sm font-semibold text-gray-900">0</dd>
									</div>
									<div class="grid grid-cols-12 items-center">
										<dt class="col-span-7 flex items-center gap-2 text-sm text-gray-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M13 2H6a4 4 0 0 0-4 4v7"/>
												<path d="M2 16h7"/>
												<circle cx="16" cy="16" r="6"/>
												<path d="M16 14v4"/>
												<path d="M14 16h4"/>
											</svg>
											<span>{{ __('loan.text.base_interest_rate') }}</span>
										</dt>
										<dd id="info-interest" class="col-span-5 text-right text-sm font-semibold text-gray-900">{{ rtrim(rtrim(number_format($interestPercent, 2, '.', ''), '0'), '.') }}%</dd>
									</div>
									<div class="grid grid-cols-12 items-center">
										<dt class="col-span-7 flex items-center gap-2 text-sm text-gray-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M4 12a8 8 0 1 1 8 8"/>
												<path d="M12 6v6l4 2"/>
											</svg>
											<span>{{ __('loan.text.recurring_payment') }}</span>
										</dt>
										<dd id="info-recurring" class="col-span-5 text-right text-sm font-semibold text-gray-900">0</dd>
									</div>
									<div class="grid grid-cols-12 items-center">
										<dt class="col-span-7 flex items-center gap-2 text-sm text-gray-600">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<rect x="3" y="4" width="18" height="18" rx="2"/>
												<path d="M16 2v4"/>
												<path d="M8 2v4"/>
												<path d="M3 10h18"/>
											</svg>
											<span>{{ __('loan.text.repayment_plan') }}</span>
										</dt>
										<dd id="info-plan" class="col-span-5 text-right text-sm font-semibold text-gray-900">-</dd>
									</div>
								</dl>
							</div>

							<!-- Agreement + Apply -->
							@php $agreementHtml = \App\Support\Settings::get('agreement_page', ''); @endphp
							<div class="flex items-start gap-3">
								<input id="loan-agree" type="checkbox" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked />
								<label for="loan-agree" class="text-sm text-gray-700">
									{{ __('loan.label.agree_terms') }}
									@if(!empty($agreementHtml))
									<a href="#" id="open-agreement" class="text-blue-600 underline ml-1">({{ __('loan.text.read_agreement') }})</a>
									@endif
								</label>
							</div>

							@if(!empty($agreementHtml))
							<!-- Agreement Modal -->
							<div id="agreement-modal" class="fixed inset-0 bg-black/40 z-50 hidden">
								<div class="absolute inset-0 flex items-center justify-center p-4">
									<div class="bg-white rounded-lg shadow-xl max-w-[740px] w-full max-h-[80vh] overflow-auto p-5 relative">
										<button type="button" id="close-agreement" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
										</button>
										<h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('loan.label.agree_terms') }}</h3>
										<div class="prose prose-sm max-w-none">
											{!! $agreementHtml !!}
										</div>
										<div class="mt-4 mb-8 text-right">
											<button type="button" id="close-agreement-2" class="inline-flex items-center px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Close</button>
										</div>
									</div>
								</div>
							</div>
							@endif

							<!-- Confirm Apply Modal -->
							<div id="confirm-modal" class="fixed inset-0 bg-black/40 z-50 hidden">
								<div class="absolute inset-0 flex items-center justify-center p-4">
									<div class="bg-white rounded-2xl shadow-2xl max-w-[740px] w-full max-h-[80vh] overflow-auto p-0 relative border border-gray-100">
										<div class="px-5 pt-5 pb-3 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
										<button type="button" id="close-confirm" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
										</button>
										<h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
											<span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<path d="M20 7h-9"/>
													<path d="M14 17H5"/>
													<circle cx="17" cy="17" r="3"/>
													<circle cx="7" cy="7" r="3"/>
												</svg>
											</span>
											<span>Confirm Loan Application</span>
										</h3>
										<p class="text-xs text-gray-500 mt-1">Please review your details before submitting your application.</p>
										</div>
										<div class="p-5">
											<dl class="space-y-3">
												<div class="grid grid-cols-12 items-center">
													<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M3 10h18"/>
															<path d="M7 15h4"/>
														</svg>
														<span>Loan amount applied</span>
													</dt>
													<dd id="confirm-amount" class="col-span-6 text-right text-sm font-semibold text-gray-900">-</dd>
												</div>
												<div class="grid grid-cols-12 items-center">
													<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
															<circle cx="12" cy="7" r="4"/>
														</svg>
														<span>Borrower</span>
													</dt>
													<dd class="col-span-6 text-right text-sm font-semibold text-gray-900">{{ $borrowerName ?: '-' }}</dd>
												</div>
												<div class="grid grid-cols-12 items-center">
													<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M3 22h18"/>
															<path d="M6 18V6a6 6 0 0 1 12 0v12"/>
														</svg>
														<span>Bank Name</span>
													</dt>
													<dd class="col-span-6 text-right text-sm font-semibold text-gray-900">{{ $bankName ?: '-' }}</dd>
												</div>
												<div class="grid grid-cols-12 items-center">
													<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M4 12a8 8 0 1 1 8 8"/>
															<path d="M12 6v6l4 2"/>
														</svg>
														<span>Recurring Payment</span>
													</dt>
													<dd id="confirm-recurring" class="col-span-6 text-right text-sm font-semibold text-gray-900">-</dd>
												</div>
												<div class="grid grid-cols-12 items-center">
													<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<rect x="3" y="4" width="18" height="18" rx="2"/>
															<path d="M16 2v4"/>
															<path d="M8 2v4"/>
															<path d="M3 10h18"/>
														</svg>
														<span>Repayment Plan</span>
													</dt>
													<dd id="confirm-plan" class="col-span-6 text-right text-sm font-semibold text-gray-900">-</dd>
												</div>
												<div class="grid grid-cols-12 items-center">
													<dt class="col-span-6 flex items-center gap-2 text-sm text-gray-600">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
															<path d="M8 2v4"/>
															<path d="M16 2v4"/>
															<rect x="3" y="4" width="18" height="18" rx="2"/>
															<path d="M3 10h18"/>
														</svg>
														<span>Loan Date</span>
													</dt>
													<dd id="confirm-date" class="col-span-6 text-right text-sm font-semibold text-gray-900">-</dd>
												</div>
											</dl>
											<div class="mt-6 mb-8 flex items-center justify-end gap-3">
												<button type="button" id="cancel-confirm" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">Cancel</button>
												<button type="button" id="confirm-submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition shadow-sm">
													<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
														<path d="M22 2L11 13"/>
														<path d="M22 2l-7 20-4-9-9-4 20-7z"/>
													</svg>
													<span>Confirm</span>
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="mt-4 mb-4">
								<button id="loan-apply-btn" type="button" class="w-full inline-flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-medium px-4 py-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
									{{ __('loan.button.apply') }}
								</button>
							</div>
							@endif
							</div>
						</div>
					</div>

					<script>
					(function() {
						const minAmount = {{ $minAmount }};
						const maxAmount = {{ $maxAmount }};
						const interestPercent = {{ $interestPercent }}; // percent value e.g. 0.5 => 0.5%
						const interestDecimal = interestPercent / 100.0;
						const currencySymbol = @json($currencySymbol);
						const monthsLabelTemplate = @json(__('loan.label.months', ['months' => '{months}']));
						const applyUrl = @json(route('loans.store', [], false));
						const csrfToken = @json(csrf_token());
						const monthButtons = document.querySelectorAll('.duration-btn');
						const amountInput = document.getElementById('loan-amount');
						const amountRange = document.getElementById('loan-amount-range');
						const agree = document.getElementById('loan-agree');
						const applyBtn = document.getElementById('loan-apply-btn');

						const infoInitial = document.getElementById('info-initial');
						const infoRecurring = document.getElementById('info-recurring');
						const infoPlan = document.getElementById('info-plan');
						const openAgreement = document.getElementById('open-agreement');
						const modal = document.getElementById('agreement-modal');
						const closeAgreement = document.getElementById('close-agreement');
						const closeAgreement2 = document.getElementById('close-agreement-2');

						// Confirm modal refs
						const confirmModal = document.getElementById('confirm-modal');
						const closeConfirm = document.getElementById('close-confirm');
						const cancelConfirm = document.getElementById('cancel-confirm');
						const confirmSubmit = document.getElementById('confirm-submit');
						const confirmAmount = document.getElementById('confirm-amount');
						const confirmRecurring = document.getElementById('confirm-recurring');
						const confirmPlan = document.getElementById('confirm-plan');
						const confirmDate = document.getElementById('confirm-date');

						// Details view refs (toggle after success)
						const detailsWrap = document.getElementById('loan-details');
						const detailsSuccess = document.getElementById('loan-success');
						const detailsLoanNumber = document.getElementById('detail-loan-number');
						const detailsAmount = document.getElementById('detail-amount');
						const detailsPlan = document.getElementById('detail-plan');
						const detailsInterest = document.getElementById('detail-interest');
						const detailsRecurring = document.getElementById('detail-recurring');
						const detailsStartDate = document.getElementById('detail-start-date');
						const formWrap = document.getElementById('loan-form-wrap');

						let months = {{ $defaultMonths }};
						let lastAmount = {{ $minAmount }};
						let lastMonthlyPayment = 0;

						function formatCurrency(n) {
							const num = (Math.round(n * 100) / 100).toLocaleString(undefined, { maximumFractionDigits: 2 });
							return currencySymbol + ' ' + num;
						}

						function updateActiveButton() {
							monthButtons.forEach(btn => {
								const isActive = parseInt(btn.dataset.months, 10) === months;
								btn.classList.toggle('bg-purple-600', isActive);
								btn.classList.toggle('text-white', isActive);
								btn.classList.toggle('border-blue-600', isActive);
								btn.classList.toggle('bg-white', !isActive);
								btn.classList.toggle('text-gray-700', !isActive);
								btn.classList.toggle('border-gray-300', !isActive);
							});
						}

						function clamp(val, min, max) { return Math.min(max, Math.max(min, val)); }

						function recalc() {
							// Do not clamp the input while typing; only validate on Apply
							const raw = parseFloat((amountInput && amountInput.value) || '');
							const amount = isNaN(raw) || raw < 0 ? 0 : raw;
							lastAmount = amount;
							// keep range in sync only if within bounds
							if (!isNaN(raw) && raw >= 0) {
								const bounded = clamp(raw, 0, {{ $maxAmount }});
								if (amountRange) amountRange.value = bounded;
							}

							// Compute preview values only when amount is positive; otherwise show zeros/placeholders
							const effAmount = Math.max(0, amount);
							const principalPerMonth = months > 0 ? (effAmount / months) : 0;
							const monthlyInterest = effAmount * interestDecimal; // flat rate per month
							const monthlyPayment = (effAmount > 0 && months > 0) ? (principalPerMonth + monthlyInterest) : 0;
							// Total across the term using flat rate fee
							const totalWithInterest = effAmount + (monthlyInterest * months);
							lastMonthlyPayment = monthlyPayment;

							if (infoInitial) infoInitial.textContent = effAmount > 0 ? formatCurrency(effAmount) : '0';
							if (infoRecurring) infoRecurring.textContent = (effAmount > 0 ? formatCurrency(monthlyPayment) : '0') + ' {{ __('loan.text.per_month') }}';
							if (infoPlan) infoPlan.textContent = (monthsLabelTemplate || '{months} months').replace('{months}', months);

							if (applyBtn) applyBtn.disabled = !agree.checked;
						}

						monthButtons.forEach(btn => {
							btn.addEventListener('click', () => {
								months = parseInt(btn.dataset.months, 10) || {{ $defaultMonths }};
								updateActiveButton();
								recalc();
							});
						});

						amountInput.addEventListener('input', recalc);
						amountRange.addEventListener('input', () => {
							amountInput.value = amountRange.value;
							recalc();
						});
						agree.addEventListener('change', recalc);

						// Agreement modal handlers
						if (openAgreement && modal) {
							openAgreement.addEventListener('click', function(e) {
								e.preventDefault();
								modal.classList.remove('hidden');
							});
						}
						function closeModal() {
							if (modal) modal.classList.add('hidden');
						}
						if (closeAgreement) closeAgreement.addEventListener('click', closeModal);
						if (closeAgreement2) closeAgreement2.addEventListener('click', closeModal);
						if (modal) {
							modal.addEventListener('click', function(e) {
								if (e.target === modal) closeModal();
							});
						}

						// Apply -> open confirm modal
						function openConfirm() {
							if (!confirmModal) return;
							confirmAmount.textContent = formatCurrency(lastAmount);
							confirmRecurring.textContent = formatCurrency(lastMonthlyPayment) + ' {{ __('loan.text.per_month') }}';
							confirmPlan.textContent = (monthsLabelTemplate || '{months} months').replace('{months}', months);
							try {
								confirmDate.textContent = new Date().toLocaleDateString();
							} catch (e) {
								confirmDate.textContent = '';
							}
							confirmModal.classList.remove('hidden');
						}
						function closeConfirmModal() {
							if (confirmModal) confirmModal.classList.add('hidden');
						}
						// Custom validation on Apply: amount must be >= minAmount
						const amountErrorWrap = document.getElementById('loan-amount-error');
						const amountErrorText = document.getElementById('loan-amount-error-text');
						const minAmountText = @json(__('loan.error.min_amount', ['min' => number_format($minAmount)]));
						applyBtn.addEventListener('click', function(){
							if (!amountInput) { openConfirm(); return; }
							if (amountErrorWrap) amountErrorWrap.classList.add('hidden');
							if (amountErrorText) amountErrorText.textContent = '';
							const raw = parseFloat((amountInput.value || '').toString());
							if (isNaN(raw) || raw < {{ $minAmount }}) {
								if (amountErrorText) {
									amountErrorText.textContent = minAmountText;
									if (amountErrorWrap) amountErrorWrap.classList.remove('hidden');
								} else {
									alert(minAmountText);
								}
								amountInput.focus();
								return;
							}
							lastAmount = raw;
							recalc(); // refresh previews using current amount without clamping to min
							openConfirm();
						});
						if (closeConfirm) closeConfirm.addEventListener('click', closeConfirmModal);
						if (cancelConfirm) cancelConfirm.addEventListener('click', closeConfirmModal);
						if (confirmModal) {
							confirmModal.addEventListener('click', function(e) {
								if (e.target === confirmModal) closeConfirmModal();
							});
						}
						if (confirmSubmit) {
							confirmSubmit.addEventListener('click', async function() {
								if (!applyUrl) return;
								confirmSubmit.disabled = true;
								confirmSubmit.classList.add('opacity-60', 'cursor-not-allowed');
								try {
									const res = await fetch(applyUrl, {
										method: 'POST',
										headers: {
											'Content-Type': 'application/json',
											'X-CSRF-TOKEN': csrfToken,
											'X-Requested-With': 'XMLHttpRequest',
										},
										body: JSON.stringify({
											amount: lastAmount,
											period: months
										}),
									});
									if (!res.ok) {
										const err = await res.json().catch(() => ({}));
										throw new Error(err.message || 'Request failed');
									}
									const data = await res.json();
									closeConfirmModal();
									// Show details view with success banner
									try {
										if (formWrap) formWrap.classList.add('hidden');
										if (detailsWrap) detailsWrap.classList.remove('hidden');
										if (detailsSuccess) detailsSuccess.classList.remove('hidden');
										if (detailsLoanNumber) detailsLoanNumber.textContent = data?.loan_number || '-';
										if (detailsAmount) detailsAmount.textContent = formatCurrency(lastAmount);
										if (detailsPlan) detailsPlan.textContent = (monthsLabelTemplate || '{months} months').replace('{months}', months);
										if (detailsInterest) detailsInterest.textContent = (Math.round(interestPercent * 100) / 100).toString().replace(/\.0+$/,'').replace(/(\.\d*[1-9])0+$/,'$1') + '%';
										if (detailsRecurring) detailsRecurring.textContent = formatCurrency(lastMonthlyPayment);
										if (detailsStartDate) {
											try { detailsStartDate.textContent = new Date().toLocaleDateString(); } catch(e) { /* noop */ }
										}
									} catch(e) {
										// no-op
									}
								} catch (e) {
									alert(e?.message || 'Submission failed');
								} finally {
									confirmSubmit.disabled = false;
									confirmSubmit.classList.remove('opacity-60', 'cursor-not-allowed');
								}
							});
						}
						// init
						updateActiveButton();
						recalc();
					})();
					</script>


				</div>
			</main>

			@include('partials.footer', ['active' => 'loan'])
		</div>
	</div>

</body>
</html>

