<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('reports.index') }}" class="hover:text-gray-300 transition-colors">Reportes</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Nuevo reporte</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('reports.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Reporte de falla</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ auth()->user()->name }} {{ auth()->user()->last_name }} · {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @php
        $stepHasError = [
            1 => $errors->hasAny(['bus_id', 'km_actual']),
            2 => $errors->hasAny(['categorias']),
            3 => $errors->hasAny(['frecuencia', 'condiciones', 'sintomas']),
            4 => $errors->hasAny(['description', 'fotos', 'videos']),
            5 => $errors->hasAny(['urgencia']),
        ];
        $initialStep = collect($stepHasError)->filter()->keys()->first() ?? 1;
    @endphp

    <div class="max-w-2xl" x-data="reportWizard({{ $initialStep }})">

        @if ($errors->any())
            <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-red-900/30 border border-red-700/50 rounded-xl text-red-300 text-sm">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>Hay {{ $errors->count() }} {{ $errors->count() === 1 ? 'error' : 'errores' }} en el formulario. Revisa los campos marcados en rojo.</span>
            </div>
        @endif

        {{-- Barra de progreso --}}
        <div class="mb-5">
            <div class="flex items-center">
                @foreach(['Unidad', 'Ubicación', 'Detalles', 'Descripción', 'Gravedad', 'Revisar'] as $i => $label)
                    @php $n = $i + 1; @endphp
                    <div class="flex items-center" :class="{ 'flex-1': {{ $n }} < 6 }">
                        <button type="button" @click="goTo({{ $n }})"
                                class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold border-2 transition-all flex-shrink-0"
                                :class="step === {{ $n }} ? 'brand-gradient border-transparent text-white shadow-lg shadow-red-950/40' : (step > {{ $n }} ? 'bg-red-500/20 border-red-500/40 text-red-400' : 'bg-gray-800 border-gray-700 text-gray-500')">
                            <template x-if="step > {{ $n }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="step <= {{ $n }}">
                                <span>{{ $n }}</span>
                            </template>
                        </button>
                        @if($n < 6)
                            <div class="flex-1 h-0.5 mx-1" :class="step > {{ $n }} ? 'bg-red-500/40' : 'bg-gray-800'"></div>
                        @endif
                    </div>
                @endforeach
            </div>
            <p class="mt-2 text-xs font-medium text-gray-400 text-center" x-text="stepLabels[step - 1] + ' · Paso ' + step + ' de 6'"></p>
        </div>

        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" @submit="onSubmit">
            @csrf

            <div id="wizard-card" class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">

                {{-- Paso 1: Unidad --}}
                <div x-show="step === 1" x-cloak class="px-6 py-6">
                    <h2 class="text-base font-semibold text-white mb-1 flex items-center gap-2">
                        <span class="w-6 h-6 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </span>
                        ¿Qué unidad estás reportando?
                    </h2>
                    <p class="text-xs text-gray-600 mb-5">Confirma la unidad y el kilometraje actual.</p>

                    <div class="space-y-4">
                        <div>
                            <label for="bus_id" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Número económico / Unidad <span class="text-red-500">*</span>
                            </label>
                            @if($buses->isEmpty())
                                <div class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                                    <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <p class="text-xs text-amber-300">No hay unidades activas disponibles.</p>
                                </div>
                            @else
                                <select id="bus_id" name="bus_id" x-model="busId"
                                        class="w-full px-3.5 py-3 bg-gray-900/80 border {{ $errors->has('bus_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                    <option value="" class="bg-gray-900">— Seleccionar unidad —</option>
                                    @foreach($buses as $bus)
                                        <option value="{{ $bus->id }}" class="bg-gray-900"
                                                {{ old('bus_id', $assignedBusId) == $bus->id ? 'selected' : '' }}>
                                            Bus #{{ $bus->num_bus }} — {{ $bus->placa }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('bus_id')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="km_actual" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Kilometraje actual <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="km_actual" name="km_actual" min="0" value="{{ old('km_actual') }}" x-model="kmActual"
                                   class="w-full px-3.5 py-3 bg-gray-900/80 border {{ $errors->has('km_actual') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Ej: 152340"/>
                            @error('km_actual')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Paso 2: ¿Dónde está la falla? --}}
                <div x-show="step === 2" x-cloak class="px-6 py-6">
                    <h2 class="text-base font-semibold text-white mb-1 flex items-center gap-2">
                        <span class="w-6 h-6 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        ¿Dónde está la falla?
                    </h2>
                    <p class="text-xs text-gray-600 mb-4">Toca una o varias zonas de la unidad.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach(\App\Models\Report::CATEGORIAS as $key => $label)
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="categorias[]" value="{{ $key }}" class="sr-only peer" x-model="categorias"
                                   {{ in_array($key, old('categorias', [])) ? 'checked' : '' }}>
                            <div class="px-3.5 py-3 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                <span class="text-sm text-gray-300">{{ $label }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('categorias')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Paso 3: Cuándo ocurre y qué se percibe (checklist guiado) --}}
                <div x-show="step === 3" x-cloak class="px-6 py-6">
                    <h2 class="text-base font-semibold text-white mb-1 flex items-center gap-2">
                        <span class="w-6 h-6 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </span>
                        Un par de preguntas rápidas
                    </h2>
                    <p class="text-xs text-gray-600 mb-5">Esto ayuda al mecánico a diagnosticar más rápido.</p>

                    <div class="space-y-6">
                        {{-- Frecuencia --}}
                        <div>
                            <p class="text-xs font-medium text-gray-400 mb-2">¿Con qué frecuencia pasa? <span class="text-red-500">*</span></p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach(\App\Models\Report::FRECUENCIAS as $key => $label)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="frecuencia" value="{{ $key }}" class="sr-only peer" x-model="frecuencia"
                                           {{ old('frecuencia') === $key ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center text-center px-3 py-3 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all h-full">
                                        <span class="text-xs font-medium text-gray-300 peer-checked:text-white">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('frecuencia')
                                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Condiciones --}}
                        <div>
                            <p class="text-xs font-medium text-gray-400 mb-2">¿Cuándo ocurre? <span class="text-red-500">*</span> <span class="text-gray-600 font-normal">(una o varias)</span></p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach(\App\Models\Report::CONDICIONES as $key => $label)
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="condiciones[]" value="{{ $key }}" class="sr-only peer" x-model="condiciones"
                                           {{ in_array($key, old('condiciones', [])) ? 'checked' : '' }}>
                                    <div class="px-3.5 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-sm text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('condiciones')
                                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Síntomas --}}
                        <div>
                            <p class="text-xs font-medium text-gray-400 mb-2">¿Qué percibes? <span class="text-red-500">*</span> <span class="text-gray-600 font-normal">(una o varias)</span></p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach(\App\Models\Report::SINTOMAS as $key => $label)
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="sintomas[]" value="{{ $key }}" class="sr-only peer"
                                           x-model="sintomas" @change="onSintomaChange('{{ $key }}')"
                                           {{ in_array($key, old('sintomas', [])) ? 'checked' : '' }}>
                                    <div class="px-3.5 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-sm text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('sintomas')
                                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Paso 4: Descripción + audio + evidencia --}}
                <div x-show="step === 4" x-cloak class="px-6 py-6" x-data="audioReport"
                     x-effect="transcription && document.getElementById('description') && (document.getElementById('description').value = transcription, description = transcription)">
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <h2 class="text-base font-semibold text-white flex items-center gap-2">
                            <span class="w-6 h-6 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            Cuéntanos qué pasa
                        </h2>
                        <button type="button" x-show="mode === 'idle'" x-cloak @click="startRecording()"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-700/60 hover:bg-gray-700 border border-gray-600/40 rounded-lg text-xs font-medium text-gray-300 transition-colors flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                            Grabar audio
                        </button>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">Escríbela o grábala por voz — se transcribe directo aquí abajo.</p>

                    {{-- Aviso si el navegador no soporta Web Speech --}}
                    <div x-show="!speechSupported && mode !== 'idle'" x-cloak
                         class="mb-3 flex items-start gap-2.5 px-3.5 py-2.5 bg-amber-500/5 border border-amber-500/20 rounded-xl">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs text-amber-300">La transcripción en tiempo real requiere Chrome o Edge. El audio se grabará, pero escribe la descripción manualmente.</p>
                    </div>

                    {{-- Estado: Grabando --}}
                    <div x-show="mode === 'recording'" x-cloak class="mb-3 space-y-3">
                        <div class="flex items-center gap-4 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            <span class="text-sm text-red-400 font-medium">Grabando...</span>
                            <span class="ml-auto font-mono text-sm text-red-300" x-text="timerDisplay"></span>
                        </div>
                        <div x-show="speechSupported" class="px-3.5 py-3 bg-gray-900/60 border border-gray-700/60 rounded-xl min-h-16">
                            <p class="text-xs text-gray-600 mb-1.5">Transcribiendo en tiempo real...</p>
                            <p class="text-sm text-gray-300 leading-relaxed" x-text="transcription"></p>
                            <p class="text-sm text-gray-500 italic" x-text="interim"></p>
                        </div>
                        <button type="button" @click="stopRecording()"
                                class="flex items-center gap-2 px-5 py-2.5 bg-gray-700 border border-gray-600 rounded-xl text-white text-sm font-medium hover:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="6" y="6" width="12" height="12" rx="1"/>
                            </svg>
                            Detener grabación
                        </button>
                    </div>

                    {{-- Estado: Grabado / Transcribiendo --}}
                    <div x-show="mode === 'recorded'" x-cloak class="mb-3 flex flex-wrap items-center gap-3 px-3.5 py-2.5 bg-green-900/10 border border-green-700/30 rounded-xl">
                        <template x-if="transcribing">
                            <svg class="w-4 h-4 text-amber-400 flex-shrink-0 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </template>
                        <template x-if="!transcribing">
                            <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <span class="text-xs flex-1" :class="transcribing ? 'text-amber-400' : 'text-green-400'"
                              x-text="transcribing ? 'Transcribiendo audio...' : 'Audio grabado y transcrito abajo.'"></span>
                        <audio controls :src="audioUrl" class="h-8 max-w-[180px]"></audio>
                        <button type="button" @click="reset()"
                                class="text-xs text-gray-500 hover:text-gray-300 transition-colors flex-shrink-0">
                            Grabar de nuevo
                        </button>
                    </div>

                    {{-- Error de transcripción en servidor (no bloquea, el audio ya se grabó) --}}
                    <div x-show="transcribeError" x-cloak class="mb-3 flex items-start gap-2 text-xs text-amber-400">
                        <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="transcribeError"></span>
                    </div>

                    {{-- Error de acceso al micrófono --}}
                    <div x-show="micError" x-cloak class="mb-3 flex items-start gap-2 text-xs text-red-400">
                        <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="micError"></span>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-medium text-gray-400 mb-1.5">
                            ¿Qué hace la unidad o cuándo ocurre? <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="4" x-model="description"
                                  class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors resize-none"
                                  placeholder='Ej: &quot;Al pasar los 60 km/h empieza a vibrar la dirección y chilla al frenar.&quot;'>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="hidden" name="status" value="nuevo">

                    {{-- Evidencia visual --}}
                    <div class="mt-4" x-data="photoUpload()">
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">
                            Evidencia visual <span class="text-gray-600 font-normal">(opcional, máx. 10 fotos, 4MB c/u)</span>
                        </label>
                        <label for="fotos"
                               class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-red-600/50 transition-all mb-3"
                               @dragover.prevent @drop.prevent="handleDrop($event)">
                            <svg class="w-7 h-7 text-gray-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <p class="text-sm text-gray-500">📷 Tomar foto o <span class="text-red-400">adjuntar imagen</span></p>
                        </label>
                        <input type="file" id="fotos" name="fotos[]" accept="image/*" multiple class="hidden"
                               @change="handleFiles($event.target.files)"/>

                        <div x-show="previews.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="relative group aspect-square">
                                    <img :src="src" class="w-full h-full object-cover rounded-xl border border-gray-700/50"/>
                                    <button type="button" @click="removePhoto(i)"
                                            class="absolute top-1 right-1 w-5 h-5 bg-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        @error('fotos')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                        @error('fotos.*')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Video de evidencia --}}
                    <div class="mt-4" x-data="videoUpload()">
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">
                            Video de evidencia <span class="text-gray-600 font-normal">(opcional, máx. 3 videos, 20MB c/u)</span>
                        </label>
                        <label for="videos"
                               class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-red-600/50 transition-all mb-3">
                            <svg class="w-7 h-7 text-gray-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">🎥 Grabar video o <span class="text-red-400">adjuntar archivo</span></p>
                        </label>
                        <input type="file" id="videos" name="videos[]" accept="video/*" multiple class="hidden"
                               @change="handleFiles($event.target.files)"/>

                        <div x-show="previews.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            <template x-for="(src, i) in previews" :key="i">
                                <div class="relative group aspect-video">
                                    <video :src="src" class="w-full h-full object-cover rounded-xl border border-gray-700/50" controls></video>
                                    <button type="button" @click="removeVideo(i)"
                                            class="absolute top-1 right-1 w-5 h-5 bg-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg z-10">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        @error('videos')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                        @error('videos.*')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Paso 5: Gravedad --}}
                <div x-show="step === 5" x-cloak class="px-6 py-6">
                    <h2 class="text-base font-semibold text-white mb-1 flex items-center gap-2">
                        <span class="w-6 h-6 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                        ¿Qué tan grave está la falla?
                    </h2>
                    <p class="text-xs text-gray-600 mb-4">Elige la opción que mejor describa el riesgo actual.</p>

                    <div class="grid grid-cols-1 gap-2.5">
                        @foreach([
                            'verde'    => ['🟢', 'Puede seguir en ruta',   'La unidad puede terminar el turno y entrar a revisión al final del día.',   'text-green-400', 'border-green-500/30 bg-green-500/5', 'border-green-500 bg-green-500/10'],
                            'amarillo' => ['🟡', 'Necesita revisión pronto', 'La unidad camina pero requiere atención antes de volver a salir.',           'text-amber-400', 'border-amber-500/30 bg-amber-500/5', 'border-amber-500 bg-amber-500/10'],
                            'rojo'     => ['🔴', 'No puede circular',      'Peligro de seguridad o falla grave. Detener la unidad de inmediato.',        'text-red-400',   'border-red-500/30 bg-red-500/5',     'border-red-500 bg-red-500/10'],
                        ] as $val => [$emoji, $label, $desc, $tc, $idle, $active])
                        <label class="relative cursor-pointer">
                            <input type="radio" name="urgencia" value="{{ $val }}" class="sr-only peer" x-model="urgencia"
                                   {{ old('urgencia') === $val ? 'checked' : '' }}>
                            <div class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl border {{ $idle }}
                                        peer-checked:{{ $active }} peer-checked:border-2 transition-all">
                                <span class="text-2xl flex-shrink-0">{{ $emoji }}</span>
                                <div>
                                    <span class="text-sm font-semibold {{ $tc }}">{{ $label }}</span>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $desc }}</p>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('urgencia')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Paso 6: Revisar y enviar --}}
                <div x-show="step === 6" x-cloak class="px-6 py-6">
                    <h2 class="text-base font-semibold text-white mb-1 flex items-center gap-2">
                        <span class="w-6 h-6 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        Revisa antes de enviar
                    </h2>
                    <p class="text-xs text-gray-600 mb-4">Verifica que todo esté correcto. Puedes volver a cualquier paso para corregir.</p>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex items-start justify-between gap-3 px-4 py-3 bg-gray-900/40 border border-gray-800 rounded-xl">
                            <div>
                                <p class="text-xs text-gray-500">Unidad</p>
                                <p class="text-white font-medium" x-text="busLabel() || '— Sin seleccionar —'"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="kmActual ? kmActual + ' km' : ''"></p>
                            </div>
                            <button type="button" @click="goTo(1)" class="text-xs text-red-400 hover:text-red-300 flex-shrink-0">Editar</button>
                        </div>

                        <div class="flex items-start justify-between gap-3 px-4 py-3 bg-gray-900/40 border border-gray-800 rounded-xl">
                            <div>
                                <p class="text-xs text-gray-500">Ubicación de la falla</p>
                                <p class="text-white" x-text="categoriaLabels() || '— Sin seleccionar —'"></p>
                            </div>
                            <button type="button" @click="goTo(2)" class="text-xs text-red-400 hover:text-red-300 flex-shrink-0">Editar</button>
                        </div>

                        <div class="flex items-start justify-between gap-3 px-4 py-3 bg-gray-900/40 border border-gray-800 rounded-xl">
                            <div>
                                <p class="text-xs text-gray-500">Detalles</p>
                                <p class="text-white" x-text="frecuenciaLabel() || '— Sin seleccionar —'"></p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="condicionLabels()"></p>
                                <p class="text-xs text-gray-400" x-text="sintomaLabels()"></p>
                            </div>
                            <button type="button" @click="goTo(3)" class="text-xs text-red-400 hover:text-red-300 flex-shrink-0">Editar</button>
                        </div>

                        <div class="flex items-start justify-between gap-3 px-4 py-3 bg-gray-900/40 border border-gray-800 rounded-xl">
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500">Descripción</p>
                                <p class="text-white truncate" x-text="description || '— Sin descripción —'"></p>
                            </div>
                            <button type="button" @click="goTo(4)" class="text-xs text-red-400 hover:text-red-300 flex-shrink-0">Editar</button>
                        </div>

                        <div class="flex items-start justify-between gap-3 px-4 py-3 bg-gray-900/40 border border-gray-800 rounded-xl">
                            <div>
                                <p class="text-xs text-gray-500">Gravedad</p>
                                <p class="font-medium" :class="urgenciaColor()" x-text="urgenciaLabel() || '— Sin seleccionar —'"></p>
                            </div>
                            <button type="button" @click="goTo(5)" class="text-xs text-red-400 hover:text-red-300 flex-shrink-0">Editar</button>
                        </div>
                    </div>

                    <div x-show="submitError" x-cloak class="mt-4 flex items-start gap-2 px-3.5 py-2.5 bg-red-900/30 border border-red-700/50 rounded-xl text-red-300 text-xs">
                        <span x-text="submitError"></span>
                    </div>
                </div>

                {{-- Navegación --}}
                <div class="px-6 py-4 border-t border-gray-700/40 flex items-center justify-between gap-3">
                    <button type="button" @click="prev()" x-show="step > 1"
                            class="px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                        Atrás
                    </button>
                    <a href="{{ route('reports.index') }}" x-show="step === 1"
                       class="px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>

                    <button type="button" @click="next()" x-show="step < 6"
                            class="ml-auto btn-login-gradient px-6 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Siguiente
                    </button>
                    <button type="submit" x-show="step === 6"
                            class="ml-auto btn-login-gradient px-6 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Crear reporte
                    </button>
                </div>
            </div>
        </form>
    </div>

