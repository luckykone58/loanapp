<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $currentDomain->name ?? config('app.name') }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="/css/main.css">
	<script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 15px rgba(124, 58, 237, 0.3)',
                    }
                }
            }
        }
    </script>
	<style>
		* { box-sizing: border-box; }
		body {
			margin: 0;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}
	</style>
</head>
<body class="bg-gray-50 text-gray-800">
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-72 bg-gradient-to-br from-blue-800 via-brand-700 to-brand-500 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')

			<main class="flex-1 pb-0 relative pb-8 px-2">
				<div class="relative z-10  p-4 space-y-6 top-[10px] pb-40">
				
					<!-- Page Title -->
					<div class="mb-2 text-center">
						<h1 class="text-2xl font-bold text-white mb-1">{{ __('contact-us.title.page') }}</h1>
						<p class="text-brand-100 text-xs opacity-90">{{ __('contact-us.text.tagline') }}</p>
					</div>

					<!-- Content Card -->
					<div class="bg-white rounded-3xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] p-6 relative overflow-hidden">
						<div class="prose prose-sm max-w-none rich-content text-gray-700">
							{!! \App\Support\Settings::get('contact_details', '') !!}
						</div>
					</div>

					<!-- FAQ -->
					@php($faqs = \App\Support\Settings::getJson('faqs_json', []))
					@if(!empty($faqs))
					<h3 class="mt-2 text-brand-600">{{ __('contact-us.title.faqs') }}</h3>
					<div class="space-y-3">
						@foreach($faqs as $i => $item)
							@php($qid = 'faq-answer-'.$i)
							@php($isOpen = $i === 0)
							<div class="border border-gray-200 rounded-lg">
								<button type="button" class="faq-toggle w-full px-4 py-3 flex items-center justify-between text-left font-semibold" aria-expanded="{{ $isOpen ? 'true' : 'false' }}" aria-controls="{{ $qid }}" data-target="{{ $qid }}">
									<span class="text-gray-600">{{ $item['q'] ?? '' }}</span>
									<svg class="h-4 w-4 transition-transform duration-200 {{ $isOpen ? 'rotate-180' : 'rotate-0' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
										<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.188l3.71-3.958a.75.75 0 111.08 1.04l-4.24 4.53a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
									</svg>
								</button>
								<div id="{{ $qid }}" class="px-4 pb-4 text-gray-700 {{ $isOpen ? '' : 'hidden' }}">
									{!! nl2br(e($item['a'] ?? '')) !!}
								</div>
							</div>
						@endforeach
					</div>
					@endif
					
				</div>
			</main>

			@include('partials.footer', ['active' => 'contact'])
		</div>
	</div>

	<script>
		(function() {
			var toggles = document.querySelectorAll('.faq-toggle');
			for (var i = 0; i < toggles.length; i++) {
				toggles[i].addEventListener('click', function() {
					var targetId = this.getAttribute('data-target');
					var panel = document.getElementById(targetId);
					if (!panel) return;
					var isHidden = panel.classList.contains('hidden');
					if (isHidden) {
						panel.classList.remove('hidden');
						this.setAttribute('aria-expanded', 'true');
					} else {
						panel.classList.add('hidden');
						this.setAttribute('aria-expanded', 'false');
					}
					// rotate chevron
					var icon = this.querySelector('svg');
					if (icon) {
						icon.classList.toggle('rotate-180', isHidden);
					}
				});
			}
		})();
	</script>
</body>
</html>

