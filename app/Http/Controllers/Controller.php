<?php

namespace App\Http\Controllers;

// Añadimos las importaciones necesarias de Laravel
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

// Hacemos que nuestro controlador extienda del controlador base de Laravel
class Controller extends BaseController
{
    /**
     * @var \Illuminate\Foundation\Auth\Access\AuthorizesRequests
     * @var \Illuminate\Foundation\Validation\ValidatesRequests
     */
    // Añadimos los traits que nos dan acceso a helpers como ->authorize() y ->validate()
    use AuthorizesRequests, ValidatesRequests;
}
