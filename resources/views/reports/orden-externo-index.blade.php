<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Mis órdenes</span>
        </div>
    </x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Mis órdenes de trabajo</h1>
        <p class="text-sm text-gray-500 mt-0.5">Unidades canalizadas a tu taller. Sube la evidencia de las piezas que cambies.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm max-w-2xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden max-w-3xl">
        <div class="divide-y divide-gray-700/40">
            @forelse($reports as $report)
                <a href="{{ route('reports.orden.externo.show', $report) }}"
                   class="flex items-center justify-between gap-3 px-5 py-4 hover:bg-gray-700/20 transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white">{{ $report->folio }} — Bus #{{ $report->bus->num_bus }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @empty
                <div class="px-5 py-16 text-center">
                    <p class="text-sm text-gray-500">Todavía no tienes órdenes de trabajo asignadas.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($reports->hasPages())
        <div class="max-w-3xl mt-4">
            {{ $reports->links() }}
        </div>
    @endif

</x-app-layout>
