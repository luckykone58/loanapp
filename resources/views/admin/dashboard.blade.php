@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
	// Build scoped base queries (respect SuperAdmin domain override below)
	$admin = auth('admin')->user();
	$isSuper = $admin && $admin->role === 'SuperAdmin';
	$baseUsers = \App\Models\User::query();
	$baseLoans = \App\Models\Loan::query(); // uses start_date for timing
	if ($isSuper) {
		$baseUsers->withoutGlobalScope(\App\Scopes\DomainScope::class);
		$baseLoans->withoutGlobalScope(\App\Scopes\DomainScope::class);
	}
	$now = \Carbon\Carbon::now();
	$todayStart = $now->copy()->startOfDay();
	$todayEnd = $now->copy()->endOfDay();
	$yStart = $now->copy()->subDay()->startOfDay();
	$yEnd = $now->copy()->subDay()->endOfDay();
	$twStart = $now->copy()->startOfWeek();
	$twEnd = $now->copy()->endOfWeek();
	$lwStart = $now->copy()->subWeek()->startOfWeek();
	$lwEnd = $now->copy()->subWeek()->endOfWeek();
	$tmStart = $now->copy()->startOfMonth();
	$tmEnd = $now->copy()->endOfMonth();
	$lmStart = $now->copy()->subMonth()->startOfMonth();
	$lmEnd = $now->copy()->subMonth()->endOfMonth();

	$regStats = [
		'Today' => (clone $baseUsers)->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
		'Yesterday' => (clone $baseUsers)->whereBetween('created_at', [$yStart, $yEnd])->count(),
		'This Week' => (clone $baseUsers)->whereBetween('created_at', [$twStart, $twEnd])->count(),
		'Last Week' => (clone $baseUsers)->whereBetween('created_at', [$lwStart, $lwEnd])->count(),
		'This Month' => (clone $baseUsers)->whereBetween('created_at', [$tmStart, $tmEnd])->count(),
		'Last Month' => (clone $baseUsers)->whereBetween('created_at', [$lmStart, $lmEnd])->count(),
	];
	$loanCounts = [
		'Today' => (clone $baseLoans)->whereBetween('start_date', [$todayStart->toDateString(), $todayEnd->toDateString()])->count(),
		'Yesterday' => (clone $baseLoans)->whereBetween('start_date', [$yStart->toDateString(), $yEnd->toDateString()])->count(),
		'This Week' => (clone $baseLoans)->whereBetween('start_date', [$twStart->toDateString(), $twEnd->toDateString()])->count(),
		'Last Week' => (clone $baseLoans)->whereBetween('start_date', [$lwStart->toDateString(), $lwEnd->toDateString()])->count(),
		'This Month' => (clone $baseLoans)->whereBetween('start_date', [$tmStart->toDateString(), $tmEnd->toDateString()])->count(),
		'Last Month' => (clone $baseLoans)->whereBetween('start_date', [$lmStart->toDateString(), $lmEnd->toDateString()])->count(),
	];
	$loanAmounts = [
		'Today' => (clone $baseLoans)->whereBetween('start_date', [$todayStart->toDateString(), $todayEnd->toDateString()])->sum('amount'),
		'Yesterday' => (clone $baseLoans)->whereBetween('start_date', [$yStart->toDateString(), $yEnd->toDateString()])->sum('amount'),
		'This Week' => (clone $baseLoans)->whereBetween('start_date', [$twStart->toDateString(), $twEnd->toDateString()])->sum('amount'),
		'Last Week' => (clone $baseLoans)->whereBetween('start_date', [$lwStart->toDateString(), $lwEnd->toDateString()])->sum('amount'),
		'This Month' => (clone $baseLoans)->whereBetween('start_date', [$tmStart->toDateString(), $tmEnd->toDateString()])->sum('amount'),
		'Last Month' => (clone $baseLoans)->whereBetween('start_date', [$lmStart->toDateString(), $lmEnd->toDateString()])->sum('amount'),
	];
	$chartLabels = ['Today','Yesterday','This Week','Last Week','This Month','Last Month'];
	$regSeries = array_map(fn($l) => (int)($regStats[$l] ?? 0), $chartLabels);
	$loanSeries = array_map(fn($l) => (int)($loanCounts[$l] ?? 0), $chartLabels);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
	<!-- Combined Registered Card -->
	<div class="relative rounded-2xl p-5 text-slate-800 overflow-hidden bg-gradient-to-br from-blue-50 to-white border border-slate-200 dashboard-section-registered">
		<div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-blue-100/50 rounded-full blur-2xl"></div>
		<div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 bg-slate-100 rounded-full blur-xl"></div>
		<div class="relative z-10 flex items-center gap-3 mb-3">
			<span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-500">
				<i data-lucide="users"></i>
			</span>
			<h3 class="text-lg font-semibold">Registered</h3>
		</div>
		<ul class="relative z-10 divide-y divide-slate-200">
			@foreach(['Today','Yesterday','This Week','Last Week','Last Month'] as $label)
				<li class="py-2 flex items-start justify-between">
					<span class="text-slate-600">{{ $label }}</span>
					<span class="font-semibold text-slate-900">{{ number_format($regStats[$label] ?? 0) }}</span>
				</li>
			@endforeach
		</ul>
	</div>

	<!-- Combined Loan Requests Card -->
	<div class="relative rounded-2xl p-5 text-slate-800 overflow-hidden bg-gradient-to-br from-indigo-50 to-white border border-slate-200 dashboard-section-loanreq">
		<div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-purple-100/50 rounded-full blur-2xl"></div>
		<div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 bg-slate-100 rounded-full blur-xl"></div>
		<div class="relative z-10 flex items-center gap-3 mb-3">
			<span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-500">
				<i data-lucide="briefcase"></i>
			</span>
			<h3 class="text-lg font-semibold">Loan Requests</h3>
		</div>
		<ul class="relative z-10 divide-y divide-slate-200">
			@foreach(['Today','Yesterday','This Week','Last Week','Last Month'] as $label)
				<li class="py-2 grid grid-cols-3 gap-2 items-center">
					<span class="text-slate-600">{{ $label }}</span>
					<span class="text-slate-900 font-semibold text-right">{{ number_format($loanCounts[$label] ?? 0) }}</span>
					<span class="text-slate-900 font-semibold text-right">{{ number_format((float)($loanAmounts[$label] ?? 0), 2) }}</span>
				</li>
			@endforeach
		</ul>
	</div>
