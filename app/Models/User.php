<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Los atributos que son asignables de manera masiva (mass assignable).
     * 
     * Estos atributos pueden ser asignados usando métodos como `create()` o `update()`.
     * 
     * @var array
     */
    protected $fillable = [
        'name',      // Nombre del usuario
        'email',     // Correo electrónico del usuario
        'password',  // Contraseña del usuario
    ];

    /**
     * Los atributos que deben ser ocultados para la serialización.
     * 
     * Estos atributos no serán incluidos cuando se convierta el modelo a JSON.
     * 
     * @var array
     */
    protected $hidden = [
        'password',        // La contraseña del usuario
        'remember_token',  // El token de "recordar sesión"
    ];

    /**
     * Los atributos que deben ser casteados a un tipo específico.
     * 
     * Esto se utiliza para transformar automáticamente ciertos atributos cuando se accede a ellos.
     * 
     * @return array
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Convierte el campo 'email_verified_at' a un objeto de tipo datetime
            'password' => 'hashed',            // Convierte el campo 'password' a un tipo 'hashed' para mayor seguridad
        ];
    }

    /**
     * Relación uno a muchos con el modelo MedicalLetter.
     * 
     * Un usuario puede tener múltiples cartas médicas (MedicalLetter).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medicalLetters()
    {
        return $this->hasMany(MedicalLetter::class); // Un usuario puede tener muchas cartas médicas
    }
}
