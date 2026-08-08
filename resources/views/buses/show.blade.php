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
            <span class="text-white">Bus #{{ $bus->num_bus }}</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('buses.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-white">Bus #{{ $bus->num_bus }}</h1>
            <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $bus->placa }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm max-w-2xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl space-y-4">

        {{-- Datos de la unidad --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-6">
            <div class="flex items-start gap-5">
                @if($bus->foto)
                    <img src="{{ Storage::url($bus->foto) }}" class="w-28 h-20 rounded-xl object-cover border border-gray-700/50 flex-shrink-0"/>
                @else
                    <div class="w-28 h-20 rounded-xl bg-gray-700/60 border border-gray-600/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Placa</p>
                        <p class="text-sm font-mono font-medium text-gray-200 tracking-wider uppercase">{{ $bus->placa }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Estado</p>
                        @php
                            $badge = match($bus->status) {
                                'activo'        => 'bg-green-500/10 text-green-400 border-green-500/20',
                                'mantenimiento' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                default         => 'bg-gray-700/50 text-gray-400 border-gray-600/40',
                            };
                        @endphp
                        <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-lg border font-medium mt-0.5 {{ $badge }}">
                            {{ ucfirst($bus->status) }}
                        </span>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Operador asignado</p>
                        @if($bus->operator)
                            <div class="flex items-center gap-2.5 mt-1">
                                @if($bus->operator->foto)
                                    <img src="{{ Storage::url($bus->operator->foto) }}" class="w-7 h-7 rounded-lg object-cover flex-shrink-0"/>
                                @else
                                    <div class="brand-gradient w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($bus->operator->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm text-white">{{ $bus->operator->name }} {{ $bus->operator->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $bus->operator->carnet }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-1">Sin asignar</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-5 pt-5 border-t border-gray-700/40">
                <a href="{{ route('buses.edit', $bus) }}"
                   class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-300 text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    Editar unidad
                </a>
            </div>
        </div>

        {{-- Herramientas y refacciones bajo responsabilidad del operador --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-white mb-1">Herramientas y refacciones pendientes</h2>
            <p class="text-xs text-gray-500 mb-4">Salidas de inventario bajo la responsabilidad del operador asignado a esta unidad, aún no devueltas.</p>

            @if(!$bus->operator)
                <p class="text-xs text-gray-600">Esta unidad no tiene operador asignado.</p>
            @elseif($prestamos->isEmpty())
                <p class="text-xs text-gray-600">Sin herramientas o refacciones pendientes.</p>
            @else
                <div class="space-y-2">
                    @foreach($prestamos as $prestamo)
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-gray-900/40 border border-gray-700/40 rounded-xl gap-3">
                            <div>
                                <p class="text-sm text-white">{{ $prestamo->item->name }}</p>
                                <p class="text-xs text-gray-500">{{ $prestamo->item->category }} · {{ $prestamo->quantity }} unidad(es) · {{ $prestamo->movement_date?->format('d/m/Y') }}</p>
                                @if($prestamo->notes)
                                    <p class="text-xs text-gray-600">{{ $prestamo->notes }}</p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('inventario.movimientos.devuelto', $prestamo) }}" class="flex-shrink-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-2.5 py-1.5 bg-green-600/20 border border-green-600/40 text-green-400 rounded-lg hover:bg-green-600/30 transition-colors whitespace-nowrap">
                                    Marcar como devuelto
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
