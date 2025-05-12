<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Imparte extends Model
{
    use HasFactory;

    protected $table = 'imparte'; // Usa 'protected' en lugar de 'public' para convenciones de Eloquent
    public $timestamps = true;

    // Relación con Maestro
    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'maestro_id'); // Asegúrate de que 'maestro_id' es el nombre correcto de la columna foránea
    }

    // Relación con Materia
   public function materia()
{
    return $this->belongsTo(Materia::class);
}

public function grupo()
{
    return $this->belongsTo(Grupo::class);
}
}
