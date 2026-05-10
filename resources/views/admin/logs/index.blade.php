@extends('admin.layouts.app')

@section('title', 'Logs')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Logs</li>
@endsection

@section('content')
<div class="logs-index">
	<fieldset class="mb-4 bg-white border border-primary rounded-lg p-4">
		<legend class="px-2 text-primary font-semibold">Filter</legend>
		<form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
			<div class="md:col-span-2">
				<label class="block text-xs text-slate-500 mb-1">Search</label>
				<div class="relative">
					<span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
						<i data-lucide="search" class="h-4 w-4"></i>
					</span>
					<input type="text" name="q" value="{{ request('q') }}" placeholder="Subject, User name or username" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
				</div>
			</div>
			<div>
				<label class="block text-xs text-slate-500 mb-1">From</label>
				<input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
			<div>
				<label class="block text-xs text-slate-500 mb-1">To</label>
				<input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
			@php
				$admin = auth('admin')->user();
			@endphp
			@if($admin && $admin->role === 'SuperAdmin')
				<div>
					<label class="block text-xs text-slate-500 mb-1">Domain</label>
					<select name="domain_id" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						<option value="">All Domains</option>
						@foreach($domains as $d)
							<option value="{{ $d->id }}" @selected((string)request('domain_id')===(string)$d->id)>{{ $d->name }} @if($d->host) ({{ $d->host }}) @endif</option>
						@endforeach
					</select>
				</div>
			@endif
			<div class="md:col-span-2 flex items-end gap-2">
				<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
					<i data-lucide="filter" class="h-4 w-4"></i><span>Filter</span>
				</button>
				<a href="{{ route('admin.logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-reset-white">
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
					<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Date</th>
					<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Subject</th>
					<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">User</th>
					@if($admin && $admin->role === 'SuperAdmin')
						<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Domain</th>
					@endif
					<th class="px-4 py-2 text-right text-sm font-medium text-slate-600">Action</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-slate-200">
				@foreach($logs as $log)
					<tr>
						<td class="px-4 py-3 text-sm">{{ $log->id }}</td>
						<td class="px-4 py-3 text-sm">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
						<td class="px-4 py-3 text-sm">
							@php
								$__subj = e($log->subject ?? '');
								// Light blue highlight for positive actions
								$__subj = preg_replace('/\b(registered)\b/i', '<span class="hl-blue">$1</span>', $__subj);
								// Red highlight for negative/destructive actions
								$__subj = preg_replace('/\b(rejected|removed|deleted)\b/i', '<span class="hl-red">$1</span>', $__subj);
                                // Yellow highlight for warning actions
                                $__subj = preg_replace('/\b(request|requested|update|updated)\b/i', '<span class="hl-yellow">$1</span>', $__subj);
                                // Green highlight for successful actions
                                $__subj = preg_replace('/\b(approved|accepted|fulfilled)\b/i', '<span class="hl-green">$1</span>', $__subj);
							@endphp
							{!! $__subj !!}
						</td>
						<td class="px-4 py-3 text-sm">
							@if($log->user)
								{{ $log->user->name }} <span class="text-slate-400">({{ $log->user->username ?? '—' }})</span>
							@else
								—
							@endif
						</td>
						@if($admin && $admin->role === 'SuperAdmin')
							<td class="px-4 py-3 text-sm">{{ $log->domain?->name ?? '—' }} @if($log->domain?->host) <span class="text-slate-400">({{ $log->domain->host }})</span> @endif</td>
						@endif
						<td class="px-4 py-3 text-sm text-right">
							<button type="button"
								class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border hover:bg-[#459699]/10 text-[#459699] btn-view"
								data-id="{{ $log->id }}"
								data-subject="{{ e($log->subject) }}"
								data-username="{{ e($log->user->username ?? '') }}"
								data-name="{{ e($log->user->name ?? '') }}"
								data-date="{{ e($log->created_at?->format('Y-m-d H:i:s')) }}"
								data-domain="{{ e($log->domain?->name ?? '') }}"
								data-json-b64="{{ base64_encode((string) $log->raw_html) }}">
								<i data-lucide="eye" class="h-3 w-3"></i><span>View</span>
							</button>
							@if($admin && $admin->role === 'SuperAdmin')
								<form action="{{ route('admin.logs.destroy', $log->id) }}" method="POST" class="inline">
									@csrf @method('DELETE')
									<button type="submit" data-confirm-delete data-title="Confirm Delete" data-ok-text="Delete" data-message="Are you sure you want to delete this log?" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border text-red-600 hover:bg-red-50 ml-1">
										<i data-lucide="trash-2" class="h-3 w-3"></i><span>Delete</span>
									</button>
								</form>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="mt-4">
		<div class="flex items-center justify-between text-sm text-slate-600 mb-2">
			<div>
				Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
				<span class="ml-2">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</span>
			</div>
		</div>
		{{ $logs->links() }}
	</div>
</div>
<script>
document.addEventListener('click', function(e){
	const btn = e.target.closest('.btn-view');
	if (!btn) return;
	const subj = btn.getAttribute('data-subject') || '';
	const name = btn.getAttribute('data-name') || '';
	const username = btn.getAttribute('data-username') || '';
	const date = btn.getAttribute('data-date') || '';
	const domain = btn.getAttribute('data-domain') || '';
	let jsonText = '';
	try {
		const b64 = btn.getAttribute('data-json-b64') || '';
		if (b64) jsonText = atob(b64);
	} catch (err) {}
	let pretty = '';
	try {
		// If raw_html is already JSON stringified, pretty print it
		const parsed = JSON.parse(jsonText);
		pretty = JSON.stringify(parsed, null, 2);
	} catch (err) {
		pretty = jsonText || '(no data)';
	}
	const html = `
		<div class="space-y-3">
			<div class="grid grid-cols-2 gap-3">
				<div><div class="text-xs text-slate-500">Subject</div><div class="text-sm">${subj}</div></div>
				<div><div class="text-xs text-slate-500">Date</div><div class="text-sm">${date}</div></div>
			</div>
			<div>
				<div class="text-xs text-slate-500 mb-1">Data</div>
				<pre class="p-3 rounded bg-slate-50 dark:bg-slate-700 text-xs overflow-auto whitespace-pre-wrap">${pretty.replace(/[&<>]/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[s]))}</pre>
			</div>
		</div>
	`;
	if (window.openQuickModal) {
		window.openQuickModal(html, 'Log Details');
		if (window.lucide && lucide.createIcons) { lucide.createIcons(); }
	}
});
</script>
@endsection


