<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RiderLoginController;
use App\Http\Controllers\Rider\DashboardController as RiderDashboardController;
use App\Http\Controllers\Rider\Schedule\ScheduleController as RiderScheduleController;
use App\Http\Controllers\Rider\ProfileController;
use App\Http\Controllers\Rider\RiderMetricsController;

Route::prefix('rider')->name('rider.')->group(function () {

    // --- Auth Rider ---
    Route::get('/login', [RiderLoginController::class, 'showLoginForm'])
        ->middleware('guest:rider')
        ->name('login');
    Route::post('/login', [RiderLoginController::class, 'login'])
        ->middleware('guest:rider');
    Route::post('/logout', [RiderLoginController::class, 'logout'])->name('logout');

    // --- Panel Rider (auth:rider) ---
    Route::middleware(['auth:rider'])->group(function () {

        // Dashboard y perfil
        Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

        // Horario - Vista
        Route::get('/schedule/{week?}', [RiderScheduleController::class, 'index'])->name('schedule.index');

        // Horario - API para cargar datos
        // Esta es la nueva ruta que debe usar tu JS para cargar el JSON
        Route::get('/schedule-api/{week?}', [RiderScheduleController::class, 'getForecastData'])->name('schedule.data');

        // Acciones AJAX horario
        Route::post('/schedule/select', [RiderScheduleController::class, 'selectSlot'])->name('schedule.select');
        Route::post('/schedule/deselect', [RiderScheduleController::class, 'deselectSlot'])->name('schedule.deselect');

        // --- Métricas del Rider ---
        Route::get('/metrics', [RiderMetricsController::class, 'index'])->name('metrics.index');
        Route::get('/metrics/list', [RiderMetricsController::class, 'list'])->name('metrics.list');
        Route::get('/metrics/kpis', [RiderMetricsController::class, 'kpis'])->name('metrics.kpis');
    });
});
