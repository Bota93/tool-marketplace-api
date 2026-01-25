<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo ModuleMedia
 * 
 * Elemento multimedia asociado a un módulo.
 * En esta fase solo almacena URLs externas.
 */
class ModuleMedia extends Model
{
    protected $table = 'module_media';

    protected $fillable = [
        'module_id',
        'type',
        'url',
        'provider',
        'sort_order',
        'alt_text',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
