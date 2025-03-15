<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalLetter extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'medical_letters';

    /**
     * Los atributos que son asignables de manera masiva (mass assignable).
     * 
     * Estos atributos pueden ser asignados usando métodos como `create()` o `update()`.
     * 
     * @var array
     */
    protected $fillable = [
        'user_id',         // Relación con el usuario
        'name_children',   // Nombre del niño
        'file_path',       // Ruta de la imagen
        'visit_place',     // Lugar de la visita
        'visit_date',      // Fecha de la visita
        'specialist_name', // Nombre del especialista
    ];

    /**
     * Relación inversa con el modelo User.
     * 
     * Un "MedicalLetter" pertenece a un "User".
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Relación inversa (un MedicalLetter pertenece a un User)
    }
}
