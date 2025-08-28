<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Si la ruta a la que se intenta acceder empieza con 'admin/'...
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            // Para cualquier otro caso (asumiendo que es del rider o la raíz)...
            return route('rider.login');
        }

        return null;
    }
}
