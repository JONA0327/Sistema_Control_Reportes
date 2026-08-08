<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('viajes.index') }}" class="hover:text-gray-300 transition-colors">Viajes</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Editar</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('viajes.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-white">Viaje {{ $viaje->no_contrato }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                @if(!$viaje->liquidacion || $viaje->liquidacion->estado === 'abierta')
                    En curso — el operador registra el km y cierra la liquidación desde Gastos
                @else
                    Finalizado
                @endif
            </p>
        </div>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('viajes.update', $viaje) }}">
            @csrf
            @method('PUT')
            @include('viajes._form', ['submitLabel' => 'Guardar cambios'])
        </form>
    </div>

</x-app-layout>