</div>

@php
	$admin = auth('admin')->user();
	$isSuper = $admin && $admin->role === 'SuperAdmin';

	$userQuery = \App\Models\User::query()->with('domain');
	$loanQuery = \App\Models\Loan::query()->with('user');
	if ($isSuper) {
		$userQuery->withoutGlobalScope(\App\Scopes\DomainScope::class);
		$loanQuery->withoutGlobalScope(\App\Scopes\DomainScope::class);
	}
	$recentUsers = $userQuery->orderByDesc('id')->limit(10)->get();
	$recentLoans = $loanQuery->orderByDesc('id')->limit(10)->get();
@endphp

<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
	<!-- Recent Registered -->
	<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 dark:bg-[#2c6366] dark:border-white/10 dashboard-section-recent-registered">
		<div class="flex items-center gap-2 mb-4">
			<span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
				<i data-lucide="users"></i>
			</span>
			<h3 class="text-lg font-semibold text-slate-800 dark:text-white">Recent Registered</h3>
		</div>
		@if($recentUsers->isEmpty())
			<p class="text-sm text-slate-500">No users yet.</p>
		@else
			<ul class="divide-y divide-slate-200 dark:divide-white/10">
				@foreach($recentUsers as $u)
					<li class="py-3 flex items-start justify-between">
						<div class="flex items-center gap-3">
							<div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-white inline-flex items-center justify-center text-xs font-bold">
								{{ strtoupper(substr($u->name ?? $u->username, 0, 1)) }}
							</div>
							<div>
								<div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $u->name ?? $u->username }}</div>
								<div class="text-xs text-slate-500">{{ '@'.$u->username }} @if($u->domain?->host)<span class="text-slate-400">• {{ $u->domain->host }}</span>@endif</div>
							</div>
						</div>
						<a href="{{ route('admin.users.edit', $u) }}" class="text-xs px-2 py-1 rounded border hover:bg-[#459699]/10 text-[#459699]">Manage</a>
					</li>
				@endforeach
			</ul>
		@endif
	</div>

	<!-- Recent Loan Requests (themed card) -->
	<div class="relative rounded-2xl shadow-lg p-6 text-white overflow-hidden bg-[#459699] dashboard-section-recent-loans">
		<div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
		<div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 bg-black/10 rounded-full blur-xl"></div>
		<div class="relative z-10 flex items-center gap-2 mb-4">
			<span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-white">
				<i data-lucide="briefcase"></i>
			</span>
			<h3 class="text-lg font-semibold text-white"><span class="text-white">Recent Loan Requests</span></h3>
		</div>
		@if($recentLoans->isEmpty())
			<p class="relative z-10 text-sm text-white/80">No recent loan requests.</p>
		@else
			<ul class="relative z-10 divide-y divide-white/10">
				@foreach($recentLoans as $loan)
					<li class="py-3 flex items-center justify-between">
						<div class="min-w-0">
							<div class="text-sm font-medium">
								{{ $loan->user?->name ?? $loan->user?->username ?? '—' }}
								@if($loan->loan_number)
									<span class="ml-2 text-xs bg-white/15 rounded px-2 py-0.5">#{{ $loan->loan_number }}</span>
								@endif
							</div>
							<div class="text-xs text-white/80 truncate">
								${{ number_format((float)$loan->amount, 2) }}
								@if($loan->status)
									<span class="mx-1">•</span>
									<span class="uppercase">{{ $loan->status }}</span>
								@endif
								@if($loan->period)
									<span class="mx-1">•</span>
									{{ $loan->period }} mo
								@endif
							</div>
						</div>
						<a href="{{ route('admin.loans.edit', $loan) }}" class="text-xs px-2 py-1 rounded bg-white text-[#459699] font-semibold hover:bg-white/90">View</a>
					</li>
				@endforeach
			</ul>
		@endif
	</div>
