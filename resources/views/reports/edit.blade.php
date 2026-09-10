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
            <span class="text-white">Editar</span>
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
            <h1 class="text-xl font-bold text-white">{{ $report->folio }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Reportado por {{ $report->operador->name }} {{ $report->operador->last_name }} · {{ $report->created_at->format('d/m/Y H:i') }}
                @if($report->bus) · Bus #{{ $report->bus->num_bus }} ({{ $report->bus->placa }}) @endif
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('reports.update', $report) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">

                {{-- Info --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        Información del reporte
                    </h2>

                    <div class="space-y-4">

                        <div>
                            <label for="bus_id" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Unidad <span class="text-red-500">*</span>
                            </label>
                            <select id="bus_id" name="bus_id"
                                    class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('bus_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}" class="bg-gray-900"
                                            {{ old('bus_id', $report->bus_id) == $bus->id ? 'selected' : '' }}>
                                        Bus #{{ $bus->num_bus }} — {{ $bus->placa }}
                                        @if($bus->operator) ({{ $bus->operator->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('bus_id')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="km_actual" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Kilometraje actual <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="km_actual" name="km_actual" min="0" value="{{ old('km_actual', $report->km_actual) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('km_actual') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                            @error('km_actual')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                ¿Dónde está la falla? <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach(\App\Models\Report::CATEGORIAS as $key => $label)
                                @php $checked = in_array($key, old('categorias', $report->categorias ?? [])); @endphp
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="categorias[]" value="{{ $key }}" class="sr-only peer" {{ $checked ? 'checked' : '' }}>
                                    <div class="px-3.5 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-sm text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('categorias')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">¿Con qué frecuencia pasa?</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach(\App\Models\Report::FRECUENCIAS as $key => $label)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="frecuencia" value="{{ $key }}" class="sr-only peer"
                                           {{ old('frecuencia', $report->frecuencia) === $key ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center text-center px-3 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all h-full">
                                        <span class="text-xs font-medium text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('frecuencia')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">¿Cuándo ocurre? <span class="text-gray-600 font-normal">(una o varias)</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach(\App\Models\Report::CONDICIONES as $key => $label)
                                @php $checked = in_array($key, old('condiciones', $report->condiciones ?? [])); @endphp
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="condiciones[]" value="{{ $key }}" class="sr-only peer" {{ $checked ? 'checked' : '' }}>
                                    <div class="px-3.5 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-sm text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('condiciones')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">¿Qué se percibe? <span class="text-gray-600 font-normal">(una o varias)</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach(\App\Models\Report::SINTOMAS as $key => $label)
                                @php $checked = in_array($key, old('sintomas', $report->sintomas ?? [])); @endphp
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="sintomas[]" value="{{ $key }}" class="sr-only peer" {{ $checked ? 'checked' : '' }}>
                                    <div class="px-3.5 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-sm text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('sintomas')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors resize-none">{{ old('description', $report->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado de la unidad <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach([
                                    'verde'    => ['🟢 Ruta normal',          'text-green-400', 'border-green-500/30 bg-green-500/5', 'border-green-500 bg-green-500/10'],
                                    'amarillo' => ['🟡 Revisión prioritaria', 'text-amber-400', 'border-amber-500/30 bg-amber-500/5', 'border-amber-500 bg-amber-500/10'],
                                    'rojo'     => ['🔴 Unidad detenida',      'text-red-400',   'border-red-500/30 bg-red-500/5',     'border-red-500 bg-red-500/10'],
                                ] as $val => [$label, $tc, $idle, $active])
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="urgencia" value="{{ $val }}" class="sr-only peer"
                                           {{ old('urgencia', $report->urgencia) === $val ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center px-3 py-3 rounded-xl border {{ $idle }}
                                                peer-checked:{{ $active }} peer-checked:border-2 transition-all">
                                        <span class="text-xs font-medium {{ $tc }}">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('urgencia')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        @can('reportes.estado')
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado de seguimiento <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach([
                                    'nuevo'      => ['Nuevo',      'text-blue-400',  'border-blue-500/30 bg-blue-500/5',  'border-blue-500 bg-blue-500/10'],
                                    'en_proceso' => ['En proceso', 'text-amber-400', 'border-amber-500/30 bg-amber-500/5', 'border-amber-500 bg-amber-500/10'],
                                    'resuelto'   => ['Resuelto',   'text-green-400', 'border-green-500/30 bg-green-500/5', 'border-green-500 bg-green-500/10'],
                                ] as $val => [$label, $tc, $idle, $active])
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="status" value="{{ $val }}" class="sr-only peer"
                                           {{ old('status', $report->status) === $val ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center px-3 py-3 rounded-xl border {{ $idle }}
                                                peer-checked:{{ $active }} peer-checked:border-2 transition-all">
                                        <span class="text-xs font-medium {{ $tc }}">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>

                {{-- Fotos existentes --}}
                @if($photos->count() > 0)
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            Fotos actuales
                        </span>
                        <span class="text-xs text-gray-600 font-normal">{{ $photos->count() }} foto(s)</span>
                    </h2>

                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($photos as $photo)
                        <div class="relative group aspect-square">
                            <a href="{{ Storage::url($photo->evidence_path) }}" target="_blank">
                                <img src="{{ Storage::url($photo->evidence_path) }}"
                                     class="w-full h-full object-cover rounded-xl border border-gray-700/50 hover:border-gray-500 transition-colors"
                                     alt="Evidencia"/>
                            </a>
                            <form method="POST" action="{{ route('reports.photos.destroy', $photo) }}"
                                  onsubmit="return confirm('¿Eliminar esta foto?')"
                                  class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-6 h-6 bg-red-600 rounded-full flex items-center justify-center shadow-lg hover:bg-red-500 transition-colors">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Videos existentes --}}
                @if($videos->count() > 0)
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            Videos actuales
                        </span>
                        <span class="text-xs text-gray-600 font-normal">{{ $videos->count() }} video(s)</span>
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($videos as $video)
                        <div class="relative group aspect-video">
                            <video src="{{ Storage::url($video->evidence_path) }}" class="w-full h-full object-cover rounded-xl border border-gray-700/50" controls></video>
                            <form method="POST" action="{{ route('reports.photos.destroy', $video) }}"
                                  onsubmit="return confirm('¿Eliminar este video?')"
                                  class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-6 h-6 bg-red-600 rounded-full flex items-center justify-center shadow-lg hover:bg-red-500 transition-colors">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Agregar más fotos --}}
                <div class="px-6 py-5" x-data="photoUpload()">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 bg-gray-700 rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </span>
                        Agregar más fotos
                        <span class="text-xs text-gray-600 font-normal">(máx. 10 por envío)</span>
                    </h2>

                    <label for="fotos"
                           class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-red-600/50 transition-all mb-3"
                           @dragover.prevent @drop.prevent="handleDrop($event)">
                        <svg class="w-7 h-7 text-gray-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <p class="text-sm text-gray-500">Arrastra o <span class="text-red-400">selecciona</span></p>
                    </label>
                    <input type="file" id="fotos" name="fotos[]" accept="image/*" multiple class="hidden"
                           @change="handleFiles($event.target.files)"/>

                    <div x-show="previews.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        <template x-for="(src, i) in previews" :key="i">
                            <div class="relative group aspect-square">
                                <img :src="src" class="w-full h-full object-cover rounded-xl border border-gray-700/50 border-dashed"/>
                                <button type="button" @click="removePhoto(i)"
                                        class="absolute top-1 right-1 w-5 h-5 bg-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Agregar más videos --}}
                <div class="px-6 py-5" x-data="videoUpload()">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 bg-gray-700 rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </span>
                        Agregar más videos
                        <span class="text-xs text-gray-600 font-normal">(máx. 3 por envío, 20MB c/u)</span>
                    </h2>

                    <label for="videos"
                           class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-red-600/50 transition-all mb-3">
                        <svg class="w-7 h-7 text-gray-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Grabar o <span class="text-red-400">seleccionar video</span></p>
                    </label>
                    <input type="file" id="videos" name="videos[]" accept="video/*" multiple class="hidden"
                           @change="handleFiles($event.target.files)"/>

                    <div x-show="previews.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <template x-for="(src, i) in previews" :key="i">
                            <div class="relative group aspect-video">
                                <video :src="src" class="w-full h-full object-cover rounded-xl border border-gray-700/50 border-dashed" controls></video>
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

                {{-- Acciones --}}
                <div class="px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                    <a href="{{ route('reports.index') }}"
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
