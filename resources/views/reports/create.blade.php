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
            <p class="text-sm text-gray-500 mt-0.5">Levantamiento de reporte — Operador</p>
        </div>
    </div>

    <div class="max-w-2xl">
        @if ($errors->any())
            <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-red-900/30 border border-red-700/50 rounded-xl text-red-300 text-sm">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>Hay {{ $errors->count() }} {{ $errors->count() === 1 ? 'error' : 'errores' }} en el formulario. Revisa los campos marcados en rojo antes de enviar.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">

                {{-- 1. Datos de identificación --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        Datos de identificación
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Reportado por --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Reportado por</label>
                            <div class="px-3.5 py-2.5 bg-gray-900/40 border border-gray-800 rounded-xl text-gray-300 text-sm">
                                {{ auth()->user()->name }} {{ auth()->user()->last_name }}
                            </div>
                        </div>

                        {{-- Fecha y hora --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Fecha y hora</label>
                            <div class="px-3.5 py-2.5 bg-gray-900/40 border border-gray-800 rounded-xl text-gray-300 text-sm">
                                {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        {{-- Unidad --}}
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
                                <select id="bus_id" name="bus_id"
                                        class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('bus_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
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

                        {{-- Kilometraje actual --}}
                        <div>
                            <label for="km_actual" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Kilometraje actual <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="km_actual" name="km_actual" min="0" value="{{ old('km_actual') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('km_actual') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Ej: 152340"/>
                            @error('km_actual')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. ¿Dónde está la falla? --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-1 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                        ¿Dónde está la falla? <span class="text-red-500">*</span>
                    </h2>
                    <p class="text-xs text-gray-600 mb-4">Selecciona una o varias opciones</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach(\App\Models\Report::CATEGORIAS as $key => $label)
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="categorias[]" value="{{ $key }}" class="sr-only peer"
                                   {{ in_array($key, old('categorias', [])) ? 'checked' : '' }}>
                            <div class="px-3.5 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                <span class="text-sm text-gray-300">{{ $label }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('categorias')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. Descripción de la falla --}}
                <div class="px-6 py-5" x-data="audioReport"
                     x-effect="transcription && document.getElementById('description') && (document.getElementById('description').value = transcription)">
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            Descripción de la falla
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
                        <textarea id="description" name="description" rows="4"
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

                {{-- 4. Estado de la unidad --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-1 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                        Estado de la unidad <span class="text-red-500">*</span>
                    </h2>
                    <p class="text-xs text-gray-600 mb-4">¿Qué tan urgente es la falla?</p>

                    <div class="grid grid-cols-1 gap-2">
                        @foreach([
                            'verde'    => ['🟢 Ruta normal',          'La unidad puede terminar el turno y entrar a revisión al final del día.',   'text-green-400', 'border-green-500/30 bg-green-500/5', 'border-green-500 bg-green-500/10'],
                            'amarillo' => ['🟡 Revisión prioritaria', 'La unidad camina pero requiere atención antes de volver a salir.',           'text-amber-400', 'border-amber-500/30 bg-amber-500/5', 'border-amber-500 bg-amber-500/10'],
                            'rojo'     => ['🔴 Unidad detenida',      'Peligro de seguridad o falla grave. No puede circular.',                     'text-red-400',   'border-red-500/30 bg-red-500/5',     'border-red-500 bg-red-500/10'],
                        ] as $val => [$label, $desc, $tc, $idle, $active])
                        <label class="relative cursor-pointer">
                            <input type="radio" name="urgencia" value="{{ $val }}" class="sr-only peer"
                                   {{ old('urgencia') === $val ? 'checked' : '' }}>
                            <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl border {{ $idle }}
                                        peer-checked:{{ $active }} peer-checked:border-2 transition-all">
                                <div>
                                    <span class="text-sm font-medium {{ $tc }}">{{ $label }}</span>
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

                {{-- Acciones --}}
                <div class="px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                    <a href="{{ route('reports.index') }}"
                       class="w-full sm:w-auto text-center px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Crear reporte
                    </button>
                </div>
            </div>
        </form>
    </div>

@push('scripts')
<script>
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
