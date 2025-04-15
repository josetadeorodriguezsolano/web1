<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alumno extends Model
{
    use HasFactory;
    //public $table= "alumnos";
    public $timestamps=true;

    protected $fillable=[
        'matricula',
        'nombres',
        'apellidos',
        'estatus',
        'curp',
        'contacto',
        'tutor'
    ];

    public function falto($fecha){
        return Falta::where([['alumno_id',$this->id],['fecha',$fecha]])->first();
    }
}
