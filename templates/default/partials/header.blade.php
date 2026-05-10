<header class="text-white px-4 py-4 flex items-center justify-between relative z-10">
	<div class="flex items-center gap-3">
		@php
			$user = \Illuminate\Support\Facades\Auth::user();
			$info = $user?->info;
			$avatarPath = $info?->id_card_selfie ?? null;
			$avatarUrl = $avatarPath ? asset('storage/'.ltrim($avatarPath, '/')) : null;
			$displayName = $user?->name ?? $user?->username ?? '';
		@endphp
		<div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center overflow-hidden border-2 border-yellow-400 shadow-md">
			@if($avatarUrl)
				<img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="w-10 h-10 object-cover">
			@else
				<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-700">
					<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
					<circle cx="12" cy="7" r="4"/>
				</svg>
			@endif
		</div>
		<div class="-mt-[2px]">
		<p class="text-white text-xs font-medium">{{ __('header.text.welcome') }}</p>
		<h2 class="text-white text-base font-bold leading-tight">{{ $displayName }}</h2>
		</div>
	</div>
	<div class="flex items-center gap-4">
		@php
			$hasActiveNotification = false;
			if (\Illuminate\Support\Facades\Auth::check()) {
				$hasActiveNotification = \App\Models\Notification::where('user_id', \Illuminate\Support\Facades\Auth::id())
					->where('status', 'unread')
					->exists();
			}
		@endphp
		
		<a href="{{ route('contact-us') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 hover:bg-white/20 backdrop-blur transition cursor-pointer" aria-label="{{ __('header.text.contact') }}">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
				<rect width="20" height="16" x="2" y="4" rx="2"/>
				<path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
			</svg>
		</a>
		<a href="{{ route('notifications') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 hover:bg-white/20 backdrop-blur transition relative cursor-pointer" aria-label="{{ __('header.text.notifications') }}">
			<span id="header-ping" class="absolute left-0 top-0 h-8 w-8 rounded-full bg-blue-400 opacity-20 animate-ping {{ $hasActiveNotification ? '' : 'hidden' }}"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
				<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
				<path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
			</svg>
			<span id="header-badge" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full {{ $hasActiveNotification ? '' : 'hidden' }}"></span>
		</a>
	</div>
</header>


