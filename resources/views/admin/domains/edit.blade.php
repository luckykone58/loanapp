@extends('admin.layouts.app')

@section('title', 'Edit Domain')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li><a href="{{ route('admin.domains.index') }}" class="text-slate-500 hover:text-slate-700">Manage Domains</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Edit</li>
@endsection

@section('content')
<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 max-w-xl domain-form form-surface">
	<form method="POST" action="{{ route('admin.domains.update', $domain) }}">
		@csrf @method('PUT')
		<div class="mb-4">
			<label class="block mb-1">Name</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="12" cy="7" r="4"/>
						<path d="M20 21a8 8 0 1 0-16 0"/>
					</svg>
				</span>
				<input type="text" name="name" value="{{ old('name', $domain->name) }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
			@error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="mb-4">
			<label class="block mb-1">Host</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="12" cy="12" r="10"/>
						<path d="M2 12h20"/>
						<path d="M12 2a15 15 0 0 1 0 20"/>
					</svg>
				</span>
				<input type="text" name="host" value="{{ old('host', $domain->host) }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
			</div>
			@error('host') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="mb-4">
			<label class="block mb-1">Status</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 15V3"/>
						<path d="M4 3h11l-1.5 3L15 9H4"/>
					</svg>
				</span>
				<select name="status" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
					@foreach(['Active','Suspend','Removed'] as $s)
						<option value="{{ $s }}" @selected(old('status', $domain->status ?? 'Active')===$s)>{{ $s }}</option>
					@endforeach
				</select>
			</div>
			@error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="mb-4">
			<label class="block mb-1">Expired Date</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<rect x="3" y="4" width="18" height="18" rx="2"/>
						<path d="M16 2v4"/>
						<path d="M8 2v4"/>
						<path d="M3 10h18"/>
					</svg>
				</span>
				<input type="date" name="expired_date" value="{{ old('expired_date', $domain->expired_date ? \Carbon\Carbon::parse($domain->expired_date)->format('Y-m-d') : '') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
			@error('expired_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="flex gap-2">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="save" class="h-4 w-4"></i><span>Update</span>
			</button>
			<a href="{{ route('admin.domains.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-cancel">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Cancel</span>
			</a>
		</div>
	</form>
	</div>
@endsection


