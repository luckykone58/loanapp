@extends('admin.layouts.app')

@section('title', 'Loans')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Loans</li>
@endsection
@section('actions')
<a href="{{ route('admin.loans.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
	<i data-lucide="plus-circle" class="h-4 w-4"></i>
	<span>Create Loan</span>
</a>
@endsection

@section('content')
<div class="loans-index">
@if(session('success'))
	<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
@endif

<fieldset class="mb-4 bg-white border border-primary rounded-lg p-4">
	<legend class="px-2 text-primary font-semibold">Filter</legend>
	<form method="GET" action="{{ route('admin.loans.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
		<div class="md:col-span-2">
			<label class="block text-xs text-slate-500 mb-1">Search</label>
			<div class="relative">
				<span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
					<i data-lucide="search" class="h-4 w-4"></i>
				</span>
				<input type="text" name="q" value="{{ request('q') }}" placeholder="Loan # or Username" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
		</div>
		<div>
			<label class="block text-xs text-slate-500 mb-1">Start From</label>
			<input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
		</div>
		<div>
			<label class="block text-xs text-slate-500 mb-1">Start To</label>
			<input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
		</div>
		<div class="md:col-span-2 flex items-end gap-2">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="filter" class="h-4 w-4"></i><span>Filter</span>
			</button>
			<a href="{{ route('admin.loans.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-reset-white">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Reset</span>
			</a>
		</div>
	</form>
</fieldset>

