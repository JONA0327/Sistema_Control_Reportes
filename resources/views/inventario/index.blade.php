<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Inventario</span>
        </div>
    </x-slot>

    {{-- Encabezado --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Inventario</h1>
            <p class="text-sm text-gray-500 mt-0.5">Catálogo de refacciones y control de stock</p>
        </div>
        <div class="flex items-center gap-2">
            @can('inventario.aprobar')
            @php $comprasPendientes = \App\Models\InventoryPurchase::where('estado', 'pendiente')->count(); @endphp
            <a href="{{ route('inventario.compras.index') }}"
               class="relative inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border transition-colors
                      {{ $comprasPendientes > 0 ? 'bg-amber-500/10 border-amber-500/30 text-amber-300 hover:bg-amber-500/20' : 'bg-gray-800/60 border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Compras pendientes
                @if($comprasPendientes > 0)
                    <span class="text-xs px-1.5 py-0.5 rounded-full bg-amber-500/20">{{ $comprasPendientes }}</span>
                @endif
            </a>
            @endcan
            @can('inventario.crear')
            <a href="{{ route('inventario.create') }}"
               class="brand-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva refacción
            </a>
            @endcan
        </div>
    </div>

    {{-- Alerta --}}
    @if (session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @error('delete')
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-900/30 border border-red-700/50 rounded-xl text-red-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </div>
    @enderror

    {{-- Tarjeta principal --}}
    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl overflow-hidden">

        {{-- Barra de búsqueda --}}
        <div class="px-5 py-4 border-b border-gray-700/40 flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" action="{{ route('inventario.index') }}" class="flex-1 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Buscar por código, nombre o categoría..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-gray-700/60 border border-gray-600/40 text-gray-300 text-sm rounded-xl hover:bg-gray-700 transition-colors">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('inventario.index') }}"
                       class="px-4 py-2 bg-gray-700/30 border border-gray-600/30 text-gray-500 text-sm rounded-xl hover:text-gray-300 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>
            <p class="text-xs text-gray-600">{{ $items->total() }} refacción(es)</p>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700/40">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Código</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Nombre</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3 hidden md:table-cell">Categoría</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Stock</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-700/20 transition-colors">
                        <td class="px-5 py-3">
                            <span class="text-sm font-mono font-medium text-gray-200">{{ $item->code }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($item->photo_path)
                                    <img src="{{ Storage::url($item->photo_path) }}"
                                         alt="{{ $item->name }}"
                                         class="w-10 h-10 rounded-lg object-cover border border-gray-700/50 flex-shrink-0"/>
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-700/60 border border-gray-600/40 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm text-white truncate">{{ $item->name }}</p>
                                    @if($item->description)
                                        <p class="text-xs text-gray-500 truncate max-w-xs">{{ $item->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell">
                            <span class="text-sm text-gray-400">{{ $item->category }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg border font-medium
                                {{ $item->low_stock ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-green-500/10 text-green-400 border-green-500/20' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->low_stock ? 'bg-red-400' : 'bg-green-400' }}"></span>
                                {{ $item->stock_quantity }} @if($item->low_stock) · bajo stock @endif
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('inventario.edit', $item) }}"
                                   class="p-2 rounded-lg text-gray-500 hover:text-white hover:bg-gray-700 transition-all"
                                   title="Editar / movimientos">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @can('inventario.eliminar')
                                <form method="POST" action="{{ route('inventario.destroy', $item) }}"
                                      onsubmit="return confirm('¿Eliminar la refacción {{ $item->code }}? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all"
                                            title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-800 border border-gray-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">No se encontraron refacciones</p>
                                @if($search)
                                    <a href="{{ route('inventario.index') }}" class="text-xs text-red-500 hover:text-red-400">Limpiar búsqueda</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($items->hasPages())
            <div class="px-5 py-4 border-t border-gray-700/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-600">
                    Mostrando {{ $items->firstItem() }}–{{ $items->lastItem() }} de {{ $items->total() }}
                </p>
                <div class="flex items-center flex-wrap gap-1 justify-center sm:justify-end">
                    @if($items->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">&lsaquo;</span>
                    @else
                        <a href="{{ $items->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">&lsaquo;</a>
                    @endif
                    @foreach($items->getUrlRange(max(1, $items->currentPage()-2), min($items->lastPage(), $items->currentPage()+2)) as $page => $url)
                        @if($page == $items->currentPage())
                            <span class="brand-gradient px-3 py-1.5 text-xs text-white rounded-lg font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-400 bg-gray-800/40 border border-gray-700/40 rounded-lg hover:bg-gray-700 transition-colors">&rsaquo;</a>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-700 bg-gray-800/40 border border-gray-700/40 rounded-lg cursor-not-allowed">&rsaquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
