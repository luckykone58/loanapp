<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $currentDomain->name ?? config('app.name') }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="/css/main.css">
	<style>
		* { box-sizing: border-box; }
		body {
			margin: 0;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}
		.carousel-slide { display: none; }
		.carousel-slide.active { display: block; }
		.carousel-dot { transition: all 0.3s ease; }
		.cursor-pointer { cursor: pointer; }
	</style>
</head>
<body class="bg-gray-50 text-gray-800">
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')

			<main class="flex-1 pb-0 relative pb-8 px-2">
				<div class="relative z-10 p-4 space-y-6 top-[10px] pb-40">
					

					<div role="alert" class="relative w-full rounded-2xl bg-white/95 backdrop-blur shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] p-4 border-l-4 border-blue-500 flex items-start gap-3">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 flex-shrink-0 mt-0.5">
							<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
							<path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
						</svg>
						<div id="notif-message" class="text-blue-900 text-sm leading-snug"></div>
					</div>

					<div class="relative overflow-hidden rounded-3xl shadow-lg group cursor-pointer">
						<div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-500 transition-transform group-hover:scale-105 duration-500"></div>
						<div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
						<div class="absolute -left-8 -bottom-8 w-24 h-24 bg-blue-900/20 rounded-full blur-xl"></div>
						<div class="relative p-5 flex items-center gap-4">
							<div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex-shrink-0 overflow-hidden shadow-inner">
								<img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=300&fit=crop" alt="Offer" class="w-full h-full object-cover" />
							</div>
							<div class="flex-1">
								<span class="inline-block bg-yellow-400/90 text-blue-900 text-[10px] font-bold px-2 py-0.5 rounded-full mb-1.5 shadow-sm">⭐ {{ __('home.title.special_offer') }}</span>
								<h3 class="text-white text-lg font-bold leading-tight mb-0.5">{{ __('home.text.complete_info') }}</h3>
								<p class="text-blue-100 text-xs font-medium">{{ __('home.text.instant_approval') }}</p>
							</div>
							<div class="bg-white/20 rounded-full p-1.5 text-white">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
							</div>
						</div>
					</div>

					<div>
						<h3 class="text-gray-800 text-base font-bold mb-3 px-1">{{ __('home.title.quick_links') }}</h3>
						<div class="grid grid-cols-3 gap-3">
							<a href="{{ route('loan') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] border border-gray-100 hover:shadow-md transition-all active:scale-95">
								<div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="M7 15h4"/></svg>
								</div>
								<span class="text-xs font-semibold text-gray-700 text-center">{{ __('home.button.apply_loan') }}</span>
							</a>
							<a href="{{ route('notifications') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] border border-gray-100 hover:shadow-md transition-all active:scale-95">
								<div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
								</div>
								<span class="text-xs font-semibold text-gray-700 text-center">{{ __('home.button.notifications') }}</span>
							</a>
							<a href="{{ route('contact-us') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] border border-gray-100 hover:shadow-md transition-all active:scale-95">
								<div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
								</div>
								<span class="text-xs font-semibold text-gray-700 text-center">{{ __('home.button.contact_us') }}</span>
							</a>
						</div>
					</div>

					<div class="bg-white rounded-3xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] p-2">
						<div class="relative w-full rounded-2xl overflow-hidden group">
							<div class="aspect-[16/9] relative bg-gray-100 rounded-2xl overflow-hidden">
								@php
									$homeSlides = \App\Support\Settings::getJson('home_sliders', []);
								@endphp
								@if(!empty($homeSlides))
									@foreach($homeSlides as $i => $path)
										<div class="carousel-slide {{ $i===0 ? 'active' : '' }} h-full"><img src="{{ asset('storage/'.ltrim($path, '/')) }}" alt="slide {{ $i+1 }}" class="w-full h-full object-cover"></div>
									@endforeach
								@else
									<div class="carousel-slide active h-full"><div class="bg-gradient-to-r from-blue-400 to-blue-600 p-8 h-full flex flex-col justify-center items-center text-white"><h3 class="text-xl font-semibold mb-2 text-center">{{ __('home.title.special_offer') }}</h3><p class="text-center text-blue-50">{{ __('home.message.special_offer_detail') }}</p></div></div>
									<div class="carousel-slide h-full"><div class="bg-gradient-to-r from-cyan-400 to-blue-500 p-8 h-full flex flex-col justify-center items-center text-white"><h3 class="text-xl font-semibold mb-2 text-center">{{ __('home.title.new_features') }}</h3><p class="text-center text-blue-50">{{ __('home.message.new_features_detail') }}</p></div></div>
									<div class="carousel-slide h-full"><div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-8 h-full flex flex-col justify-center items-center text-white"><h3 class="text-xl font-semibold mb-2 text-center">{{ __('home.title.premium_access') }}</h3><p class="text-center text-blue-50">{{ __('home.message.premium_access_detail') }}</p></div></div>
								@endif
							</div>
							<button onclick="previousSlide()" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur hover:bg-white rounded-full p-2 shadow-sm transition-all opacity-0 group-hover:opacity-100"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-800"><path d="m15 18-6-6 6-6"/></svg></button>
							<button onclick="nextSlide()" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur hover:bg-white rounded-full p-2 shadow-sm transition-all opacity-0 group-hover:opacity-100"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-800"><path d="m9 18 6-6-6-6"/></svg></button>
							<div class="flex justify-center gap-1.5 py-3">
								@php $hasSlides = !empty($homeSlides); @endphp
								@if($hasSlides)
									@foreach($homeSlides as $i => $_)
										<button onclick="goToSlide({{ $i }})" class="carousel-dot {{ $i===0 ? 'w-6 bg-blue-600' : 'w-1.5 bg-gray-200' }} h-1.5 rounded-full cursor-pointer"></button>
									@endforeach
								@else
									<button onclick="goToSlide(0)" class="carousel-dot w-6 h-1.5 rounded-full bg-blue-600 cursor-pointer"></button>
									<button onclick="goToSlide(1)" class="carousel-dot w-1.5 h-1.5 rounded-full bg-gray-200 cursor-pointer"></button>
									<button onclick="goToSlide(2)" class="carousel-dot w-1.5 h-1.5 rounded-full bg-gray-200 cursor-pointer"></button>
								@endif
							</div>
						</div>
					</div>
				</div>
			</main>

			@include('partials.footer', ['active' => 'home'])
		</div>
	</div>

	<script>
		let currentSlide = 0;
		const slides = document.querySelectorAll('.carousel-slide');
		const dots = document.querySelectorAll('.carousel-dot');
		function showSlide(n) {
			slides.forEach(slide => slide.classList.remove('active'));
			dots.forEach(dot => {
				dot.classList.remove('w-6', 'bg-blue-600');
				dot.classList.add('w-2', 'bg-gray-300');
			});
			currentSlide = (n + slides.length) % slides.length;
			slides[currentSlide].classList.add('active');
			dots[currentSlide].classList.remove('w-2', 'bg-gray-300');
			dots[currentSlide].classList.add('w-6', 'bg-blue-600');
		}
		function nextSlide() { showSlide(currentSlide + 1); }
		function previousSlide() { showSlide(currentSlide - 1); }
		function goToSlide(n) { showSlide(n); }
		setInterval(() => { nextSlide(); }, 5000);

		// Notifications count banner
		(function() {
			const el = document.getElementById('notif-message');
			if (!el) return;
			const template = "{{ __('home.message.new_notifications') }}";
			fetch('{{ route('ajax.notifications.unread_count') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
				.then(r => r.ok ? r.json() : Promise.resolve({ count: 0 }))
				.then(data => {
					const count = (data && typeof data.count === 'number') ? data.count : 0;
					el.textContent = template.replace('{number_notification}', count);
				})
				.catch(() => {
					el.textContent = template.replace('{number_notification}', 0);
				});
		})();
	</script>
</body>
</html>

