<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('reports.orden.externo.index') }}" class="hover:text-gray-300 transition-colors">Mis órdenes</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">{{ $report->folio }}</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('reports.orden.externo.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-white">{{ $report->folio }} — Bus #{{ $report->bus->num_bus }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">Placa {{ $report->bus->placa }}</p>
        </div>
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
        {{-- Detalle del reporte que te canalizaron --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-3">Reporte del operador</h2>
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
            <p class="text-sm text-gray-300 mb-3">{{ $report->description }}</p>
            @if($report->photos->count() > 0)
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach($report->photos as $photo)
                        <a href="{{ Storage::url($photo->evidence_path) }}" target="_blank">
                            <img src="{{ Storage::url($photo->evidence_path) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-700/50"/>
                        </a>
                    @endforeach
                </div>
            @endif
            @if($report->videos->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($report->videos as $video)
                        <video src="{{ Storage::url($video->evidence_path) }}" class="w-32 h-20 object-cover rounded-lg border border-gray-700/50" controls></video>
                    @endforeach
                </div>
            @endif
        </div>

        @if($orden->diagnostico)
            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
                <h2 class="text-sm font-semibold text-white mb-2">Diagnóstico</h2>
                <p class="text-sm text-gray-300">{{ $orden->diagnostico }}</p>
            </div>
        @endif

        @php $soloLectura = true; @endphp
        @include('reports._orden_piezas')
    </div>

</x-app-layout>
