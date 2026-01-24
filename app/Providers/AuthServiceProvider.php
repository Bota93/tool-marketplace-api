<?php

namespace App\Providers;

use App\Models\Module;
use App\Policies\ModulePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * AuthServiceProvider
 * 
 * Este provider es el punto central donde se registran
 * las políticas de autorización (Policies) del sistema.
 * 
 * Su responsabilidad es decirle a Laravel:
 * - Que Policy corresponde a cada Modelo
 * - Cómo debe resolverse la autorización cuando se llama a Gate::authorize()
 * 
 * IMPORTANTE:
 * - Este archivo NO contiene lógica de negocio.
 * - Solo define el "mapa" entre modelos y policies.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de modelos a policies
     * 
     * La clave es el modelo.
     * El valor es la policy que controla el acceso a ese modelo.
     * 
     * Ejemplo:
     * - Module::class = ModulePolicy::class
     * 
     * Esto permite llamadas como:
     *  Gate::authorize('view', $module);
     */
    protected $policies = [
        Module::class => ModulePolicy::class,
    ];

    /**
     * Método boot del provider.
     * 
     * Se ejecuta automáticamente al iniciar la aplicación.
     * Registra las policies definidas arriba dentro del sistema
     * de autorización de Laravel
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
