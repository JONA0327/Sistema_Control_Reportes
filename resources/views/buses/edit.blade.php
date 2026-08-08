<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('buses.index') }}" class="hover:text-gray-300 transition-colors">Unidades</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Editar</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('buses.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex items-center gap-3">
            @if($bus->foto)
                <img src="{{ Storage::url($bus->foto) }}"
                     class="w-12 h-10 rounded-xl object-cover border border-gray-700"/>
            @else
                <div class="w-12 h-10 rounded-xl bg-gray-700/60 border border-gray-600/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            @endif
            <div>
                <h1 class="text-xl font-bold text-white">Bus #{{ $bus->num_bus }}</h1>
                <p class="text-xs text-gray-500 font-mono tracking-wider">{{ $bus->placa }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('buses.update', $bus) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">

                {{-- Datos de la unidad --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </span>
                        Datos de la unidad
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label for="num_bus" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Número de unidad <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="num_bus" name="num_bus" value="{{ old('num_bus', $bus->num_bus) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('num_bus') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   min="1"/>
                            @error('num_bus')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="placa" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Placa <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="placa" name="placa" value="{{ old('placa', $bus->placa) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('placa') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors font-mono uppercase tracking-wider"
                                   maxlength="20" oninput="this.value = this.value.toUpperCase()"/>
                            @error('placa')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach(['activo' => ['text-green-400', 'border-green-500/30 bg-green-500/5', 'border-green-500 bg-green-500/10'], 'inactivo' => ['text-gray-400', 'border-gray-600/40 bg-gray-800/40', 'border-gray-500 bg-gray-700/40'], 'mantenimiento' => ['text-amber-400', 'border-amber-500/30 bg-amber-500/5', 'border-amber-500 bg-amber-500/10']] as $val => $cls)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="status" value="{{ $val }}" class="sr-only peer"
                                           {{ old('status', $bus->status) === $val ? 'checked' : '' }}>
                                    <div class="flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border {{ $cls[1] }} peer-checked:{{ $cls[2] }} peer-checked:border-2 transition-all text-center">
                                        <span class="text-xs font-medium {{ $cls[0] }}">{{ ucfirst($val) }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('status')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Foto --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        Foto de la unidad
                        <span class="text-xs text-gray-600 font-normal">(dejar vacío para mantener la actual)</span>
                    </h2>

                    <div x-data="{ preview: null }" class="flex items-start gap-5">
                        <div class="flex-shrink-0">
                            <div class="w-24 h-20 rounded-2xl border-2 border-gray-700 overflow-hidden flex items-center justify-center bg-gray-900/60">
                                <img x-show="preview" :src="preview" class="w-full h-full object-cover" alt="Preview"/>
                                @if($bus->foto)
                                    <img x-show="!preview" src="{{ Storage::url($bus->foto) }}"
                                         class="w-full h-full object-cover" alt="Bus #{{ $bus->num_bus }}"/>
                                @else
                                    <svg x-show="!preview" class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="foto"
                                   class="flex flex-col items-center justify-center w-full h-24 border border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-gray-600 transition-all">
                                <svg class="w-6 h-6 text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span class="text-xs text-gray-500">Clic para cambiar foto</span>
                                <span class="text-xs text-gray-700 mt-0.5">PNG, JPG hasta 2MB</span>
                            </label>
                            <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"/>
                            @error('foto')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Operador asignado --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        Operador asignado
                    </h2>

                    @if($operadores->isEmpty())
                        <div class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                            <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-xs text-amber-300">No hay operadores activos disponibles.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-2 max-h-52 overflow-y-auto pr-1">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="operator_id" value="" class="sr-only peer"
                                       {{ old('operator_id', $bus->operator_id) === null ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-700/40 bg-gray-900/30
                                            peer-checked:border-gray-500 peer-checked:bg-gray-700/40 transition-all">
                                    <div class="w-8 h-8 rounded-lg bg-gray-700 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-500">Sin operador asignado</span>
                                </div>
                            </label>

                            @foreach($operadores as $op)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="operator_id" value="{{ $op->id }}" class="sr-only peer"
                                       {{ old('operator_id', $bus->operator_id) == $op->id ? 'checked' : '' }}>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-700/40 bg-gray-900/30
                                            peer-checked:border-red-600/50 peer-checked:bg-red-600/5 transition-all">
                                    @if($op->foto)
                                        <img src="{{ Storage::url($op->foto) }}"
                                             class="w-8 h-8 rounded-lg object-cover flex-shrink-0"/>
                                    @else
                                        <div class="brand-gradient w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($op->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-white truncate">{{ $op->name }} {{ $op->last_name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $op->carnet }}</p>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                    <a href="{{ route('buses.index') }}"
                       class="w-full sm:w-auto text-center px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>
