<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Materia extends Model 
{
    use HasFactory;

    protected $table = 'materias';

    protected $fillable = ['nombre', 'clave', 'creditos', 'grado'];

    
    public function scopePorGrado($query, $grado)
    {
        return $query->where('grado', $grado);
    }

   
    public function impartes()
    {
        return $this->hasMany(Imparte::class);
    }
    
    
    public static function porGrado($grado)
    {
        return self::where('grado', $grado)->get();
    }
}