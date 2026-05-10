@extends('admin.layouts.app')

@section('title', 'Create Notification')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li><a href="{{ route('admin.notifications.index') }}" class="text-slate-500 hover:text-slate-700">Notifications</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Create</li>
@endsection

@section('content')
<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 max-w-3xl form-surface">
	<form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-4">
		@csrf
		<div>
			<label class="block mb-1">User</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
						<circle cx="9" cy="7" r="4"/>
						<path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
						<path d="M16 3.13a4 4 0 0 1 0 7.75"/>
					</svg>
				</span>
				<select name="user_id" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					<option value="">Select user</option>
					@foreach($users as $u)
						<option value="{{ $u->id }}" @selected(old('user_id')==$u->id)>{{ $u->username ?? $u->name }}</option>
					@endforeach
				</select>
			</div>
			@error('user_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div>
			<label class="block mb-1">Type</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 13h8l-1 8L21 3H9l1 10z"/>
					</svg>
				</span>
				<select name="type" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					@foreach(['loan','withdrawal','account'] as $t)
						<option value="{{ $t }}" @selected(old('type')===$t)>{{ ucfirst($t) }}</option>
					@endforeach
				</select>
			</div>
			@error('type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div>
			<label class="block mb-1">Title</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 6h16"/>
						<path d="M4 12h16"/>
						<path d="M4 18h10"/>
					</svg>
				</span>
				<input type="text" name="title" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ old('title') }}" required>
			</div>
			@error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div>
			<label class="block mb-1">Message</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
					</svg>
				</span>
				<input type="text" name="message" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" value="{{ old('message') }}" required>
			</div>
			@error('message') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div>
			<label class="block mb-1">Subtext</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
					</svg>
				</span>
				<textarea name="subtext" rows="4" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">{{ old('subtext') }}</textarea>
			</div>
			@error('subtext') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div>
			<label class="block mb-1">Notes</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 3v14a2 2 0 0 0 2 2h11"/>
						<path d="M17 21c0-4 4-3.5 4-7a4 4 0 1 0-8 0c0 3.5 4 3 4 7"/>
					</svg>
				</span>
				<textarea name="notes" rows="3" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">{{ old('notes') }}</textarea>
			</div>
			@error('notes') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div>
			<label class="block mb-1">Status</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 13h8l-1 8L21 3H9l1 10z"/>
					</svg>
				</span>
				<select name="status" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					@foreach(['unread','read'] as $s)
						<option value="{{ $s }}" @selected(old('status')===$s)>{{ ucfirst($s) }}</option>
					@endforeach
				</select>
			</div>
			@error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div>
			<label class="block mb-1">Created Date (optional)</label>
			<div class="relative">
				<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<rect x="3" y="4" width="18" height="18" rx="2"/>
						<path d="M16 2v4"/>
						<path d="M8 2v4"/>
						<path d="M3 10h18"/>
					</svg>
				</span>
				<input type="datetime-local" name="created_date" value="{{ old('created_date') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
			</div>
			@error('created_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
		</div>
		<div class="pt-2">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="save" class="h-4 w-4"></i><span>Create</span>
			</button>
			<a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center gap-2 ml-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-cancel">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Cancel</span>
			</a>
		</div>
	</form>
	</div>
@endsection


