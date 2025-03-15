<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     * 
     * Este método se ejecuta cuando se corre `php artisan migrate` para crear la tabla 'medical_letters' en la base de datos.
     * 
     * La tabla 'medical_letters' almacena la información relacionada con las cartas médicas. Esta tabla está relacionada con los usuarios a través de la columna 'user_id'.
     * 
     * @return void
     */
    public function up(): void
    {
        // Crear la tabla 'medical_letters'
        Schema::create('medical_letters', function (Blueprint $table) {
            // La columna 'id' es la clave primaria auto-incremental
            $table->id();

            // La columna 'user_id' es una clave foránea que hace referencia a la tabla 'users' (relación uno a muchos)
            // Si un usuario se elimina, las cartas médicas asociadas a él también se eliminarán (onDelete('cascade'))
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 

            // La columna 'name_children' almacena el nombre del niño asociado con la carta médica
            $table->string('name_children');

            // La columna 'file_path' almacena la ruta al archivo (imagen) de la carta médica
            $table->string('file_path');

            // La columna 'visit_place' almacena el lugar de la visita médica
            $table->string('visit_place');

            // La columna 'visit_date' almacena la fecha de la visita médica
            $table->date('visit_date');

            // Las columnas 'created_at' y 'updated_at' que Laravel maneja automáticamente
            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     * 
     * Este método se ejecuta cuando se corre `php artisan migrate:rollback` para eliminar la tabla 'medical_letters' de la base de datos.
     * 
     * @return void
     */
    public function down(): void
    {
        // Eliminar la tabla 'medical_letters' si existe
        Schema::dropIfExists('medical_letters');
    }
};
