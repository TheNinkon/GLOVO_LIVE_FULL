<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Rider\MetricsController as AdminRiderMetricsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas de la API de tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider y todas ellas serán
| asignadas al grupo de middleware "api".
|
*/

// Rutas de la API para el panel de administración
Route::group(['middleware' => 'auth:web', 'prefix' => 'admin/api', 'as' => 'admin.api.'], function () {
    // Endpoints para las métricas detalladas de un Rider
    Route::get('riders/{id}/metrics/kpis', [AdminRiderMetricsController::class, 'kpis'])->name('riders.metrics.kpis');
    Route::get('riders/{id}/metrics/list', [AdminRiderMetricsController::class, 'list'])->name('riders.metrics.list');

    // Aquí puedes incluir otras rutas de la API del administrador.
});

// Rutas de la API para el perfil del Rider autenticado
Route::group(['middleware' => 'auth:rider', 'prefix' => 'rider/api', 'as' => 'rider.api.'], function () {
    // Endpoints para las métricas del Rider autenticado
    Route::get('metrics/kpis', [App\Http\Controllers\Rider\RiderMetricsController::class, 'kpis'])->name('metrics.kpis');
    Route::get('metrics/list', [App\Http\Controllers\Rider\RiderMetricsController::class, 'list'])->name('metrics.list');
});
