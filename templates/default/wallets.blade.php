<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $currentDomain->name ?? config('app.name') }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="/css/main.css">
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
<body>
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')

			<main class="flex-1 pb-0 relative pb-8 px-2">
				<div class="relative z-10 p-4 space-y-6 top-[10px] pb-40">
				
					@php
						$user = \Illuminate\Support\Facades\Auth::user();
						$info = $user?->info;
						$currency = (string) \App\Support\Settings::get('currency_symbol', '$');
						$wallet = (float) ($info?->wallet ?? 0);
						$creditScore = (int) ($info?->credit_score ?? \App\Support\Settings::get('default_credit_score', 30));
						$creditScore = max(0, min(1000, $creditScore)); // clamp 0-1000
						$pendingWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)->where('status', 'processing')->count();
						$pendingWithdrawalAmount = (float) \App\Models\Withdrawal::where('user_id', $user->id)->where('status', 'processing')->sum('amount');
						$available = max(0, $wallet - $pendingWithdrawalAmount);
						$withdrawable = $available;
						$withdrawnAmount = (float) \App\Models\Withdrawal::where('user_id', $user->id)->whereIn('status', ['approved', 'fulfilled'])->sum('amount');
						$hasPending = $pendingWithdrawals > 0;
						$loan = \App\Models\Loan::where('user_id', $user->id)->orderByDesc('id')->first();
						$loanVisible = $loan && strtolower((string)$loan->status) !== 'rejected';
						$interestPercent = (float) ($loan?->interest ?? \App\Support\Settings::get('loan_interest_rate', 0.5));
						$monthly = ($loanVisible && (int)$loan->period > 0 && (float)$loan->amount > 0)
							? (((float)$loan->amount / (int)$loan->period) + ((float)$loan->amount * ($interestPercent/100.0)))
							: 0;
						$contractHtml = (string) \App\Support\Settings::get('contract_page', '');
						// Prepare contract view with variables replaced
						$fullName = (string) ($info?->full_name ?? ($user->name ?? $user->username ?? ''));
						$idNumber = (string) ($info?->id_card_number ?? '');
						$username = (string) ($user->username ?? '');
						$loanAmountStr = $loanVisible ? ($currency.' '.number_format((float)$loan->amount, 2)) : '';
						$loanPeriodStr = $loanVisible ? ((int)$loan->period) : '';
						$bankName = (string) ($info?->bank_name ?? '');
						$contractView = $contractHtml;
						if (!empty($contractView)) {
							$contractView = strtr($contractView, [
								'[full_name]' => e($fullName),
								'[id_card_number]' => e($idNumber),
								'[username]' => e($username),
								'[loan_amount]' => e($loanAmountStr),
								'[loan_period]' => e((string) $loanPeriodStr),
								'[bank_name]' => e($bankName),
							]);
						}
						$signaturePath = (string) ($info?->signature ?? '');
						$profileComplete = \App\Support\ProfileProgress::isComplete($user);
						$canWithdraw = $profileComplete && $withdrawable > 0;
					@endphp
					@if(session('success'))
					<div class="mb-3 p-3 rounded bg-green-50 text-green-700 text-sm">
						{{ session('success') }}
					</div>
					@endif
					@unless($profileComplete)
					<div class="w-full max-w-2xl mt-2">
						<div class="bg-white rounded-lg border-l-4 border-red-500 shadow-md p-5 flex items-start gap-4">
							<div class="text-red-500 mt-0.5">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
									<line x1="12" y1="9" x2="12" y2="13"></line>
									<line x1="12" y1="17" x2="12.01" y2="17"></line>
								</svg>
							</div>
							<div class="flex-1">
								<h3 class="text-gray-900 font-semibold">{{ __('wallets.title.complete_profile_required') }}</h3>
								<p class="text-gray-600 text-sm mt-1">
									{{ __('wallets.message.complete_profile_required') }}
								</p>
								<div class="mt-4 flex items-center gap-4">
									<a href="{{ route('profile') }}" class="text-sm font-medium text-red-600 hover:text-red-800 flex items-center gap-1 group">
										{{ __('wallets.button.go_to_profile') }}
										<svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
										</svg>
									</a>
								</div>
							</div>
						</div>
					</div>
					@endunless
					@if($profileComplete)
					<!-- Hero Card (Balance & Score) -->
					<div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] mb-2">
						<div class="flex items-start justify-between mb-6">
							<div>
								<p class="text-sm text-gray-500 font-medium mb-1">{{ __('wallets.label.account_balance') }}</p>
								<h1 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $currency }} {{ number_format($wallet, 2) }}</h1>
								
							</div>
							<div class="relative w-20 h-20 flex-shrink-0">
								<div class="w-full h-full rounded-full flex items-center justify-center -rotate-90"
									 style="background: conic-gradient(#2563eb {{ $creditScore * 0.36 }}deg, #f3f4f6 0deg);">
									<div class="bg-white w-[85%] h-[85%] rounded-full flex flex-col items-center justify-center rotate-90">
										<span class="text-xl font-bold text-gray-900 leading-none">{{ $creditScore }}</span>
										<span class="text-[8px] font-bold text-gray-400 uppercase mt-0.5">{{ __('wallets.text.score') }}</span>
									</div>
								</div>
							</div>
						</div>
						<div class="grid grid-cols-2 gap-3">
							<div class="bg-blue-50 rounded-2xl p-2 border border-blue-100 flex flex-col justify-center">
								<p class="text-xs text-gray-400 font-medium">{{ __('wallets.label.available_balance') }}</p>
								<p class="text-sm font-bold text-gray-800 truncate">{{ $currency }} {{ number_format($available, 2) }}</p>
							</div>
							<div class="bg-emerald-50 rounded-2xl p-2 border border-emerald-100 flex flex-col justify-center">
								<p class="text-xs text-gray-400 font-medium">{{ __('wallets.label.withdrawn_amount') }}</p>
								<p class="text-sm font-bold text-gray-800 truncate">{{ $currency }} {{ number_format($withdrawnAmount, 2) }}</p>
							</div>
						</div>
					</div>

					<!-- Actions -->
					<div class="grid grid-cols-2 gap-4 mb-2">
						<button type="button" id="open-withdraw"
							class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-white font-medium shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white active:scale-[.98] {{ $canWithdraw && !$hasPending ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-300' : 'bg-gray-300 cursor-not-allowed focus:ring-gray-300' }}"
							{{ $canWithdraw && !$hasPending ? '' : 'disabled' }}>
							<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 3v12"/>
								<path d="M7 8l5 5 5-5"/>
								<path d="M19 21H5"/>
							</svg>
							<span>{{ __('wallets.button.withdraw_fund') }}</span>
						</button>
						<a href="{{ route('withdrawals.index') }}"
							class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg font-medium {{ $profileComplete ? 'bg-yellow-100 text-yellow-800 border border-yellow-300 hover:bg-yellow-200' : 'bg-gray-100 text-gray-500 cursor-not-allowed pointer-events-none opacity-60' }}">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M3 3v18h18"/>
								<path d="m19 9-5 5-4-4-3 3"/>
							</svg>
							<span>{{ __('wallets.button.view_history') }}</span>
						</a>
					</div>

					<!-- Loan Information -->
					<div class="bg-white rounded-3xl p-6 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] border border-gray-100 mb-2 relative overflow-hidden">
						<div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full opacity-50 pointer-events-none"></div>
						<div class="flex items-center justify-between mb-3">
							<div class="flex items-center gap-2">
								<span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 shadow-sm"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg></span>
								<h3 class="text-base font-bold text-gray-900">{{ __('wallets.title.loan_info') }}</h3>
							</div>
							@if($loanVisible)
								<span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ strtolower($loan->status)==='approved' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200' }}">
									{{ ucfirst($loan->status) }}
								</span>
							@else
								<span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">{{ __('wallets.text.no_active_loan') }}</span>
							@endif
						</div>
						@if($loanVisible)
						<dl class="space-y-4 relative">
							<div class="flex justify-between items-center pb-3 border-b border-dashed border-gray-200">
								<dt class="text-sm text-gray-500">{{ __('wallets.label.loan_number') }}</dt>
								<dd class="text-sm font-semibold text-gray-900 font-mono">{{ $loan->loan_number }}</dd>
							</div>
							<div class="flex justify-between items-center pb-3 border-b border-dashed border-gray-200">
								<dt class="text-sm text-gray-500">{{ __('wallets.label.amount') }}</dt>
								<dd class="text-sm font-semibold text-gray-900">{{ $currency }} {{ number_format((float)$loan->amount, 2) }}</dd>
							</div>
							<div class="flex justify-between items-center pb-3 border-b border-dashed border-gray-200">
								<dt class="text-sm text-gray-500">{{ __('wallets.label.repayment_plan') }}</dt>
								<dd class="text-sm font-semibold text-gray-900">{{ (int)$loan->period }} months</dd>
							</div>
							<div class="flex justify-between items-center pb-3 border-b border-dashed border-gray-200">
								<dt class="text-sm text-gray-500">{{ __('wallets.label.interest') }}</dt>
								<dd class="text-sm font-semibold text-blue-700">{{ rtrim(rtrim(number_format($interestPercent, 2, '.', ''), '0'), '.') }}%</dd>
							</div>
							<div class="flex justify-between items-center">
								<dt class="text-sm text-gray-500">{{ __('wallets.label.recurring_payment') }}</dt>
								<dd class="text-sm font-bold text-gray-900">{{ $currency }} {{ number_format($monthly, 2) }}</dd>
							</div>
						</dl>
						@endif
					</div>

					<!-- Review Contract -->
					@if($loanVisible && !empty($contractHtml))
					<div>
						<button type="button" id="open-contract" class="w-full flex items-center justify-between p-4 rounded-2xl bg-blue-900 text-white shadow-lg hover:shadow-xl hover:bg-blue-800 transition-all active:scale-[0.99]">
							<div class="flex items-center gap-3">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
								<span class="font-semibold">{{ __('wallets.button.review_contract') }}</span>
							</div>
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
						</button>
					</div>
					<!-- Contract Modal -->
					<div id="contract-modal" class="fixed inset-0 bg-black/40 z-50 hidden">
						<div class="absolute inset-0 flex items-center justify-center p-4">
							<div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[85vh] overflow-hidden flex flex-col">
								<div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex items-center justify-between">
									<h3 class="text-lg font-bold text-gray-900">{{ __('wallets.title.contract') }}</h3>
									<button type="button" id="close-contract" class="bg-white rounded-full p-1 text-gray-400 hover:text-gray-500 hover:bg-gray-100 border border-gray-200">
										<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
									</button>
								</div>
								<div class="px-6 py-4 overflow-y-auto rich-content text-sm text-gray-600 bg-white">
									<div class="prose prose-sm max-w-none bg-white rich-content">
										{!! $contractView !!}
									</div>
									@if(!empty($signaturePath))
									<div class="mt-6">
										<div class="text-xs font-semibold text-gray-600 mb-2">{{ __('profile.label.signature') }}</div>
										<img src="{{ asset('storage/'.ltrim($signaturePath, '/')) }}" alt="Signature" class="max-h-40 object-contain border rounded p-2 bg-gray-50">
									</div>
									@endif
								</div>
								<div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
									<button type="button" id="close-contract-2" class="w-full inline-flex justify-center rounded-xl bg-blue-600 px-3 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">{{ __('wallets.button.close') }}</button>
								</div>
							</div>
						</div>
					</div>
					@endif
					@endif

				</div>
			</main>

			@include('partials.footer', ['active' => 'wallet'])
		</div>
	</div>

	<script>
	(function() {
		const openBtn = document.getElementById('open-contract');
		const modal = document.getElementById('contract-modal');
		const close1 = document.getElementById('close-contract');
		const close2 = document.getElementById('close-contract-2');
		if (openBtn && modal) {
			openBtn.addEventListener('click', function(e) {
				e.preventDefault();
				modal.classList.remove('hidden');
			});
		}
		function closeModal() { if (modal) modal.classList.add('hidden'); }
		if (close1) close1.addEventListener('click', closeModal);
		if (close2) close2.addEventListener('click', closeModal);
		if (modal) {
			modal.addEventListener('click', function(e) {
				if (e.target === modal) closeModal();
			});
		}
	})();
	</script>
	<script>
	(function() {
		function setError(msg) {
			var errBox = document.getElementById('withdraw-error');
			var errText = document.getElementById('withdraw-error-text');
			if (errBox) {
				if (errText) {
					errText.textContent = msg || '';
				} else {
					errBox.textContent = msg || '';
				}
				errBox.classList.toggle('hidden', !msg);
			}
		}
		// i18n strings
		const tErrValidAmount = @json(__('wallets.error.enter_valid_amount'));
		const tErrExceeds = @json(__('wallets.error.amount_exceeds_wallet'));
		const tErrCodeRequired = @json(__('wallets.error.withdrawal_code_required'));
		const tErrInvalidCode = @json(__('wallets.error.invalid_withdrawal_code'));
		// Open modal on button click (works even if modal is defined later)
		document.addEventListener('click', function(e){
			var openBtn = e.target.closest('#open-withdraw');
			if (openBtn) {
				e.preventDefault();
				setError('');
				var modal = document.getElementById('withdraw-modal');
				if (modal) {
					modal.classList.remove('hidden');
					setTimeout(function(){
						var amount = document.getElementById('withdraw-amount');
						if (amount) {
							amount.focus();
							amount.select();
						}
					}, 0);
				}
			}
		});
		// Close handlers (header X, footer Close, backdrop)
		document.addEventListener('click', function(e){
			if (e.target.closest('#close-withdraw') || e.target.closest('#close-withdraw-2')) {
				e.preventDefault();
				var modal = document.getElementById('withdraw-modal');
				if (modal) modal.classList.add('hidden');
			}
		});
		document.addEventListener('click', function(e){
			var modal = document.getElementById('withdraw-modal');
			if (modal && e.target === modal) {
				modal.classList.add('hidden');
			}
		});
		// Apply Now validation and submit
		document.addEventListener('click', function(e){
			if (e.target.closest('#apply-withdraw')) {
				e.preventDefault();
				setError('');
				var form = document.getElementById('withdraw-form');
				var amountInput = document.getElementById('withdraw-amount');
				var codeInput = document.getElementById('withdraw-code');
				if (!form) return;
				var wallet = parseFloat(form.getAttribute('data-wallet') || '0') || 0;
				var expectedCode = (form.getAttribute('data-code') || '').trim();
				var amount = parseFloat((amountInput && amountInput.value) || '0');
				var code = (codeInput && codeInput.value || '').trim();
				if (!amount || isNaN(amount) || amount <= 0) {
					return setError(tErrValidAmount);
				}
				if (amount > wallet) {
					return setError(tErrExceeds);
				}
				if (!code) {
					return setError(tErrCodeRequired);
				}
				if (!expectedCode) {
					return setError(tErrInvalidCode);
				}
				if (code !== expectedCode) {
					return setError(tErrInvalidCode);
				}
				form.submit();
			}
		});
		// Pressing Enter inside the modal triggers Apply
		document.addEventListener('keydown', function(e){
			var modal = document.getElementById('withdraw-modal');
			if (modal && !modal.classList.contains('hidden') && e.key === 'Enter') {
				// Avoid double submit if focus is on a button already
				var active = document.activeElement;
				if (active && (active.id === 'apply-withdraw' || active.id === 'close-withdraw-2')) return;
				e.preventDefault();
				var btn = document.getElementById('apply-withdraw');
				if (btn) btn.click();
			}
		});
	})();
	</script>

	<!-- Withdraw Modal -->
	<div id="withdraw-modal" class="fixed inset-0 bg-black/40 z-50 hidden">
		<div class="absolute inset-0 flex items-center justify-center p-4">
			<div class="bg-white rounded-lg shadow-xl max-w-[420px] w-full overflow-hidden">
				<div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
					<h3 class="text-sm font-semibold text-gray-900">{{ __('wallets.button.withdraw_fund') }}</h3>
					<button type="button" id="close-withdraw" class="text-gray-500 hover:text-gray-700">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
					</button>
				</div>
				<form id="withdraw-form" method="POST" action="{{ route('withdrawals.store') }}" class="p-4 space-y-3"
					  data-wallet="{{ number_format($available, 2, '.', '') }}"
					  data-code="{{ (string)($info?->withdrawal_code ?? '') }}">
					@csrf
					<div id="withdraw-error" class="hidden text-sm text-red-700 bg-red-50 border border-red-200 rounded p-2 flex items-start gap-2" role="alert" aria-live="polite">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10"></circle>
							<line x1="12" y1="8" x2="12" y2="12"></line>
							<line x1="12" y1="16" x2="12.01" y2="16"></line>
						</svg>
						<span id="withdraw-error-text"></span>
					</div>
					<div>
						<label class="block text-sm text-gray-700 mb-1">{{ __('wallets.label.amount') }}</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect width="20" height="12" x="2" y="6" rx="2"></rect>
									<circle cx="12" cy="12" r="2"></circle>
									<path d="M6 12h.01M18 12h.01"></path>
								</svg>
							</span>
							<input id="withdraw-amount" name="amount" type="number" step="0.01" min="0" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500" placeholder="0.00">
						</div>
						<div class="mt-1 text-xs text-gray-500">{{ __('wallets.text.available') }} {{ $currency }} {{ number_format($available, 2) }}</div>
					</div>
					<div>
						<label class="block text-sm text-gray-700 mb-1">{{ __('wallets.label.withdrawal_code') }}</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-blue-600">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="11" width="18" height="11" rx="2"></rect>
									<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
								</svg>
							</span>
							<input id="withdraw-code" name="withdrawal_code" type="password" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500" placeholder="••••••">
						</div>
					</div>
					<div class="pt-2 flex justify-end gap-2">
						<button type="button" id="close-withdraw-2" class="inline-flex items-center gap-2 px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
							<span>{{ __('wallets.button.close') }}</span>
						</button>
						<button type="button" id="apply-withdraw" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 active:scale-[.98]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<polyline points="20 6 9 17 4 12"></polyline>
							</svg>
							<span>{{ __('wallets.button.apply_now') }}</span>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</body>
</html>

