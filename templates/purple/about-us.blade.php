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
				<div class="relative z-10 p-4 space-y-6 top-[10px] pb-40">
				
					<!-- Page Title -->
					<div class="mb-2 text-center">
						<h1 class="text-2xl font-bold text-white mb-1">{{ __('about-us.title.page') }}</h1>
						<p class="text-brand-100 text-xs opacity-90">{{ __('about-us.text.tagline') }}</p>
					</div>

					<!-- Content Card -->
					<div class="bg-white rounded-3xl shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)] p-6 relative overflow-hidden">
						<div class="prose prose-sm max-w-none rich-content text-gray-700">
							{!! \App\Support\Settings::get('about_us', '') !!}
						</div>
					</div>

				</div>
			</main>

			@include('partials.footer', ['active' => 'about'])
		</div>
	</div>

</body>
</html>

