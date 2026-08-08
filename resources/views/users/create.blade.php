<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('users.index') }}" class="hover:text-gray-300 transition-colors">Usuarios</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Nuevo usuario</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('users.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Nuevo usuario</h1>
            <p class="text-sm text-gray-500 mt-0.5">Completa los datos para registrar la cuenta</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">

                {{-- Sección: Información personal --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        Información personal
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Nombre --}}
                        <div>
                            <label for="name" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Nombre(s) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Ej: Juan Carlos"/>
                            @error('name')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Apellido --}}
                        <div>
                            <label for="last_name" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Apellido(s) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('last_name') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Ej: Pérez García"/>
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Username --}}
                        <div>
                            <label for="username" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Usuario <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('username') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Ej: jperez"/>
                            @error('username')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Carnet --}}
                        <div>
                            <label for="carnet" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Carnet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="carnet" name="carnet" value="{{ old('carnet') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('carnet') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors font-mono"
                                   placeholder="Ej: EMP-2024-001"/>
                            @error('carnet')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Correo electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="correo@ejemplo.com"/>
                            @error('email')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Rol --}}
                        <div>
                            <label for="role" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Rol <span class="text-red-500">*</span>
                            </label>
                            <select id="role" name="role"
                                    class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('role') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                <option value="" class="bg-gray-900">— Seleccionar rol —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" class="bg-gray-900"
                                            {{ old('role') === $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div class="flex items-center justify-between px-4 py-3 bg-gray-900/40 border border-gray-700/50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-white">Usuario activo</p>
                                <p class="text-xs text-gray-500">Permite iniciar sesión en el sistema</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                       class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-gray-700 rounded-full peer
                                            peer-checked:bg-red-600
                                            after:content-[''] after:absolute after:top-[3px] after:left-[3px]
                                            after:bg-white after:rounded-full after:h-[18px] after:w-[18px]
                                            after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Sección: Foto --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        Foto de perfil
                        <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                    </h2>

                    <div x-data="{ preview: null }" class="flex items-start gap-5">
                        {{-- Preview --}}
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden flex items-center justify-center bg-gray-900/60">
                                <img x-show="preview" :src="preview" class="w-full h-full object-cover" alt="Preview"/>
                                <svg x-show="!preview" class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        {{-- Input --}}
                        <div class="flex-1">
                            <label for="photo"
                                   class="flex flex-col items-center justify-center w-full h-24 border border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-gray-600 transition-all">
                                <svg class="w-6 h-6 text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span class="text-xs text-gray-500">Clic para subir imagen</span>
                                <span class="text-xs text-gray-700 mt-0.5">PNG, JPG hasta 2MB</span>
                            </label>
                            <input type="file" id="photo" name="photo" accept="image/*" class="hidden"
                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"/>
                            @error('photo')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Sección: Contraseña --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center text-white flex-shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        Contraseña
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Mínimo 8 caracteres"/>
                            @error('password')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Confirmar contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Repite la contraseña"/>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}"
                       class="w-full sm:w-auto text-center px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Registrar usuario
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>
