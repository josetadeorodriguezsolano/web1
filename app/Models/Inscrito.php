<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inscrito extends Model
{
    use HasFactory;

   protected $fillable = [
        'alumno_id',
        'grupo_id',
        'estatus',
    ];

    // Relaciones
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    // Accesores para datos del grupo
    public function getGradoAttribute()
    {
        return $this->grupo->grado ?? null;
    }

    public function getGeneracionAttribute()
    {
        return $this->grupo->generacion ?? null;
    }

    public function getSalonAttribute()
    {
        return $this->grupo->letra ?? null;
    }

}
