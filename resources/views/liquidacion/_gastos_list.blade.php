@if($liquidacion->gastos->isEmpty())
    <p class="text-xs text-gray-600">Aún no se han registrado gastos.</p>
@else
    <div class="space-y-3">
        @foreach($liquidacion->gastos as $gasto)
            @include('liquidacion._gasto_item', ['gasto' => $gasto, 'context' => $context ?? 'operador'])
        @endforeach
    </div>
@endif
