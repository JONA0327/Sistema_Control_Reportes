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
            <span class="text-white">Histórico</span>
        </div>
    </x-slot>

    {{-- Encabezado --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('ingresos-egresos.index', ['pais' => $pais]) }}"
               class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-800/60 border border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600 transition-all flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <img src="{{ asset('images/' . ($pais === 'usa' ? 'us.png' : 'mx.png')) }}" alt="{{ \App\Models\IngresoEgreso::PAISES[$pais] }}"
                 class="w-10 h-10 object-contain rounded-xl border border-gray-700/50 bg-gray-900/40"/>
            <div>
                <h1 class="text-2xl font-bold text-white">Histórico · {{ \App\Models\IngresoEgreso::PAISES[$pais] }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Ingresos y egresos a lo largo del tiempo</p>
            </div>
        </div>
    </div>

    {{-- Filtros de periodo --}}
    <form method="GET" action="{{ route('ingresos-egresos.historico') }}" id="historico-form">
        <input type="hidden" name="pais" value="{{ $pais }}"/>
        <div class="flex flex-wrap items-center gap-2 mb-5">
            @foreach(['semana' => 'Semana', 'mes' => 'Mes', 'anio' => 'Año', 'todos' => 'Todos los años'] as $key => $label)
                <button type="submit" name="periodo" value="{{ $key }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium border transition-colors
                               {{ $periodo === $key ? 'brand-gradient text-white border-transparent shadow-lg shadow-red-950/40' : 'bg-gray-800/60 border-gray-700/50 text-gray-400 hover:text-white hover:border-gray-600' }}">
                    {{ $label }}
                </button>
            @endforeach

            <div class="ml-0 sm:ml-2">
                @if($periodo === 'semana')
                    <input type="week" name="semana" value="{{ $semana }}" onchange="document.getElementById('historico-form').submit()"
                           class="px-3.5 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @elseif($periodo === 'mes')
                    <input type="month" name="mes" value="{{ $mes }}" onchange="document.getElementById('historico-form').submit()"
                           class="px-3.5 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @elseif($periodo === 'anio')
                    <input type="number" name="anio" value="{{ $anio }}" min="2020" max="2100" onchange="document.getElementById('historico-form').submit()"
                           class="w-28 px-3.5 py-2 bg-gray-900/80 border border-gray-700 rounded-xl text-white text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors"/>
                @endif
            </div>
        </div>
    </form>

    {{-- Tarjetas resumen del periodo --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Ingresos del periodo</p>
            <p class="text-2xl font-bold text-green-400 mt-1">${{ number_format($totalIngresos, 2) }}</p>
        </div>
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Egresos del periodo</p>
            <p class="text-2xl font-bold text-red-400 mt-1">${{ number_format($totalEgresos, 2) }}</p>
        </div>
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Balance</p>
            <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-white' : 'text-red-400' }} mt-1">${{ number_format($balance, 2) }}</p>
        </div>
    </div>

    {{-- Gráfica: ingresos vs egresos --}}
    <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5 mb-5">
        <h2 class="text-sm font-semibold text-white mb-1">Ingresos vs egresos</h2>
        <p class="text-xs text-gray-600 mb-4">
            @if($periodo === 'semana') Por día de la semana
            @elseif($periodo === 'mes') Por día del mes
            @elseif($periodo === 'anio') Por mes del año
            @else Por año @endif
        </p>
        @if($movimientosCount > 0)
            <div style="height: 300px;">
                <canvas id="chart-tendencia"></canvas>
            </div>
        @else
            <div class="py-16 text-center">
                <p class="text-sm text-gray-500">No hay movimientos registrados en este periodo.</p>
            </div>
        @endif
    </div>

    {{-- Gráfica: egresos por categoría --}}
    @if($categoriasEgresos->isNotEmpty())
        <div class="bg-gray-800/40 border border-gray-700/40 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-white mb-1">Egresos por categoría</h2>
            <p class="text-xs text-gray-600 mb-4">En qué se fue el dinero durante este periodo</p>
            <div style="height: {{ max(180, $categoriasEgresos->count() * 42) }}px;">
                <canvas id="chart-categorias"></canvas>
            </div>
        </div>
    @endif

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.1/chart.umd.min.js"></script>
<script>
(function () {
    const labels = @json($chartLabels);
    const ingresos = @json($chartIngresos);
    const egresos = @json($chartEgresos);
    const categoriaLabels = @json($categoriasEgresos->keys());
    const categoriaValores = @json($categoriasEgresos->values());

    const moneda = (v) => '$' + Number(v).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const gridColor = 'rgba(255,255,255,0.06)';
    const tickColor = '#9ca3af';
    const colorIngreso = '#0ca30c';
    const colorEgreso = '#d03b3b';
    const categoricos = ['#3987e5', '#d95926', '#199e70', '#c98500', '#d55181', '#008300', '#9085e9', '#e66767'];
    const colorOtros = '#6b7280';

    const tendenciaCanvas = document.getElementById('chart-tendencia');
    if (tendenciaCanvas) {
        new Chart(tendenciaCanvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Ingresos', data: ingresos, backgroundColor: colorIngreso, borderRadius: 4, maxBarThickness: 22 },
                    { label: 'Egresos', data: egresos, backgroundColor: colorEgreso, borderRadius: 4, maxBarThickness: 22 },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { grid: { display: false }, ticks: { color: tickColor, font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { color: tickColor, callback: (v) => moneda(v) } },
                },
                plugins: {
                    legend: { position: 'top', align: 'start', labels: { color: '#e5e7eb', usePointStyle: true, pointStyle: 'rect', boxWidth: 10, boxHeight: 10, padding: 16 } },
                    tooltip: {
                        backgroundColor: '#111827', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1,
                        titleColor: '#f9fafb', bodyColor: '#e5e7eb', padding: 10,
                        callbacks: { label: (ctx) => ctx.dataset.label + ': ' + moneda(ctx.parsed.y) },
                    },
                },
            },
        });
    }

    const categoriasCanvas = document.getElementById('chart-categorias');
    if (categoriasCanvas) {
        new Chart(categoriasCanvas, {
            type: 'bar',
            data: {
                labels: categoriaLabels,
                datasets: [{
                    data: categoriaValores,
                    backgroundColor: categoriaLabels.map((l, i) => l === 'Otros' ? colorOtros : categoricos[i % categoricos.length]),
                    borderRadius: 4,
                    maxBarThickness: 22,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { color: tickColor, callback: (v) => moneda(v) } },
                    y: { grid: { display: false }, ticks: { color: '#e5e7eb', font: { size: 11 } } },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1,
                        titleColor: '#f9fafb', bodyColor: '#e5e7eb', padding: 10,
                        callbacks: { label: (ctx) => moneda(ctx.parsed.x) },
                    },
                },
            },
        });
    }
})();
</script>
@endpush

</x-app-layout>
