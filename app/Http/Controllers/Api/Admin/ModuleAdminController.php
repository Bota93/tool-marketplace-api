<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ModuleAccess;
use App\Models\User;
use App\Models\ModuleMedia;
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
     * Crea un nuevo módulo.
     */
    public function store(Request $request)
    {
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

    /**
     * MVP:
     * - Solo admin
     * Solo URL externa
     */
    public function addMedia(Request $request, int $moduleId)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:image,video'],
            'url' => ['required', 'url'],
            'provider' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $module = Module::query()->findOrFail($moduleId);

        $media = ModuleMedia::create([
            'module_id' => $module->id,
            'type' => $validated['type'],
            'url' => $validated['url'],
            'provider' => $validated['provider'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'alt_text' => $validated['alt_text'] ?? null,
        ]);

        return response()->json(['data' => $media], 201);
    }
}
