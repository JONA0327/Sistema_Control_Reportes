<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span>Panel</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('contratos.index') }}" class="hover:text-gray-300 transition-colors">Contratos</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">{{ $contrato->folio }}</span>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('contratos.index') }}"
               class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Editar contrato {{ $contrato->folio }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $contrato->cliente_nombre }}</p>
            </div>
        </div>
        <div class="relative" x-data="{ pdfOpen: false }" @click.outside="pdfOpen = false">
            <button type="button" @click="pdfOpen = !pdfOpen"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800/60 border border-gray-700/50 text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-700 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ver PDF
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="pdfOpen" x-cloak x-transition
                 class="absolute right-0 mt-2 w-44 bg-gray-800 border border-gray-700/60 rounded-xl shadow-lg overflow-hidden z-20">
                <a href="{{ route('contratos.pdf', $contrato) }}?tamano=oficio" target="_blank"
                   class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    Tamaño oficio
                </a>
                <a href="{{ route('contratos.pdf', $contrato) }}?tamano=carta" target="_blank"
                   class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    Tamaño carta
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 max-w-3xl flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-700/50 rounded-xl text-green-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 max-w-3xl flex items-start gap-3 px-4 py-3 bg-red-900/30 border border-red-700/50 rounded-xl text-red-300 text-sm">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p>No se pudo guardar. Hay {{ $errors->count() }} {{ $errors->count() === 1 ? 'error' : 'errores' }} en el formulario:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('contratos.update', $contrato) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('contratos._form')

            <div class="bg-gray-800/40 border border-gray-700/40 border-t-0 rounded-b-2xl px-6 py-4 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3">
                <a href="{{ route('contratos.index') }}"
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
