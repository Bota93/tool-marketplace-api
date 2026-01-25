<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla: module_mmedia
 * 
 * Guarda elementos multimedia asociados a un módulo (imágenes)
 * En esta fase: solo URLs (sin upload)
 */
return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('module_media', function (Blueprint $table) {
            $table->id();

            // Relación con modules
            $table->foreignId('module_id')
                ->constrained('modules')
                ->cascadeOnDelete();
            
            // Tipo de media: imagen
            $table->string('type', 20); // Imagen más adelante vídeo

            // URL absoluta (GitHub)
            $table->text('url');

            // Proveedor (opcional): github
            $table->string('provider', 50)->nullable();

            // Orden en la galería
            $table->unsignedBigInteger('sort_order')->default(0);

            // Texto alternativo (accesibilidad/SEO)
            $table->string('alt_text', 255)->nullable();

            $table->timestamps();

            // Índices adicionales
            $table->index(['module_id', 'type']);
            $table->index(['module_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_media');
    }
};
