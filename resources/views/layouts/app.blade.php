<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $notificaciones = auth()->check()
        ? auth()->user()->unreadNotifications()->latest()->take(15)->get()->map(fn($n) => [
            'id'         => $n->id,
            'title'      => $n->data['title'] ?? 'Notificación',
            'message'    => $n->data['message'] ?? '',
            'url'        => $n->data['url'] ?? route('dashboard'),
            'created_at' => $n->created_at->diffForHumans(),
        ])
        : collect();
@endphp
<body class="font-sans antialiased bg-gray-950 text-white">

<div class="flex min-h-screen" x-data="{ sidebarOpen: true, mobileOpen: false }">

    {{-- Overlay móvil --}}
    <div x-show="mobileOpen"
         x-cloak
         @click="mobileOpen = false"
         class="fixed inset-0 z-20 bg-black/60 lg:hidden">
    </div>

    {{-- Sidebar --}}
    @include('layouts.navigation')

    {{-- Contenido principal --}}
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300"
         :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'">

        {{-- Top bar --}}
        <header class="sticky top-0 z-10 bg-gray-900/90 backdrop-blur-sm border-b border-gray-800/80 px-4 lg:px-6 h-16 flex items-center justify-between">

            <div class="flex items-center gap-3">
                {{-- Toggle sidebar (desktop) --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-white hover:bg-gray-800 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                {{-- Toggle sidebar (móvil) --}}
                <button @click="mobileOpen = !mobileOpen"
                        class="flex lg:hidden items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-white hover:bg-gray-800 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                @isset($header)
                    <div class="text-sm font-medium text-gray-400 truncate max-w-[50vw] sm:max-w-none">{{ $header }}</div>
                @endisset
            </div>

            {{-- Usuario --}}
            <div class="flex items-center gap-3" x-data="{ userMenu: false }">
                {{-- Notificaciones --}}
                @auth
                <div class="relative"
                     x-data="{
                        open: false,
                        notifications: {{ Js::from($notificaciones ?? []) }},
                        init() {
                            if (!window.Echo) return;
                            window.Echo.private('App.Models.User.{{ auth()->id() }}')
                                .notification((notification) => {
                                    this.notifications.unshift({
                                        id: notification.id,
                                        title: notification.title,
                                        message: notification.message,
                                        url: notification.url,
                                        created_at: 'Justo ahora',
                                    });
                                });
                        }
                     }">
                    <button @click="open = !open"
                            class="relative w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-white hover:bg-gray-800 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="notifications.length > 0" x-cloak class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 mt-2 w-72 sm:w-80 bg-gray-800 border border-gray-700/50 rounded-xl shadow-2xl overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-700/50 flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Notificaciones</p>
                            <form method="POST" action="{{ route('notificaciones.leer-todas') }}" x-show="notifications.length > 0" @submit="notifications = []">
                                @csrf
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">Marcar todas como leídas</button>
                            </form>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-700/40">
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="'{{ url('notificaciones') }}/' + n.id + '/ir'"
                                   class="block px-4 py-3 hover:bg-gray-700/40 transition-colors">
                                    <p class="text-sm text-white" x-text="n.title"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="n.message"></p>
                                    <p class="text-xs text-gray-600 mt-0.5" x-text="n.created_at"></p>
                                </a>
                            </template>
                            <p x-show="notifications.length === 0" class="px-4 py-6 text-center text-xs text-gray-600">Sin notificaciones nuevas.</p>
                        </div>
                    </div>
                </div>
                @endauth

                {{-- Avatar + menú --}}
                <div class="relative">
                    <button @click="userMenu = !userMenu"
                            class="flex items-center gap-2.5 px-2 py-1.5 rounded-xl hover:bg-gray-800 transition-all">
                        <div class="brand-gradient w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-red-950/50">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-semibold text-white leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 leading-none mt-0.5">{{ Auth::user()->role_label }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="userMenu"
                         x-cloak
                         @click.outside="userMenu = false"
                         class="absolute right-0 mt-2 w-48 bg-gray-800 border border-gray-700/50 rounded-xl shadow-2xl overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-700/50">
                            <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-gray-700/50 transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mi perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-300 hover:text-red-400 hover:bg-gray-700/50 transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Contenido de la página --}}
        <main class="flex-1 p-4 lg:p-6 bg-gray-950">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-800/60 px-6 py-3 bg-gray-900/50">
            <p class="text-xs text-gray-700 text-center">
                {{ config('app.name') }} &copy; {{ date('Y') }} — Panel Administrativo
            </p>
        </footer>

        @stack('scripts')
    </div>
</div>

</body>
</html>
