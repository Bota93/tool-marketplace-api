<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla modules
 * 
 * Representa los módulos que ofrece la plataforma.
 * 
 * Los módulos:
 * - Son creados únicamente por administradores.
 * - Son privados por defecto (el acceso se controla en module_accessses).
 * - Pueden ser gratuitos (price_cents = 0) o de pago.
 * 
 * La visibilidad real de un módulo no depende de esta tabla,
 * sino de la existencia de un acceso válido para el usuario.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            // Nombre visible del módulo
            $table->string('name');

            // Identificador único para URLs y referencias externas
            $table->string('slug')->unique();

            // Resumen corto (listado)
            $table->string('summary')->nullable();
            // Descripción larga (detalle del módulo)
            $table->text('description')->nullable();

            // Versión del módulo (semver u otro formato)
            $table->string('version')->nullable();

            /**
             * Precio en céntimos para evitar problemas de precisión.
             * 0 = gratuito, pero sigue siendo privado.
             */
            $table->unsignedInteger('price_cents')->default(0);
            // Moneda ISO
            $table->string('currency', 3)->default('EUR');

            /**
             * Estado editorial del módulo.
             * - draft: no visible ni accesible
             * - published: disponible para usuarios con acceso
             */
            $table->string('status')->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
