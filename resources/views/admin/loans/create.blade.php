@extends('admin.layouts.app')

@section('title', 'Create Loan')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li><a href="{{ route('admin.loans.index') }}" class="text-slate-500 hover:text-slate-700">Loans</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Create</li>
@endsection

@section('content')
<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 max-w-2xl form-surface">
	<form method="POST" action="{{ route('admin.loans.store') }}">
		@csrf
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
						@foreach($users as $user)
							<option value="{{ $user->id }}" @selected(old('user_id')==$user->id)>{{ $user->username }}</option>
						@endforeach
					</select>
				</div>
				@error('user_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
			<div>
				<label class="block mb-1">Loan Number</label>
				<div class="relative">
					<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M7 9h10M7 15h10M9 7v10M15 7v10"/>
						</svg>
					</span>
					<input type="text" name="loan_number" value="{{ old('loan_number') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
				</div>
				@error('loan_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
			<div>
				<label class="block mb-1">Amount</label>
				<div class="relative">
					<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="2" y="7" width="20" height="10" rx="2"/>
							<path d="M16 7v10"/>
							<path d="M6 11h4"/>
						</svg>
					</span>
					<input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
				</div>
				@error('amount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
			<div>
				<label class="block mb-1">Start Date</label>
				<div class="relative">
					<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="4" width="18" height="18" rx="2"/>
							<path d="M16 2v4"/>
							<path d="M8 2v4"/>
							<path d="M3 10h18"/>
						</svg>
					</span>
					<input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
				</div>
				@error('start_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
			<div>
				<label class="block mb-1">Period (months)</label>
				<div class="relative">
					<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M12 6v6l4 2"/>
							<circle cx="12" cy="12" r="10"/>
						</svg>
					</span>
					<input type="number" name="period" value="{{ old('period') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
				</div>
				@error('period') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
			<div>
				<label class="block mb-1">Interest (%)</label>
				<div class="relative">
					<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M19 5L5 19"/>
							<circle cx="6.5" cy="6.5" r="2.5"/>
							<circle cx="17.5" cy="17.5" r="2.5"/>
						</svg>
					</span>
					<input type="number" step="0.01" name="interest" value="{{ old('interest') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
				</div>
				@error('interest') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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
						@foreach(['processing','approved','rejected','paid'] as $status)
							<option value="{{ $status }}" @selected(old('status')===$status)>{{ ucfirst($status) }}</option>
						@endforeach
					</select>
				</div>
				@error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
			</div>
		</div>
		<div class="flex gap-2 mt-4">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="save" class="h-4 w-4"></i><span>Save</span>
			</button>
			<a href="{{ route('admin.loans.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors btn-cancel">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Cancel</span>
			</a>
		</div>
	</form>
</div>
@endsection


