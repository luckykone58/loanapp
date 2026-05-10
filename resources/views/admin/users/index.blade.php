@extends('admin.layouts.app')

@section('title', 'Users')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Users</li>
@endsection
@section('actions')
<a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
	<i data-lucide="user-plus" class="h-4 w-4"></i>
	<span>Create User</span>
</a>
@endsection

@section('content')
<div class="users-index">
<fieldset class="mb-4 bg-white border border-primary rounded-lg p-4">
	<legend class="px-2 text-primary font-semibold">Filter</legend>
	<form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
		<div class="md:col-span-2">
			<label class="block text-xs text-slate-500 mb-1">Search</label>
			<div class="relative">
				<span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
					<i data-lucide="search" class="h-4 w-4"></i>
				</span>
				<input type="text" name="q" value="{{ request('q') }}" placeholder="Name or Username" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
		</div>
		<div>
			<label class="block text-xs text-slate-500 mb-1">Registered From</label>
			<input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
		</div>
		<div>
			<label class="block text-xs text-slate-500 mb-1">Registered To</label>
			<input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
		</div>
		<div class="md:col-span-2 flex items-end gap-2">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="filter" class="h-4 w-4"></i><span>Filter</span>
			</button>
			<a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-reset-white">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Reset</span>
			</a>
		</div>
	</form>
</fieldset>
@if(session('success'))
	<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
@endif

