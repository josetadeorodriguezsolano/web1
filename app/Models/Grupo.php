<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grupo extends Model
{
    use HasFactory;

    public function inscritos(){//Collection
        return $this->hasMany(Inscrito::class);//,'grupo_id','id');
    }

    public function alumnos()
{
    return $this->hasManyThrough(
        Alumno::class,
        Inscrito::class,
        'grupo_id',   // Foreign key on inscritos table...
        'id',         // Local key on alumnos table...
        'id',         // Local key on grupos table...
        'alumno_id'   // Foreign key on inscritos table...
    )->orderBy('apellidos');
}

    

    public function materia(){
        return $this->belongsTo(Materia::class);
    }

    public function imparte() {
        return $this->hasMany(Imparte::class);
      }
}
