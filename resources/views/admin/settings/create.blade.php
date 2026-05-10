@extends('admin.layouts.app')

@section('title', 'Create Setting')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li><a href="{{ route('admin.settings.index') }}" class="text-slate-500 hover:text-slate-700">Settings</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Create</li>
@endsection

@section('content')
<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 max-w-xl form-surface">
	<form method="POST" action="{{ route('admin.settings.store') }}">
		@csrf
		<div class="mb-4">
			<label class="block mb-1">Name</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 7h18"/>
						<path d="M3 12h18"/>
						<path d="M3 17h18"/>
					</svg>
				</span>
				<input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
			</div>
			@error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="mb-4">
			<label class="block mb-1">Value</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 6h16"/>
						<path d="M4 12h16"/>
						<path d="M4 18h10"/>
					</svg>
				</span>
				<textarea name="value" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" rows="4">{{ old('value') }}</textarea>
			</div>
			@error('value') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="flex gap-2">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="save" class="h-4 w-4"></i><span>Save</span>
			</button>
			<a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-cancel">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Cancel</span>
			</a>
		</div>
	</form>
	</div>
@endsection


