@extends('admin.layouts.app')

@section('title', 'Manage Domains')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Manage Domains</li>
@endsection
@section('actions')
<a href="{{ route('admin.domains.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
	<i data-lucide="plus" class="h-4 w-4"></i>
	<span>Add Domain</span>
</a>
@endsection

@section('content')
<div class="domains-index">
@if(session('success'))
	<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
@endif

<fieldset class="mb-4 bg-white border border-primary rounded-lg p-4">
	<legend class="px-2 text-primary font-semibold">Filter</legend>
	<form method="GET" action="{{ route('admin.domains.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
		<div class="md:col-span-2">
			<label class="block text-xs text-slate-500 mb-1">Search</label>
			<div class="relative">
				<span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
					<i data-lucide="search" class="h-4 w-4"></i>
				</span>
				<input type="text" name="q" value="{{ request('q') }}" placeholder="Name or Host" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
		</div>
		<div>
			<label class="block text-xs text-slate-500 mb-1">Expired From</label>
			<input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
		</div>
		<div>
			<label class="block text-xs text-slate-500 mb-1">Expired To</label>
			<input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded pl-2 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
		</div>
		<div class="md:col-span-2 flex items-end gap-2">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="filter" class="h-4 w-4"></i><span>Filter</span>
			</button>
			<a href="{{ route('admin.domains.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-reset-white">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Reset</span>
			</a>
		</div>
	</form>
</fieldset>

<div class="bg-white shadow overflow-x-auto">
	<table class="min-w-full divide-y divide-slate-200">
		<thead class="bg-slate-200 dark:bg-slate-800">
			<tr>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">ID</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Name</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Host</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Status</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Expired</th>
				<th class="px-4 py-2"></th>
			</tr>
		</thead>
		<tbody class="divide-y divide-slate-200">
			@foreach($domains as $d)
				<tr>
					<td class="px-4 py-3 text-sm">{{ $d->id }}</td>
					<td class="px-4 py-3 text-sm">{{ $d->name }}</td>
					<td class="px-4 py-3 text-sm">{{ $d->host }}</td>
					<td class="px-4 py-3 text-sm">
						<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
							{{ $d->status === 'Active' ? 'bg-green-100 text-green-700' : ($d->status === 'Suspend' ? 'bg-yellow-100 text-yellow-700 status-suspend' : 'bg-red-100 text-red-700') }}">
							{{ $d->status ?? 'Active' }}
						</span>
					</td>
					<td class="px-4 py-3 text-sm">
						@php
							$label = '—'; $cls = 'text-slate-500';
							if (!empty($d->expired_date)) {
								$now = \Carbon\Carbon::now()->startOfDay();
								$exp = \Carbon\Carbon::parse($d->expired_date)->startOfDay();
								$days = $now->diffInDays($exp, false); // negative expired
								if ($days < 0) {
                                    $label = 'Expired '.abs($days).' day(s) ago';
                                    $cls = 'text-red-600 font-semibold';
                                } elseif ($days <= 2) {
                                    $label = 'In '.$days.' day(s)';
                                    $cls = 'text-red-600 font-semibold';
                                } elseif ($days <= 7) {
                                    $label = 'In '.$days.' day(s)';
                                    $cls = 'text-yellow-600 font-medium';
                                } else {
                                    $label = \Carbon\Carbon::parse($d->expired_date)->toDateString();
                                    $cls = 'text-slate-700';
                                }
							}
						@endphp
						<span class="{{ $cls }}">{{ $label }}</span>
					</td>
					<td class="px-4 py-3 text-sm text-right space-x-2">
						<a href="{{ route('admin.domains.edit', $d) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border hover:bg-[#459699]/10 text-[#459699] btn-edit">
							<i data-lucide="pencil" class="h-3 w-3"></i><span>Edit</span>
						</a>
						<form action="{{ route('admin.domains.destroy', $d) }}" method="POST" class="inline">
							@csrf @method('DELETE')
							<button type="submit" data-confirm-delete data-message="Are you sure you want to remove this domain?" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border text-red-600 hover:bg-red-50">
								<i data-lucide="trash-2" class="h-3 w-3"></i><span>Delete</span>
							</button>
						</form>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>

<div class="mt-4">
	{{ $domains->links() }}
</div>
</div>
@endsection


