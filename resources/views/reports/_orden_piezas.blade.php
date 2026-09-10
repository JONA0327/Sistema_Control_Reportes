{{-- Piezas reemplazadas / retiradas y ajustes (interno y externo, con evidencia) --}}
<div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
    <h2 class="text-sm font-semibold text-white mb-1 flex items-center gap-2">
        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </span>
        Piezas y ajustes realizados
    </h2>
    <p class="text-xs text-gray-600 mb-4">Registra cada pieza reemplazada, retirada o ajustada, con foto como evidencia. Aplica tanto para taller interno como externo.</p>

    @if($orden->piezas->isNotEmpty())
        <div class="space-y-2 mb-4">
            @foreach($orden->piezas as $pieza)
            <div class="flex items-start gap-3 px-3.5 py-2.5 bg-gray-900/40 border border-gray-700/40 rounded-xl">
                @if($pieza->evidencia_path)
                    <a href="{{ Storage::url($pieza->evidencia_path) }}" target="_blank" class="flex-shrink-0">
                        <img src="{{ Storage::url($pieza->evidencia_path) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-700/50"/>
                    </a>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        @php
                            $accionColor = match($pieza->accion) {
                                'reemplazada' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                'retirada' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'ajustada' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                default => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                            };
                        @endphp
                        <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-lg border font-medium {{ $accionColor }}">
                            {{ \App\Models\OrdenTrabajoPieza::ACCIONES[$pieza->accion] ?? $pieza->accion }}
                        </span>
                        <span class="text-sm text-white truncate">{{ $pieza->pieza }}</span>
                    </div>
                    @if($pieza->notas)
                        <p class="text-xs text-gray-400 mt-1">{{ $pieza->notas }}</p>
                    @endif
                    <p class="text-xs text-gray-600 mt-1">{{ $pieza->user->name }} {{ $pieza->user->last_name }} · {{ $pieza->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if(!isset($soloLectura) || !$soloLectura || $pieza->user_id === auth()->id())
                <form method="POST" action="{{ route('reports.orden.piezas.destroy', $pieza) }}"
                      onsubmit="return confirm('¿Eliminar este registro?')" class="flex-shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Eliminar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('reports.orden.piezas.store', $report) }}" enctype="multipart/form-data" class="space-y-3"
          x-data="{ preview: null, fileName: '', setFile(file) { if (!file || !file.type.startsWith('image/')) return; this.preview = URL.createObjectURL(file); this.fileName = file.name; }, clearFile() { this.preview = null; this.fileName = ''; document.getElementById('evidencia').value = ''; } }">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">¿Qué se hizo con la pieza? <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                @foreach(\App\Models\OrdenTrabajoPieza::ACCIONES as $val => $label)
                <label class="relative cursor-pointer">
                    <input type="radio" name="accion" value="{{ $val }}" class="sr-only peer" {{ old('accion') === $val ? 'checked' : '' }}>
                    <div class="flex items-center justify-center text-center px-3 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                        <span class="text-xs font-medium text-gray-300">{{ $label }}</span>
                    </div>
                </label>
                @endforeach
            </div>
            @error('accion')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <label for="pieza" class="block text-xs font-medium text-gray-400 mb-1.5">Pieza / componente <span class="text-red-500">*</span></label>
                <input type="text" id="pieza" name="pieza" value="{{ old('pieza') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('pieza') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: Balata delantera izquierda"/>
                @error('pieza')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex-1 min-w-48">
                <label for="notas" class="block text-xs font-medium text-gray-400 mb-1.5">Notas <span class="text-gray-600 font-normal">(opcional)</span></label>
                <input type="text" id="notas" name="notas" value="{{ old('notas') }}"
                       class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                       placeholder="Ej: Se ajustó torque a especificación"/>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">
                Evidencia fotográfica <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-600 mb-2">Sube una foto clara de la pieza retirada o instalada. Es obligatoria para llevar control del taller.</p>

            <div x-show="!preview">
                <label for="evidencia"
                       class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed {{ $errors->has('evidencia') ? 'border-red-500' : 'border-gray-700' }} rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-red-600/50 transition-all"
                       @dragover.prevent @drop.prevent="setFile($event.dataTransfer.files[0])">
                    <svg class="w-7 h-7 text-gray-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">📷 Tomar foto o <span class="text-red-400">adjuntar imagen</span></p>
                </label>
            </div>

            <div x-show="preview" x-cloak class="flex items-center gap-3 px-3.5 py-2.5 bg-gray-900/40 border border-gray-700/50 rounded-xl">
                <img :src="preview" class="w-14 h-14 object-cover rounded-lg border border-gray-700/50 flex-shrink-0" alt="Vista previa"/>
                <span class="text-xs text-gray-300 truncate flex-1" x-text="fileName"></span>
                <button type="button" @click="clearFile()" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all flex-shrink-0" title="Quitar foto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <input type="file" id="evidencia" name="evidencia" accept="image/*" class="hidden"
                   @change="setFile($event.target.files[0])"/>
            @error('evidencia')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2.5 brand-gradient text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                Registrar pieza
            </button>
        </div>
    </form>
</div>
