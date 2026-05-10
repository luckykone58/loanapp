<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://unpkg.com/lucide@latest"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
					colors: {
						boxdark: '#24303F',
						boxdark2: '#1A222C',
						primary: '#459699' // brand color
					}
				}
			},
			darkMode: 'class'
		}
	</script>
	<style>
		.admin-root h3 { color: #459699 !important; }
		/* Dark mode harmonization for admin surfaces */
		.dark .admin-surface .bg-white { background-color: #2c6366 !important; }
		.dark .admin-surface .bg-slate-50 { background-color: #244b4d !important; }
		.dark .admin-surface .border-slate-200 { border-color: rgba(255,255,255,0.1) !important; }
		.dark .admin-surface .divide-slate-200 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(255,255,255,0.1) !important; }
		.dark .admin-surface .text-slate-800 { color: #ffffff !important; }
		.dark .admin-surface .text-slate-700 { color: #e6f1f1 !important; }
		.dark .admin-surface .text-slate-600 { color: #cfe3e3 !important; }
		.dark .admin-surface thead.bg-slate-50 { background-color: #244b4d !important; }
		.dark .admin-surface tbody tr { background-color: transparent; }
		/* Users list: force table header background in all modes */
		.admin-surface .users-index thead { background-color: rgb(4, 64, 66) !important; }
		.admin-surface .users-index thead th { color: #ffffff !important; }
		/* Users list: force table borders/dividers color in all modes */
		.admin-surface .users-index table,
		.admin-surface .users-index thead,
		.admin-surface .users-index tbody tr { border-color: rgb(69 150 153 / var(--tw-border-opacity, 1)) !important; }
		.admin-surface .users-index .divide-slate-200 > :not([hidden]) ~ :not([hidden]) { border-color: rgb(69 150 153 / var(--tw-border-opacity, 1)) !important; }
		/* Users list: reset button background stays white */
		.admin-surface .users-index .btn-reset-white { background-color: #ffffff !important; }
		.dark .admin-surface .users-index .btn-reset-white { background-color: #ffffff !important; color: #459699 !important; border-color: #459699 !important; }
		/* Night mode: force brand text color within Registered and Loan Request sections */
		.dark .admin-surface .dashboard-section-registered,
		.dark .admin-surface .dashboard-section-registered * { color: #459699 !important; }
		.dark .admin-surface .dashboard-section-loanreq,
		.dark .admin-surface .dashboard-section-loanreq * { color: #459699 !important; }
		/* Night mode: force white text/links for recent and chart sections */
		.dark .admin-surface .dashboard-section-recent-registered,
		.dark .admin-surface .dashboard-section-recent-registered * { color: #ffffff !important; }
		.dark .admin-surface .dashboard-section-recent-loans,
		.dark .admin-surface .dashboard-section-recent-loans * { color: #ffffff !important; }
		.dark .admin-surface .dashboard-section-bar-registered,
		.dark .admin-surface .dashboard-section-bar-registered * { color: #ffffff !important; }
		.dark .admin-surface .dashboard-section-bar-loans,
		.dark .admin-surface .dashboard-section-bar-loans * { color: #ffffff !important; }
		/* Dark mode: Domains index page overrides */
		/* Match users-index filter styling: white text on teal bg, white border */
		.dark .admin-surface .domains-index fieldset {
			background-color: #2c6366 !important;
			border-color: rgba(255, 255, 255, 0.10) !important;
		}
		.dark .admin-surface .domains-index fieldset label { color: #ffffff !important; }
		.dark .admin-surface .domains-index thead { background-color: rgb(4, 64, 66) !important; }
		.dark .admin-surface .domains-index thead th { color: #ffffff !important; }
		/* Inputs in domains filter use white text in dark mode */
		.dark .admin-surface .domains-index input[type="text"],
		.dark .admin-surface .domains-index input[type="search"],
		.dark .admin-surface .domains-index input[type="date"],
		.dark .admin-surface .domains-index select,
		.dark .admin-surface .domains-index textarea { color: #ffffff !important; }
		.dark .admin-surface .domains-index input::placeholder { color: rgba(255,255,255,0.7) !important; }
		/* Dark mode: Domains table borders/dividers use brand tone */
		.dark .admin-surface .domains-index table,
		.dark .admin-surface .domains-index thead,
		.dark .admin-surface .domains-index tbody tr { border-color: rgb(69 150 153 / var(--tw-bg-opacity, 1)) !important; }
		.dark .admin-surface .domains-index .divide-slate-200 > :not([hidden]) ~ :not([hidden]) { border-color: rgb(69 150 153 / var(--tw-bg-opacity, 1)) !important; }
		/* Preserve delete red coloring */
		.dark .admin-surface .domains-index .text-red-600 { color: #dc2626 !important; }
		.dark .admin-surface .domains-index [data-confirm-delete],
		.dark .admin-surface .domains-index [data-confirm-delete] *,
		.dark .admin-surface .domains-index [data-confirm-delete] svg,
		.dark .admin-surface .domains-index [data-confirm-delete] svg * {
			color: #dc2626 !important;
			stroke: #dc2626 !important;
		}
		/* Dark mode: inputs in domains filter/search should display white text */
		.dark .admin-surface .domains-index input[type="text"],
		.dark .admin-surface .domains-index input[type="search"],
		.dark .admin-surface .domains-index input[type="date"],
		.dark .admin-surface .domains-index select,
		.dark .admin-surface .domains-index textarea { color: #ffffff !important; }
		.dark .admin-surface .domains-index input::placeholder { color: rgba(255,255,255,0.7) !important; }
		/* Dark mode: status badge text colors */
		.dark .admin-surface .domains-index .text-yellow-700 { color: yellow !important; }
		.dark .admin-surface .domains-index .text-red-700 { color: red !important; }
		.dark .admin-surface .domains-index .text-green-700 { color: rgb(4, 64, 66) !important; }
		/* Dark mode: make Suspend badge red for readability */
		.dark .admin-surface .domains-index .status-suspend { 
			color: #dc2626 !important; 
			background-color: rgba(220, 38, 38, 0.15) !important; 
		}
		/* Dark mode: Domain add/edit form select backgrounds and option hover */
		.dark .admin-surface .domain-form select {
			background-color: #2c6366 !important;
			color: #ffffff !important;
			/* Match input border color in dark mode */
			border-color: #459699 !important;
		}
		/* Domain select dropdown options (supported by most modern browsers) */
		.dark .admin-surface .domain-form select option {
			/* lighter variant of #2c6366 for better contrast in the drop-down list */
			background-color: #3f9a99 !important;
			color: #ffffff !important;
		}
		/* Improve hover/selected visibility for option rows where supported */
		.dark .admin-surface .domain-form select option:hover,
		.dark .admin-surface .domain-form select option:checked {
			background-color: rgba(69, 150, 153, 0.25) !important;
			color: #ffffff !important;
		}
		/* Dark mode: Domain add/edit input borders use light teal (readable) */
		.dark .admin-surface .domain-form input[type="text"],
		.dark .admin-surface .domain-form input[type="email"],
		.dark .admin-surface .domain-form input[type="number"],
		.dark .admin-surface .domain-form input[type="password"],
		.dark .admin-surface .domain-form input[type="date"],
		.dark .admin-surface .domain-form input[type="datetime-local"],
		.dark .admin-surface .domain-form textarea {
			border-color: #459699 !important;
		}
		/* Dark mode: ensure form Cancel buttons are white with teal text */
		.dark .admin-surface .btn-cancel {
			background-color: #ffffff !important;
			color: #459699 !important;
			border-color: #459699 !important;
		}
		.dark .admin-surface .btn-cancel:hover { background-color: rgba(255,255,255,0.9) !important; }
		/* Dark mode: Breadcrumb text white across admin */
		.dark .admin-surface .admin-breadcrumb,
		.dark .admin-surface .admin-breadcrumb *,
		.dark .admin-surface .admin-breadcrumb a { color: #ffffff !important; }
		/* Dark mode: Users index page overrides */
		.dark .admin-surface .users-index thead { background-color: rgb(4, 64, 66) !important; }
		.dark .admin-surface .users-index thead th { color: #ffffff !important; }
		.dark .admin-surface .users-index input[type="text"],
		.dark .admin-surface .users-index input[type="search"],
		.dark .admin-surface .users-index input[type="date"],
		.dark .admin-surface .users-index select,
		.dark .admin-surface .users-index textarea { color: #ffffff !important; }
		.dark .admin-surface .users-index label { color: #ffffff !important; }
		.dark .admin-surface .users-index input::placeholder { color: rgba(255,255,255,0.7) !important; }
		.dark .admin-surface .users-index .btn-edit,
		.dark .admin-surface .users-index .btn-edit *,
		.dark .admin-surface .users-index .btn-edit svg,
		.dark .admin-surface .users-index .btn-edit svg * { color: #ffffff !important; stroke: #ffffff !important; }
		/* Dark mode: highlight 3-dot submenu items on hover with light brand tint */
		.dark .admin-surface .users-index .submenu-item:hover { background-color: rgb(69 150 153 / 0.15) !important; }
		/* Dark mode: make all add/edit form <select> backgrounds and borders visible (light teal) */
		.dark .admin-surface .form-surface select {
			background-color: #2c6366 !important;
			color: #ffffff !important;
			/* Match input border color in dark mode */
			border-color: #459699 !important;
		}
		.dark .admin-surface .form-surface select:focus {
			outline: none !important;
			box-shadow: inset 0 0 0 2px rgba(69, 150, 153, 0.25) !important;
		}
		.dark .admin-surface .form-surface select option {
			/* lighter variant of #2c6366 for better contrast in the drop-down list */
			background-color: #3f9a99 !important;
			color: #ffffff !important;
		}
		/* Some UAs support option:hover styling; also style :checked as a fallback */
		.dark .admin-surface .form-surface select option:hover,
		.dark .admin-surface .form-surface select option:checked {
			background-color: rgba(69, 150, 153, 0.25) !important;
			color: #ffffff !important;
		}
		/* Dark mode: make text input/textarea borders match teal accent in all add/edit forms */
		.dark .admin-surface .form-surface input[type="text"],
		.dark .admin-surface .form-surface input[type="email"],
		.dark .admin-surface .form-surface input[type="number"],
		.dark .admin-surface .form-surface input[type="password"],
		.dark .admin-surface .form-surface input[type="date"],
		.dark .admin-surface .form-surface input[type="datetime-local"],
		.dark .admin-surface .form-surface textarea {
			border-color: #459699 !important;
		}
		/* Notifications list: Type chip text color in all modes */
		.admin-surface .notifications-index .type-chip { color: rgb(4, 64, 66) !important; }
		/* Dark mode: Loans index page overrides (match Users style) */
		.dark .admin-surface .loans-index thead { background-color: rgb(4, 64, 66) !important; }
		.dark .admin-surface .loans-index thead th { color: #ffffff !important; }
		.dark .admin-surface .loans-index input[type="text"],
		.dark .admin-surface .loans-index input[type="search"],
		.dark .admin-surface .loans-index input[type="date"],
		.dark .admin-surface .loans-index select,
		.dark .admin-surface .loans-index textarea { color: #ffffff !important; }
		.dark .admin-surface .loans-index label { color: #ffffff !important; }
		.dark .admin-surface .loans-index input::placeholder { color: rgba(255,255,255,0.7) !important; }
		.dark .admin-surface .loans-index .btn-edit,
		.dark .admin-surface .loans-index .btn-edit *,
		.dark .admin-surface .loans-index .btn-edit svg,
		.dark .admin-surface .loans-index .btn-edit svg * { color: #ffffff !important; stroke: #ffffff !important; }
		/* Dark mode: Withdrawals index page overrides (match Users style) */
		.dark .admin-surface .withdrawals-index thead { background-color: rgb(4, 64, 66) !important; }
		.dark .admin-surface .withdrawals-index thead th { color: #ffffff !important; }
		/* Match users-index filter container background/border in dark mode */
		.dark .admin-surface .withdrawals-index fieldset {
			background-color: #2c6366 !important;
			border-color: rgba(255, 255, 255, 0.10) !important;
		}
		.dark .admin-surface .withdrawals-index input[type="text"],
		.dark .admin-surface .withdrawals-index input[type="search"],
		.dark .admin-surface .withdrawals-index input[type="date"],
		.dark .admin-surface .withdrawals-index select,
		.dark .admin-surface .withdrawals-index textarea { color: #ffffff !important; }
		.dark .admin-surface .withdrawals-index label { color: #ffffff !important; }
		.dark .admin-surface .withdrawals-index input::placeholder { color: rgba(255,255,255,0.7) !important; }
		.dark .admin-surface .withdrawals-index .btn-edit,
		.dark .admin-surface .withdrawals-index .btn-edit *,
		.dark .admin-surface .withdrawals-index .btn-edit svg,
		.dark .admin-surface .withdrawals-index .btn-edit svg * { color: #ffffff !important; stroke: #ffffff !important; }
		/* Dark mode: Notifications index page overrides (match Users style) */
		.dark .admin-surface .notifications-index thead { background-color: rgb(4, 64, 66) !important; }
		.dark .admin-surface .notifications-index thead th { color: #ffffff !important; }
		.dark .admin-surface .notifications-index input[type="text"],
		.dark .admin-surface .notifications-index input[type="search"],
		.dark .admin-surface .notifications-index input[type="date"],
		.dark .admin-surface .notifications-index select,
		.dark .admin-surface .notifications-index textarea { color: #ffffff !important; }
		.dark .admin-surface .notifications-index label { color: #ffffff !important; }
		.dark .admin-surface .notifications-index input::placeholder { color: rgba(255,255,255,0.7) !important; }
		.dark .admin-surface .notifications-index .btn-edit,
		.dark .admin-surface .notifications-index .btn-edit *,
		.dark .admin-surface .notifications-index .btn-edit svg,
		.dark .admin-surface .notifications-index .btn-edit svg * { color: #ffffff !important; stroke: #ffffff !important; }
		/* Dark mode: Notifications list Type chip text color */
		.dark .admin-surface .notifications-index .type-chip { color: rgb(4, 64, 66) !important; }
		/* All modes: align headers and borders for domains, loans, withdrawals, notifications, logs to match users */
		.admin-surface .domains-index thead,
		.admin-surface .loans-index thead,
		.admin-surface .withdrawals-index thead,
		.admin-surface .notifications-index thead,
		.admin-surface .logs-index thead { background-color: rgb(4, 64, 66) !important; }
		.admin-surface .domains-index thead th,
		.admin-surface .loans-index thead th,
		.admin-surface .withdrawals-index thead th,
		.admin-surface .notifications-index thead th,
		.admin-surface .logs-index thead th { color: #ffffff !important; }
		.admin-surface .domains-index table,
		.admin-surface .domains-index thead,
		.admin-surface .domains-index tbody tr,
		.admin-surface .loans-index table,
		.admin-surface .loans-index thead,
		.admin-surface .loans-index tbody tr,
		.admin-surface .withdrawals-index table,
		.admin-surface .withdrawals-index thead,
		.admin-surface .withdrawals-index tbody tr,
		.admin-surface .notifications-index table,
		.admin-surface .notifications-index thead,
		.admin-surface .notifications-index tbody tr,
		.admin-surface .logs-index table,
		.admin-surface .logs-index thead,
		.admin-surface .logs-index tbody tr { border-color: rgb(69 150 153 / var(--tw-border-opacity, 1)) !important; }
		.admin-surface .domains-index .divide-slate-200 > :not([hidden]) ~ :not([hidden]),
		.admin-surface .loans-index .divide-slate-200 > :not([hidden]) ~ :not([hidden]),
		.admin-surface .withdrawals-index .divide-slate-200 > :not([hidden]) ~ :not([hidden]),
		.admin-surface .notifications-index .divide-slate-200 > :not([hidden]) ~ :not([hidden]),
		.admin-surface .logs-index .divide-slate-200 > :not([hidden]) ~ :not([hidden]) { border-color: rgb(69 150 153 / var(--tw-border-opacity, 1)) !important; }
		/* All modes: ensure reset buttons are white background */
		.admin-surface .domains-index .btn-reset-white,
		.admin-surface .loans-index .btn-reset-white,
		.admin-surface .withdrawals-index .btn-reset-white,
		.admin-surface .notifications-index .btn-reset-white,
		.admin-surface .logs-index .btn-reset-white { background-color: #ffffff !important; }
		.dark .admin-surface .domains-index .btn-reset-white,
		.dark .admin-surface .loans-index .btn-reset-white,
		.dark .admin-surface .withdrawals-index .btn-reset-white,
		.dark .admin-surface .notifications-index .btn-reset-white,
		.dark .admin-surface .logs-index .btn-reset-white { background-color: #ffffff !important; color: #459699 !important; border-color: #459699 !important; }
		/* Dark mode: Logs index filter text visible and View button white */
		.dark .admin-surface .logs-index input[type="text"],
		.dark .admin-surface .logs-index input[type="search"],
		.dark .admin-surface .logs-index input[type="date"],
		.dark .admin-surface .logs-index select,
		.dark .admin-surface .logs-index textarea { color: #ffffff !important; }
		.dark .admin-surface .logs-index label { color: #ffffff !important; }
		.dark .admin-surface .logs-index input::placeholder { color: rgba(255,255,255,0.7) !important; }
		.dark .admin-surface .logs-index .btn-view,
		.dark .admin-surface .logs-index .btn-view *,
		.dark .admin-surface .logs-index .btn-view svg,
		.dark .admin-surface .logs-index .btn-view svg * { color: #ffffff !important; stroke: #ffffff !important; }
		/* Logs subject keyword highlights */
		.admin-surface .logs-index .hl-blue { color: #38bdf8 !important; font-weight: 600; } /* light blue */
		.admin-surface .logs-index .hl-red { color: #dc2626 !important; font-weight: 600; }  /* red */
		.admin-surface .logs-index .hl-yellow { color: #eab308 !important; font-weight: 600; } /* amber-500 */
		.admin-surface .logs-index .hl-green { color: #16a34a !important; font-weight: 600; }  /* green-600 */
		/* Dark mode: make domains edit buttons white tone similar to others */
		.dark .admin-surface .domains-index .btn-edit,
		.dark .admin-surface .domains-index .btn-edit *,
		.dark .admin-surface .domains-index .btn-edit svg,
		.dark .admin-surface .domains-index .btn-edit svg * { color: #ffffff !important; stroke: #ffffff !important; }
		/* Dark mode: Users submenu hover color (3-dot menu) */
		.dark .admin-surface .users-index [id^="menu-"] .submenu-item:hover {
			background-color: rgba(69, 150, 153, 0.15) !important;
		}
	</style>
</head>
<body class="admin-root m-0 bg-gray-50 dark:bg-[#204c4e] text-slate-800 dark:text-slate-100">
	@vite('resources/js/admin-wysiwyg.js')
	<div class="min-h-screen flex">
		<!-- Sidebar -->
		<aside class="w-64 bg-[#459699] text-white border-r border-white/10 hidden md:flex md:flex-col">
			<div class="h-16 flex items-center justify-between px-4 border-b border-white/10">
				<a href="{{ route('admin.dashboard') }}" class="font-semibold text-white flex items-center gap-2">
					<span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/20">
						<i data-lucide="layout-dashboard" class="text-white"></i>
					</span>
					LoanApp System
				</a>
				<button class="md:hidden text-slate-400">
					<i data-lucide="x"></i>
				</button>
			</div>
			@php
				$__admin = auth('admin')->user();
				$__expired = null;
				if ($__admin && $__admin->domain_id) {
					$__domain = \App\Models\Domain::find($__admin->domain_id);
					$__expired = $__domain?->expired_date;
				}
			@endphp
			@if($__expired)
				@php
					$__now = \Carbon\Carbon::now()->startOfDay();
					$__exp = \Carbon\Carbon::parse($__expired)->startOfDay();
					$__days = $__now->diffInDays($__exp, false);
					$__chip = 'border-white/20 bg-white/10 text-white';
					if ($__days <= 2) {
						$__chip = 'border-red-200 bg-red-100 text-red-800';
					} elseif ($__days <= 7) {
						$__chip = 'border-yellow-200 bg-yellow-100 text-yellow-800';
					}
					$__tip = 'Your domain will suspend on '.$__exp->toDateString();
				@endphp
				<div class="px-4 mt-3">
					<div class="rounded-lg border {{ $__chip }} px-3 py-2 flex items-center gap-2" title="{{ $__tip }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="4" width="18" height="18" rx="2"/>
							<path d="M16 2v4"/>
							<path d="M8 2v4"/>
							<path d="M3 10h18"/>
						</svg>
						<div class="text-xs leading-tight">
							<div class="font-semibold">System Expired:</div>
							<div class="font-bold">{{ $__exp->toDateString() }}</div>
						</div>
					</div>
				</div>
			@endif

			<nav class="flex-1 p-4 space-y-1">
				<p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider mb-2 mt-2 border-b border-white/20 pb-2">Menu</p>
				<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
					{{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
					<i data-lucide="layout-dashboard" size="18"></i>
					<span>Dashboard</span>
				</a>
				@php($adminUser = auth('admin')->user())
				@if($adminUser && $adminUser->role === 'SuperAdmin')
					<a href="{{ route('admin.domains.index') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
						{{ request()->routeIs('admin.domains.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
						<i data-lucide="globe" size="18"></i>
						<span>Manage Domains</span>
					</a>
				@endif
				<a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
					{{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
					<i data-lucide="users" size="18"></i>
					<span>Users</span>
				</a>
				<a href="{{ route('admin.loans.index') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
					{{ request()->routeIs('admin.loans.*') ? 'bg-white/10 text_white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
					<i data-lucide="briefcase" size="18"></i>
					<span>Loans</span>
				</a>
				<a href="{{ route('admin.withdrawals.index') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
					{{ request()->routeIs('admin.withdrawals.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
					<i data-lucide="credit-card" size="18"></i>
					<span>Withdrawals</span>
				</a>
				<a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
					{{ request()->routeIs('admin.notifications.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
					<i data-lucide="bell" size="18"></i>
					<span>Notifications</span>
				</a>
				<p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider mb-2 mt-2 border-b border-white/20 pb-2">Settings</p>
				<a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
					{{ request()->routeIs('admin.settings.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
					<i data-lucide="settings" size="18"></i>
					<span>Configuration</span>
				</a>
				<a href="{{ route('admin.logs.index') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
					{{ request()->routeIs('admin.logs.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
					<i data-lucide="file-text" size="18"></i>
					<span>Logs</span>
				</a>
				<a href="{{ route('admin.password.edit') }}" class="flex items-center gap-2.5 rounded-sm px-3 py-2 font-medium duration-200
					{{ request()->routeIs('admin.password.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
					<i data-lucide="lock" size="18"></i><span>Change Password</span></a>
				<form method="POST" action="{{ route('admin.logout') }}">
					@csrf
					<button type="submit" class="flex items-center gap-2.5 rounded-lg px-3 py-2 font-medium duration-200 text-white/80 hover:bg-white/10 hover:text-white"><i data-lucide="log-out" size="18"></i><span>Logout</button>
				</form>
			</nav>
		
		</aside>

		<!-- Main -->
		<div class="flex-1 flex flex-col min-w-0">
			<!-- Topbar -->
			<header class="h-16 bg-white dark:bg-[#2c6366] border-b border-slate-200 dark:border-white/10 flex items-center justify-between px-6">
				<div class="flex items-center gap-2">
					<button class="md:hidden inline-flex items-center justify-center p-2 rounded-lg hover:bg-[#459699]/10" onclick="document.getElementById('mobile-drawer').classList.remove('hidden')">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
					</button>
					<h1 class="text-lg font-semibold text-black dark:text-white"><span class="text-[#459699]">@yield('title', 'Admin')</span></h1>
				</div>
				<ul class="flex items-center gap-2">
					<li>
						<button id="dark-mode-toggle" class="relative flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-[#459699]/10 text-slate-600 dark:border-slate-700 dark:bg-[#459699] dark:text-slate-300">
							<i data-lucide="moon" class="dark:hidden"></i>
							<i data-lucide="sun" class="hidden dark:block"></i>
						</button>
					</li>
					<li class="text-sm text-slate-600 dark:text-slate-300">
						@auth
							<span>{{ Auth::user()->name }}</span>
						@endauth
					</li>
				</ul>
			</header>

			<!-- Content -->
			<main class="p-6 admin-surface">
				@hasSection('breadcrumb')
					<nav class="mb-6 flex items-center justify-between admin-breadcrumb">
						<ol class="flex items-center gap-2 text-sm">
							@yield('breadcrumb')
						</ol>
						<div class="flex items-center gap-2">
							@yield('actions')
						</div>
					</nav>
				@endif
				@yield('content')
			</main>
		</div>
	</div>

	<!-- Mobile Drawer -->
	<div id="mobile-drawer" class="fixed inset-0 z-50 hidden">
		<div class="absolute inset-0 bg-black/40" onclick="document.getElementById('mobile-drawer').classList.add('hidden')"></div>
		<div class="absolute inset-y-0 left-0 w-72 bg-[#459699] text-white shadow-lg p-4">
			<div class="h-12 flex items-center justify-between">
				<a href="{{ route('admin.dashboard') }}" class="font-semibold text-white">TailAdmin</a>
				<button class="p-2 rounded-lg hover:bg-white/10" onclick="document.getElementById('mobile-drawer').classList.add('hidden')">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
				</button>
			</div>
			<nav class="mt-4 space-y-1">
				<a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-white/10
					{{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Dashboard</a>
				<a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded hover:bg-white/10
					{{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Users</a>
				<a href="{{ route('admin.loans.index') }}" class="block px-3 py-2 rounded hover:bg-white/10
					{{ request()->routeIs('admin.loans.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Loans</a>
				<a href="{{ route('admin.withdrawals.index') }}" class="block px-3 py-2 rounded hover:bg-white/10
					{{ request()->routeIs('admin.withdrawals.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Withdrawals</a>
				<a href="{{ route('admin.notifications.index') }}" class="block px-3 py-2 rounded hover:bg-white/10
					{{ request()->routeIs('admin.notifications.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Notifications</a>
				<a href="{{ route('admin.settings.index') }}" class="block px-3 py-2 rounded hover:bg-white/10
					{{ request()->routeIs('admin.settings.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/80' }}">Settings</a>
				<a href="{{ route('admin.password.edit') }}" class="block px-3 py-2 rounded hover:bg-white/10">Change Password</a>
			</nav>
			<div class="mt-6">
				<form method="POST" action="{{ route('admin.logout') }}">
					@csrf
					<button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-white hover:bg-white/10">Logout</button>
				</form>
			</div>
		</div>
	</div>

	<script>
		// Init lucide
		if (window.lucide && lucide.createIcons) {
			lucide.createIcons();
		}
		// Dark mode toggle
		const toggle = document.getElementById('dark-mode-toggle');
		const htmlEl = document.documentElement;
		if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
			htmlEl.classList.add('dark');
		} else {
			htmlEl.classList.remove('dark');
		}
		if (toggle) {
			toggle.addEventListener('click', () => {
				if (htmlEl.classList.contains('dark')) {
					htmlEl.classList.remove('dark');
					localStorage.theme = 'light';
				} else {
					htmlEl.classList.add('dark');
					localStorage.theme = 'dark';
				}
			});
		}
	</script>
	<!-- Global Confirm Delete Modal -->
	<div id="confirm-delete-backdrop" class="fixed inset-0 z-[60] hidden">
		<div class="absolute inset-0 bg-black/50"></div>
		<div class="absolute inset-0 flex items-center justify-center p-4">
			<div class="w-full max-w-sm rounded-lg bg-white dark:bg-[#2c6366] shadow-xl">
				<div class="px-4 py-3 border-b border-slate-200 dark:border-white/10 flex items-center gap-2">
					<i data-lucide="alert-triangle" class="text-red-500"></i>
					<h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Confirm Deletion</h3>
				</div>
				<div class="px-4 py-4 text-sm text-slate-700 dark:text-slate-100" id="confirm-delete-message">
					Are you sure you want to remove this item?
				</div>
				<div class="px-4 py-3 border-t border-slate-200 dark:border-white/10 flex justify-end gap-2">
					<button type="button" id="confirm-delete-cancel" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-cancel">
						<i data-lucide="x-circle" class="h-4 w-4"></i><span>Cancel</span>
					</button>
					<button type="button" id="confirm-delete-ok" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
						<i data-lucide="trash-2" class="h-4 w-4"></i><span>Delete</span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<script>
		(function(){
			let pendingAction = null;
			const backdrop = document.getElementById('confirm-delete-backdrop');
			const msg = document.getElementById('confirm-delete-message');
			const btnOk = document.getElementById('confirm-delete-ok');
			const titleEl = backdrop.querySelector('h3');
			const okTextSpan = btnOk ? btnOk.querySelector('span') : null;
			const defaultTitle = 'Confirm Deletion';
			const defaultOkText = 'Delete';
			const btnCancel = document.getElementById('confirm-delete-cancel');
			function openModal(message, onConfirm) {
				msg.textContent = message || 'Are you sure you want to remove this item?';
				pendingAction = onConfirm;
				backdrop.classList.remove('hidden');
				if (window.lucide && lucide.createIcons) { lucide.createIcons(); }
			}
			function closeModal() {
				backdrop.classList.add('hidden');
				pendingAction = null;
				// restore defaults
				if (titleEl) titleEl.textContent = defaultTitle;
				if (okTextSpan) okTextSpan.textContent = defaultOkText;
			}
			btnCancel && btnCancel.addEventListener('click', closeModal);
			btnOk && btnOk.addEventListener('click', function(){
				if (typeof pendingAction === 'function') pendingAction();
				closeModal();
			});
			document.addEventListener('click', function(e){
				const trigger = e.target.closest('[data-confirm-delete]');
				if (!trigger) return;
				e.preventDefault();
				const form = trigger.closest('form');
				const message = trigger.getAttribute('data-message') || 'Are you sure you want to remove this item?';
				// allow custom title and OK button text
				const customTitle = trigger.getAttribute('data-title') || defaultTitle;
				const customOkText = trigger.getAttribute('data-ok-text') || defaultOkText;
				if (titleEl) titleEl.textContent = customTitle;
				if (okTextSpan) okTextSpan.textContent = customOkText;
				openModal(message, function(){ form && form.submit(); });
			});
			backdrop && backdrop.addEventListener('click', function(e){
				if (e.target === backdrop) closeModal();
			});
		})();
	</script>
	<!-- Global Quick Edit Modal -->
	<div id="quick-edit-backdrop" class="fixed inset-0 z-[70] hidden">
		<div class="absolute inset-0 bg-black/50"></div>
		<div class="absolute inset-0 flex items-center justify-center p-4">
			<div class="w-full max-w-[760px] max-h-[85vh] rounded-lg bg-white dark:bg-[#2c6366] shadow-xl flex flex-col">
				<div class="px-4 py-3 border-b border-slate-200 dark:border-white/10 flex items-center justify-between shrink-0">
					<h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100" id="quick-edit-title">Quick Edit</h3>
					<button class="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700" data-quick-close>&times;</button>
				</div>
				<div class="px-4 py-4 text-sm text-slate-700 dark:text-slate-100 overflow-auto flex-1" id="quick-edit-body"></div>
				<div class="px-4 py-3 border-t border-slate-200 dark:border-white/10 flex justify-end gap-2 shrink-0">
					<button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors" data-quick-close>
						<i data-lucide="x-circle" class="h-4 w-4"></i><span>Close</span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<script>
		(function(){
			const backdrop = document.getElementById('quick-edit-backdrop');
			const body = document.getElementById('quick-edit-body');
			const title = document.getElementById('quick-edit-title');
			function openQuickModal(html, modalTitle) {
				if (typeof html === 'string') {
					body.innerHTML = html;
				} else {
					body.textContent = '';
					body.appendChild(html);
				}
				title.textContent = modalTitle || 'Quick Edit';
				backdrop.classList.remove('hidden');
			}
			function closeQuickModal() {
				backdrop.classList.add('hidden');
				body.innerHTML = '';
			}
			backdrop && backdrop.addEventListener('click', function(e){
				if (e.target === backdrop) closeQuickModal();
			});
			document.addEventListener('click', function(e){
				const closeBtn = e.target.closest('[data-quick-close]');
				if (closeBtn) {
					e.preventDefault();
					closeQuickModal();
				}
			});
			window.openQuickModal = openQuickModal;
			window.closeQuickModal = closeQuickModal;
		})();
	</script>
</body>
</html>


