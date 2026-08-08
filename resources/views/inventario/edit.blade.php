<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('inventario.index') }}" class="hover:text-gray-300 transition-colors">Inventario</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">{{ $item->code }}</span>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('inventario.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-white">{{ $item->name }}</h1>
            <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $item->code }}</p>
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

        {{-- Datos de la refacción --}}
        <form method="POST" action="{{ route('inventario.update', $item) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </span>
                        Datos de la refacción
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="code" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Código / N° de parte <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="code" name="code" value="{{ old('code', $item->code) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors font-mono"/>
                            @error('code')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                            @error('name')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Categoría <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="category" name="category" value="{{ old('category', $item->category) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('category') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                            @error('category')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="min_stock" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Stock mínimo <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="min_stock" name="min_stock" min="0" value="{{ old('min_stock', $item->min_stock) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('min_stock') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                            @error('min_stock')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Descripción <span class="text-gray-600 font-normal">(opcional)</span>
                            </label>
                            <textarea id="description" name="description" rows="2"
                                      class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors resize-none">{{ old('description', $item->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Foto de la refacción --}}
                <div class="px-6 py-5">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        Foto de la refacción
                        <span class="text-xs text-gray-600 font-normal">(dejar vacío para mantener la actual)</span>
                    </h2>

                    <div x-data="{ preview: null }" class="flex items-start gap-5">
                        <div class="flex-shrink-0">
                            <div class="w-24 h-20 rounded-2xl border-2 border-gray-700 overflow-hidden flex items-center justify-center bg-gray-900/60">
                                <img x-show="preview" :src="preview" class="w-full h-full object-cover" alt="Preview"/>
                                @if($item->photo_path)
                                    <img x-show="!preview" src="{{ Storage::url($item->photo_path) }}"
                                         class="w-full h-full object-cover" alt="{{ $item->name }}"/>
                                @else
                                    <svg x-show="!preview" class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="foto"
                                   class="flex flex-col items-center justify-center w-full h-24 border border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-gray-600 transition-all">
                                <svg class="w-6 h-6 text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span class="text-xs text-gray-500">Clic para cambiar foto</span>
                                <span class="text-xs text-gray-700 mt-0.5">PNG, JPG hasta 2MB</span>
                            </label>
                            <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"/>
                            @error('foto')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 flex items-center justify-end gap-3">
                    <button type="submit"
                            class="btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </form>

        {{-- Stock actual + registrar movimiento --}}
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl divide-y divide-gray-700/40">
            <div class="px-6 py-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                        <span class="w-5 h-5 brand-gradient rounded-md flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </span>
                        Stock actual
                    </h2>
                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg border font-medium
                        {{ $item->low_stock ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-green-500/10 text-green-400 border-green-500/20' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $item->low_stock ? 'bg-red-400' : 'bg-green-400' }}"></span>
                        {{ $item->stock_quantity }} unidades @if($item->low_stock) · bajo el mínimo ({{ $item->min_stock }}) @endif
                    </span>
                </div>

                <form method="POST" action="{{ route('inventario.movimientos.store', $item) }}"
                      x-data="{ movementType: '{{ old('movement_type', 'entrada') }}', bajoResponsabilidad: {{ old('responsable_id') ? 'true' : 'false' }} }"
                      class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Tipo</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="movement_type" value="entrada" x-model="movementType" class="sr-only peer" checked>
                                <div class="px-3 py-2 rounded-lg border border-gray-700 bg-gray-900/40 peer-checked:border-green-500 peer-checked:bg-green-600/10 transition-all text-center">
                                    <span class="text-xs text-gray-300">Entrada</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="movement_type" value="salida" x-model="movementType" class="sr-only peer">
                                <div class="px-3 py-2 rounded-lg border border-gray-700 bg-gray-900/40 peer-checked:border-red-500 peer-checked:bg-red-600/10 transition-all text-center">
                                    <span class="text-xs text-gray-300">Salida</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="quantity" class="block text-xs font-medium text-gray-400 mb-1.5">Cantidad</label>
                        <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity') }}"
                               class="w-28 px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('quantity') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                    </div>
                    <div class="flex-1 min-w-40">
                        <label for="notes" class="block text-xs font-medium text-gray-400 mb-1.5">Notas <span class="text-gray-600 font-normal">(opcional)</span></label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}"
                               class="w-full px-3.5 py-2.5 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                               placeholder="Ej: Usado en reparación, compra externa..."/>
                    </div>

                    <div x-show="movementType === 'salida'" x-cloak class="w-full">
                        @if($item->is_herramienta)
                            <label for="responsable_id" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Responsable <span class="text-red-500">*</span>
                                <span class="text-gray-600 font-normal">(esta categoría es Herramienta, siempre queda bajo responsabilidad de alguien)</span>
                            </label>
                            <select id="responsable_id" name="responsable_id"
                                    class="w-full sm:w-72 px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('responsable_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                <option value="" class="bg-gray-900">— Seleccionar usuario —</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}" class="bg-gray-900" {{ old('responsable_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} {{ $u->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" x-model="bajoResponsabilidad" class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-500 focus:ring-offset-gray-800"/>
                                <span class="text-xs text-gray-400">¿Queda bajo la responsabilidad de un usuario?</span>
                            </label>
                            <div x-show="bajoResponsabilidad" x-cloak class="mt-2">
                                <select name="responsable_id"
                                        class="w-full sm:w-72 px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('responsable_id') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                                    <option value="" class="bg-gray-900">— Seleccionar usuario —</option>
                                    @foreach($usuarios as $u)
                                        <option value="{{ $u->id }}" class="bg-gray-900" {{ old('responsable_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} {{ $u->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @error('responsable_id')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="px-4 py-2.5 brand-gradient text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40 hover:opacity-90 transition-opacity">
                        Registrar
                    </button>
                </form>
                @error('quantity')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Movimientos recientes --}}
            <div class="px-6 py-5">
                <h2 class="text-sm font-semibold text-white mb-4">Movimientos recientes</h2>
                @if($movements->isEmpty())
                    <p class="text-xs text-gray-600">Sin movimientos registrados todavía.</p>
                @else
                    <div class="space-y-2">
                        @foreach($movements as $mov)
                        <div class="px-3.5 py-2.5 bg-gray-900/40 border border-gray-700/40 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-lg border font-medium
                                        {{ $mov->movement_type === 'entrada' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20' }}">
                                        {{ $mov->movement_type === 'entrada' ? '+' : '-' }}{{ $mov->quantity }}
                                    </span>
                                    <div>
                                        <p class="text-xs text-gray-300">{{ $mov->user->name }} {{ $mov->user->last_name }}</p>
                                        @if($mov->notes)
                                            <p class="text-xs text-gray-600">{{ $mov->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $mov->movement_date?->format('d/m/Y H:i') }}</span>
                            </div>

                            @if($mov->responsable)
                                <div class="mt-2 pt-2 border-t border-gray-700/30 flex items-center justify-between gap-2 flex-wrap">
                                    <p class="text-xs text-gray-500">
                                        Bajo responsabilidad de <span class="text-gray-300">{{ $mov->responsable->name }} {{ $mov->responsable->last_name }}</span>
                                    </p>
                                    @if($mov->pendiente)
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-lg border font-medium bg-amber-500/10 text-amber-400 border-amber-500/20">
                                                Pendiente
                                            </span>
                                            <form method="POST" action="{{ route('inventario.movimientos.devuelto', $mov) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs px-2.5 py-1 bg-green-600/20 border border-green-600/40 text-green-400 rounded-lg hover:bg-green-600/30 transition-colors">
                                                    Marcar como devuelto
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-lg border font-medium bg-green-500/10 text-green-400 border-green-500/20">
                                            Devuelto {{ $mov->devuelto_at?->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
