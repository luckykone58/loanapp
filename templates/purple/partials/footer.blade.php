@php
	$active = $active ?? null;
	$cls = fn($name) => $active === $name ? 'text-white' : 'text-white/80 hover:text-white transition-colors';
@endphp
<nav class="fixed bottom-0 left-0 right-0 max-w-[767px] mx-auto bg-gradient-to-br from-blue-900/90 via-brand-800/90 to-brand-600/90 border-t border-white/10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-50">
	<div class="flex items-center justify-around px-4 py-3 relative">
		<a href="{{ route('home') }}" class="flex flex-col items-center gap-1 {{ $cls('home') }} cursor-pointer">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
				<polyline points="9 22 9 12 15 12 15 22"/>
			</svg>
			<span class="text-xs font-bold">{{ __('footer.menu.home') }}</span>
		</a>
		<a href="{{ route('about') }}" class="flex flex-col items-center gap-1 {{ $cls('about') }} cursor-pointer">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="12" cy="12" r="10"/>
				<path d="M12 16v-4"/>
				<path d="M12 8h.01"/>
			</svg>
			<span class="text-xs font-bold">{{ __('footer.menu.about') }}</span>
		</a>
		<a href="{{ route('wallets') }}" class="flex flex-col items-center gap-1 text-white cursor-pointer -mt-8">
			<div class="bg-gradient-to-br from-purple-500 to-blue-600 rounded-full p-4 shadow-lg hover:shadow-xl transition-all hover:scale-105">
				<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/>
					<path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
				</svg>
			</div>
			<span class="text-xs text-white mt-1 font-bold">{{ __('footer.menu.wallet') }}</span>
		</a>
		<a href="{{ route('profile') }}" class="flex flex-col items-center gap-1 {{ $cls('profile') }} cursor-pointer">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
				<circle cx="12" cy="7" r="4"/>
			</svg>
			<span class="text-xs font-bold">{{ __('footer.menu.profile') }}</span>
		</a>
		<form method="POST" action="{{ route('logout') }}" class="flex flex-col items-center gap-1 {{ $cls('logout') }} cursor-pointer" data-require-logout-confirm="true">
			@csrf
			<button type="submit" class="flex flex-col items-center gap-1">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
					<polyline points="16 17 21 12 16 7"/>
					<line x1="21" x2="9" y1="12" y2="12"/>
				</svg>
				<span class="text-xs font-bold">{{ __('footer.menu.logout') }}</span>
			</button>
		</form>
	</div>
</nav>


<!-- Logout Confirmation Modal -->
<div id="logout-confirm-backdrop" class="fixed inset-0 z-[60] hidden">
	<div class="absolute inset-0 bg-black/50"></div>
	<div class="absolute inset-0 flex items-center justify-center p-4">
		<div class="w-full max-w-sm rounded-lg bg-white dark:bg-[#2c6366] shadow-xl">
			<div class="px-4 py-3 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
				<h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ __('footer.confirm.logout_title') }}</h3>
			</div>
			<div class="px-4 py-4 text-sm text-slate-700 dark:text-slate-100" id="logout-confirm-message">
				{{ __('footer.confirm.logout_message') }}
			</div>
			<div class="px-4 py-3 border-t border-slate-200 dark:border-white/10 flex justify-end gap-2">
				<button type="button" id="logout-confirm-cancel" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition-colors">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
					<span>{{ __('footer.confirm.cancel') }}</span>
				</button>
				<button type="button" id="logout-confirm-ok" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-purple-600 text-white hover:bg-red-700">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
					<span>{{ __('footer.confirm.ok') }}</span>
				</button>
			</div>
		</div>
	</div>
	</div>

<script>
(function() {
	if (!window.__purpleLogoutConfirmInit) {
		window.__purpleLogoutConfirmInit = true;
		let pendingLogoutForm = null;
		const backdrop = document.getElementById('logout-confirm-backdrop');
		const btnOk = document.getElementById('logout-confirm-ok');
		const btnCancel = document.getElementById('logout-confirm-cancel');
		function openModal(form) {
			pendingLogoutForm = form;
			if (backdrop) backdrop.classList.remove('hidden');
		}
		function closeModal() {
			if (backdrop) backdrop.classList.add('hidden');
			pendingLogoutForm = null;
		}
		document.addEventListener('submit', function(e){
			const form = e.target && e.target.closest('form[data-require-logout-confirm]');
			if (!form) return;
			e.preventDefault();
			openModal(form);
		});
		btnCancel && btnCancel.addEventListener('click', closeModal);
		backdrop && backdrop.addEventListener('click', function(e){
			if (e.target === backdrop) closeModal();
		});
		btnOk && btnOk.addEventListener('click', function(){
			if (pendingLogoutForm) {
				const f = pendingLogoutForm;
				closeModal();
				f.submit();
			}
		});
	}
	// Poll unread notifications every 30s and toggle header indicators
	const url = @json(route('ajax.notifications.unread_count', [], false));
	if (!url) return;
	const ping = document.getElementById('header-ping');
	const badge = document.getElementById('header-badge');

	async function checkUnread() {
		try {
			const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
			if (!res.ok) return;
			const data = await res.json();
			const count = (data && typeof data.count === 'number') ? data.count : 0;
			const has = count > 0;
			if (ping) ping.classList.toggle('hidden', !has);
			if (badge) badge.classList.toggle('hidden', !has);
		} catch (e) {
			// silently ignore network errors
		}
	}

	// Initial check and interval
	checkUnread();
	setInterval(checkUnread, 30000);
})();
</script>
