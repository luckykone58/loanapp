<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $currentDomain->name ?? config('app.name') }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="/css/main.css">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<style>
		* { box-sizing: border-box; }
		body {
			margin: 0;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}
	</style>
	@php
		$user = \Illuminate\Support\Facades\Auth::user();
		$currency = (string) \App\Support\Settings::get('currency_symbol', '$');
		$items = \App\Models\Withdrawal::where('user_id', optional($user)->id)->orderByDesc('id')->limit(100)->get();
	@endphp
</head>
<body class="bg-gray-50 text-gray-800">
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')

			<main class="flex-1 pb-0 relative pb-8 px-6">
				<div class="relative z-10 bg-white rounded-3xl shadow-lg p-4 space-y-6 top-[30px] pb-40 min-h-[500px]">
				
					<div class="bg-white">
						<div class="flex items-center justify-between mb-6">
							<div class="flex items-center gap-3">
								<h1 class="text-2xl font-bold text-blue-500">{{ __('withdrawal.title.page') }}</h1>
								<span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $items->count() }}</span>
							</div>
						</div>

						@if($items->isEmpty())
							<p class="text-gray-500 text-center py-8">{{ __('withdrawal.message.empty') }}</p>
						@else
							<div class="space-y-3 pb-10">
								@foreach($items as $w)
									@php
										$status = strtolower((string)$w->status);
										$theme = match($status) {
											'fulfilled' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'border' => 'border-green-100', 'dot' => 'bg-green-500'],
                                            'approved' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'border' => 'border-green-100', 'dot' => 'bg-green-500'],
											'rejected' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'border' => 'border-red-100', 'dot' => 'bg-red-500'],
                                            'processing' => ['bg' => 'bg-yellow-50', 'text' => 'text-grey-600', 'border' => 'border-yellow-100', 'dot' => 'bg-yellow-500'],
											default => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-100', 'dot' => 'bg-blue-500'],
										};
									@endphp
									<div class="group flex gap-4 p-4 rounded-xl border transition-all relative overflow-hidden bg-white shadow-sm hover:shadow-md border-gray-100">
										<div class="flex-shrink-0">
											<div class="h-12 w-12 rounded-full {{ $theme['bg'] }} {{ $theme['text'] }} flex items-center justify-center border {{ $theme['border'] }}">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<path d="M12 17V3"></path><path d="m6 11 6 6 6-6"></path><path d="M19 21H5"></path>
												</svg>
											</div>
										</div>
										<div class="flex-1 pr-4">
											<div class="flex justify-between items-start mb-1">
												<p class="text-xs font-bold uppercase tracking-wider {{ $theme['text'] }}">{{ __('withdrawal.label.withdrawal') }} • {{ ucfirst($status) }}</p>
												<span class="text-xs text-gray-400 whitespace-nowrap ml-2">{{ optional($w->created_at)->diffForHumans() }}</span>
											</div>
											<h3 class="text-gray-900 text-sm mb-1 font-bold">{{ $currency }} {{ number_format((float)$w->amount, 2) }}</h3>
											
										</div>
									</div>
								@endforeach
							</div>
						@endif
					</div>

				</div>
			</main>

			@include('partials.footer', ['active' => 'about'])
		</div>
	</div>
</body>
</html>


