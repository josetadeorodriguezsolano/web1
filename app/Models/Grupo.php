<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grupo extends Model
{
    use HasFactory;


    protected $fillable = [
        'grado',
        'letra',
        'generacion',
    ];

    public function inscritos(){//Collection
        return $this->hasMany(Inscrito::class);//,'grupo_id','id');
    }

    public function impartes()
    {
        return $this->hasMany(Imparte::class);
    }

    public function alumnos()
    {
        return $this->hasManyThrough(
            Alumno::class,
            Inscrito::class,
            'grupo_id',
            'id',
            'id',
            'alumno_id'
        )->orderBy('apellidos');
    }

    public function materias() //Cambio para poder obtener las materias que cursa un grupo
    {
        return $this->hasManyThrough(
            Materia::class,
            Imparte::class,
            'grupo_id',
            'id',
            'id',
            'materia_id'
        );
    }
}
