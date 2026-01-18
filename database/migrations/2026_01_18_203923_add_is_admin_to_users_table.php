<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * Añade el flag iis_admin a la tabla users.
 * 
 * Este campoo se usa para identificar usuarios con permisos
 * administrativos dentro del sistema (gestión de módulos,
 * concesión de acceso, etc.).
 * 
 * Se evita un sistema de roles complejo en el MVP para
 * mantener simplicidad y control.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
