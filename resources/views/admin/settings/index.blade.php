@extends('admin.layouts.app')

@section('title', 'Settings')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Settings</li>
@endsection

@section('content')
<div class="flex items-center justify-between mb-4">
	<h3 class="text-lg font-semibold">Settings</h3>
	<a href="{{ route('admin.settings.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">Create Setting</a>
	</div>

@if(session('success'))
	<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow overflow-x-auto">
	<table class="min-w-full divide-y divide-slate-200">
		<thead class="bg-slate-50">
			<tr>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">ID</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Name</th>
				<th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Value</th>
				<th class="px-4 py-2"></th>
			</tr>
		</thead>
		<tbody class="divide-y divide-slate-200">
			@foreach($settings as $s)
				<tr>
					<td class="px-4 py-2 text-sm">{{ $s->id }}</td>
					<td class="px-4 py-2 text-sm">{{ $s->name }}</td>
					<td class="px-4 py-2 text-sm">{{ Str::limit($s->value, 60) }}</td>
					<td class="px-4 py-2 text-sm text-right space-x-2">
						<a href="{{ route('admin.settings.edit', $s) }}" class="text-[#459699] hover:underline">Edit</a>
						<form action="{{ route('admin.settings.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Delete this setting?');">
							@csrf @method('DELETE')
							<button type="submit" class="text-red-600 hover:underline">Delete</button>
						</form>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>

<div class="mt-4">
	{{ $settings->links() }}
</div>
@endsection