<div class="bg-white shadow">
	<table class="min-w-full divide-y divide-slate-200">
		<thead class="bg-slate-200 dark:bg-slate-800">
			<tr>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">ID</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Name</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Username</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Wallet</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Credit</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">IP Location</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Status</th>
				@php($admin = auth('admin')->user())
				@if($admin && $admin->role === 'SuperAdmin')
					<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Domain</th>
					<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Role</th>
				@endif
				<th class="px-4 py-2"></th>
			</tr>
		</thead>
		<tbody class="divide-y divide-slate-200">
			@foreach($users as $user)
				<tr>
					<td class="px-4 py-3 text-sm">{{ $user->id }}</td>
					<td class="px-4 py-3 text-sm">{{ $user->name }}</td>
					<td class="px-4 py-3 text-sm">{{ $user->username }}</td>
					<td class="px-4 py-3 text-sm">{{ number_format((float)($user->info?->wallet ?? 0), 2) }}</td>
					<td class="px-4 py-3 text-sm">{{ $user->info?->credit_score ?? '—' }}</td>
					<td class="px-4 py-3 text-sm">{{ $user->ip_location ?? '—' }}</td>
					<td class="px-4 py-3 text-sm">
						<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ ($user->status ?? 'Active') === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
							{{ $user->status ?? 'Active' }}
						</span>
					</td>
					@if($admin && $admin->role === 'SuperAdmin')
						<td class="px-4 py-3 text-sm">{{ $user->domain?->name ?? '—' }} @if($user->domain?->host) <span class="text-slate-400">({{ $user->domain->host }})</span> @endif</td>
						<td class="px-4 py-3 text-sm">{{ $user->role }}</td>
					@endif
					<td class="px-4 py-3 text-sm text-right space-x-2 relative">
						<a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border hover:bg-[#459699]/10 text-[#459699] btn-edit">
							<i data-lucide="pencil" class="h-3 w-3"></i><span>Edit</span>
						</a>
						<form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
							@csrf @method('DELETE')
							<button type="submit" data-confirm-delete data-message="Are you sure you want to remove this user?" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border text-red-600 hover:bg-red-50">
								<i data-lucide="trash-2" class="h-3 w-3"></i><span>Delete</span>
							</button>
						</form>
						<button type="button" class="inline-flex items-center gap-1 text-slate-600 hover:text-slate-900 ml-1"
							data-menu-toggle="menu-{{ $user->id }}">
							<i data-lucide="more-horizontal" class="h-4 w-4"></i><span class="sr-only">More</span>
						</button>
						<!-- Submenu -->
						<div id="menu-{{ $user->id }}" class="hidden absolute right-0 mt-2 z-20 bg-white dark:bg-slate-800 border border-primary rounded-lg shadow p-2 overflow-hidden">
							<div class="flex items-center gap-1">
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2 submenu-item"
									data-quick-open="#tpl-wallet-{{ $user->id }}">
									<i data-lucide="wallet" class="h-4 w-4 text-[#459699]"></i><span>Wallet Modification</span>
								</button>
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2 submenu-item"
									data-quick-open="#tpl-wcode-{{ $user->id }}">
									<i data-lucide="key-round" class="h-4 w-4 text-[#459699]"></i><span>Withdrawal Code</span>
								</button>
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2 submenu-item"
									data-quick-open="#tpl-score-{{ $user->id }}">
									<i data-lucide="star" class="h-4 w-4 text-[#459699]"></i><span>Credit Score</span>
								</button>
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2 submenu-item"
									data-quick-open="#tpl-id-{{ $user->id }}">
									<i data-lucide="id-card" class="h-4 w-4 text-[#459699]"></i><span>ID Verify</span>
								</button>
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2 submenu-item"
									data-quick-open="#tpl-pass-{{ $user->id }}">
									<i data-lucide="lock" class="h-4 w-4 text-[#459699]"></i><span>Password Modification</span>
								</button>
								<button class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2 submenu-item"
									data-quick-open="#tpl-bank-{{ $user->id }}">
									<i data-lucide="banknote" class="h-4 w-4 text-[#459699]"></i><span>Bank Modification</span>
								</button>
								<form action="{{ route('admin.users.quick.disable', $user) }}" method="POST" class="inline">
									@csrf
									<button type="submit" class="px-3 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-2 text-red-600 submenu-item"
										data-confirm-delete
										data-title="Confirm Disable"
										data-ok-text="Disabled"
										data-message="Are you sure you want to disable this user?">
										<i data-lucide="user-x" class="h-4 w-4"></i><span>Disabled Account</span>
									</button>
								</form>
							</div>
						</div>
						<!-- Hidden templates per user (cloned into modal on open) -->
						<div class="hidden">
							<div id="tpl-wallet-{{ $user->id }}">
								<form method="POST" action="{{ route('admin.users.quick.wallet', $user) }}" class="space-y-3">
									@csrf
									<div>
										<label class="block mb-1">Wallet</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<rect x="2" y="7" width="20" height="10" rx="2"/>
													<path d="M16 7v10"/>
													<path d="M6 11h4"/>
												</svg>
											</span>
											<input type="number" step="0.01" name="wallet" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ $user->info?->wallet ?? 0 }}" required>
										</div>
									</div>
									<div class="flex justify-end gap-2">
										<button type="button" class="px-4 py-2 rounded-lg border" data-quick-close>Cancel</button>
										<button type="submit" class="px-4 py-2 rounded-lg bg-[#459699] text-white">Save</button>
									</div>
								</form>
							</div>
							<div id="tpl-wcode-{{ $user->id }}">
								<form method="POST" action="{{ route('admin.users.quick.withdrawal_code', $user) }}" class="space-y-3">
									@csrf
									<div>
										<label class="block mb-1">Withdrawal Code</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<circle cx="12" cy="12" r="3"/>
													<path d="M3 12h3"/>
													<path d="M18 12h3"/>
													<path d="M12 3v3"/>
													<path d="M12 18v3"/>
												</svg>
											</span>
											<input type="text" name="withdrawal_code" maxlength="12" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ $user->info?->withdrawal_code ?? '' }}" required>
										</div>
									</div>
									<div class="flex justify-end gap-2">
										<button type="button" class="px-4 py-2 rounded-lg border" data-quick-close>Cancel</button>
										<button type="submit" class="px-4 py-2 rounded-lg bg-[#459699] text-white">Save</button>
									</div>
								</form>
							</div>
							<div id="tpl-score-{{ $user->id }}">
								<form method="POST" action="{{ route('admin.users.quick.score', $user) }}" class="space-y-3">
									@csrf
                                    <div>
										<label class="block mb-1">Credit Score</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<path d="m12 17-5 3 1.9-5.9L4 9h6l2-5 2 5h6l-4.9 5.1L17 20z"/>
												</svg>
											</span>
											<input type="number" name="credit_score" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ $user->info?->credit_score ?? '' }}" required>
										</div>
									</div>
									<div class="flex justify-end gap-2">
										<button type="button" class="px-4 py-2 rounded-lg border" data-quick-close>Cancel</button>
										<button type="submit" class="px-4 py-2 rounded-lg bg-[#459699] text-white">Save</button>
									</div>
								</form>
							</div>
							<div id="tpl-id-{{ $user->id }}">
								<form method="POST" action="{{ route('admin.users.quick.id', $user) }}" class="space-y-3">
									@csrf
									<div>
										<label class="block mb-1">Full Name</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<circle cx="12" cy="7" r="4"/>
													<path d="M20 21a8 8 0 1 0-16 0"/>
												</svg>
											</span>
											<input type="text" name="full_name" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ $user->info?->full_name ?? $user->name }}" required>
										</div>
									</div>
									<div>
										<label class="block mb-1">ID Number</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<rect x="3" y="5" width="18" height="14" rx="2"/>
													<path d="M7 9h6"/>
													<path d="M7 13h10"/>
												</svg>
											</span>
											<input type="text" name="id_card_number" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ $user->info?->id_card_number ?? '' }}" required>
										</div>
									</div>
									<div class="flex justify-end gap-2">
										<button type="button" class="px-4 py-2 rounded-lg border" data-quick-close>Cancel</button>
										<button type="submit" class="px-4 py-2 rounded-lg bg-[#459699] text-white">Save</button>
									</div>
								</form>
							</div>
							<div id="tpl-pass-{{ $user->id }}">
								<form method="POST" action="{{ route('admin.users.quick.password', $user) }}" class="space-y-3">
									@csrf
									<div>
										<label class="block mb-1">New Password</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
													<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
												</svg>
											</span>
											<input type="password" name="password" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
										</div>
									</div>
									<div>
										<label class="block mb-1">Confirm New Password</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
													<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
												</svg>
											</span>
											<input type="password" name="password_confirmation" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
										</div>
									</div>
									<div class="flex justify-end gap-2">
										<button type="button" class="px-4 py-2 rounded-lg border" data-quick-close>Cancel</button>
										<button type="submit" class="px-4 py-2 rounded-lg bg-[#459699] text-white">Save</button>
									</div>
								</form>
							</div>
							<div id="tpl-bank-{{ $user->id }}">
								<form method="POST" action="{{ route('admin.users.quick.bank', $user) }}" class="space-y-3">
									@csrf
									<div>
										<label class="block mb-1">Bank Name</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<rect x="2" y="7" width="20" height="10" rx="2"/>
													<path d="M8 11h8"/>
													<path d="M6 15h2"/>
												</svg>
											</span>
											<input type="text" name="bank_name" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ $user->info?->bank_name ?? '' }}">
										</div>
									</div>
									<div>
										<label class="block mb-1">Bank Number</label>
										<div class="relative">
											<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<rect x="3" y="5" width="18" height="14" rx="2"/>
													<path d="M3 10h18"/>
													<path d="M7 15h4"/>
												</svg>
											</span>
											<input type="text" name="bank_number" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ $user->info?->bank_number ?? '' }}">
										</div>
									</div>
									<div class="flex justify-end gap-2">
										<button type="button" class="px-4 py-2 rounded-lg border" data-quick-close>Cancel</button>
										<button type="submit" class="px-4 py-2 rounded-lg bg-[#459699] text-white">Save</button>
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
			Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
			<span class="ml-2">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</span>
		</div>
	</div>
	{{ $users->links() }}
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
		document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
		const tplSel = openBtn.getAttribute('data-quick-open');
		const tpl = document.querySelector(tplSel);
		if (tpl && window.openQuickModal) {
			window.openQuickModal(tpl.innerHTML, 'Quick Edit');
			if (window.lucide && lucide.createIcons) { lucide.createIcons(); }
		}
		return;
	}
	// Close submenu when clicking outside
	if (!e.target.closest('[id^="menu-"]')) {
		document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
	}
});
</script>
</div>
@endsection


