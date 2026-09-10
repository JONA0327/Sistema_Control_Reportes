<?php

use App\Http\Controllers\AnticipoController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ContratoHistoricoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DieselCargaController;
use App\Http\Controllers\IngresoEgresoController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InventoryPurchaseController;
use App\Http\Controllers\LiquidacionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViajeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('notificaciones/{notification}/ir', [NotificationController::class, 'redirect'])->name('notificaciones.ir');
    Route::post('notificaciones/leer-todas', [NotificationController::class, 'leerTodas'])->name('notificaciones.leer-todas');

    Route::resource('reports', ReportController::class)->only(['index', 'destroy'])
        ->middlewareFor('index', 'can:reportes.ver')
        ->middlewareFor('destroy', 'can:reportes.eliminar');
    Route::delete('reports/photos/{evidence}', [ReportController::class, 'destroyPhoto'])->name('reports.photos.destroy')->middleware('can:reportes.editar');

    Route::resource('users', UserController::class)->except(['show'])
        ->middlewareFor('index', 'can:usuarios.ver')
        ->middlewareFor(['create', 'store'], 'can:usuarios.crear')
        ->middlewareFor(['edit', 'update'], 'can:usuarios.editar')
        ->middlewareFor('destroy', 'can:usuarios.eliminar');

    Route::get('usuarios/permisos', [RolePermissionController::class, 'edit'])->name('roles.permisos.edit')->middleware('can:usuarios.editar');
    Route::put('usuarios/permisos', [RolePermissionController::class, 'update'])->name('roles.permisos.update')->middleware('can:usuarios.editar');

    Route::resource('buses', BusController::class)
        ->middlewareFor('index', 'can:unidades.ver')
        ->middlewareFor('show', 'can:unidades.ver')
        ->middlewareFor(['create', 'store'], 'can:unidades.crear')
        ->middlewareFor(['edit', 'update'], 'can:unidades.editar')
        ->middlewareFor('destroy', 'can:unidades.eliminar');

    Route::resource('viajes', ViajeController::class)->only(['edit', 'update', 'destroy'])
        ->middlewareFor(['edit', 'update'], 'can:viajes.editar')
        ->middlewareFor('destroy', 'can:viajes.eliminar');
    Route::get('viajes', [ViajeController::class, 'index'])->name('viajes.index')->middleware('can:viajes.ver');

    Route::get('ingresos-egresos/historico', [IngresoEgresoController::class, 'historico'])
        ->name('ingresos-egresos.historico')->middleware('can:egresos_ingresos.ver');
    Route::resource('ingresos-egresos', IngresoEgresoController::class)->parameters(['ingresos-egresos' => 'movimiento'])->except(['show'])
        ->middlewareFor('index', 'can:egresos_ingresos.ver')
        ->middlewareFor(['create', 'store'], 'can:egresos_ingresos.crear')
        ->middlewareFor(['edit', 'update'], 'can:egresos_ingresos.editar')
        ->middlewareFor('destroy', 'can:egresos_ingresos.eliminar');

    Route::middleware('can:contratos.ver')->group(function () {
        Route::get('contratos/{contrato}/pdf', [ContratoController::class, 'exportPdf'])->name('contratos.pdf');
    });
    Route::middleware('can:contratos.pagos')->group(function () {
        Route::post('contratos/{contrato}/pagos', [ContratoController::class, 'storePago'])->name('contratos.pagos.store');
        Route::delete('contratos/pagos/{pago}', [ContratoController::class, 'destroyPago'])->name('contratos.pagos.destroy');
    });
    Route::middleware('can:contratos.anticipos')->group(function () {
        Route::post('contratos/{contrato}/anticipos', [AnticipoController::class, 'store'])->name('contratos.anticipos.store');
        Route::put('contratos/{contrato}/anticipos/{anticipo}', [AnticipoController::class, 'update'])->name('contratos.anticipos.update');
        Route::patch('contratos/{contrato}/anticipos/{anticipo}/cancelar', [AnticipoController::class, 'cancelar'])->name('contratos.anticipos.cancelar');
        Route::get('contratos/{contrato}/anticipos/{anticipo}/comprobante', [AnticipoController::class, 'comprobantePdf'])->name('contratos.anticipos.comprobante');
        Route::get('contratos/{contrato}/anticipos/{anticipo}/evidencia', [AnticipoController::class, 'evidencia'])->name('contratos.anticipos.evidencia');
    });
    Route::resource('contratos', ContratoController::class)->except(['show'])
        ->middlewareFor('index', 'can:contratos.ver')
        ->middlewareFor(['create', 'store'], 'can:contratos.crear')
        ->middlewareFor(['edit', 'update'], 'can:contratos.editar')
        ->middlewareFor('destroy', 'can:contratos.eliminar');
    Route::resource('contratos-historicos', ContratoHistoricoController::class)->parameters(['contratos-historicos' => 'contrato'])->except(['show', 'create'])
        ->middlewareFor('index', 'can:contratos_historicos.ver')
        ->middlewareFor('update', 'can:contratos_historicos.editar')
        ->middlewareFor('destroy', 'can:contratos_historicos.eliminar');

    Route::middleware('can:reportes.editar')->group(function () {
        Route::resource('reports', ReportController::class)->only(['edit', 'update']);
    });

    Route::middleware('can:reportes.crear')->group(function () {
        Route::post('reports/transcribe', [ReportController::class, 'transcribe'])->name('reports.transcribe');
        Route::resource('reports', ReportController::class)->only(['create', 'store']);
    });

    Route::middleware('can:gastos.aprobar')->group(function () {
        Route::get('viajes/{viaje}/gastos', [DieselCargaController::class, 'showForViaje'])->name('viajes.gastos');
        Route::post('viajes/{viaje}/gastos/diesel-extra', [DieselCargaController::class, 'storeExtraByAdmin'])->name('viajes.gastos.diesel-extra.store');
        Route::patch('gastos/diesel/{dieselCarga}/aprobar-solicitud', [DieselCargaController::class, 'aprobarSolicitud'])->name('gastos.diesel.aprobar-solicitud');
        Route::patch('gastos/diesel/{dieselCarga}/rechazar-solicitud', [DieselCargaController::class, 'rechazarSolicitud'])->name('gastos.diesel.rechazar-solicitud');
        Route::patch('gastos/diesel/{dieselCarga}/aprobar-evidencia', [DieselCargaController::class, 'aprobarEvidencia'])->name('gastos.diesel.aprobar-evidencia');
        Route::patch('gastos/diesel/{dieselCarga}/rechazar-evidencia', [DieselCargaController::class, 'rechazarEvidencia'])->name('gastos.diesel.rechazar-evidencia');
        Route::patch('liquidacion-gastos/{liquidacionGasto}/aceptar', [LiquidacionController::class, 'aceptarGasto'])->name('liquidacion.gastos.aceptar');
        Route::patch('liquidacion-gastos/{liquidacionGasto}/rechazar', [LiquidacionController::class, 'rechazarGasto'])->name('liquidacion.gastos.rechazar');
        Route::get('liquidaciones/{liquidacion}/pdf', [LiquidacionController::class, 'exportPdf'])->name('liquidacion.pdf');
    });

    Route::middleware('can:gastos.ver')->group(function () {
        Route::get('gastos', [DieselCargaController::class, 'index'])->name('gastos.index');
    });
    Route::middleware('can:gastos.registrar')->group(function () {
        Route::post('gastos/viajes/{viaje}/diesel-extra', [DieselCargaController::class, 'storeExtraByOperador'])->name('gastos.diesel-extra.store');
        Route::post('gastos/diesel/{dieselCarga}/evidencia', [DieselCargaController::class, 'storeEvidencia'])->name('gastos.diesel.evidencia.store');
        Route::patch('liquidaciones/{liquidacion}/km', [LiquidacionController::class, 'updateKm'])->name('liquidacion.km.update');
        Route::post('liquidaciones/{liquidacion}/gastos', [LiquidacionController::class, 'storeGasto'])->name('liquidacion.gastos.store');
        Route::delete('liquidacion-gastos/{liquidacionGasto}', [LiquidacionController::class, 'destroyGasto'])->name('liquidacion.gastos.destroy');
        Route::patch('liquidaciones/{liquidacion}/cerrar', [LiquidacionController::class, 'cerrar'])->name('liquidacion.cerrar');
    });

    Route::middleware('can:inventario.movimientos')->group(function () {
        Route::post('inventario/{item}/movimientos', [InventoryItemController::class, 'storeMovement'])->name('inventario.movimientos.store');
        Route::patch('inventario/movimientos/{movimiento}/devuelto', [InventoryItemController::class, 'marcarDevuelto'])->name('inventario.movimientos.devuelto');
        Route::post('inventario/{item}/compras', [InventoryPurchaseController::class, 'store'])->name('inventario.compras.store');
    });
    Route::middleware('can:inventario.aprobar')->group(function () {
        Route::get('inventario/compras', [InventoryPurchaseController::class, 'index'])->name('inventario.compras.index');
        Route::patch('inventario/compras/{compra}/aprobar', [InventoryPurchaseController::class, 'aprobar'])->name('inventario.compras.aprobar');
        Route::patch('inventario/compras/{compra}/rechazar', [InventoryPurchaseController::class, 'rechazar'])->name('inventario.compras.rechazar');
    });
    Route::resource('inventario', InventoryItemController::class)->parameters(['inventario' => 'item'])->except(['show'])
        ->middlewareFor('index', 'can:inventario.ver')
        ->middlewareFor('edit', 'can:inventario.ver')
        ->middlewareFor(['create', 'store'], 'can:inventario.crear')
        ->middlewareFor('update', 'can:inventario.editar')
        ->middlewareFor('destroy', 'can:inventario.eliminar');

    Route::middleware('can:ordenes_trabajo.gestionar')->group(function () {
        Route::get('reports/{report}/orden-trabajo', [OrdenTrabajoController::class, 'show'])->name('reports.orden.show');
        Route::put('reports/{report}/orden-trabajo', [OrdenTrabajoController::class, 'update'])->name('reports.orden.update');
        Route::patch('reports/{report}/orden-trabajo/completar', [OrdenTrabajoController::class, 'completar'])->name('reports.orden.completar');
        Route::get('reports/{report}/orden-trabajo/pdf', [OrdenTrabajoController::class, 'exportPdf'])->name('reports.orden.pdf');
        Route::post('reports/{report}/orden-trabajo/partes', [OrdenTrabajoController::class, 'storeParte'])->name('reports.orden.partes.store');
        Route::delete('orden-trabajo/partes/{parte}', [OrdenTrabajoController::class, 'destroyParte'])->name('reports.orden.partes.destroy');
    });

    // Piezas y evidencia: taller interno (mecánico/admin) y taller externo (mecánico externo, solo su propia orden).
    Route::middleware('can:ordenes_trabajo.piezas')->group(function () {
        Route::post('reports/{report}/orden-trabajo/piezas', [OrdenTrabajoController::class, 'storePieza'])->name('reports.orden.piezas.store');
        Route::delete('orden-trabajo/piezas/{pieza}', [OrdenTrabajoController::class, 'destroyPieza'])->name('reports.orden.piezas.destroy');
    });

    Route::middleware('can:taller_externo.ver')->group(function () {
        Route::get('mi-taller', [OrdenTrabajoController::class, 'externoIndex'])->name('reports.orden.externo.index');
        Route::get('mi-taller/{report}', [OrdenTrabajoController::class, 'externoShow'])->name('reports.orden.externo.show');
    });
});

require __DIR__.'/auth.php';
