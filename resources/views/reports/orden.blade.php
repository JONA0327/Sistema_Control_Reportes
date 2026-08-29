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
            <span class="text-white">Orden de trabajo</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('reports.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-white">Orden de trabajo — {{ $report->folio }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Recepción: {{ $orden->recibido_at->format('d/m/Y H:i') }}</p>
        </div>
        @php
            $statusBadge = match($report->status) {
                'nuevo'      => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                'en_proceso' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                'resuelto'   => 'bg-green-500/10 text-green-400 border-green-500/20',
                default      => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
            };
        @endphp
        <span class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg border font-medium {{ $statusBadge }}">
            {{ ucfirst(str_replace('_', ' ', $report->status)) }}
        </span>
        <a href="{{ route('reports.orden.pdf', $report) }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-gray-700/60 hover:bg-gray-700 border border-gray-600/40 rounded-xl text-sm font-medium text-gray-200 transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H8a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exportar PDF
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm max-w-3xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-3xl space-y-4">

        {{-- Reporte original del operador --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                <span class="w-5 h-5 bg-gray-700 rounded-md flex items-center justify-center flex-shrink-0">
                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                Reporte original del operador
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm mb-3">
                <p class="text-gray-400">Unidad: <span class="text-white">Bus #{{ $report->bus->num_bus }} ({{ $report->bus->placa }})</span></p>
                <p class="text-gray-400">Operador: <span class="text-white">{{ $report->operador->name }} {{ $report->operador->last_name }}</span></p>
                <p class="text-gray-400">Kilometraje reportado: <span class="text-white">{{ number_format($report->km_actual) }} km</span></p>
                <p class="text-gray-400">
                    Urgencia:
                    @php
                        $urgLabel = \App\Models\Report::URGENCIAS[$report->urgencia] ?? '—';
                        $urgColor = match($report->urgencia) {
                            'verde' => 'text-green-400', 'amarillo' => 'text-amber-400', 'rojo' => 'text-red-400', default => 'text-gray-400',
                        };
                    @endphp
                    <span class="{{ $urgColor }} font-medium">{{ $urgLabel }}</span>
                </p>
            </div>
            <div class="flex flex-wrap gap-1.5 mb-3">
                @foreach($report->categorias ?? [] as $cat)
                    <span class="text-xs px-2 py-1 rounded-lg bg-gray-700/50 text-gray-300 border border-gray-600/40">{{ \App\Models\Report::CATEGORIAS[$cat] ?? $cat }}</span>
                @endforeach
            </div>
            @if($report->description_resumen)
                <div class="mb-3" x-data="{ verOriginal: false }">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Puntos clave (generado por IA)
                    </p>
                    <ul class="space-y-1 text-sm text-gray-200 list-disc list-inside">
                        @foreach(preg_split('/\r?\n/', trim($report->description_resumen)) as $linea)
                            @continue(trim($linea) === '')
                            <li>{{ ltrim(trim($linea), '-• ') }}</li>
                        @endforeach
                    </ul>
                    <button type="button" @click="verOriginal = !verOriginal" class="mt-2 text-xs text-gray-500 hover:text-gray-300 transition-colors">
                        <span x-show="!verOriginal">Ver descripción original del operador</span>
                        <span x-show="verOriginal" x-cloak>Ocultar descripción original</span>
                    </button>
                    <p x-show="verOriginal" x-cloak class="mt-1.5 text-sm text-gray-400 italic border-l-2 border-gray-700 pl-3">{{ $report->description }}</p>
                </div>
            @else
                <p class="text-sm text-gray-300 mb-3">{{ $report->description }}</p>
            @endif
            @if($report->photos->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($report->photos as $photo)
                        <a href="{{ Storage::url($photo->evidence_path) }}" target="_blank">
                            <img src="{{ Storage::url($photo->evidence_path) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-700/50"/>
                        </a>
                    @endforeach
                </div>
            @endif
            @if($report->videos->count() > 0)
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($report->videos as $video)
                        <video src="{{ Storage::url($video->evidence_path) }}" class="w-32 h-20 object-cover rounded-lg border border-gray-700/50" controls></video>
                    @endforeach
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('reports.orden.update', $report) }}" x-data="{ tipoAtencion: '{{ old('tipo_atencion', $orden->tipo_atencion) }}' }">
            @csrf
            @method('PUT')

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">

                {{-- 1. Vincular folio y recepción --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">1</span>
                        Vincular folio y recepción
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Folio del reporte original</label>
                            <div class="px-3.5 py-2.5 bg-gray-900/40 border border-gray-800 rounded-xl text-gray-300 text-sm">{{ $report->folio }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Fecha y hora de recepción</label>
                            <div class="px-3.5 py-2.5 bg-gray-900/40 border border-gray-800 rounded-xl text-gray-300 text-sm">{{ $orden->recibido_at->format('d/m/Y H:i') }}</div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="mecanico_id" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Mecánico / Técnico asignado <span class="text-red-500">*</span>
                            </label>
                            <select id="mecanico_id" name="mecanico_id"
                                    class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('mecanico_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                @foreach($mecanicos as $mec)
                                    <option value="{{ $mec->id }}" class="bg-gray-900"
                                            {{ old('mecanico_id', $orden->mecanico_id) == $mec->id ? 'selected' : '' }}>
                                        {{ $mec->name }} {{ $mec->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mecanico_id')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">
                                Tipo de atención <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach(\App\Models\OrdenTrabajo::TIPOS_ATENCION as $val => $label)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="tipo_atencion" value="{{ $val }}" class="sr-only peer"
                                           x-model="tipoAtencion"
                                           {{ old('tipo_atencion', $orden->tipo_atencion) === $val ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center text-center px-3 py-3 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-xs font-medium text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('tipo_atencion')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Diagnóstico técnico y canalización --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">2</span>
                        Diagnóstico técnico y canalización
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Falla confirmada</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach(\App\Models\OrdenTrabajo::FALLA_CONFIRMADA as $val => $label)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="falla_confirmada" value="{{ $val }}" class="sr-only peer"
                                           {{ old('falla_confirmada', $orden->falla_confirmada) === $val ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center text-center px-3 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-xs font-medium text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Subsistema afectado (especializado)</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach(\App\Models\OrdenTrabajo::SUBSISTEMAS as $key => $label)
                                @php $checked = in_array($key, old('subsistemas', $orden->subsistemas ?? [])); @endphp
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="subsistemas[]" value="{{ $key }}" class="sr-only peer" {{ $checked ? 'checked' : '' }}>
                                    <div class="px-3.5 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-xs text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="codigo_falla" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Código de falla / escáner (DTC / J1939)
                            </label>
                            <input type="text" id="codigo_falla" name="codigo_falla" value="{{ old('codigo_falla', $orden->codigo_falla) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors font-mono"
                                   placeholder="Ej: SPN 102 FMI 3"/>
                        </div>

                        <div>
                            <label for="diagnostico" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Diagnóstico y causa raíz
                            </label>
                            <textarea id="diagnostico" name="diagnostico" rows="3"
                                      class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors resize-none"
                                      placeholder="Ej: Fuga de aire en manguera principal de la válvula de purga debido a desgaste por fricción.">{{ old('diagnostico', $orden->diagnostico) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 3. Canalización a taller externo --}}
                <div class="px-6 py-5" x-show="tipoAtencion === 'taller_externo'" x-cloak>
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">3</span>
                        Canalización a taller externo / agencia
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="proveedor_externo" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Nombre del proveedor / taller externo
                            </label>
                            <input type="text" id="proveedor_externo" name="proveedor_externo" value="{{ old('proveedor_externo', $orden->proveedor_externo) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Ej: Volvo, especialista en frenos..."/>
                        </div>

                        <div>
                            <label for="folio_proveedor" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Número de orden / folio del proveedor
                            </label>
                            <input type="text" id="folio_proveedor" name="folio_proveedor" value="{{ old('folio_proveedor', $orden->folio_proveedor) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="proveedor_externo_user_id" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Cuenta de acceso del taller externo
                                <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                            </label>
                            @if($mecanicosExternos->isEmpty())
                                <p class="text-xs text-gray-600">No hay cuentas de mecánico externo creadas. Puedes crear una desde Usuarios con el rol "mecanico_externo".</p>
                            @else
                                <select id="proveedor_externo_user_id" name="proveedor_externo_user_id"
                                        class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                    <option value="" class="bg-gray-900">— Sin asignar —</option>
                                    @foreach($mecanicosExternos as $mecExt)
                                        <option value="{{ $mecExt->id }}" class="bg-gray-900"
                                                {{ old('proveedor_externo_user_id', $orden->proveedor_externo_user_id) == $mecExt->id ? 'selected' : '' }}>
                                            {{ $mecExt->name }} {{ $mecExt->last_name }} ({{ $mecExt->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-xs text-gray-600">Si asignas una cuenta, ese mecánico podrá entrar al sistema y subir la evidencia de las piezas que cambie, sin ver el resto del sistema.</p>
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Motivo del envío externo</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach(\App\Models\OrdenTrabajo::MOTIVOS_EXTERNO as $key => $label)
                                @php $checked = in_array($key, old('motivo_externo', $orden->motivo_externo ?? [])); @endphp
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="motivo_externo[]" value="{{ $key }}" class="sr-only peer" {{ $checked ? 'checked' : '' }}>
                                    <div class="px-3.5 py-2.5 rounded-xl border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all">
                                        <span class="text-xs text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="fecha_promesa_entrega" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Fecha promesa de entrega
                            </label>
                            <input type="datetime-local" id="fecha_promesa_entrega" name="fecha_promesa_entrega"
                                   value="{{ old('fecha_promesa_entrega', $orden->fecha_promesa_entrega?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                    <a href="{{ route('reports.index') }}"
                       class="w-full sm:w-auto text-center px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                        Volver
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Guardar avance
                    </button>
                </div>
            </div>
        </form>

        {{-- Refacciones utilizadas (solo taller interno) --}}
        @if($orden->tipo_atencion === 'taller_interno')
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-1 flex items-center gap-2">
                <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </span>
                Refacciones utilizadas
            </h2>
            <p class="text-xs text-gray-600 mb-4">Se descuentan automáticamente del inventario al registrarlas.</p>

            @if($report->partsUsed->isNotEmpty())
                <div class="space-y-2 mb-4">
                    @foreach($report->partsUsed as $parte)
                    <div class="flex items-center justify-between gap-3 px-3.5 py-2.5 bg-gray-900/40 border border-gray-700/40 rounded-xl">
                        <div class="min-w-0">
                            <p class="text-sm text-white truncate">
                                {{ $parte->item->name }}
                                <span class="text-xs text-gray-500 font-mono">({{ $parte->item->code }})</span>
                            </p>
                            <p class="text-xs text-gray-500">
                                Cantidad: {{ $parte->quantity_used }} · Instalado por {{ $parte->installedBy->name }} {{ $parte->installedBy->last_name }} · {{ $parte->installed_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('reports.orden.partes.destroy', $parte) }}"
                              onsubmit="return confirm('¿Quitar esta refacción? Se devolverá al stock del inventario.')" class="flex-shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Quitar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif

            @if($inventario->isEmpty())
                <p class="text-xs text-amber-400">No hay refacciones con stock disponible en el inventario.</p>
            @else
                <form method="POST" action="{{ route('reports.orden.partes.store', $report) }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="flex-1 min-w-48">
                        <label for="item_id" class="block text-xs font-medium text-gray-400 mb-1.5">Refacción</label>
                        <select id="item_id" name="item_id"
                                class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('item_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                            <option value="" class="bg-gray-900">— Seleccionar —</option>
                            @foreach($inventario as $item)
                                <option value="{{ $item->id }}" class="bg-gray-900">{{ $item->name }} ({{ $item->code }}) — {{ $item->stock_quantity }} disp.</option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="quantity_used" class="block text-xs font-medium text-gray-400 mb-1.5">Cantidad</label>
                        <input type="number" id="quantity_used" name="quantity_used" min="1" value="1"
                               class="w-24 px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('quantity_used') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                        @error('quantity_used')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="px-4 py-2.5 brand-gradient text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                        Agregar
                    </button>
                </form>
            @endif
        </div>
        @endif

        @include('reports._orden_piezas')

        {{-- Cierre de la orden --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            @if($report->status === 'resuelto')
                <div class="flex items-center gap-3 text-green-300 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Este reporte ya fue marcado como completado.
                </div>
            @elseif($orden->updated_at->gt($orden->created_at))
                <p class="text-sm text-gray-400 mb-3">Cuando termines de atender la falla, marca el reporte como completado.</p>
                <form method="POST" action="{{ route('reports.orden.completar', $report) }}"
                      onsubmit="return confirm('¿Marcar el reporte {{ $report->folio }} como completado?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-5 py-2.5 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-green-950/40 transition-colors">
                        Marcar como completado
                    </button>
                </form>
            @else
                <p class="text-xs text-gray-600">Guarda el avance de la orden (arriba) para poder marcar el reporte como completado.</p>
            @endif
        </div>

        <p class="text-xs text-gray-600">El detalle de trabajos realizados y control de tiempos se agregará en una siguiente etapa.</p>
    </div>

</x-app-layout>
