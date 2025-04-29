<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Falta;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos'; // Opcional, pero recomendable

    public $timestamps = true;

    protected $fillable = [
        'matricula',
        'nombres',
        'apellidos',
        'estatus',
        'curp',
        'contacto',
        'tutor',
    ];

    protected $attributes = [
        'estatus' => 'vigente',
    ];

    public function inscritos(): HasMany
    {
        return $this->hasMany(Inscrito::class);
    }

    public function faltas(): HasMany
    {
        return $this->hasMany(Falta::class);
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    public function falto($fecha)
    {
        return $this->faltas()->where('fecha', $fecha)->first();
    }
}
