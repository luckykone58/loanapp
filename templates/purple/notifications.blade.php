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
</head>
<body class="bg-gray-50 text-gray-800">
	<div class="bg-gray-50">
		<div class="relative max-w-[500px] mx-auto min-h-screen flex flex-col bg-gray-50 shadow-2xl overflow-hidden">
			<div class="absolute top-0 left-0 right-0 h-64 bg-gradient-to-br from-blue-800 via-brand-700 to-brand-500 rounded-b-[2.5rem] z-0"></div>
			@include('partials.header')

			<main class="flex-1 pb-0 relative pb-8 px-6">
				<div class="relative z-10 bg-white rounded-3xl shadow-lg p-4 space-y-6 top-[10px] pb-40 min-h-[500px]">
				
					<div class="bg-white rich-content">
						<!-- Page Title & Actions -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
								<h1 class="text-2xl font-bold text-brand-600">{{ __('notifications.title.page') }}</h1>
                                <!-- List Count Badge -->
                                <span id="list-count-badge" class="bg-brand-100 text-brand-700 text-xs font-bold px-2.5 py-0.5 rounded-full hidden">0</span>
                            </div>
                            <button onclick="markAllAsRead()" class="text-sm text-brand-600 hover:text-brand-700 font-medium transition-colors">
                                {{ __('notifications.button.mark_all') }}
                            </button>
                        </div>

                        <!-- Notification List Container -->
                        <div id="notification-list" class="space-y-3 pb-10">
                            <!-- Items injected by JS -->
                        </div>
					</div>


				</div>
			</main>

			@include('partials.footer', ['active' => 'about'])
		</div>
	</div>
    <!-- JAVASCRIPT LOGIC -->
    <script>
		// State
		let notifications = [];
		const i18n = {
			empty: "{{ __('notifications.message.empty') }}"
		};

		// Theme Configuration
		const themeConfig = {
			blue:   { bg: 'bg-blue-50',   text: 'text-blue-600',   border: 'border-blue-100',  dot: 'bg-blue-500' },
			orange: { bg: 'bg-orange-50', text: 'text-orange-600', border: 'border-orange-100', dot: 'bg-orange-500' },
			gray:   { bg: 'bg-gray-100',  text: 'text-gray-600',   border: 'border-gray-200',  dot: 'bg-gray-500' },
			red:    { bg: 'bg-red-50',    text: 'text-red-600',    border: 'border-red-100',   dot: 'bg-red-500' },
			green:  { bg: 'bg-green-50',  text: 'text-green-600',  border: 'border-green-100', dot: 'bg-green-500' }
		};
		const typeMap = {
			loan:      { colorTheme: 'blue',   iconPath: '<rect width="20" height="12" x="2" y="6" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path>' },
			withdrawal:{ colorTheme: 'orange', iconPath: '<path d="M12 17V3"></path><path d="m6 11 6 6 6-6"></path><path d="M19 21H5"></path>' },
			payment:   { colorTheme: 'red',    iconPath: '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>' },
			welcome:   { colorTheme: 'blue',   iconPath: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>' },
			account:   { colorTheme: 'gray',   iconPath: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>' },
			security:  { colorTheme: 'gray',   iconPath: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>' },
		};

		function csrfToken() {
			const tag = document.querySelector('meta[name="csrf-token"]');
			return tag ? tag.getAttribute('content') : '';
		}

		async function loadNotifications() {
			try {
				const res = await fetch('{{ route('ajax.notifications') }}', {
					headers: { 'X-Requested-With': 'XMLHttpRequest' }
				});
				if (!res.ok) throw new Error('Failed to load notifications');
				const json = await res.json();
				const items = Array.isArray(json.data) ? json.data : [];
				notifications = items.map(item => {
					const t = (item.type || 'account').toLowerCase();
					const map = typeMap[t] || typeMap.account;
					// Dynamic color overrides for loan/withdrawal based on title keywords
					let colorTheme = map.colorTheme;
					const titleStr = (item.title || '').toString();
					if (t === 'loan' || t === 'withdrawal') {
						if (/(approved|accepted)/i.test(titleStr)) {
							colorTheme = 'green';
						} else if (/(denied|rejected)/i.test(titleStr)) {
							colorTheme = 'red';
						}
					}
					return {
						id: item.id,
						type: t,
						title: item.title || '',
						message: item.message || '',
						subtext: item.subtext || '',
						note: item.notes || '',
						date: item.created_human || '',
						isRead: !!item.isRead,
						colorTheme,
						iconPath: map.iconPath
					};
				});
				renderNotifications();
			} catch (e) {
				console.error(e);
				notifications = [];
				renderNotifications();
			}
		}

		function renderNotifications() {
			const container = document.getElementById('notification-list');
			const listBadge = document.getElementById('list-count-badge');
			const headerBadge = document.getElementById('header-badge');

			container.innerHTML = '';

			const unreadCount = notifications.filter(n => !n.isRead).length;

			if (unreadCount > 0) {
				listBadge.textContent = unreadCount;
				listBadge.classList.remove('hidden');
				if (headerBadge) headerBadge.classList.remove('hidden');
			} else {
				listBadge.classList.add('hidden');
				if (headerBadge) headerBadge.classList.add('hidden');
			}

			if (notifications.length === 0) {
				container.innerHTML = `<p class="text-gray-500 text-center py-8">${i18n.empty}</p>`;
				return;
			}

			notifications.forEach(item => {
				const theme = themeConfig[item.colorTheme] || themeConfig.gray;

				const wrapperClasses = item.isRead
					? 'opacity-70 bg-gray-50 hover:bg-white'
					: 'bg-white shadow-sm hover:shadow-md border-gray-100';

				const titleWeight = item.isRead ? 'font-medium' : 'font-bold';

				const dotHtml = !item.isRead
					? `<div class="absolute top-4 right-4 h-2.5 w-2.5 rounded-full ${theme.dot} ring-2 ring-white"></div>`
					: '';

				const noteHtml = item.note
					? `<div class="mt-2 text-xs ${theme.bg} ${theme.text} px-2 py-1 rounded w-fit font-medium border ${theme.border}">${item.note}</div>`
					: '';

				const html = `
					<div onclick="markAsRead(${item.id})" class="notification-item group flex gap-4 p-4 rounded-xl border transition-all relative cursor-pointer overflow-hidden ${wrapperClasses}">
						${dotHtml}
						<div class="flex-shrink-0">
							<div class="h-12 w-12 rounded-full ${theme.bg} ${theme.text} flex items-center justify-center border ${theme.border}">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									${item.iconPath}
								</svg>
							</div>
						</div>
						<div class="flex-1 pr-4">
							<div class="flex justify-between items-start mb-1">
								<p class="text-xs font-bold uppercase tracking-wider ${theme.text}">${item.title}</p>
								<span class="text-xs text-gray-400 whitespace-nowrap ml-2">${item.date}</span>
							</div>
							<h3 class="text-gray-900 text-sm mb-1 ${titleWeight}">${item.message}</h3>
							<p class="text-sm text-gray-500 leading-relaxed">${item.subtext}</p>
							${noteHtml}
						</div>
					</div>
				`;
				container.innerHTML += html;
			});
		}

		async function markAsRead(id) {
			const index = notifications.findIndex(n => n.id === id);
			if (index === -1 || notifications[index].isRead) return;
			try {
				await fetch(`{{ url('ajax/notifications') }}/${id}/read`, {
					method: 'POST',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					}
				});
			} catch (e) {
				console.warn('Failed to mark read', e);
			}
			notifications[index].isRead = true;
			renderNotifications();
		}

		async function markAllAsRead() {
			try {
				await fetch(`{{ route('ajax.notifications.read_all') }}`, {
					method: 'POST',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					}
				});
			} catch (e) {
				console.warn('Failed to mark all read', e);
			}
			notifications = notifications.map(n => ({ ...n, isRead: true }));
			renderNotifications();
		}

		document.addEventListener('DOMContentLoaded', loadNotifications);
    </script>    
</body>
</html>

