<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Falta extends Model
{
    use HasFactory;
    public $table="faltas";
    public $timestamps=true;
    protected $fillable = ['alumno_id', 'fecha', 'justificada'];

    // Relación con Alumnos
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}
