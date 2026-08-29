<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar contraseña — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen flex bg-gradient-to-br from-gray-950 via-gray-900 to-gray-800">

    {{-- Panel izquierdo decorativo --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-br from-red-950 via-red-900 to-gray-900"></div>

        {{-- Círculos decorativos --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-700 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-red-600 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-red-800 rounded-full opacity-10 blur-2xl"></div>

        {{-- Líneas decorativas --}}
        <div class="brand-gradient-pattern absolute inset-0 opacity-5"></div>

        {{-- Contenido central --}}
        <div class="relative z-10 text-center px-14">
            <div class="w-20 h-20 bg-white/10 border border-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm shadow-2xl mx-auto mb-8">
                <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="w-12 h-0.5 bg-gradient-to-r from-transparent via-red-400 to-transparent mx-auto mb-6"></div>
            <h1 class="text-4xl font-bold text-white tracking-tight mb-4">
                Control de<br>
                <span class="text-red-400">Reportes</span>
            </h1>
            <p class="text-gray-400 text-base leading-relaxed">
                Gestión centralizada de reportes<br>y seguimiento de empleados
            </p>
            <div class="w-12 h-0.5 bg-gradient-to-r from-transparent via-red-400 to-transparent mx-auto mt-6"></div>

            {{-- Stats decorativas --}}
            <div class="mt-12 grid grid-cols-3 gap-4">
                <div class="bg-white/5 border border-white/10 rounded-xl p-3 backdrop-blur-sm">
                    <p class="text-2xl font-bold text-white">—</p>
                    <p class="text-xs text-gray-500 mt-1">Reportes</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-xl p-3 backdrop-blur-sm">
                    <p class="text-2xl font-bold text-white">—</p>
                    <p class="text-xs text-gray-500 mt-1">Empleados</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-xl p-3 backdrop-blur-sm">
                    <p class="text-2xl font-bold text-white">—</p>
                    <p class="text-xs text-gray-500 mt-1">Usuarios</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Header del formulario --}}
            <div class="mb-10 text-center">
                <img src="{{ asset('Logo.png') }}" alt="Merlo Transportes" class="h-24 w-auto mx-auto object-contain drop-shadow-xl mb-6">
                <h2 class="text-3xl font-bold text-white">Recuperar contraseña</h2>
                <p class="text-gray-500 mt-2 text-sm">Ingresa tu correo y te enviaremos un código de seguridad válido durante 15 minutos</p>
            </div>

            {{-- Tarjeta del formulario --}}
            <div class="auth-card-shadow bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-2xl p-8 shadow-2xl">

                {{-- Línea roja superior --}}
                <div class="h-0.5 bg-gradient-to-r from-red-700 via-red-500 to-transparent rounded-full -mt-8 mb-8 -mx-8 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-red-300/30 to-transparent animate-pulse"></div>
                </div>

                @if (session('status'))
                    <div class="mb-6 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Correo electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="usuario@ejemplo.com"
                                class="w-full pl-10 pr-4 py-3 bg-gray-900/80 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white placeholder-gray-600 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                            />
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Botón --}}
                    <button
                        type="submit"
                        class="btn-login-gradient w-full py-3 px-4 text-white font-semibold rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-800 active:scale-95 shadow-lg shadow-red-950/50"
                    >
                        Enviar código
                    </button>

                    <a href="{{ route('login') }}"
                       class="block text-center text-sm text-gray-500 hover:text-gray-300 transition-colors">
                        &larr; Volver a iniciar sesión
                    </a>
                </form>
            </div>

            {{-- Footer --}}
            <p class="mt-8 text-center text-xs text-gray-700">
                {{ config('app.name') }} &copy; {{ date('Y') }} — Todos los derechos reservados
            </p>
        </div>
    </div>
</div>

@if (session('code_sent'))
    <div class="fixed inset-0 z-10 flex items-center justify-center bg-gray-950/70 backdrop-blur-sm px-4" role="dialog" aria-modal="true" aria-labelledby="code-title">
        <div class="w-full max-w-md auth-card-shadow bg-gray-800/95 border border-gray-700/50 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
            <div class="h-0.5 bg-gradient-to-r from-red-700 via-red-500 to-transparent rounded-full -mt-8 mb-8 -mx-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-red-300/30 to-transparent animate-pulse"></div>
            </div>

            <h2 id="code-title" class="text-lg font-semibold text-white">Verifica tu código</h2>
            <p class="mt-2 text-sm text-gray-400">Revisa tu correo e ingresa el código de 6 dígitos. Solo puede utilizarse una vez y vence en 15 minutos.</p>

            <form method="POST" action="{{ route('password.code.verify') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="email" value="{{ session('reset_email') }}">

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-300 mb-1.5">
                        Código de seguridad
                    </label>
                    <input
                        id="code"
                        type="text"
                        name="code"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        maxlength="6"
                        required
                        autofocus
                        placeholder="000000"
                        class="w-full py-3 px-4 bg-gray-900/80 border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white placeholder-gray-600 text-center text-lg tracking-[0.5em] focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                    />
                    @error('code')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="btn-login-gradient w-full py-3 px-4 text-white font-semibold rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-800 active:scale-95 shadow-lg shadow-red-950/50"
                >
                    Validar código
                </button>
            </form>
        </div>
    </div>
@endif

</body>
</html>
