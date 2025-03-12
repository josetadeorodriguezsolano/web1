<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grupo extends Model
{
    use HasFactory;

    public function inscritos(){
        return $this->hasMany(Inscrito::class);//,'grupo_id','id');
    }

    public function alumnos(){// es un hasMany pero utiliza una tabla pivote
        return $this->hasManyThrough(Alumno::class,Inscrito::class
                    ,'grupo_id','id','id','alumno_id')->orderBy('apellidos');;
    }
}
