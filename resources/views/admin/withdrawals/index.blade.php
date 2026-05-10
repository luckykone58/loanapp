@extends('admin.layouts.app')

@section('title', 'Withdrawals')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Withdrawals</li>
@endsection
@section('actions')
<a href="{{ route('admin.withdrawals.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
	<i data-lucide="plus-square" class="h-4 w-4"></i>
	<span>Create Withdrawal</span>
</a>
@endsection

@section('content')
<div class="withdrawals-index">
@if(session('success'))
	<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
@endif

<fieldset class="mb-4 bg-white border border-primary rounded-lg p-4">
	<legend class="px-2 text-primary font-semibold">Filter</legend>
	<form method="GET" action="{{ route('admin.withdrawals.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
		<div class="md:col-span-2">
			<label class="block text-xs text-slate-500 mb-1">Search</label>
			<div class="relative">
				<span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
					<i data-lucide="search" class="h-4 w-4"></i>
				</span>
				<input type="text" name="q" value="{{ request('q') }}" placeholder="Username or Name" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
		</div>
		<div>
			<label class="block text-xs text-slate-500 mb-1">Created From</label>
			<input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
		</div>
		<div>
			<label class="block text-xs text-slate-500 mb-1">Created To</label>
			<input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
		</div>
		<div class="md:col-span-2 flex items-end gap-2">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="filter" class="h-4 w-4"></i><span>Filter</span>
			</button>
			<a href="{{ route('admin.withdrawals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-reset-white">
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
				@php($admin = auth('admin')->user())
				@if($admin && $admin->role === 'SuperAdmin')
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Domain</th>
				@endif
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Amount</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Date</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Bank Name</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Bank Account</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Status</th>
				<th class="px-4 py-2"></th>
			</tr>
		</thead>
		<tbody class="divide-y divide-slate-200">
			@foreach($withdrawals as $w)
				<tr>
					<td class="px-4 py-3 text-sm">{{ $w->id }}</td>
					<td class="px-4 py-3 text-sm">{{ optional($w->user ?? null)->username ?? $w->user_id }}</td>
					@if($admin && $admin->role === 'SuperAdmin')
					<td class="px-4 py-3 text-sm">
						{{ $w->user?->domain?->name ?? '—' }}
						@if($w->user?->domain?->host)
							<span class="text-slate-400">({{ $w->user->domain->host }})</span>
						@endif
					</td>
					@endif
					<td class="px-4 py-3 text-sm">{{ number_format((float)$w->amount, 2) }}</td>
					<td class="px-4 py-3 text-sm">{{ optional($w->created_date ? \Carbon\Carbon::parse($w->created_date) : null)?->format('Y-m-d H:i') }}</td>
					<td class="px-4 py-3 text-sm">{{ $w->withdraw_name ?? '—' }}</td>
					<td class="px-4 py-3 text-sm font-mono">{{ $w->withdraw_number ?? '—' }}</td>
					@php($_s = strtolower((string) $w->status))
					@php($_badge = 'bg-slate-100 text-slate-700')
					@if($_s === 'processing') @php($_badge = 'bg-yellow-100 text-yellow-700')
					@elseif($_s === 'approved') @php($_badge = 'bg-blue-100 text-blue-700')
					@elseif($_s === 'rejected') @php($_badge = 'bg-red-100 text-red-700')
					@elseif($_s === 'fulfilled') @php($_badge = 'bg-green-100 text-green-700')
					@endif
					<td class="px-4 py-3 text-sm">
						<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $_badge }}">{{ ucfirst($_s) }}</span>
					</td>
					<td class="px-4 py-3 text-sm text-right space-x-2 relative">
						<a href="{{ route('admin.withdrawals.edit', $w) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border hover:bg-[#459699]/10 text-[#459699] btn-edit">
							<i data-lucide="pencil" class="h-3 w-3"></i><span>Edit</span>
						</a>
						<form action="{{ route('admin.withdrawals.destroy', $w) }}" method="POST" class="inline">
							@csrf @method('DELETE')
							<button type="submit" data-confirm-delete data-message="Are you sure you want to remove this withdrawal?" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border text-red-600 hover:bg-red-50">
								<i data-lucide="trash-2" class="h-3 w-3"></i><span>Delete</span>
							</button>
						</form>
						<button type="button" class="inline-flex items-center gap-1 text-slate-600 hover:text-slate-900 ml-1" data-menu-toggle="menu-w-{{ $w->id }}">
							<i data-lucide="more-horizontal" class="h-4 w-4"></i><span class="sr-only">More</span>
						</button>
						<!-- Submenu -->
						<div id="menu-w-{{ $w->id }}" class="hidden absolute right-0 mt-2 z-20 bg-white dark:bg-slate-800 border border-primary rounded-lg shadow p-2 overflow-hidden">
							<div class="flex items-center gap-1">
								@if($_s == 'processing')
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2"
									data-quick-open="#tpl-w-approve-{{ $w->id }}" data-quick-title="Approve Withdrawal">
									<i data-lucide="check-circle" class="h-4 w-4 text-green-600"></i><span>Approve</span>
								</button>
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2"
									data-quick-open="#tpl-w-reject-{{ $w->id }}" data-quick-title="Reject Withdrawal">
									<i data-lucide="x-circle" class="h-4 w-4 text-red-600"></i><span>Reject</span>
								</button>
								@endif
								@if( $_s === 'approved' )
								<form action="{{ route('admin.withdrawals.update', $w) }}" method="POST" class="inline">
									@csrf @method('PUT')
									<input type="hidden" name="user_id" value="{{ $w->user_id }}">
									<input type="hidden" name="amount" value="{{ $w->amount }}">
									<input type="hidden" name="status" value="fulfilled">
									<button type="submit"
										data-confirm-delete
										data-title="Confirm Fulfilled"
										data-ok-text="Apply"
										data-message="Are you sure this withdrawal has been fulfilled?"
										class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2">
										<i data-lucide="badge-check" class="h-4 w-4 text-[#459699]"></i><span>Fulfilled</span>
									</button>
								</form>
								@endif
							</div>
						</div>
						<!-- Hidden templates per withdrawal -->
						<div class="hidden">
							<div id="tpl-w-approve-{{ $w->id }}">
								<form method="POST" action="{{ route('admin.withdrawals.review', $w) }}" class="space-y-3">
									@csrf
									<input type="hidden" name="status" value="approved">
									<div>
										<label class="block mb-1">Select Title</label>
										<select name="title_suggestion" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
											<option value="Withdrawal Approved">Withdrawal Approved</option>
											<option value="We are processing your withdrawal">We are processing your withdrawal</option>
										</select>
									</div>
									<div>
										<label class="block mb-1">Final Title</label>
										<input type="text" name="title" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent js-title-input" placeholder="Enter final title">
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
							<div id="tpl-w-reject-{{ $w->id }}">
								<form method="POST" action="{{ route('admin.withdrawals.review', $w) }}" class="space-y-3">
									@csrf
									<input type="hidden" name="status" value="rejected">
									<div>
										<label class="block mb-1">Select Title</label>
										<select name="title_suggestion" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
											<option value="Withdrawal Rejected">Withdrawal Rejected</option>
											<option value="Withdrawal Cannot Be Processed">Withdrawal Cannot Be Processed</option>
										</select>
									</div>
									<div>
										<label class="block mb-1">Final Title</label>
										<input type="text" name="title" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent js-title-input" placeholder="Enter final title">
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
			Page {{ $withdrawals->currentPage() }} of {{ $withdrawals->lastPage() }}
			<span class="ml-2">Showing {{ $withdrawals->firstItem() }}–{{ $withdrawals->lastItem() }} of {{ $withdrawals->total() }}</span>
		</div>
	</div>
	{{ $withdrawals->links() }}
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
		document.querySelectorAll('[id^="menu-w-"]').forEach(m => m.classList.add('hidden'));
		const tplSel = openBtn.getAttribute('data-quick-open');
		const tpl = document.querySelector(tplSel);
		if (tpl && window.openQuickModal) {
			const customTitle = openBtn.getAttribute('data-quick-title') || 'Quick Action';
			window.openQuickModal(tpl.innerHTML, customTitle);
			if (window.lucide && lucide.createIcons) { lucide.createIcons(); }
			// ensure Final Title mirrors current Select Title after open
			setTimeout(function(){
				const sel = document.querySelector('#quick-edit-backdrop select[name="title_suggestion"]');
				const out = document.querySelector('#quick-edit-backdrop .js-title-input');
				if (sel && out && !out.value) { out.value = sel.value || ''; }
				// ensure a Save button is present in footer
				const footer = document.querySelector('#quick-edit-backdrop .border-t.flex.justify-end.gap-2');
				if (footer && !footer.querySelector('[data-quick-save]')) {
					const saveBtn = document.createElement('button');
					saveBtn.type = 'button';
					saveBtn.setAttribute('data-quick-save','true');
					saveBtn.className = 'inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90';
					saveBtndiv = document.createElement('span'); // placeholder to avoid parsing issues
					saveBtn.innerHTML = '<i data-lucide="save" class="h-4 w-4"></i><span>Save</span>';
					const form = document.querySelector('#quick-edit-backdrop form');
					if (form) {
						saveBtn.addEventListener('click', function(){ form.requestSubmit ? form.requestSubmit() : form.submit(); });
						footer.appendChild(saveBtn);
						if (window.lucide && typeof lucide.createIcons === 'function') { lucide.createIcons(); }
					}
				}
			}, 0);
		}
		return;
	}
	// Close submenu when clicking outside
	if (!e.target.closest('[id^="menu-w-"]')) {
		document.querySelectorAll('[id^="menu-w-"]').forEach(m => m.classList.add('hidden'));
	}
});
// Sync title input with suggestion inside modal
(function(){
	document.addEventListener('change', function(e){
		const sel = e.target.closest('#quick-edit-backdrop select[name="title_suggestion"]');
		if (sel) {
			const form = sel.closest('form');
			if (!form) return;
			const out = form.querySelector('.js-title-input');
			if (out) out.value = sel.value || '';
		}
	});
	const originalOpen = window.openQuickModal;
	window.openQuickModal = function(html, title){
		originalOpen && originalOpen(html, title);
		setTimeout(function(){
			const sel = document.querySelector('#quick-edit-backdrop select[name="title_suggestion"]');
			const out = document.querySelector('#quick-edit-backdrop .js-title-input');
			if (sel && out) out.value = sel.value || '';
			// Inject Save button into modal footer for withdrawal approve/reject forms
			const footer = document.querySelector('#quick-edit-backdrop .border-t.flex.justify-end.gap-2');
			if (footer) {
				footer.querySelectorAll('[data-quick-save]').forEach(function(btn){ btn.remove(); });
				const form = document.querySelector('#quick-edit-backdrop form');
				if (form) {
					const saveBtn = document.createElement('button');
					saveBtn.type = 'button';
					saveBtn.setAttribute('data-quick-save', 'true');
					saveBtn.className = 'inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90';
					saveBtn.innerHTML = '<i data-lucide="save" class="h-4 w-4"></i><span>Save</span>';
					let submitControl = form.querySelector('button[type="submit"],input[type="submit"]');
					if (!submitControl) {
						submitControl = document.createElement('button');
						submitControl.type = 'submit';
						submitControl.style.display = 'none';
						form.appendChild(submitControl);
					}
					saveBtn.addEventListener('click', function(){
						if (typeof form.reportValidity === 'function' && !form.reportValidity()) { return; }
						if (typeof submitControl.click === 'function') {
							submitControl.click();
						} else if (typeof form.requestSubmit === 'function') {
							form.requestSubmit();
						} else {
							form.submit();
						}
					});
					footer.appendChild(saveBtn);
					if (window.lucide && typeof lucide.createIcons === 'function') { lucide.createIcons(); }
				}
			}
		}, 0);
	};
})();
</script>
</div>
@endsection


