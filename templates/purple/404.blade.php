<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>404 Not Found</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
					colors: {
						'primary-teal': '#1D4ED8',
						'primary-dark': '#3B82F6',
						'bg-main': '#F9FAFB',
					}
				}
			}
		}
	</script>
	<style>
		body { font-family: 'Inter', ui-sans-serif, system-ui; background-color: #FAFAFA; }
	</style>
</head>
<body class="min-h-screen flex items-center justify-center p-0 sm:p-6">
	<div class="w-full min-h-screen bg-white p-8 sm:p-10 rounded-none shadow-none sm:min-h-0 sm:max-w-md sm:rounded-3xl sm:border sm:border-gray-200 sm:shadow-xl">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
            <p class="text-blue-600 text-base mb-2">You look a little lost</p>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-blue-900 mb-4">Oops! Page Not Found</h1>
            <p class="text-blue-900 text-lg sm:text-xl max-w-3xl mb-10">The link may be broken or the page may have moved.</p>
            <div class="w-full max-w-lg mx-auto mb-12">
                <img src="/storage/error.svg" alt="404 Not Found Illustration" class="mx-auto">
            </div>
        </div>
    </div>
</body>
</html>


