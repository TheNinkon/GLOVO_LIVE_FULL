<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RiderLoginController;

// Carga las rutas del panel de administración
require __DIR__ . '/admin.php';

// Carga las rutas para los riders
require __DIR__ . '/rider.php';
require __DIR__ . '/api.php';
/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('rider.login');
});
