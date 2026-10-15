<?php

use App\Http\Controllers\Api\MerloCorteController;
use App\Http\Middleware\MerloApiToken;
use Illuminate\Support\Facades\Route;

Route::middleware(MerloApiToken::class)->prefix('merlo')->group(function () {
    Route::get('contratos-compras', [MerloCorteController::class, 'contratosCompras']);
    Route::post('cortes', [MerloCorteController::class, 'store']);
    Route::post('cortes-paqueteria', [MerloCorteController::class, 'storePaqueteria']);
});
