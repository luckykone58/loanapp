<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ __('profile.title.signature') }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="/css/main.css">
</head>
<body class="bg-gray-50 text-gray-800">
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')
			<main class="flex-1 pb-0 relative pb-8 px-6">
				<div class="relative z-10 bg-white rounded-3xl shadow-lg p-4 space-y-6 top-[30px] pb-40">
					@php($viewOnly = in_array(request()->query('view'), [1, '1', true, 'true'], true))
					<h1 class="text-xl font-bold text-blue-500 mb-2">
						<span class="inline-flex items-center gap-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600">
								<path d="M12 20h9"/>
								<path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
							</svg>
							{{ __('profile.title.signature') }}
						</span>
					</h1>
					@if($viewOnly)
						@if(!empty($info?->signature))
							<div class="mt-1 border rounded-lg p-3 bg-slate-50">
								<img src="{{ asset('storage/'.ltrim($info->signature, '/')) }}" alt="Signature" class="w-full max-h-64 object-contain">
							</div>
						@else
							<div class="text-xs text-slate-500">—</div>
						@endif
					@elseif(session('success'))
						<div class="text-sm text-green-700 bg-green-100 rounded px-3 py-2">{{ session('success') }}</div>
						<a href="{{ route('profile') }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/>
                                <path d="m12 5 7 7-7 7"/>
                            </svg>
                            {{ __('profile.button.continue_profile') }}
                        </a>
						<a href="{{ route('loan') }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="5" width="18" height="14" rx="2"/>
								<path d="M3 10h18"/>
								<path d="M7 15h4"/>
							</svg>
							{{ __('home.button.apply_loan') }}
						</a>
						@if(!empty($info?->signature))
							<div class="mt-3 border rounded-lg p-3 bg-slate-50">
								<img src="{{ asset('storage/'.ltrim($info->signature, '/')) }}" alt="Signature" class="w-full max-h-64 object-contain">
							</div>
						@endif
					@else
					<form id="sig-form" method="POST" action="{{ route('profile.signature.update') }}" class="space-y-4">
						@csrf
						<div>
							<label class="block mb-2 text-sm">{{ __('profile.label.upload_signature') }}</label>
							<div class="border rounded-lg bg-slate-50 p-3">
								<canvas id="sig-canvas" class="w-full bg-white rounded border"></canvas>
								<div class="mt-3 flex items-center gap-3">
									<button type="button" id="sig-clear" class="px-3 py-1.5 text-sm border rounded hover:bg-slate-100">Clear</button>
									<button type="button" id="sig-undo" class="px-3 py-1.5 text-sm border rounded hover:bg-slate-100">Undo</button>
								</div>
							</div>
							<input type="hidden" name="signature_data" id="signature_data" value="">
						</div>
						<button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
								<polyline points="17 21 17 13 7 13 7 21"/>
								<polyline points="7 3 7 8 15 8"/>
							</svg>
							{{ __('profile.button.save') }}
						</button>
					</form>
					@endif
				</div>
			</main>
			@include('partials.footer', ['active' => 'profile'])
		</div>
	</div>

	<script>
	(function() {
		const canvas = document.getElementById('sig-canvas');
		const ctx = canvas.getContext('2d');
		const clearBtn = document.getElementById('sig-clear');
		const undoBtn = document.getElementById('sig-undo');
		const form = document.getElementById('sig-form');
		const out = document.getElementById('signature_data');
		if (!canvas || !ctx || !form || !out) return;

		const strokes = [];
		let drawing = false;
		let current = [];
		let lastW = 0, lastH = 0;

		function getPos(e){
			const rect = canvas.getBoundingClientRect();
			const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
			const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
			return { x: Math.max(0, Math.min(canvas.width, x)), y: Math.max(0, Math.min(canvas.height, y)) };
		}
		function redraw(){
			ctx.fillStyle = '#ffffff';
			ctx.fillRect(0,0,canvas.width, canvas.height);
			ctx.lineJoin = 'round';
			ctx.lineCap = 'round';
			ctx.lineWidth = 2;
			ctx.strokeStyle = '#111827';
			strokes.forEach(path => {
				ctx.beginPath();
				for (let i=0;i<path.length;i++){
					const p = path[i];
					if (i===0) ctx.moveTo(p.x, p.y); else ctx.lineTo(p.x, p.y);
				}
				ctx.stroke();
			});
			if (current.length){
				ctx.beginPath();
				for (let i=0;i<current.length;i++){
					const p = current[i];
					if (i===0) ctx.moveTo(p.x, p.y); else ctx.lineTo(p.x, p.y);
				}
				ctx.stroke();
			}
		}

		function resizeCanvasToContainer() {
			const wrap = canvas.parentElement;
			if (!wrap) return;
			const newW = Math.max(300, Math.floor(wrap.clientWidth));
			const newH = Math.max(180, Math.floor(newW * 0.4));
			if (canvas.width === newW && canvas.height === newH) return;
			// scale existing points to new size
			const oldW = canvas.width || newW;
			const oldH = canvas.height || newH;
			const sx = oldW ? (newW / oldW) : 1;
			const sy = oldH ? (newH / oldH) : 1;
			for (let i = 0; i < strokes.length; i++) {
				for (let j = 0; j < strokes[i].length; j++) {
					strokes[i][j] = { x: strokes[i][j].x * sx, y: strokes[i][j].y * sy };
				}
			}
			for (let k = 0; k < current.length; k++) {
				current[k] = { x: current[k].x * sx, y: current[k].y * sy };
			}
			canvas.width = newW;
			canvas.height = newH;
			lastW = newW; lastH = newH;
			redraw();
		}
		function start(e){ drawing = true; current = []; const p = getPos(e); current.push(p); redraw(); }
		function move(e){ if (!drawing) return; const p = getPos(e); current.push(p); redraw(); }
		function end(){ if (!drawing) return; drawing = false; if (current.length>0) strokes.push(current); current = []; redraw(); }

		canvas.addEventListener('mousedown', start);
		canvas.addEventListener('mousemove', move);
		window.addEventListener('mouseup', end);
		canvas.addEventListener('touchstart', function(e){ e.preventDefault(); start(e); });
		canvas.addEventListener('touchmove', function(e){ e.preventDefault(); move(e); });
		canvas.addEventListener('touchend', function(e){ e.preventDefault(); end(e); });

		clearBtn.addEventListener('click', function(){ strokes.length = 0; current = []; redraw(); });
		undoBtn.addEventListener('click', function(){ strokes.pop(); redraw(); });

		form.addEventListener('submit', function(e){
			// If nothing drawn, block submit
			if (strokes.length === 0) {
				e.preventDefault();
				alert('Please sign before submitting.');
				return;
			}
			// Render to white background and capture JPEG
			redraw();
			out.value = canvas.toDataURL('image/jpeg', 0.9);
		});

		// Initialize size and background, and listen to resize
		resizeCanvasToContainer();
		window.addEventListener('resize', function(){ resizeCanvasToContainer(); });
	})();
	</script>
</body>
</html>

