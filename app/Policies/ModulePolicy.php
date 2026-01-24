<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;

/**
 * Policy: ModulePolicy
 * 
 * Centraliza las reglas de autorización sobre módulos.
 * 
 * Notas de seguridad:
 * - Los módulos son privados.
 * - Si el usuario no tiene acceso, el endpoint debe comportarse como si no existiera (404).
 * Esa decisión se aplica en el controlador (no aquí), porque Laravel por defecto responde 403.
 */
class ModulePolicy
{
    /**
     * Regla base para visualizar un módulo.
     * 
     * Condiciones:
     * - El módulo debe estar publicado
     * - El usuario debe tener un acceso activo (revoked_at = null)
     */
    public function view(User $user, Module $module): bool
    {
        if  ($module->status !== 'published') {
            return false;
        }

        return $module->accesses()
            ->where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->exists();
    }

    /**
     * Descarga: por ahora, misma regla que view.
     * Si más adelante se quiere limitar la descarga se separa aquí
     */
    public function download(User $user, Module $module): bool
    {
        return $this->view($user, $module);
    }
}
