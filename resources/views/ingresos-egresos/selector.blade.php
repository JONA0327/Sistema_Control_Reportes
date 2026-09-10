<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Egresos y Ingresos</span>
        </div>
    </x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Egresos y Ingresos</h1>
        <p class="text-sm text-gray-500 mt-0.5">Selecciona el país para ver y registrar sus movimientos financieros</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-3xl">
        @foreach(\App\Models\IngresoEgreso::PAISES as $codigoPais => $nombrePais)
            @php
                $totales = $resumenPorPais[$codigoPais];
                $balancePais = $totales['ingresos'] - $totales['egresos'];
                $bandera = $codigoPais === 'usa' ? 'us.png' : 'mx.png';
            @endphp
            <a href="{{ route('ingresos-egresos.index', ['pais' => $codigoPais]) }}"
               class="group bg-gray-800/40 border border-gray-700/40 rounded-2xl p-6 flex flex-col items-center text-center hover:border-red-500/50 hover:bg-gray-800/70 transition-all">
                <img src="{{ asset('images/' . $bandera) }}" alt="{{ $nombrePais }}"
                     class="w-20 h-20 object-contain rounded-2xl border border-gray-700/50 bg-gray-900/40 mb-4 group-hover:scale-105 transition-transform"/>
                <p class="text-lg font-bold text-white">{{ $nombrePais }}</p>
                <div class="grid grid-cols-3 gap-3 mt-4 w-full">
                    <div>
                        <p class="text-[11px] text-gray-500 uppercase tracking-wider">Ingresos</p>
                        <p class="text-sm font-bold text-green-400 mt-0.5">${{ number_format($totales['ingresos'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-500 uppercase tracking-wider">Egresos</p>
                        <p class="text-sm font-bold text-red-400 mt-0.5">${{ number_format($totales['egresos'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-500 uppercase tracking-wider">Balance</p>
                        <p class="text-sm font-bold {{ $balancePais >= 0 ? 'text-white' : 'text-red-400' }} mt-0.5">${{ number_format($balancePais, 2) }}</p>
                    </div>
                </div>
                <span class="mt-5 inline-flex items-center gap-1.5 text-xs font-semibold text-red-400 group-hover:text-red-300 transition-colors">
                    Entrar
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
        @endforeach
    </div>

</x-app-layout>