</div>

<!-- Bar Charts -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
	<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 dark:bg-[#2c6366] dark:border-white/10 dashboard-section-bar-registered">
		<h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Registered (Bar Chart)</h3>
		<canvas id="regBarChart" height="220"></canvas>
	</div>
	<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 dark:bg-[#2c6366] dark:border-white/10 dashboard-section-bar-loans">
		<h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Loan Requests (Bar Chart)</h3>
		<canvas id="loanBarChart" height="220"></canvas>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
	const labels = @json($chartLabels);
	const regData = @json($regSeries);
	const loanData = @json($loanSeries);
	const isDark = document.documentElement.classList.contains('dark');
	function makeOptions(isDarkMode){
		const textColor = isDarkMode ? '#ffffff' : '#334155'; // slate-700 for light
		const gridColor = isDarkMode ? 'rgba(255,255,255,0.12)' : 'rgba(15,23,42,0.08)'; // slate-900 ~
		return {
			responsive: true,
			plugins: {
				legend: { display: false, labels: { color: textColor } },
				tooltip: {
					mode: 'index',
					intersect: false,
					titleColor: textColor,
					bodyColor: textColor,
					borderWidth: isDarkMode ? 0 : 1,
					backgroundColor: isDarkMode ? 'rgba(15, 23, 42, 0.9)' : '#ffffff',
					borderColor: 'rgba(15,23,42,0.08)',
				}
			},
			scales: {
				x: {
					ticks: { color: textColor },
					grid: { color: gridColor, drawBorder: false }
				},
				y: {
					beginAtZero: true,
					ticks: { precision: 0, color: textColor },
					grid: { color: gridColor, drawBorder: false }
				}
			}
		};
	}
	const baseOpts = makeOptions(isDark);
	const regCtx = document.getElementById('regBarChart').getContext('2d');
	const regChart = new Chart(regCtx, {
		type: 'bar',
		data: {
			labels,
			datasets: [{ label: 'Registered', data: regData, backgroundColor: 'rgba(59,130,246,0.6)', borderColor: 'rgb(59,130,246)', borderWidth: 1, borderRadius: 6 }]
		},
		options: baseOpts
	});
	const loanCtx = document.getElementById('loanBarChart').getContext('2d');
	const loanChart = new Chart(loanCtx, {
		type: 'bar',
		data: {
			labels,
			datasets: [{ label: 'Loan Requests', data: loanData, backgroundColor: 'rgba(147,51,234,0.6)', borderColor: 'rgb(147,51,234)', borderWidth: 1, borderRadius: 6 }]
		},
		options: baseOpts
	});
	// React to dark mode changes
	const applyTheme = () => {
		const darkNow = document.documentElement.classList.contains('dark');
		const nextOpts = makeOptions(darkNow);
		[regChart, loanChart].forEach((c) => {
			c.options.plugins = nextOpts.plugins;
			c.options.scales = nextOpts.scales;
			c.update();
		});
	};
	const mo = new MutationObserver((muts) => {
		for (const m of muts) {
			if (m.type === 'attributes' && m.attributeName === 'class') {
			 applyTheme();
			 break;
			}
		}
	});
	mo.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
})();
</script>
@endsection



