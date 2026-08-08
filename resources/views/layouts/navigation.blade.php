{{-- Sidebar desktop --}}
<aside class="hidden lg:flex fixed inset-y-0 left-0 z-30 bg-gray-900 border-r border-gray-800/80 flex-col transition-all duration-300 overflow-hidden"
       :class="sidebarOpen ? 'w-64' : 'w-16'">

    {{-- Logo / Brand --}}
    <div class="flex items-center gap-3 px-4 h-16 border-b border-gray-800/80 flex-shrink-0 overflow-hidden">
        <img src="{{ asset('Logo.png') }}" alt="Merlo Transportes" class="h-8 w-auto flex-shrink-0 object-contain">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200 delay-100"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-100" x-transition:leave-end="opacity-0"
             class="min-w-0 flex-1 overflow-hidden">
            <p class="text-sm font-bold text-white truncate">Control Reportes</p>
            <p class="text-xs text-gray-500 truncate">Panel Administrativo</p>
        </div>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 py-4 px-2 space-y-0.5 overflow-y-auto overflow-x-hidden">

        {{-- Sección principal --}}
        <div x-show="sidebarOpen" class="px-2 mb-2">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-widest">Principal</p>
        </div>
        <div x-show="!sidebarOpen" class="border-t border-gray-800/60 my-2 mx-2"></div>

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group relative
                  {{ request()->routeIs('dashboard') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-red-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 7a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H4a1 1 0 01-1-1V7zm0 10a1 1 0 011-1h5a1 1 0 011 1v.5a1 1 0 01-1 1H4a1 1 0 01-1-1V17zm10-10a1 1 0 011-1h5a1 1 0 011 1v.5a1 1 0 01-1 1h-5a1 1 0 01-1-1V7zm0 7a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5z"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-75" x-transition:leave-end="opacity-0"
                  class="text-sm font-medium whitespace-nowrap">
                Dashboard
            </span>
            {{-- Tooltip cuando sidebar colapsado --}}
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 px-2 py-1 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                Dashboard
            </div>
        </a>

        {{-- Gastos (solo operador) --}}
        @role('operador')
        <a href="{{ route('gastos.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group relative
                  {{ request()->routeIs('gastos.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('gastos.*') ? 'text-red-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-75" x-transition:leave-end="opacity-0"
                  class="text-sm font-medium whitespace-nowrap">
                Gastos
            </span>
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 px-2 py-1 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                Gastos
            </div>
        </a>
        @endrole

        {{-- Separador sección gestión --}}
        <div class="pt-4 pb-1">
            <div x-show="sidebarOpen" class="px-2 mb-1">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-widest">Gestión</p>
            </div>
            <div x-show="!sidebarOpen" class="border-t border-gray-800/60 mx-2"></div>
        </div>

        @hasanyrole('administrador|administracion')
        {{-- Usuarios --}}
        <a href="{{ route('users.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group relative
                  {{ request()->routeIs('users.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('users.*') ? 'text-red-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-75" x-transition:leave-end="opacity-0"
                  class="text-sm font-medium whitespace-nowrap flex-1">
                Usuarios
            </span>
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 px-2 py-1 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                Usuarios
            </div>
        </a>

        {{-- Unidades --}}
        <a href="{{ route('buses.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group relative
                  {{ request()->routeIs('buses.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('buses.*') ? 'text-red-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-75" x-transition:leave-end="opacity-0"
                  class="text-sm font-medium whitespace-nowrap flex-1">
                Unidades
            </span>
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 px-2 py-1 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                Unidades
            </div>
        </a>
        @endhasanyrole

        {{-- Reportes --}}
        <a href="{{ route('reports.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group relative
                  {{ request()->routeIs('reports.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('reports.*') ? 'text-red-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-75" x-transition:leave-end="opacity-0"
                  class="text-sm font-medium whitespace-nowrap flex-1">
                Reportes
            </span>
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 px-2 py-1 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                Reportes
            </div>
        </a>

        @unlessrole('mecanico')
        {{-- Viajes --}}
        <a href="{{ route('viajes.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group relative
                  {{ request()->routeIs('viajes.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('viajes.*') ? 'text-red-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-75" x-transition:leave-end="opacity-0"
                  class="text-sm font-medium whitespace-nowrap flex-1">
                Viajes
            </span>
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 px-2 py-1 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                Viajes
            </div>
        </a>
        @endunlessrole

        @hasanyrole('administrador|administracion')
        {{-- Inventario --}}
        <a href="{{ route('inventario.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group relative
                  {{ request()->routeIs('inventario.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('inventario.*') ? 'text-red-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-75" x-transition:leave-end="opacity-0"
                  class="text-sm font-medium whitespace-nowrap flex-1">
                Inventario
            </span>
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 px-2 py-1 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                Inventario
            </div>
        </a>
        @endhasanyrole

        {{-- Separador configuración --}}
        <div class="pt-4 pb-1">
            <div x-show="sidebarOpen" class="px-2 mb-1">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-widest">Sistema</p>
            </div>
            <div x-show="!sidebarOpen" class="border-t border-gray-800/60 mx-2"></div>
        </div>

        {{-- Configuración --}}
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group relative
                  text-gray-400 hover:text-white hover:bg-gray-800/70">
            <svg class="w-5 h-5 flex-shrink-0 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="sidebarOpen"
                  x-transition:enter="transition-opacity duration-150 delay-75"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-75" x-transition:leave-end="opacity-0"
                  class="text-sm font-medium whitespace-nowrap">
                Configuración
            </span>
            <div x-show="!sidebarOpen"
                 class="absolute left-full ml-3 px-2 py-1 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                Configuración
            </div>
        </a>

    </nav>

    {{-- Footer del sidebar --}}
    <div class="px-4 py-3 border-t border-gray-800/80 flex-shrink-0">
        <div x-show="sidebarOpen" class="flex items-center gap-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse flex-shrink-0"></div>
            <p class="text-xs text-gray-600 truncate">Sistema activo · v1.0</p>
        </div>
        <div x-show="!sidebarOpen" class="flex justify-center">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
        </div>
    </div>
</aside>

{{-- Sidebar móvil --}}
<aside class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 border-r border-gray-800/80 flex flex-col lg:hidden transition-transform duration-300"
       x-show="mobileOpen"
       x-cloak
       x-transition:enter="transition-transform duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition-transform duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full">

    <div class="flex items-center gap-3 px-4 h-16 border-b border-gray-800/80 flex-shrink-0">
        <img src="{{ asset('Logo.png') }}" alt="Merlo Transportes" class="h-8 w-auto flex-shrink-0 object-contain">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-white truncate">Control Reportes</p>
            <p class="text-xs text-gray-500 truncate">Panel Administrativo</p>
        </div>
        <button @click="mobileOpen = false"
                class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-white hover:bg-gray-800 transition-all"
                aria-label="Cerrar menú">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 py-4 px-2 space-y-0.5 overflow-y-auto">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }} transition-all">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 7a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H4a1 1 0 01-1-1V7zm0 10a1 1 0 011-1h5a1 1 0 011 1v.5a1 1 0 01-1 1H4a1 1 0 01-1-1V17zm10-10a1 1 0 011-1h5a1 1 0 011 1v.5a1 1 0 01-1 1h-5a1 1 0 01-1-1V7zm0 7a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5z"/>
            </svg>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        @role('operador')
        <a href="{{ route('gastos.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                  {{ request()->routeIs('gastos.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-medium">Gastos</span>
        </a>
        @endrole
        @hasanyrole('administrador|administracion')
        <a href="{{ route('users.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                  {{ request()->routeIs('users.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="text-sm font-medium">Usuarios</span>
        </a>
        <a href="{{ route('buses.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                  {{ request()->routeIs('buses.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <span class="text-sm font-medium">Unidades</span>
        </a>
        @endhasanyrole
        <a href="{{ route('reports.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                  {{ request()->routeIs('reports.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-sm font-medium">Reportes</span>
        </a>
        @unlessrole('mecanico')
        <a href="{{ route('viajes.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                  {{ request()->routeIs('viajes.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span class="text-sm font-medium">Viajes</span>
        </a>
        @endunlessrole
        @hasanyrole('administrador|administracion')
        <a href="{{ route('inventario.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                  {{ request()->routeIs('inventario.*') ? 'bg-red-600/15 text-red-400 border border-red-700/30' : 'text-gray-400 hover:text-white hover:bg-gray-800/70' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span class="text-sm font-medium">Inventario</span>
        </a>
        @endhasanyrole
    </nav>
</aside>
