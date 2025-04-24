<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alumno extends Model
{
    use HasFactory;
    //public $table= "alumnos";
    public $timestamps=true;

    public function falto($fecha){
        return Falta::where([['alumno_id',$this->id],['fecha',$fecha]])->first();
    }
    public function inscritos()
{
    return $this->hasMany(Inscrito::class);
}
}
