<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

/**
 * Controlador de módulos (lectura privada).
 *
 * Expone únicamente los módulos a los que el usuario autenticado
 * tiene acceso válido. Si no hay acceso, el sistema responde 404
 * para no revelar la existencia del recurso.
 * 
 * Regla de seguridad:
 *  - Si no hay un acceso, devolvemos 404 (no revelamos existencia)
 */
class ModuleController extends Controller
{
    /**
     * Lista los módulos publicados a los que el usuario tiene acceso.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $modules = Module::query()
            ->published()
            ->accessibleBy($user)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $modules,
        ]);
    }

    /**
     * Devuelve el detalle de un módulo por slug.
     *
     * Si el módulo no existe, no está publicado o el usuario
     * no tiene acceso, se responde con 404.
     */
    public function show(Request $request, string $slug)
    {
        $user = $request->user();

        $module = Module::query()
            ->where('slug', $slug)
            ->with('media')
            ->first();

        if (!$module) {
            abort(404);
        }

        try {
            $this->authorize('view', $module);
        } catch (AuthorizationException $e) {
            abort(404);
        }

        return response()->json([
            'data' => $module,
        ]);
    }
}
