<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Horario extends Model
{
 use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'horarios';

    // Campos que se pueden asignar masivamente
    protected $fillable = ['imparte_id', 'hora_numero', 'dia_semana'];



    // Relación con Imparte
    public function imparte()
    {
        return $this->belongsTo(Imparte::class, 'imparte_id');
    }

    // Relación con inasistencias (opcional, útil si quieres acceder desde aquí)
    public function inasistencias()
    {
        return $this->hasMany(Inasistencia::class, 'horario_id');
    }
}