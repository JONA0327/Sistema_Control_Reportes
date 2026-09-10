<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('ingresos-egresos.index', ['pais' => $pais]) }}" class="hover:text-gray-300 transition-colors">Egresos y Ingresos</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Editar movimiento</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('ingresos-egresos.index', ['pais' => $pais]) }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Editar movimiento</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $movimiento->concepto }}</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('ingresos-egresos.update', $movimiento) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('ingresos-egresos._form')

            <div class="bg-gray-800/40 border border-gray-700/40 border-t-0 rounded-b-2xl px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                <a href="{{ route('ingresos-egresos.index', ['pais' => $pais]) }}"
                   class="w-full sm:w-auto text-center px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="w-full sm:w-auto btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
