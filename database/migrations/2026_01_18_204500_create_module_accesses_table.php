<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla module_accesses
 * 
 * Controla el acceso de los usuarios a los módulos.
 * 
 * Un acceso puede originarse por:
 * - grant: concesión manual por un administrador
 * - purchase: compra del módulo (futuro)
 * 
 * Esta tabla es la fuente de verdad para saber
 * si un usuario puede ver/usar un módulo.
 */
return new class extends Migration
{
    
    public function up(): void
{
    Schema::create('module_accesses', function (Blueprint $table) {
        $table->id();

        // Modulo al que se concede acceso
        $table->foreignId('module_id')
            ->constrained('modules')
            ->cascadeOnDelete();
        // Usuario que recibe el acceso    
        $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

        /**
         * Tipo de acceso:
         * - grant: acceso concedido manualmente por admin
         * - purchase: acceso por compra (futuro)
         */
        $table->string('access_type')->default('grant');

        /**
         * Usuario administrador que concedio el acceso.
         * Null cuando el acceso no es manual (ej: compra).
         */
        $table->foreignId('granted_by_user_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        // Momento en que se concedio el acceso
        $table->timestamp('granted_at')->useCurrent();

        /**
         * Si se establece, el acceso queda revocado
         * sin eliminar el registro (auditoria).
         */
        $table->timestamp('revoked_at')->nullable();

        /**
         * Campo flexible para información adicional:
         * - id de pago
         * - proveedor
         * - notas internas
         */
        $table->json('metadata')->nullable();

        $table->timestamps();

        /**
         * Garantiza que un usuario no tenga múltiples
         * accesos activos duplicados al mismo módulo.
         * 
         * Simplificación válida para el MVP
         */
        $table->unique(['module_id', 'user_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_accesses');
    }
};
