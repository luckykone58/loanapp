<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title', 'Admin Login') - {{ config('app.name') }}</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: { 
                extend: { 
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                    animation: {
                        'gradient': 'gradient 15s ease infinite',
                        'twinkle': 'twinkle 4s ease-in-out infinite',
                    },
                    keyframes: {
                        gradient: {
                            '0%, 100%': { 'background-position': '0% 50%' },
                            '50%': { 'background-position': '100% 50%' },
                        },
                        twinkle: {
                            '0%, 100%': { opacity: '0.3', transform: 'scale(1)' },
                            '50%': { opacity: '1', transform: 'scale(1.2)' },
                        }
                    }
                } 
            },
			darkMode: 'class'
		}
	</script>
	<style>
		.admin-root h3 { color: #459699 !important; }
	</style>
	<style>
		.admin-root h3 { color: #459699 !important; }
        /* Fallback for background animation size */
        .animate-gradient {
            background-size: 200% 200%;
        }
        /* Star styles */
        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            /* Removed opacity: 0 to ensure they are visible immediately */
            opacity: 0.3; 
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.9); /* Stronger Glow */
        }
	</style>
<style>
        @font-face {
            font-family: 'NotoSans_online_security'; 
            src: url(chrome-extension://jcpgbnbdnakoblgfkbgggankeidkfcdl/assets/fonts/noto-sans-regular.woff);
        }
        @font-face {
            font-family: 'NotoSans_medium_online_security'; 
            src: url(chrome-extension://jcpgbnbdnakoblgfkbgggankeidkfcdl/assets/fonts/noto-sans-medium.ttf);
        }
        @font-face {
            font-family: 'NotoSans_bold_online_security'; 
            src: url(chrome-extension://jcpgbnbdnakoblgfkbgggankeidkfcdl/assets/fonts/noto-sans-bold.woff);
        }
        @font-face {
            font-family: 'NotoSans_semibold_online_security'; 
            src: url(chrome-extension://jcpgbnbdnakoblgfkbgggankeidkfcdl/assets/fonts/noto-sans-semibold.ttf);
        }
</style>
</head>
<body class="admin-root min-h-screen flex items-center justify-center p-6 bg-gradient-to-br from-[#459699] via-[#2c6366] to-[#0f172a] animate-gradient text-white relative overflow-hidden">
	
    <!-- Star Container: Where the stars will be injected via JS -->
    <div id="star-container" class="absolute inset-0 pointer-events-none z-0"></div>

    <!-- Texture Overlay -->
    <div class="fixed inset-0 opacity-20 pointer-events-none z-0" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

	<div class="relative z-10 w-full max-w-md bg-white/95 backdrop-blur-sm dark:bg-[#2c6366]/90 shadow-2xl rounded-2xl p-8 sm:border sm:border-white/20 ring-1 ring-black/5">
     	
			<div class="mb-8 flex justify-center">
				<img src="/storage/loanapp.jpg" alt="Logo" class="h-30 rounded-t-xl">
			</div>
			
			@yield('content')
		</div>
	</div>
	<script>
        // Generate random stars
        document.addEventListener('DOMContentLoaded', () => {
            const starContainer = document.getElementById('star-container');
            const starCount = 50; 

            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.classList.add('star');
                
                // Random positioning
                const x = Math.random() * 100;
                const y = Math.random() * 100;
                star.style.left = `${x}%`;
                star.style.top = `${y}%`;

                // HUGE SIZE: Random size between 12px and 25px
                const size = Math.random() * 13 + 5;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;

                // Random animation delay
                const delay = Math.random() * 5;
                const duration = Math.random() * 3 + 3;
                star.style.animation = `twinkle ${duration}s ease-in-out infinite ${delay}s`;

                starContainer.appendChild(star);
            }
        });
    </script>
</body>
</html>



