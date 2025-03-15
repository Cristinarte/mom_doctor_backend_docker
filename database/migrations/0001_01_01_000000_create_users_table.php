<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Esta función se ejecuta cuando se corre `php artisan migrate` para crear las tablas en la base de datos.
     * 
     * Aquí estamos creando tres tablas:
     * 1. 'users' para almacenar la información de los usuarios.
     * 2. 'password_reset_tokens' para almacenar los tokens de restablecimiento de contraseñas.
     * 3. 'sessions' para almacenar las sesiones activas de los usuarios.
     * 
     * @return void
     */
    public function up(): void
    {
        // Crear la tabla 'users'
        Schema::create('users', function (Blueprint $table) {
            // La columna 'id' es la clave primaria auto-incremental
            $table->id();

            // La columna 'name' para almacenar el nombre del usuario
            $table->string('name');

            // La columna 'email' para almacenar el correo del usuario, con restricción de ser único
            $table->string('email')->unique();

            // La columna 'email_verified_at' para almacenar la fecha de verificación del correo
            $table->timestamp('email_verified_at')->nullable();

            // La columna 'password' para almacenar la contraseña del usuario
            $table->string('password');

            // La columna 'remember_token' para almacenar el token de "Recordarme" (opcional)
            $table->rememberToken();

            // Las columnas 'created_at' y 'updated_at' que Laravel maneja automáticamente
            $table->timestamps();
        });

        // Crear la tabla 'password_reset_tokens'
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // La columna 'email' es la clave primaria (para asociar el token a un usuario)
            $table->string('email')->primary();

            // La columna 'token' para almacenar el token de restablecimiento
            $table->string('token');

            // La columna 'created_at' para almacenar la fecha y hora en la que se creó el token
            $table->timestamp('created_at')->nullable();
        });

        // Crear la tabla 'sessions'
        Schema::create('sessions', function (Blueprint $table) {
            // La columna 'id' es la clave primaria
            $table->string('id')->primary();

            // La columna 'user_id' es una clave foránea opcional que hace referencia a la tabla 'users'
            $table->foreignId('user_id')->nullable()->index();

            // La columna 'ip_address' para almacenar la dirección IP desde la que el usuario se conecta
            $table->string('ip_address', 45)->nullable();

            // La columna 'user_agent' para almacenar el agente de usuario (navegador, dispositivo)
            $table->text('user_agent')->nullable();

            // La columna 'payload' para almacenar información adicional sobre la sesión
            $table->longText('payload');

            // La columna 'last_activity' para almacenar el último momento de actividad de la sesión
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Revertir las migraciones.
     * 
     * Esta función se ejecuta cuando se corre `php artisan migrate:rollback` para eliminar las tablas creadas.
     * 
     * @return void
     */
    public function down(): void
    {
        // Eliminar las tablas que fueron creadas
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
