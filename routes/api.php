<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\Admin\ModuleAdminController;

/**
 * --------------------------------------------------------------
 * Rutas de la API
 * --------------------------------------------------------------
 * 
 * Este archivo define todas las rutas expuestas por la API del proyecto.
 * El enfoque es API-first: no se sirven vistas ni sesiones web.
 * 
 * La autenticación se realiza mediante tokens (Laravel Sanctum),
 * enviados por el cliente en el header:
 * Authorization: Bearer <token>
 * 
 * Todas las rutas aquí definidas estarán prefijadas automáticamente
 * con /api.
 * 
 */

/**
 * --------------------------------------------------------------
 * Rutas de autenticación
 * --------------------------------------------------------------
 * 
 * Agrupamos todas las rutas relacinoadas con autenticación bajo el 
 * prefijo /api/auth para mantener un contrato claro y coherente.
 * 
 * - Rutas públicas: register, login
 * - Rutas protegidas: me, logout
 * 
 */
Route::prefix('v1')->group(function () {

    /**
     * Auth
     */
    Route::prefix('auth')->group(function () {
        /**
         * --------------------------------------------------------------
         * Registro de usuario (ruta pública)
         * --------------------------------------------------------------
         * 
         * Permite crear un nuevo usuario en el sistema.
         * Devuelve un token de autenticación que deberá ser usado
         * por el cliente en las siguientes peticiones.
         * 
         */
        Route::post('/register', [AuthController::class, 'register']);

        /**
         * --------------------------------------------------------------
         * Login de usuario (ruta pública)
         * --------------------------------------------------------------
         * 
         * Verifica las credenciales del usuario y genera un nuevo token.
         * No utiliza sesiones ni cookies.
         * 
         */
        Route::post('/login', [AuthController::class, 'login']);

        /**
         * --------------------------------------------------------------
         * Rutas protegidas por autenticación
         * --------------------------------------------------------------
         * 
         * Estas rutas requieren un token válido enviado en el header
         * Authorization mediante el middleware auth:sanctum.
         * 
         */
        Route::middleware('auth:sanctum')->group(function () {

            /**
             * --------------------------------------------------------------
             * Usuario autenticado actual
             * --------------------------------------------------------------
             * 
             * Devuelve la información del usuario asociado al token
             * utilizado en la petición.
             * 
             */
            Route::get('/me', [AuthController::class, 'me']);

            /**
             * --------------------------------------------------------------
             * Logout
             * --------------------------------------------------------------
             * 
             * Revoca el token actual para impedir su uso posterior.
             * 
             */
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    /**
     * Modules (Marketplace read)
     * 
     * Se hace público
     */
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{module:slug}', [ModuleController::class, 'show']);

    /**
     * Admin (oculto con 404 si no admin)
     */
    Route::middleware('auth:sanctum')->prefix('admin')->middleware('admin')->group(function () {
        // Administración de módulos
        Route::post('/modules', [ModuleAdminController::class, 'store']);
        //Route::patch('/modules/{module}', [ModuleAdminController::class, 'update']);

        Route::post('/modules/{module}/accesses', [ModuleAdminController::class, 'grant']);
        //Route::delete('/modules/{module}/accesses/{user}', [ModuleAdminController::class, 'revoke']);

        Route::post('/modules/{module}/media', [ModuleAdminController::class, 'addMedia']);
    });
});
