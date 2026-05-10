@extends('admin.layouts.app')

@section('title', 'Notifications')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Notifications</li>
@endsection
@section('actions')
<a href="{{ route('admin.notifications.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
	<i data-lucide="bell-plus" class="h-4 w-4"></i>
	<span>New Notification</span>
</a>
@endsection

@section('content')
<div class="notifications-index">
@if(session('success'))
	<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
@endif

<fieldset class="mb-4 bg-white border border-primary rounded-lg p-4">
	<legend class="px-2 text-primary font-semibold">Filter</legend>
	<form method="GET" action="{{ route('admin.notifications.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
		<div class="md:col-span-2">
			<label class="block text-xs text-slate-500 mb-1">Search</label>
			<div class="relative">
				<span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
					<i data-lucide="search" class="h-4 w-4"></i>
				</span>
				<input type="text" name="q" value="{{ request('q') }}" placeholder="Title, Message or Username" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
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
			<a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-reset-white">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Reset</span>
			</a>
		</div>
	</form>
</fieldset>


@php($admin = auth('admin')->user())
<div class="bg-white shadow">
	<table class="min-w-full text-sm">
		<thead class="bg-slate-200 dark:bg-slate-800">
			<tr>
				<th class="text-left px-4 py-3">ID</th>
				<th class="text-left px-4 py-3">User</th>
				@if($admin && $admin->role === 'SuperAdmin')
				<th class="text-left px-4 py-3">Domain</th>
				@endif
				<th class="text-left px-4 py-3">Type</th>
				<th class="text-left px-4 py-3">Text</th>
				<th class="text-left px-4 py-3">Notes</th>
				<th class="text-left px-4 py-3">Status</th>
				<th class="text-left px-4 py-3">Created</th>
				<th class="text-left px-4 py-3">Actions</th>
			</tr>
		</thead>
		<tbody>
			@forelse($notifications as $n)
				<tr class="border-t border-slate-100 dark:border-slate-700">
					<td class="px-4 py-3">{{ $n->id }}</td>
					<td class="px-4 py-3">{{ $n->user?->username ?? $n->user?->name ?? '-' }}</td>
					@if($admin && $admin->role === 'SuperAdmin')
					<td class="px-4 py-3">
						{{ $n->user?->domain?->name ?? '—' }}
						@if($n->user?->domain?->host)
							<span class="text-slate-400">({{ $n->user->domain->host }})</span>
						@endif
					</td>
					@endif
					<td class="px-4 py-3">
						<span class="inline-flex items-center rounded px-2 py-0.5 text-xs bg-slate-100 type-chip">
							{{ ucfirst($n->type ?? 'account') }}
						</span>
					</td>
					<td class="px-4 py-3 max-w-xs truncate" title="{{ $n->text }}">{{ $n->text }}</td>
					<td class="px-4 py-3 max-w-xs truncate" title="{{ $n->notes }}">{{ $n->notes }}</td>
					<td class="px-4 py-3">
						<span class="inline-flex items-center rounded px-2 py-0.5 text-xs {{ $n->status==='unread' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
							{{ ucfirst($n->status) }}
						</span>
					</td>
					<td class="px-4 py-3">{{ \Carbon\Carbon::parse($n->created_date)->format('Y-m-d H:i') }}</td>
					<td class="px-4 py-3">
						<div class="flex items-center gap-2">
							<a href="{{ route('admin.notifications.edit', $n) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border hover:bg-[#459699]/10 text-[#459699] btn-edit">
								<i data-lucide="pencil" class="h-3 w-3"></i><span>Edit</span>
							</a>
							<form method="POST" action="{{ route('admin.notifications.destroy', $n) }}">
								@csrf
								@method('DELETE')
								<button type="submit" data-confirm-delete data-message="Are you sure you want to remove this notification?" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-lg border text-red-600 hover:bg-red-50">
									<i data-lucide="trash-2" class="h-3 w-3"></i><span>Delete</span>
								</button>
							</form>
						</div>
					</td>
				</tr>
			@empty
				<tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">No notifications found.</td></tr>
			@endforelse
		</tbody>
	</table>
</div>

<div class="mt-4">
	{{ $notifications->links() }}
</div>
</div>
@endsection