<div class="bg-white shadow">
	<table class="min-w-full divide-y divide-slate-200">
		<thead class="bg-slate-200 dark:bg-slate-800">
			<tr>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">ID</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">User</th>
				@php
					$admin = auth('admin')->user();
				@endphp
				@if($admin && $admin->role === 'SuperAdmin')
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Domain</th>
				@endif
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Loan #</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Amount</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Terms</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Status</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Start</th>
				<th class="px-4 py-2"></th>
			</tr>
		</thead>
		<tbody class="divide-y divide-slate-200">
			@foreach($loans as $loan)
				<tr>
					<td class="px-4 py-3 text-sm">{{ $loan->id }}</td>
					<td class="px-4 py-3 text-sm">{{ optional($loan->user)->username ?? $loan->user_id }}</td>
					@if($admin && $admin->role === 'SuperAdmin')
					<td class="px-4 py-3 text-sm">
						{{ $loan->user?->domain?->name ?? '—' }}
						@if($loan->user?->domain?->host)
							<span class="text-slate-400">({{ $loan->user->domain->host }})</span>
						@endif
					</td>
					@endif
					<td class="px-4 py-3 text-sm">{{ $loan->loan_number }}</td>
					<td class="px-4 py-3 text-sm">{{ number_format($loan->amount, 2) }}</td>
					<td class="px-4 py-3 text-sm">{{ (int) $loan->period }}</td>
					@php
						$_s = strtolower((string) $loan->status);
						$_badgeColors = [
							'approved' => 'bg-green-100 text-green-700',
							'approve' => 'bg-green-100 text-green-700',
							'processing' => 'bg-yellow-100 text-yellow-700',
							'pending' => 'bg-yellow-100 text-yellow-700',
							'rejected' => 'bg-red-100 text-red-700',
							'reject' => 'bg-red-100 text-red-700',
						];
						$_color = $_badgeColors[$_s] ?? 'bg-slate-100 text-slate-700';
					@endphp
					<td class="px-4 py-3 text-sm">
						<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $_color }}">{{ ucfirst($_s) }}</span>
					</td>
					<td class="px-4 py-3 text-sm">{{ optional($loan->start_date)->format('Y-m-d') }}</td>
					<td class="px-4 py-3 text-sm text-right space-x-2 relative">
						<a href="{{ route('admin.loans.edit', $loan) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border hover:bg-[#459699]/10 text-[#459699] btn-edit">
							<i data-lucide="pencil" class="h-3 w-3"></i><span>Edit</span>
						</a>
						<form action="{{ route('admin.loans.destroy', $loan) }}" method="POST" class="inline">
							@csrf @method('DELETE')
							<button type="submit" data-confirm-delete data-message="Are you sure you want to remove this loan?" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border text-red-600 hover:bg-red-50">
								<i data-lucide="trash-2" class="h-3 w-3"></i><span>Delete</span>
							</button>
						</form>
						<button type="button" class="inline-flex items-center gap-1 text-slate-600 hover:text-slate-900 ml-1"
							data-menu-toggle="menu-loan-{{ $loan->id }}">
							<i data-lucide="more-horizontal" class="h-4 w-4"></i><span class="sr-only">More</span>
						</button>
						<!-- Submenu -->
						<div id="menu-loan-{{ $loan->id }}" class="hidden absolute right-0 mt-2 z-20 bg-white dark:bg-slate-800 border border-primary rounded-lg shadow p-2 overflow-hidden">
							<div class="flex items-center gap-1">
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2"
									data-quick-open="#tpl-loan-review-{{ $loan->id }}" data-quick-title="Review">
									<i data-lucide="check-circle" class="h-4 w-4 text-[#459699]"></i><span>Review</span>
								</button>
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2"
									data-quick-open="#tpl-loan-contract-{{ $loan->id }}" data-quick-title="Loan Contract">
									<i data-lucide="file-text" class="h-4 w-4 text-[#459699]"></i><span>View Contract</span>
								</button>
								<a href="{{ route('admin.users.edit', $loan->user_id) }}" class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2">
									<i data-lucide="user" class="h-4 w-4 text-[#459699]"></i><span>Checking Data</span>
								</a>
							</div>
						</div>
						<!-- Hidden templates per loan (cloned into modal on open) -->
						<div class="hidden">
							<div id="tpl-loan-review-{{ $loan->id }}">
								<form method="POST" action="{{ route('admin.loans.review', $loan) }}" class="space-y-3 quick-review-form">
									@csrf
									<div>
										<label class="block mb-1">Select Title</label>
										<select name="title_suggestion" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent dark:[&>option]:bg-[#459699]">
											<option value="Overdue Record">Overdue Record</option>
											<option value="Insurance">Insurance</option>
											<option value="Invalid Card Number or Bank Account Number">Invalid Card Number or Bank Account Number</option>
											<option value="Platform Free">Platform Free</option>
											<option value="Unfrozen Account">Unfrozen Account</option>
											<option value="Confirmed New OTP And New Documentary">Confirmed New OTP And New Documentary</option>
											<option value="New Document And New OTP Code">New Document And New OTP Code</option>
											<option value="Live Insurance">Live Insurance</option>
											<option value="Loan Account is Insured">Loan Account is Insured</option>
											<option value="Bank Account Involving Illegal Activities">Bank Account Involving Illegal Activities</option>
											<option value="Conformation of Bank Account">Conformation of Bank Account</option>
											<option value="Handling Fee">Handling Fee</option>
											<option value="VIP Channel">VIP Channel</option>
											<option value="Approved">Approved</option>
											<option value="Successfully Application">Successfully Application</option>
											<option value="Freeze Loan Account">Freeze Loan Account</option>
											<option value="Insufficient Credit">Insufficient Credit</option>
											<option value="Withdraw Successfully">Withdraw Successfully</option>
											<option value="Gambling Insurance">Gambling Insurance</option>
											<option value="Invalid Card Number Or Invalid Bank Number">Invalid Card Number Or Invalid Bank Number</option>
											<option value="Rejected">Rejected</option>
										</select>
									</div>
									<div>
										<label class="block mb-1">Title</label>
										<input type="text" name="title" value="" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent js-title-input" placeholder="Enter final title">
									</div>
									<div>
										<label class="block mb-1">Message</label>
										<textarea name="message" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required></textarea>
									</div>
									<div>
										<label class="block mb-1">Subtext</label>
										<input type="text" name="subtext" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
									</div>
									<div>
										<label class="block mb-1">Notes</label>
										<textarea name="notes" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent"></textarea>
									</div>
								</form>
							</div>
							<div id="tpl-loan-contract-{{ $loan->id }}" class="max-w-[720px]">
								@php
									$__user = $loan->user;
									$__info = $__user?->info;
									$__currency = (string) \App\Support\Settings::get('currency_symbol', '$');
									$__contractHtml = (string) \App\Support\Settings::get('contract_page', '');
									$__fullName = (string) ($__info?->full_name ?? ($__user->name ?? $__user->username ?? ''));
									$__idNumber = (string) ($__info?->id_card_number ?? '');
									$__username = (string) ($__user->username ?? '');
									$__loanAmountStr = $__currency.' '.number_format((float)$loan->amount, 2);
									$__loanPeriodStr = (string) ((int)$loan->period);
									$__bankName = (string) ($__info?->bank_name ?? '');
									$__contractView = $__contractHtml;
									if (!empty($__contractView)) {
										$__contractView = strtr($__contractView, [
											'[full_name]' => e($__fullName),
											'[id_card_number]' => e($__idNumber),
											'[username]' => e($__username),
											'[loan_amount]' => e($__loanAmountStr),
											'[loan_period]' => e($__loanPeriodStr),
											'[bank_name]' => e($__bankName),
										]);
									}
									$__signaturePath = (string) ($__info?->signature ?? '');
								@endphp
								<div class="flex flex-col w-[720px] max-w-[90vw]">
									<div class="border rounded bg-white p-3 max-h-[60vh] overflow-y-auto dark:bg-[#459699]">
										<div class="prose prose-sm max-w-none rich-content ">{!! $__contractView !!}</div>
										@if(!empty($__signaturePath))
										<div class="mt-4">
											<div class="text-xs font-semibold text-gray-600 mb-1">Signature</div>
											<img src="{{ asset('storage/'.ltrim($__signaturePath, '/')) }}" alt="Signature" class="max-h-40 object-contain border rounded p-2 bg-gray-50">
										</div>
										@endif
									</div>
								</div>
							</div>
						</div>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>

<div class="mt-4">
	<div class="flex items-center justify-between text-sm text-slate-600 mb-2">
		<div>
			Page {{ $loans->currentPage() }} of {{ $loans->lastPage() }}
			<span class="ml-2">Showing {{ $loans->firstItem() }}–{{ $loans->lastItem() }} of {{ $loans->total() }}</span>
		</div>
	</div>
	{{ $loans->links() }}
</div>
<script>
document.addEventListener('click', function(e){
	// Toggle submenu
	const toggle = e.target.closest('[data-menu-toggle]');
	if (toggle) {
		const id = toggle.getAttribute('data-menu-toggle');
		const menu = document.getElementById(id);
		if (menu) {
			menu.classList.toggle('hidden');
		}
		return;
	}
	// Open quick modal from template
	const openBtn = e.target.closest('[data-quick-open]');
	if (openBtn) {
		e.preventDefault();
		// hide any open menus
		document.querySelectorAll('[id^="menu-loan-"]').forEach(m => m.classList.add('hidden'));
		const tplSel = openBtn.getAttribute('data-quick-open');
		const tpl = document.querySelector(tplSel);
		if (tpl && window.openQuickModal) {
			const customTitle = openBtn.getAttribute('data-quick-title') || 'Quick Action';
			window.openQuickModal(tpl.innerHTML, customTitle);
			if (window.lucide && lucide.createIcons) { lucide.createIcons(); }
			// After modal opens, sync title and inject footer Save for review form
			setTimeout(function(){
				const sel = document.querySelector('#quick-edit-backdrop select[name="title_suggestion"]');
				const out = document.querySelector('#quick-edit-backdrop .js-title-input');
				if (sel && out) out.value = sel.value || '';
				const footer = document.querySelector('#quick-edit-backdrop .border-t.flex.justify-end.gap-2');
				if (footer) {
					footer.querySelectorAll('[data-quick-save]').forEach(function(btn){ btn.remove(); });
					const form = document.querySelector('#quick-edit-backdrop .quick-review-form');
					if (form) {
						const saveBtn = document.createElement('button');
						saveBtn.setAttribute('type', 'button');
						saveBtn.setAttribute('data-quick-save', 'true');
						saveBtn.className = 'inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90';
						saveBtn.innerHTML = '<i data-lucide="save" class="h-4 w-4"></i><span>Save</span>';
						saveBtn.addEventListener('click', function(){ form.submit(); });
						footer.appendChild(saveBtn);
						if (window.lucide && lucide.createIcons) { lucide.createIcons(); }
					}
				}
			}, 0);
		}
		return;
	}
	// Close submenu when clicking outside
	if (!e.target.closest('[id^="menu-loan-"]')) {
		document.querySelectorAll('[id^="menu-loan-"]').forEach(m => m.classList.add('hidden'));
	}
});
</script>
<script>
(function(){
	// Keep "Title" read-only field in sync with selected option inside the quick modal
	document.addEventListener('change', function(e){
		const sel = e.target.closest('#quick-edit-backdrop select[name="title_suggestion"]');
		if (sel) {
			const form = sel.closest('form');
			if (!form) return;
			const out = form.querySelector('.js-title-input');
			if (out) out.value = sel.value || '';
		}
	});
})();
</script>
</div>
@endsection



