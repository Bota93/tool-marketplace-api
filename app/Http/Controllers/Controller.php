<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controlador base de la aplicación
 * 
 * Todos los controladores deben extender de esta clase.
 * 
 * Proporciona:
 * - authorize(): autorización por vía Policies
 * - validate(): validación de request
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
