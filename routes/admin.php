<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RiderController as AdminRiderController;
use App\Http\Controllers\Admin\Rider\MetricsController as AdminRiderMetricsController;
use App\Http\Controllers\Admin\Rider\Schedule\ScheduleController as AdminRiderScheduleController;
use App\Http\Controllers\Admin\ForecastController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\CoverageController;
use App\Http\Controllers\Admin\RiderStatusController;
use App\Http\Controllers\Admin\PrefacturaController;
use App\Http\Controllers\Admin\MetricController;
use App\Http\Controllers\Admin\MetricSyncController;

Route::prefix('admin')->name('admin.')->group(function () {

    // --- Auth Admin ---
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
        ->middleware('guest:web')
        ->name('login.form');
    Route::post('/login', [AdminLoginController::class, 'login'])
        ->middleware('guest:web')
        ->name('login');
    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->name('logout');

    // --- Panel protegido (auth:web) ---
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // CRUDs de Riders
        Route::get('/riders/list', [AdminRiderController::class, 'list'])->name('riders.list');
        Route::get('/riders/{rider}/assignments', [AdminRiderController::class, 'getPrefacturaAssignments'])->name('riders.assignments');
        Route::resource('/riders', AdminRiderController::class);

        // Módulo de Horario de Riders
        Route::group(['prefix' => 'riders/{rider}/schedules', 'as' => 'riders.schedules.'], function () {
            Route::get('/', [AdminRiderScheduleController::class, 'index'])->name('index');
            Route::get('/show', [AdminRiderScheduleController::class, 'show'])->name('show');
            Route::post('/lock', [AdminRiderScheduleController::class, 'lock'])->name('lock');
            Route::post('/unlock', [AdminRiderScheduleController::class, 'unlock'])->name('unlock');
            Route::post('/mark-submitted', [AdminRiderScheduleController::class, 'markAsSubmitted'])->name('mark-submitted');
        });

        // Módulo de Métricas por Rider (dentro de la carpeta del rider)
        Route::get('/riders/{id}/metrics', [AdminRiderMetricsController::class, 'index'])->name('riders.metrics.index');

        // Otros CRUDs y funcionalidades
        Route::resource('/forecasts', ForecastController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::resource('/accounts', AdminAccountController::class);

        // Asignaciones
        Route::get('/accounts/{account}/assign', [AdminAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/accounts/{account}/assign', [AdminAssignmentController::class, 'store'])->name('assignments.store');
        Route::post('/assignments/{assignment}/end', [AdminAssignmentController::class, 'end'])->name('assignments.end');

        // Cobertura
        Route::get('/coverage/{city?}/{week?}', [CoverageController::class, 'index'])->name('coverage.index');

        // Estado de Riders
        Route::get('/rider-status/{city?}/{week?}', [RiderStatusController::class, 'index'])->name('rider-status.index');

        // --- Métricas de Operación (general) ---
        Route::get('/metrics', [MetricController::class, 'index'])->name('metrics.index');
        Route::get('/metrics/list', [MetricController::class, 'list'])->name('metrics.list');
        Route::get('/metrics/kpis', [MetricController::class, 'kpis'])->name('metrics.kpis');
        Route::post('/metrics/sync', [MetricSyncController::class, 'sync'])->name('metrics.sync');

        // --- Prefacturación ---
        Route::get('/prefacturas', [PrefacturaController::class, 'index'])->name('prefacturas.index');
        Route::get('/prefacturas/{prefactura}', [PrefacturaController::class, 'show'])->name('prefacturas.show');
        Route::post('/prefacturas', [PrefacturaController::class, 'store'])->name('prefacturas.store');
        Route::post('/prefacturas/items/{item}/assign', [PrefacturaController::class, 'assignRider'])->name('prefacturas.assignRider');
        Route::post('/prefacturas/assignments/{assignment}/status', [PrefacturaController::class, 'updateAssignmentStatus'])->name('prefacturas.assignments.updateStatus');
    });
});
