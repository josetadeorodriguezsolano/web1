<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Materia extends Model {
    use HasFactory;

    protected $table = 'materias';

    protected $fillable = [
        'nombre',
        'clave',
        'creditos',
        'grado',
    ];

    public static function porGrado($grado){
        return Materia::where('grado',$grado)->get();
    }

    public function imparte(): HasMany
    {
        return $this->hasMany(Imparte::class);
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

}
