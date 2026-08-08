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
            <span class="text-white">Nueva refacción</span>
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
            <h1 class="text-2xl font-bold text-white">Nueva refacción</h1>
            <p class="text-sm text-gray-500 mt-0.5">Registra un artículo en el catálogo de inventario</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('inventario.store') }}" enctype="multipart/form-data">
            @csrf

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

                        {{-- Código --}}
                        <div>
                            <label for="code" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Código / N° de parte <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="code" name="code" value="{{ old('code') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors font-mono"
                                   placeholder="Ej: MANG-NEUM-38"/>
                            @error('code')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nombre --}}
                        <div>
                            <label for="name" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Ej: Manguera para aire alta presión 3/8&quot;"/>
                            @error('name')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Categoría --}}
                        <div>
                            <label for="category" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Categoría <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="category" name="category" value="{{ old('category') }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('category') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"
                                   placeholder="Ej: Neumático / Frenos"/>
                            @error('category')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Descripción <span class="text-gray-600 font-normal">(opcional)</span>
                            </label>
                            <textarea id="description" name="description" rows="2"
                                      class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm placeholder-gray-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors resize-none">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stock inicial --}}
                        <div>
                            <label for="stock_quantity" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Stock inicial <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="{{ old('stock_quantity', 0) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('stock_quantity') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                            @error('stock_quantity')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stock mínimo --}}
                        <div>
                            <label for="min_stock" class="block text-xs font-medium text-gray-400 mb-1.5">
                                Stock mínimo <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="min_stock" name="min_stock" min="0" value="{{ old('min_stock', 0) }}"
                                   class="w-full px-3.5 py-2.5 bg-gray-900/80 border {{ $errors->has('min_stock') ? 'border-red-500' : 'border-gray-700' }} rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                            @error('min_stock')
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
                        <span class="text-xs text-gray-600 font-normal">(opcional)</span>
                    </h2>

                    <div x-data="{ preview: null }" class="flex items-start gap-5">
                        <div class="flex-shrink-0">
                            <div class="w-24 h-20 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden flex items-center justify-center bg-gray-900/60">
                                <img x-show="preview" :src="preview" class="w-full h-full object-cover" alt="Preview"/>
                                <svg x-show="!preview" class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="foto"
                                   class="flex flex-col items-center justify-center w-full h-24 border border-dashed border-gray-700 rounded-xl cursor-pointer bg-gray-900/40 hover:bg-gray-900/60 hover:border-gray-600 transition-all">
                                <svg class="w-6 h-6 text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span class="text-xs text-gray-500">Clic para subir foto</span>
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

                {{-- Acciones --}}
                <div class="px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                    <a href="{{ route('inventario.index') }}"
                       class="w-full sm:w-auto text-center px-5 py-2.5 bg-gray-700/50 border border-gray-600/40 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto btn-login-gradient px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-950/40">
                        Registrar refacción
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>
