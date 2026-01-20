<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ModuleAccess;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controlador administrativo de módulos.
 *
 * Permite a usuarios administradores:
 * - Crear módulos
 * - Conceder acceso a usuarios
 *
 * Los endpoints se ocultan (404) si el usuario no es admin.
 */
class ModuleAdminController extends Controller
{
    /**
     * Verifica que el usuario sea administrador.
     * Si no lo es, se responde con 404 para ocultar el endpoint.
     */
    private function assertAdmin(Request $request): void
    {
        if (!$request->user() || !$request->user()->is_admin) {
            abort(404);
        }
    }

    /**
     * Crea un nuevo módulo.
     */
    public function store(Request $request)
    {
        $this->assertAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:modules,slug'],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $module = Module::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'version' => $validated['version'] ?? null,
            'price_cents' => $validated['price_cents'],
            'currency' => $validated['currency'] ?? 'EUR',
            'status' => $validated['status'],
        ]);

        return response()->json([
            'data' => $module,
        ], 201);
    }

    /**
     * Concede acceso a un módulo a un usuario (gratis).
     */
    public function grant(Request $request, int $moduleId)
    {
        $this->assertAdmin($request);

        $validated = $request->validate([
            'user_email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $validated['user_email'])->firstOrFail();
        $module = Module::query()->findOrFail($moduleId);

        $access = ModuleAccess::query()
            ->where('module_id', $module->id)
            ->where('user_id', $user->id)
            ->first();

        if ($access) {
            $access->update([
                'access_type' => 'grant',
                'granted_by_user_id' => $request->user()->id,
                'revoked_at' => null,
                'metadata' => null,
            ]);
        } else {
            $access = ModuleAccess::create([
                'module_id' => $module->id,
                'user_id' => $user->id,
                'access_type' => 'grant',
                'granted_by_user_id' => $request->user()->id,
            ]);
        }

        return response()->json([
            'data' => $access,
        ]);
    }
}
