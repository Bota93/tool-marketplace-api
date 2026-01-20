<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use App\Models\User;

/**
 * Modelo ModuleAccess
 * 
 * Representa un permiso de acceso de un usuario a un módulo.
 * 
 * Esta tabla es la "fuente de verdad" del acceso:
 * - Si existe un registro activo (revoked_at = null), el usuario tiene acceso.
 * - Si revoked_at tiene valor, el acceso está revocado (auditoria sin borrar).
 * 
 * Además guarda el origen del acceso:
 * - grant: concesión manual (gratis)
 * - purchase: compra (futuro)
 */
class ModuleAccess extends Model
{
    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'module_id',
        'user_id',
        'access_type',
        'granted_by_user_id',
        'granted_at',
        'revoked_at',
        'metadata',
    ];

    /**
     * Casts para trabajar con tipos correctos en PHP.
     */
    protected $casts = [
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relación: este acceso pertenece a un módulo.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Relación: este acceso pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
