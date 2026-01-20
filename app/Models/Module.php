<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModuleAccess;

/**
 * Modelo Module
 * 
 * Representa un módulo que ofrece la plataforma.
 * 
 * Un módulo:
 * - Es creado únicamente por administradores.
 * - Es privado por defecto.
 * - Puede ser gratuito o de pago.
 * - Solo es accesible si existe un acceso válido (module_accesses).
 * - Este modelo NO decide quién puede acceder al módulo, esa
 * lógica vive en la tabla module_accesses y en los controladores.
 */

class Module extends Model
{
    /**
     * Atributos asignables en masa.
     * 
     * Protege el modelo frente a mass assigment
     * y deja explícito qué campos pueden rellenarse
     * al crear o actualizar un módulo.
     */
    protected $fillable = [
        'name',
        'slug',
        'summary',
        'description',
        'version',
        'price_cents',
        'currency',
        'status',
    ];

    /**
     * Relación con los accesos al módulo.
     * 
     * Un módulo puede tener múltiples accesos
     * (usuarios distintos, compras, concesiones).
     */
    public function accesses()
    {
        return $this->hasMany(ModuleAccess::class);
    }
}