@push('scripts')
<script>
function reportWizard(initialStep) {
    return {
        step: initialStep,
        stepLabels: [
            'Unidad',
            '¿Dónde está la falla?',
            'Un par de preguntas',
            'Cuéntanos qué pasa',
            '¿Qué tan grave está?',
            'Revisar y enviar',
        ],
        busId: @json(old('bus_id', $assignedBusId ?? '')),
        kmActual: @json(old('km_actual', '')),
        categorias: @json(old('categorias', [])),
        frecuencia: @json(old('frecuencia', '')),
        condiciones: @json(old('condiciones', [])),
        sintomas: @json(old('sintomas', [])),
        description: @json(old('description', '')),
        urgencia: @json(old('urgencia', '')),
        submitError: '',
        busOptions: @json($buses->mapWithKeys(fn($b) => [(string) $b->id => "Bus #{$b->num_bus} — {$b->placa}"])),
        categoriaOptions: @json(\App\Models\Report::CATEGORIAS),
        frecuenciaOptions: @json(\App\Models\Report::FRECUENCIAS),
        condicionOptions: @json(\App\Models\Report::CONDICIONES),
        sintomaOptions: @json(\App\Models\Report::SINTOMAS),
        urgenciaOptions: @json(\App\Models\Report::URGENCIAS),

        onSintomaChange(key) {
            if (key === 'ninguno' && this.sintomas.includes('ninguno')) {
                this.sintomas = ['ninguno'];
            } else if (key !== 'ninguno') {
                this.sintomas = this.sintomas.filter(s => s !== 'ninguno');
            }
        },

        valid(n) {
            switch (n) {
                case 1: return !!this.busId && this.kmActual !== '' && this.kmActual !== null;
                case 2: return this.categorias.length > 0;
                case 3: return !!this.frecuencia && this.condiciones.length > 0 && this.sintomas.length > 0;
                case 4: return this.description.trim().length > 0;
                case 5: return !!this.urgencia;
                default: return true;
            }
        },

        next() {
            if (!this.valid(this.step)) {
                document.getElementById('wizard-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }
            if (this.step < 6) this.step++;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        prev() {
            if (this.step > 1) this.step--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        goTo(n) {
            if (n < this.step || this.valid(this.step)) {
                this.step = n;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        onSubmit(e) {
            for (let n = 1; n <= 5; n++) {
                if (!this.valid(n)) {
                    e.preventDefault();
                    this.submitError = 'Falta completar información. Revisa los pasos anteriores.';
                    this.step = n;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
            }
        },

        busLabel() { return this.busOptions[this.busId] || ''; },
        categoriaLabels() { return this.categorias.map(k => this.categoriaOptions[k]).join(', '); },
        frecuenciaLabel() { return this.frecuenciaOptions[this.frecuencia] || ''; },
        condicionLabels() {
            const l = this.condiciones.map(k => this.condicionOptions[k]);
            return l.length ? 'Cuándo: ' + l.join(', ') : '';
        },
        sintomaLabels() {
            const l = this.sintomas.map(k => this.sintomaOptions[k]);
            return l.length ? 'Percibido: ' + l.join(', ') : '';
        },
        urgenciaLabel() { return this.urgenciaOptions[this.urgencia] || ''; },
        urgenciaColor() {
            return { 'text-green-400': this.urgencia === 'verde', 'text-amber-400': this.urgencia === 'amarillo', 'text-red-400': this.urgencia === 'rojo' };
        },
    }
}

function photoUpload() {
    return {
        previews: [],
        files: [],
        handleFiles(fileList) {
            Array.from(fileList).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                this.files.push(file);
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
            this.syncInput();
        },
        handleDrop(e) { this.handleFiles(e.dataTransfer.files); },
        removePhoto(index) {
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            this.syncInput();
        },
        syncInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            document.getElementById('fotos').files = dt.files;
        }
    }
}

function videoUpload() {
    return {
        previews: [],
        files: [],
        handleFiles(fileList) {
            Array.from(fileList).forEach(file => {
                if (!file.type.startsWith('video/')) return;
                this.files.push(file);
                this.previews.push(URL.createObjectURL(file));
            });
            this.syncInput();
        },
        removeVideo(index) {
            URL.revokeObjectURL(this.previews[index]);
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            this.syncInput();
        },
        syncInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            document.getElementById('videos').files = dt.files;
        }
    }
}
</script>
@endpush

</x-app-layout>
