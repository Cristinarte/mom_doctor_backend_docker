<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     * 
     * Este método se ejecuta cuando se corre `php artisan migrate` para modificar la tabla 'medical_letters' y agregar una nueva columna.
     * 
     * Se agrega la columna 'specialist_name' que almacena el nombre del especialista médico que atendió al niño.
     * 
     * @return void
     */
    public function up(): void
    {
        // Modificar la tabla 'medical_letters'
        Schema::table('medical_letters', function (Blueprint $table) {
            // Agregar la columna 'specialist_name' después de la columna 'name_children'
            $table->string('specialist_name')->after('name_children')->nullable();
        });
    }

    /**
     * Revertir la migración.
     * 
     * Este método se ejecuta cuando se corre `php artisan migrate:rollback` para eliminar la columna 'specialist_name' de la tabla 'medical_letters'.
     * 
     * @return void
     */
    public function down(): void
    {
        // Modificar la tabla 'medical_letters' para eliminar la columna 'specialist_name'
        Schema::table('medical_letters', function (Blueprint $table) {
            // Eliminar la columna 'specialist_name'
            $table->dropColumn('specialist_name');
        });
    }
};
