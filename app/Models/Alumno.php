<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Falta;

class Alumno extends Model
{
    use HasFactory;
    //public $table= "alumnos";
    public $timestamps=true;

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

    public function falto($fecha)
    {
    return Falta::where([
        ['alumno_id', '=', $this->id],
        ['fecha', '=', $fecha]
    ])->first();
    }

    public function inscritos()
    {
        return $this->hasMany(Inscrito::class);
    }

    public function faltas()
    {
        return $this->hasMany(Falta::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }
}
