@extends('admin.layouts.app')

@section('title', 'Create User')

@section('breadcrumb')
<li><a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
<li>/</li>
<li><a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-slate-700">Users</a></li>
<li>/</li>
<li class="text-slate-900 dark:text-white font-medium">Create</li>
@endsection

@section('content')
<div class="bg-white dark:bg-[#2c6366] rounded-lg shadow p-6 max-w-3xl form-surface">
	<form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
		@csrf
		<div class="rounded border p-4 mb-6">
			<h4 class="font-semibold mb-3 inline-flex items-center gap-2">
				<span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#459699]/10 text-[#459699]">
					<i data-lucide="user-cog"></i>
				</span>
				Account
			</h4>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				@php($admin = auth('admin')->user())
				@if($admin && $admin->role === 'SuperAdmin')
					<div class="md:col-span-2">
						<label class="block mb-1">Domain</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="12" r="10"/>
									<path d="M2 12h20"/>
									<path d="M12 2a15 15 0 0 1 0 20"/>
								</svg>
							</span>
							<select name="domain_id" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
								<option value="">Select domain</option>
								@foreach($domains as $d)
									<option value="{{ $d->id }}" @selected(old('domain_id')==$d->id)>{{ $d->name }} ({{ $d->host }})</option>
								@endforeach
							</select>
						</div>
						@error('domain_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
					</div>
				@endif
				<div>
					<label class="block mb-1">Name</label>
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="12" cy="7" r="4"/>
								<path d="M20 21a8 8 0 1 0-16 0"/>
							</svg>
						</span>
						<input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					</div>
					@error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Username</label>
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M16 8a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/>
								<path d="M6 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/>
							</svg>
						</span>
						<input type="text" name="username" value="{{ old('username') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					</div>
					@error('username') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Password</label>
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
							</svg>
						</span>
						<input type="password" name="password" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
					</div>
					@error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Role</label>
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
								<circle cx="9" cy="7" r="4"/>
								<path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
								<path d="M16 3.13a4 4 0 0 1 0 7.75"/>
							</svg>
						</span>
						<select name="role" id="role" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
							<option value="user" @selected(old('role')==='user')>User</option>
							<option value="admin" @selected(old('role')==='admin')>Admin</option>
						</select>
					</div>
					@error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block mb-1">Status</label>
					<div class="relative">
						<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
							<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M4 15V3"/>
								<path d="M4 3h11l-1.5 3L15 9H4"/>
							</svg>
						</span>
						<select name="status" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent" required>
							<option value="Active" @selected(old('status','Active')==='Active')>Active</option>
							<option value="Block" @selected(old('status')==='Block')>Block</option>
						</select>
					</div>
					@error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
				</div>
			</div>
		</div>

		<div id="user-sections" class="{{ old('role','user')==='user' ? '' : 'hidden' }} space-y-6">
			<div class="rounded border p-4">
				<h4 class="font-semibold mb-3 inline-flex items-center gap-2">
					<span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#459699]/10 text-[#459699]">
						<i data-lucide="user"></i>
					</span>
					Details
				</h4>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block mb-1">Wallet</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="2" y="7" width="20" height="10" rx="2"/>
									<path d="M16 7v10"/>
									<path d="M6 11h4"/>
								</svg>
							</span>
							<input type="number" step="0.01" name="wallet" value="{{ old('wallet', 0) }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
						@error('wallet') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
					</div>
					<div>
						<label class="block mb-1">Credit Score</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="m12 17-5 3 1.9-5.9L4 9h6l2-5 2 5h6l-4.9 5.1L17 20z"/>
								</svg>
							</span>
							<input type="number" name="credit_score" value="{{ old('credit_score') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">Withdrawal Code</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="12" r="3"/>
									<path d="M3 12h3"/>
									<path d="M18 12h3"/>
									<path d="M12 3v3"/>
									<path d="M12 18v3"/>
								</svg>
							</span>
							<input type="text" name="withdrawal_code" value="{{ old('withdrawal_code') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">Bank Name</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="2" y="7" width="20" height="10" rx="2"/>
									<path d="M8 11h8"/>
									<path d="M6 15h2"/>
								</svg>
							</span>
							<input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">Bank Number</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="5" width="18" height="14" rx="2"/>
									<path d="M3 10h18"/>
									<path d="M7 15h4"/>
								</svg>
							</span>
							<input type="text" name="bank_number" value="{{ old('bank_number') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
				</div>
			</div>

			<div class="rounded border p-4">
				<h4 class="font-semibold mb-3 inline-flex items-center gap-2">
					<span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#459699]/10 text-[#459699]">
						<i data-lucide="id-card"></i>
					</span>
					Verification
				</h4>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block mb-1">Full Name</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="7" r="4"/>
									<path d="M20 21a8 8 0 1 0-16 0"/>
								</svg>
							</span>
							<input type="text" name="full_name" value="{{ old('full_name') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">ID Card Number</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="5" width="18" height="14" rx="2"/>
									<path d="M7 9h6"/>
									<path d="M7 13h10"/>
								</svg>
							</span>
							<input type="text" name="id_card_number" value="{{ old('id_card_number') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">ID Card Front</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="4" width="18" height="14" rx="2"/>
									<path d="M8 12h8"/>
									<path d="M8 8h5"/>
								</svg>
							</span>
							<input type="file" name="id_card_front" accept="image/*" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">ID Card Back</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="4" width="18" height="14" rx="2"/>
									<path d="M8 12h8"/>
									<path d="M8 8h5"/>
								</svg>
							</span>
							<input type="file" name="id_card_back" accept="image/*" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div class="md:col-span-2">
						<label class="block mb-1">ID Card Selfie</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="4" width="18" height="14" rx="2"/>
									<path d="M8 12h8"/>
									<path d="M8 8h5"/>
								</svg>
							</span>
							<input type="file" name="id_card_selfie" accept="image/*" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div class="md:col-span-2">
						<label class="block mb-1">Signature</label>
						<p class="text-sm text-slate-500">Signature is captured on the frontend. It will display here once available.</p>
					</div>
				</div>
			</div>

			<div class="rounded border p-4">
				<h4 class="font-semibold mb-3 inline-flex items-center gap-2">
					<span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#459699]/10 text-[#459699]">
						<i data-lucide="home"></i>
					</span>
					Personal
				</h4>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div class="md:col-span-2">
						<label class="block mb-1">Address</label>
						<textarea name="address" rows="3" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">{{ old('address') }}</textarea>
					</div>
					<div>
						<label class="block mb-1">Company</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M3 21V7a2 2 0 0 1 2-2h3V3h8v2h3a2 2 0 0 1 2 2v14H3Z"/>
									<path d="M3 10h18"/>
								</svg>
							</span>
							<input type="text" name="company" value="{{ old('company') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div class="md:col-span-2">
						<label class="block mb-1">Company Address</label>
						<textarea name="company_address" rows="2" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">{{ old('company_address') }}</textarea>
					</div>
					<div>
						<label class="block mb-1">Position</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
									<circle cx="9" cy="7" r="4"/>
								</svg>
							</span>
							<input type="text" name="position" value="{{ old('position') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">Monthly Income</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M12 1v22"/>
									<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>
								</svg>
							</span>
							<input type="number" step="0.01" name="monthly_income" value="{{ old('monthly_income') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
				</div>
			</div>

			<div class="rounded border p-4">
				<h4 class="font-semibold mb-3 inline-flex items-center gap-2">
					<span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#459699]/10 text-[#459699]">
						<i data-lucide="users"></i>
					</span>
					Reference
				</h4>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block mb-1">Contact 1 Person</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="7" r="4"/>
									<path d="M20 21a8 8 0 1 0-16 0"/>
								</svg>
							</span>
							<input type="text" name="contact_1_person" value="{{ old('contact_1_person') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">Contact 1 Phone</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.09 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z"/>
								</svg>
							</span>
							<input type="text" name="contact_1_phone" value="{{ old('contact_1_phone') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div class="md:col-span-2">
						<label class="block mb-1">Contact 1 Relativity</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M10 13a5 5 0 1 0-7 7l7-7Z"/>
									<path d="M12 12l7-7a5 5 0 1 1 7 7l-7 7"/>
									<path d="M16 5l3 3"/>
								</svg>
							</span>
							<input type="text" name="contact_1_relativity" value="{{ old('contact_1_relativity') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">Contact 2 Person</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="7" r="4"/>
									<path d="M20 21a8 8 0 1 0-16 0"/>
								</svg>
							</span>
							<input type="text" name="contact_2_person" value="{{ old('contact_2_person') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div>
						<label class="block mb-1">Contact 2 Phone</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.09 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z"/>
								</svg>
							</span>
							<input type="text" name="contact_2_phone" value="{{ old('contact_2_phone') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
					<div class="md:col-span-2">
						<label class="block mb-1">Contact 2 Relativity</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#459699]">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M10 13a5 5 0 1 0-7 7l7-7Z"/>
									<path d="M12 12l7-7a5 5 0 1 1 7 7l-7 7"/>
									<path d="M16 5l3 3"/>
								</svg>
							</span>
							<input type="text" name="contact_2_relativity" value="{{ old('contact_2_relativity') }}" class="w-full border rounded pl-10 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#459699]/40 focus:border-[#459699] dark:bg-transparent">
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="flex gap-2 mt-4">
			<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#459699] text-white hover:bg-[#459699]/90">
				<i data-lucide="save" class="h-4 w-4"></i><span>Save</span>
			</button>
			<a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-primary border border-primary hover:bg-primary/5 transition-colors">
				<i data-lucide="x-circle" class="h-4 w-4"></i><span>Cancel</span>
			</a>
		</div>
	</form>
	</div>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const role = document.getElementById('role');
		const sections = document.getElementById('user-sections');
		function toggle() {
			if (role.value === 'user') sections.classList.remove('hidden');
			else sections.classList.add('hidden');
		}
		role.addEventListener('change', toggle);
		toggle();
	});
</script>
@endsection


