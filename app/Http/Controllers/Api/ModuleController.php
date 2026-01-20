<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

/**
 * Controlador de módulos (lectura).
 *
 * Expone únicamente los módulos a los que el usuario autenticado
 * tiene acceso válido. Si no hay acceso, el sistema responde 404
 * para no revelar la existencia del recurso.
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
            ->where('status', 'published')
            ->whereHas('accesses', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereNull('revoked_at');
            })
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
            ->where('status', 'published')
            ->whereHas('accesses', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereNull('revoked_at');
            })
            ->first();

        if (!$module) {
            abort(404);
        }

        return response()->json([
            'data' => $module,
        ]);
    }
}
