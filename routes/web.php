<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DieselCargaController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\LiquidacionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
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

    Route::resource('reports', ReportController::class)->only(['index', 'destroy']);
    Route::delete('reports/photos/{evidence}', [ReportController::class, 'destroyPhoto'])->name('reports.photos.destroy');

    Route::get('notificaciones/{notification}/ir', [NotificationController::class, 'redirect'])->name('notificaciones.ir');
    Route::post('notificaciones/leer-todas', [NotificationController::class, 'leerTodas'])->name('notificaciones.leer-todas');

    Route::middleware('role:administrador|administracion')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('buses', BusController::class);
        Route::resource('viajes', ViajeController::class)->except(['show', 'index']);
    });

    Route::middleware('role:administrador|administracion|operador')->group(function () {
        Route::get('viajes', [ViajeController::class, 'index'])->name('viajes.index');
        Route::resource('reports', ReportController::class)->only(['edit', 'update']);
    });

    Route::middleware('role:operador|mecanico')->group(function () {
        Route::post('reports/transcribe', [ReportController::class, 'transcribe'])->name('reports.transcribe');
        Route::resource('reports', ReportController::class)->only(['create', 'store']);
    });

    Route::middleware('role:administrador|administracion')->group(function () {
        Route::get('viajes/{viaje}/gastos', [DieselCargaController::class, 'showForViaje'])->name('viajes.gastos');
        Route::post('viajes/{viaje}/gastos/diesel-extra', [DieselCargaController::class, 'storeExtraByAdmin'])->name('viajes.gastos.diesel-extra.store');
        Route::patch('gastos/diesel/{dieselCarga}/aprobar-solicitud', [DieselCargaController::class, 'aprobarSolicitud'])->name('gastos.diesel.aprobar-solicitud');
        Route::patch('gastos/diesel/{dieselCarga}/rechazar-solicitud', [DieselCargaController::class, 'rechazarSolicitud'])->name('gastos.diesel.rechazar-solicitud');
        Route::patch('gastos/diesel/{dieselCarga}/aprobar-evidencia', [DieselCargaController::class, 'aprobarEvidencia'])->name('gastos.diesel.aprobar-evidencia');
        Route::patch('gastos/diesel/{dieselCarga}/rechazar-evidencia', [DieselCargaController::class, 'rechazarEvidencia'])->name('gastos.diesel.rechazar-evidencia');
        Route::patch('liquidacion-gastos/{liquidacionGasto}/aceptar', [LiquidacionController::class, 'aceptarGasto'])->name('liquidacion.gastos.aceptar');
        Route::patch('liquidacion-gastos/{liquidacionGasto}/rechazar', [LiquidacionController::class, 'rechazarGasto'])->name('liquidacion.gastos.rechazar');
    });

    Route::middleware('role:operador')->group(function () {
        Route::get('gastos', [DieselCargaController::class, 'index'])->name('gastos.index');
        Route::post('gastos/viajes/{viaje}/diesel-extra', [DieselCargaController::class, 'storeExtraByOperador'])->name('gastos.diesel-extra.store');
        Route::post('gastos/diesel/{dieselCarga}/evidencia', [DieselCargaController::class, 'storeEvidencia'])->name('gastos.diesel.evidencia.store');
        Route::patch('liquidaciones/{liquidacion}/km', [LiquidacionController::class, 'updateKm'])->name('liquidacion.km.update');
        Route::post('liquidaciones/{liquidacion}/gastos', [LiquidacionController::class, 'storeGasto'])->name('liquidacion.gastos.store');
        Route::delete('liquidacion-gastos/{liquidacionGasto}', [LiquidacionController::class, 'destroyGasto'])->name('liquidacion.gastos.destroy');
        Route::patch('liquidaciones/{liquidacion}/cerrar', [LiquidacionController::class, 'cerrar'])->name('liquidacion.cerrar');
    });

    Route::middleware('role:administrador|administracion')->group(function () {
        Route::post('inventario/{item}/movimientos', [InventoryItemController::class, 'storeMovement'])->name('inventario.movimientos.store');
        Route::patch('inventario/movimientos/{movimiento}/devuelto', [InventoryItemController::class, 'marcarDevuelto'])->name('inventario.movimientos.devuelto');
        Route::resource('inventario', InventoryItemController::class)->parameters(['inventario' => 'item'])->except(['show']);
    });

    Route::middleware('role:administrador|administracion|mecanico')->group(function () {
        Route::get('reports/{report}/orden-trabajo', [OrdenTrabajoController::class, 'show'])->name('reports.orden.show');
        Route::put('reports/{report}/orden-trabajo', [OrdenTrabajoController::class, 'update'])->name('reports.orden.update');
        Route::patch('reports/{report}/orden-trabajo/completar', [OrdenTrabajoController::class, 'completar'])->name('reports.orden.completar');
        Route::get('reports/{report}/orden-trabajo/pdf', [OrdenTrabajoController::class, 'exportPdf'])->name('reports.orden.pdf');
        Route::post('reports/{report}/orden-trabajo/partes', [OrdenTrabajoController::class, 'storeParte'])->name('reports.orden.partes.store');
        Route::delete('orden-trabajo/partes/{parte}', [OrdenTrabajoController::class, 'destroyParte'])->name('reports.orden.partes.destroy');
    });
});

require __DIR__.'/auth.php';
