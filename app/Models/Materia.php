<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Materia extends Model {
    use HasFactory;

    protected $table = 'materias';

    protected $fillable = ['nombre', 'clave', 'creditos'];

    public static function porGrado($grado){
        return Materia::where('grado',$grado)->get();
    }

}
