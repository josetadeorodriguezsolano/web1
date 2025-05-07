<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inasistencia extends Model
{
    use HasFactory;

    protected $table = 'inacistencias';

    protected $fillable = [
        'maestros_id',
        'materias_id',
        'grupos_id',
        'horario_falta',
        'horario_llegada',
        'justificacion',
    ];

    // Relaciones (opcional pero recomendado)
    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'maestros_id');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materias_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupos_id');
    }
}